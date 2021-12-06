<?
include_once("include.php");

$wlog=new Logging();
$wlog->reset();

if(!isset($_SESSION['member_id'])) {
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	$mysqli->close();
	$wlog=null;
	exit;
}
$log="";
foreach($_POST as $key=>$value)	{
    $log.="{$key} [{$value}]<br>";
}
foreach($_SESSION as $key=>$value)	{
    $log.="SESSION {$key} [{$value}]<br>";
}
$wlog->write("{$log}",__LINE__,__FUNCTION__,__FILE__);

$mysqli->autocommit(false);
try {
    $rs=sqlrun("select viewtype from i_grid where rowid='{$_POST['tblid']}'",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception("No viewtype found");
    $row=$rs->fetch_assoc();
    $viewtype=$row['viewtype'];

    switch($viewtype){
    case "HRZN": case "VERT":
        $ndx=intval($_POST['pos'])-1;
        break;
    case "TREE":
        $ndx=intval($_POST['pos'])+2;
    }
    $sql="select * from i_col where par_rowid='{$_POST['tblid']}' and inactivated='0' order by seqno";
    $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
    
    if($rs->num_rows<1) throw new Exception("Column info not found");

    $rs->data_seek($ndx);
    $row=$rs->fetch_assoc();
    
    $tname=$row['tname'];
    $fname=$row['fname'];

    $wlog->write("viewtype [{$viewtype}]",__LINE__,__FUNCTION__,__FILE__);
    $wlog->write("ndx [{$ndx}] tname [{$row['tname']}] fname [{$row['fname']}] dtype [".getDtype($row['dtype'])."]]",__LINE__,__FUNCTION__,__FILE__);

    // check the type of updated column and enclose the update column with quote mark when it is character type
    $wlog->write("dtype [".getDtype($row['dtype'])."]",__LINE__,__FUNCTION__,__FILE__);
    switch(getDtype($row['dtype'])){
    case "string":
        $newval="'{$_POST['newval']}'";
        break;
    case "html":
        $newval=str_replace("'","\"",$_POST['newval']);
        $newval="'{$newval}'";
        break;
    case "bool":
        if($_POST['newval']=="on"){
            $newval="'1'";
        } else if($_POST['newval']=="off"){
            $newval="'0'";
        } else {
            $newval="'{$_POST['newval']}'";
        }
        break;
    default:
        $newval=trim($_POST['newval']);
    }
    $wlog->write("newval [{$newval}]",__LINE__,__FUNCTION__,__FILE__);
    // Unfolding trasferred record values.
    $arCol=explode("|",$_POST['oldrecord']);
    if(count($arCol)<1) throw new Exception("No record data.");
    
    // Finding unique key as primary key.
    $uniq_val=""; $primary_field="";
    $rs->data_seek(0);
    $n=0;
    while($v=$rs->fetch_assoc()){
        $wlog->write("unique_key [{$v['unique_key']}] tname [{$v['tname']}] _tname [{$tname}]",__LINE__,__FUNCTION__,__FILE__);
        if($v['unique_key']=='1' && $v['tname']==$tname) {
            $primary_field=$v['fname'];
            $uniq_val=trim($arCol[$n]);
            $wlog->write("k [{$n}] arCol [".ord($arCol[$n])."] pk [{$primary_field}] uniq_val [{$uniq_val}]",__LINE__,__FUNCTION__,__FILE__);
            break;
        }
        $n++;
    }
    $wlog->write("uniq_val [{$uniq_val}]",__LINE__,__FUNCTION__,__FILE__);
    if($uniq_val!=""){
        $sql="update {$tname} set {$fname}={$newval} where {$primary_field}='{$uniq_val}'";
    } else {  // no primary key as it is non-existant data record and by OUTER-JOIN
        $uniq_val=getROWID();
        $sql="insert into {$tname} set {$primary_field}='{$uniq_val}',{$fname}={$newval}";
        $rs->data_seek(0);
        $rset=$rs;
        $n=-1;
        while($v=$rs->fetch_assoc()){
            $n++;
            if($v['tname']!=$tname || $v['unique_key']=="1" || $v['fname']==$fname) continue;
            if($arCol[$n]!="" || $v['_join']=="" || $v['_join']=="0"){
                $sql.=",{$v['fname']}={$arCol[$n]}";
            } else {
                $sch=$v['_join'];
                if(substr($sch,-1)=="+") $sch=substr($sch,0,strlen($sch)-1);
                $sch=intval($sch);
                if(--$sch<1) throw new Exception("No join field");
                
                foreach($rset as $key=>$val){
                    if($val['_join']==strval($sch)){
                        $sql.=",{$v['fname']}='{$arCol[$key]}'";
                        break;
                    }
                }  
            }
        }
    }
    $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
    if($mysqli->error!="") throw new Exception("Update SQL failed.");
    $mysqli->commit();
    $answer['result']="0";
} catch(Exception $e){
    $mysqli->rollback();
    $answer['msg']=$e->getMessage();
}
$mysqli->autocommit(true);
$mysqli->close();
$wlog->write(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$wlog=null;
echo json_encode($answer);
?>