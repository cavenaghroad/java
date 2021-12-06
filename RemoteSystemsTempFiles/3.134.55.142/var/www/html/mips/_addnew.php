<?
include_once("include.php");

errorlog("party [{$_SESSION['party']}]",__LINE__,__FUNCTION__,__FILE__);
if(!isset($_SESSION['member_id'])) {
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	$mysqli->close();
	exit;
}

$log="";
foreach($_POST as $key=>$value)	{
	$log.="{$key} [{$value}] ";
}
foreach($_SESSION as $key=>$value)	{
    $log.="SESSION {$key} [{$value}]\n";
}
errorlog("{$log}",__LINE__,__FUNCTION__,__FILE__);

try{
    $rs=sqlrun("select viewtype from i_grid where rowid='{$_POST['tblid']}'",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception("No viewtype found");
    $row=$rs->fetch_assoc();
    $viewtype=$row['viewtype'];

    switch(strtoupper($viewtype)){
    /*
    * ********************************************************************************
    * parvalue="" regardless of value of sametable => make new root node with par_rowid =""
    * parvalue<>"" and parent record is in the same table =>make new child node with par_rowid = parvalue
    * parvalue<>"" and parent record is in the other(parent) table => make new ROOT node with rowid = parvalue 
    * ********************************************************************************
    */
    case "TREE":
        $arSQL=array();
        $sametable=$_POST['sametable'];
        
        $mysqli->autocommit(false);

        $rs=sqlrun("select * from i_col where par_rowid='{$_POST['tblid']}' and inactivated='0' order by seqno",__LINE__,__FUNCTION__,__FILE__);
        if($rs->num_rows<1) throw new Exception("No iCOL data [{$_POST['tblid']}]");
        
        if($_POST['parvalue']==""){
            $par_rowid="";
            $rowid=getROWID();
            $pClass="folder";
        } else if($sametable=="N"){
            $par_rowid="";
            $rowid=$_POST['parvalue'];
            $pClass="folder";
        } else {
            $par_rowid=$_POST['parvalue'];
            $rowid=getROWID();
            $pClass="file";
        }
        errorlog("ROWID [{$rowid}] PAR_ROWID [{$par_rowid}]",__LINE__,__FUNCTION__,__FILE__);
        $pstr="<tr data-tt-id={$rowid} data-tt-parent-id={$par_rowid}><td><span class={$pClass}>New</span></td>";
        
        $tagstr="";
        
        $tname=""; 
//         foreach($rs as $i=>$row){
        $n=0;
        while($row=$rs->fetch_assoc()){
            errorlog("i [{$n}] fname [{$row['fname']}] dtype [{$row['dtype']}] unique [{$row['unique_key']}] default [{$row['defvalue']}] childCol [{$childCol}]",__LINE__,__FUNCTION__,__FILE__);
            // check if new row has same table name
            $n=0;
            foreach($arSQL as $key=>$sql){
                if($key==$row['tname']) break;
                $n++;
            }
            // when new row has different table name, add new element to arSQL and set INSERT statement.
            if($n<count($arSQL)){
                $arSQL[$row['tname']].=",";
            } else {
                $arSQL[$row['tname']]="insert into {$row['tname']} set ";
            }
            errorlog("{$row['fname']} defvalue [{$row['defvalue']}]",__LINE__,__FUNCTION__,__FILE__);
            
            $tagvalue="";
            switch($row['fname']){
            case "rowid":
                $sqlvalue="'{$rowid}'"; break;
            case "par_rowid":
                $sqlvalue="'{$par_rowid}'"; break;
            case "title":
                $sqlvalue="'NEW'"; break;
            default:
                switch($row['defvalue']){
                case "parent":
                    $tagvalue=$_POST['parvalue'];
                    $sqlvalue="'{$tagvalue}'";
                    break;
                case "unique":  // unique value for rowid or other unique key.
                    $tagvalue=getROWID();
                    $sqlvalue="'{$tagvalue}'";
                    errorlog("_join [{$row['_join']}] defvalue [{$sqlvalue}]",__LINE__,__FUNCTION__,__FILE__);
                    break;
                default:
                    if(substr($row['defvalue'],0,1)=="#"){  // '#' means SESSION variable.
                        $tagvalue=$_SESSION[substr($row['defvalue'],1)];
                        $sqlvalue="'{$tagvalue}'";
                    } else {
                        switch(getDtype($row['dtype'])){
                        case "number":
                            if($row['defvalue']=="") {
                                $sqlvalue=$tagvalue="0";
                            } else {
                                $sqlvalue=$tagvalue=$row['defvalue'];
                            }
                            break;
                        case "html":
                            $tagvalue=htmlentities($row['defvalue']);
                            $sqlvalue="'{$tagvalue}'";
                            break;
                        case "bool":
                            if($row['defvalue']=="1"){
                                $tagvalue="1";
                            } else {
                                $tagvalue="0";
                            }
                            $sqlvalue="'{$tagvalue}'";
                            break;
                        default:
                            $tagvalue=$row['defvalue'];
                            $sqlvalue="'{$tagvalue}'";
                        }
                    }
                }
                if($row['align']!="") $align="align={$row['align']}";
                if($row['pixel']=="0") $align=" style='display:none;'";
                $pstr.="<td {$align}>{$tagvalue}</td>";
            }
            errorlog("tagvalue [{$tagvalue}] sqlvalue [{$sqlvalue}]",__LINE__,__FUNCTION__,__FILE__);
            if($row['_join']=="0"){
                $arSQL[$row['tname']].="{row['fname']}={$sqlvalue}";
                errorlog("{$row['fname']}={$sqlvalue}",__LINE__,__FUNCTION__,__FILE__);
            } else {
                for($x=0;$x<count($arSQL);$x++){
                    $arSQL[$x].="{$row['fname']}={$sqlvalue}";
                }
            }
        }
        foreach($arSQL as $key=>$sql){
            $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
            if($rs->num_rows<1) throw new Exception("failed [{$sql}]");
        }
        $answer['html']=$pstr."</tr>";
        $mysqli->commit();
        $answer['result']=0;
        break;
        /*
        * ********************************************************************************
        */
    case "HRZN":
        $childCol=intval($_POST['childcol']);

        $sql="select * from i_col where par_rowid='{$_POST['tblid']}' and inactivated='0' order by seqno";
        $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
        if($rs->num_rows<1) throw new Exception("No iCOL data [{$_POST['tblid']}]");

        $rowid=getROWID();
        
        $ndx=1;
        $strHTML="<tr><td align=center>*</td>";
        $arSQL=array();
        $i=-1;
        while($row=$rs->fetch_assoc()){
            $i++;
            errorlog("fname [{$row['fname']}] dtype [{$row['dtype']}] unique [{$row['unique_key']}] defvalue [{$row['defvalue']}] ndx [{$ndx}] childCol [{$childCol}]",__LINE__,__FUNCTION__,__FILE__);
            // when new row has different table name, add new element to arSQL and set INSERT statement.
            $tname=$row['tname'];
            $fname=$row['fname'];
            if(isset($arSQL[$tname])){
                $arSQL[$tname].=",";
            } else {
                $arSQL[$tname]="insert into {$tname} set ";
            }  
            
            // // $tagvalue="";
            if($i+1==$childCol){    // foreign key COLUMN
                $tagvalue=$_POST['parvalue'];
                $sqlvalue="'{$tagvalue}'";
            } else if($row['unique_key']=="1"){
                $tagvalue=$rowid;
                $sqlvalue="'{$tagvalue}'";
            } else if($row['_join']!="" && $row['_join']!="0"){ // inner/outer join
                $tagvalue=$rowid;
                $sqlvalue="'{$tagvalue}'";
            } else {
                switch($row['defvalue']){
                case "parent":
                    if($_POST['parvalue']=="") throw new Exception("Parent value is not given.");
                    $tagvalue=$_POST['parvalue'];
                    $sqlvalue="'{$tagvalue}'";
                    break;
                case "unique":  // unique value for rowid or other unique key.
                    $tagvalue=$rowid;
                    $sqlvalue="'{$tagvalue}'";
                    errorlog("_join [{$row['_join']}] defvalue [{$sqlvalue}]",__LINE__,__FUNCTION__,__FILE__);
                    break;
                default:
                    if(substr($row['defvalue'],0,1)=="#"){  // '#' means SESSION variable.
                        $tagvalue=$_SESSION[substr($row['defvalue'],1)];
                        $sqlvalue="'{$tagvalue}'";
                    } else {
                        switch(getDtype($row['dtype'])){
                        case "number":
                            if($row['defvalue']=="") {
                                $sqlvalue=$tagvalue="0";
                            } else {
                                $sqlvalue=$tagvalue=$row['defvalue'];
                            }
                            break;
                        case "html":
                            $tagvalue=htmlentities($row['defvalue']);
                            $sqlvalue="'{$tagvalue}'";
                            break;
                        case "bool":
                            $tagvalue="<input type=checkbox ";
                            if($row['defvalue']=="1"){
                                $tagvalue.="checked";
                            }
                            $tagvalue.=">";
                            $sqlvalue="'{$row['defvalue']}'";
                            break;
                        default:
                            $tagvalue=$row['defvalue'];
                            $sqlvalue="'{$tagvalue}'";
                        }
                    }
                }
            }
            errorlog("tagvalue [{$tagvalue}] sqlvalue [{$sqlvalue}]",__LINE__,__FUNCTION__,__FILE__);
            // array_push($arTag,$tagvalue);
            $arSQL[$tname].="{$fname}=".$sqlvalue;
            errorlog("{$tname} [{$arSQL[$tname]}]",__LINE__,__FUNCTION__,__FILE__);
            
            $align="";
            if($row['align']!="") $align="align={$row['align']}";
            if($row['pixel']=="0") $align.=" style='display:none;'";
            $strHTML.="<td {$align}>{$tagvalue}</td>";
        }
        foreach($arSQL as $key=>$sql){
            $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
        }
    }
    $answer['html']=$strHTML."</tr>";
    errorlog("TAG [".htmlentities($answer['html'])."]",__LINE__,__FUNCTION__,__FILE__);
    $answer['result']=0;
    $mysqli->commit();
} catch(Exception $e){
    $mysqli->rollback();
    if($e->getMessage()=="root"){
        $answer['html']=$pstr;
        $answer['result']=0;
    } else {
        errorlog($pstr,__LINE__,__FUNCTION__,__FILE__);
        $answer['msg']=$e->getMessage();
        errorlog("msg [{$answer['msg']}]",__LINE__,__FUNCTION__,__FILE__);
    }
}
$mysqli->autocommit(true);
errorlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$mysqli->close();
echo json_encode($answer);
?>