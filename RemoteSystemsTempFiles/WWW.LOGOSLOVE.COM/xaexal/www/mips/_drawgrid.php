<?
include_once("include.php");

if(!isset($_SESSION['member_id'])) {
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	$mysqli->close();
}
$wlog=new Logging();
$log="";
foreach($_POST as $key=>$value)	{
	$log.="{$key} [{$value}] ";
}
foreach($_SESSION as $key=>$value)	{
    $log.="SESSION {$key} [{$value}]\n";
}
$wlog->write("{$log}",__LINE__,__FUNCTION__,__FILE__);

/*
 * ********************************************************************************
 */   
try {    
    $rs=sqlrun("select * from i_grid where rowid='{$_POST['rowid']}' order by seqno",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception($mysqli->error);
  
    $row=$rs->fetch_assoc();
    $viewtype=$row['viewtype'];  
    $start=$_POST['start'];
    $pagesize=$row['pagesize'];
    
    $rs=sqlrun("select * from i_col where par_rowid='{$_POST['rowid']}' and inactivated='0' order by seqno",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception("No record from iCol");

    $column=array();
    $pixel=array();
    $align=array();
    // $tname=array();
    $dtype=array();
    $orderby=array();
    
    $query_key=array();
    $nWidth=0;
    $x=0;
    $tname=array();
    $colinfo=array();

    $n=1;
    while($row=$rs->fetch_assoc()){        
        $wlog->write("{$n} {$row['tname']}.{$row['fname']} pixel [{$row['pixel']}] _join [{$row['_join']}] dtype [{$row['dtype']}]",__LINE__,__FUNCTION__,__FILE__);
        array_push($colinfo,$row);
        array_push($column,$row['tname'].".".$row['fname']." f".$n);
        array_push($tname,$row['tname']);
        $nWidth+=intval($row['pixel']);
        $pixel["f".$n]=$row['pixel'];
        $align["f".$n]=$row['align'];
        $dtype["f".$n]=$row['dtype'];
        $n++;
    }
    // ORDER BY
    foreach($colinfo as $n=>$v){
        if($v['orderby'] == "0" || $v['orderby']=="") continue;

        while(count($orderby)<intval($v['orderby'])) array_push($orderby,"");
        $orderby[intval($v['orderby'])-1]="{$v['tname']}.{$v['fname']} {$v['ordertype']}";
    }
    $wlog->write("orderby [".implode(",",$orderby)."]",__LINE__,__FUNCTION__,__FILE__);
    
    // query_key is used for WHERE, whereas unique_key is used for INSERT.
    foreach($colinfo as $n=>$v){
        if(trim($v['query_key'])=="") continue;
        
        if(substr($v['query_key'],0,1)=="#"){   // session variable 
            if(!isset($_SESSION[substr($v['query_key'],1)])) {
                throw new Exception("No Session Data in [{$v['query_key']}]");
            }
            $query_val=$_SESSION[substr($v['query_key'],1)];
        } else {
            $query_val=$v['query_key'];
        }
        switch(getDtype($v['dtype'])){
        case "number": case "bool": break;
        default:
            $query_val="'{$query_val}'";
        }
        array_push($query_key,"{$v['tname']}.{$v['fname']}={$query_val}");
        $wlog->write("query_key [{$n}] [{$query_key[count($query_key)-1]}]",__LINE__,__FUNCTION__,__FILE__);
    }
    // INNER,OUTER JOIN
    $join_key=array();$join_ndx=array();
    foreach($colinfo as $n=>$v){
        if($v['_join']=="0" || $v['_join']=="") continue;
        array_push($join_key,$v['_join']);
        array_push($join_ndx,$n);
        $wlog->write("_join [{$v['_join']}] [{$n}]",__LINE__,__FUNCTION__,__FILE__);
    }
    $from=$prev_col="";
    array_multisort($join_key,$join_ndx);
    foreach($join_key as $n=>$val){
        $wlog->write("join_key [{$val}] [".substr($val,-1)."]",__LINE__,__FUNCTION__,__FILE__);
        $x=$join_ndx[$n];
        if(substr($val,-1)=="+"){
            $from.=" left outer join {$colinfo[$x]['tname']} on ".
                "{$prev_col}={$colinfo[$x]['tname']}.{$colinfo[$x]['fname']} ";
        } else {
            if($from!="") $from.=",";
            $from.="{$colinfo[$x]['tname']}";
            if($n>0){
                array_push($query_key,"{$prev_col}={$colinfo[$x]['tname']}.{$colinfo[$x]['fname']}");
            }
            $prev_col=$colinfo[$x]['tname'].".".$colinfo[$x]['fname'];
        }
        $wlog->write("from [{$from}]",__LINE__,__FUNCTION__,__FILE__);
    }
    if(count($join_key)<1){
        $tname=array_unique($tname);
        if(count($tname)>1) throw new Exception("Need to join between two more tables");
        if(count($tname)<1) throw new Exception("No table described in SQL");
        $from.=$tname[0];
    }
    $sql="select ".implode(",",$column)." from {$from} ";
    if(count($query_key)>0) $sql.="where ".implode(" and ",$query_key)." ";
    if(count($orderby)>0) $sql.="order by ".implode(",",$orderby);
        
//     throw new Exception("do here.");
    
    switch($viewtype){
    case "HRZN":
        if($pagesize!="0") $sql.=" limit {$start},{$pagesize}";
        $wlog->write("HRZN [{$sql}]",__LINE__,__FUNCTION__,__FILE__);
        
        $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
        $wlog->write("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs->num_rows<1) throw new Exception("No record found");
        $pstr="";
        if($rs->num_rows<$pagesize) $answer['pagesize']=0;
        else $answer['pagesize']=$pagesize;

        $ndx=1;
        while($row=$rs->fetch_assoc()){
            $pstr.="<tr height=20px><td align=center>{$ndx}</td>";
            $kndx=0;
            foreach($row as $k=>$v) {
                if($pixel[$k]=="0"){
                    $tdStyle="style='display:none;'";
                } else {
                    $tdStyle="style='width:{$pixel[$k]}px;'";
                    switch($align[$k]){
                    case "right": $tdStyle.=" align=right"; break;
                    case "center": $tdStyle.=" align=center"; break;
                    }
                }
                if($v!=strip_tags($v)) {    // this column has HTML tags
                    $pstr.="<td {$tdStyle}>".htmlentities($v)."</td>";
                } else if($dtype[$k]=="bool"){
                    $pstr.="<td {$tdStyle}><input type=checkbox ".($v=="1"?"checked":"")."></td>";
                } else if($v==""){
                    $pstr.="<td {$tdStyle}>&nbsp;</td>";
                } else {
                    $pstr.= "<td {$tdStyle}>{$v}</td>";
                }
                $kndx++;
            }
            $pstr.="</tr>";
            if($row['fname']=="inactivated" && $row['tname']=="i_col"){
                $wlog->write("ROW [{$pstr}]",__LINE__,__FUNCTION__,__FILE__);
            }
            if($ndx==1){
                $wlog->write(htmlentities($pstr),__LINE__,__FUNCTION__,__FILE__);
            }
            $ndx++;
        }
        break;
    case "TREE":
        $wlog->write("TREE [{$sql}]",__LINE__,__FUNCTION__,__FILE__);
        
        //             $sql=str_replace("order by","where par_rowid=party order by",$sql);
        $skip_col=3;
        $answer['tree']=array();
        $wlog->write(">>>> buildTree start [{$row['rowid']}]",__LINE__,__FUNCTION__,__FILE__);
        
        //         buildTree($tname,implode(",",$column),$joinstr,$orderby,$row['f1']);
        buildTree(implode(",",$column),$from,$query_key,$orderby,"");
        break;
    case "VERT":
        if($pagesize!="0") $sql.=" limit {$start},{$pagesize}";
        $wlog->write("VERT [{$sql}]",__LINE__,__FUNCTION__,__FILE__);
        
        //             $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
//         $wlog->write("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        
        $pstr="";
        if($rs->num_rows<$pagesize) $answer['pagesize']=0;
        else $answer['pagesize']=$pagesize;
        
        $answer['vert']=array();
        $vert=array();
        while($row=$rs->fetch_assoc()){
            $n=0;
            foreach($row as $k=>$val){
                $wlog->write("key [{$k}] val [{$val}] pixel [{$pixel[$k]}] align [{$align[$k]}]",__LINE__,__FUNCTION__,__FILE__);
                if(count($vert)<=$n) array_push($vert,"");
                $tdStyle="";
                if(intval($pixel[$k])==0){
                    $tdStyle="style='display:none;";
                } else {
                    switch($align[$k]){
                    case "right":
                        $tdStyle=" align=right"; break;
                    case "center":
                        $tdStyle=" align=center";
                    }
                }
                $vert[$n++].="<td {$tdStyle}>{$val}</td>";
            }
        }
        $wlog->write(implode("/",$vert),__LINE__,__FUNCTION__,__FILE__);

        array_push($answer['vert'],$vert);
//         break;
        
    }
    $answer['html']= $pstr;
    $answer['pagesize']=$pagesize;
    $answer['result']=0;
} catch(Exception $e){
    $answer['msg']=$e->getMessage();
    $wlog->write("msg [{$answer['msg']}]",__LINE__,__FUNCTION__,__FILE__);
}
// //     $wlog->write("[TREE HTML] {$pstr}",__LINE__,__FUNCTION___,__FILE__);

$mysqli->close();
$wlog=null;
echo json_encode($answer);

/*
 * ********************************************************************************
 */   
function buildTree($column,$from,$query_key,$orderby,$par_rowid){
    global $answer,$wlog;
    
//     $wlog->write("tname [{$tname}]/join [{$joinstr}]/orderby [".implode(",",$orderby)."]/par_rowid [{$par_rowid}]",__LINE__,__FUNCTION__,__FILE__);
    $wlog->write("column [{$column}] from [{$from}] query [".implode(" and ",$query_key)."] order by [".implode(",",$orderby)."] par_rowid [{$par_rowid}]",__LINE__,__FUNCTION__,__FILE__);
//     array_unshift($query_key,"par_rowid='{$par_rowid}'");
    
    if(count($orderby)>0) $sql.="order by ".implode(",",$orderby);
    
    $sql="select {$column} from {$from} ";
    $where="";
    if($par_rowid!=""){
        $where="where par_rowid='{$par_rowid}' ";
    } else {
        $where="where par_rowid='' ";
    }
    if(count($query_key)>0){
        $where.="and ".implode(" and ",$query_key);
    }
    $sql.="{$where}";

    if(count($orderby)>0) $sql.=" order by ".implode(",",$orderby);
    
    $pstr="";
    $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
    
    if($rs->num_rows<1) return 0;
    
    $num_cols=substr_count($column,",")-2;
    
    while($row=$rs->fetch_assoc()){
        $tag=array();
        $line="";
        foreach($row as $k=>$v){
            switch($k){
            case "f1": // rowid
                $rowid="<tr data-tt-id='{$v}' data-tt-parent-id={$row['f2']}>";
                break;
            case "f2": // par_rowid
                $tag['par_rowid']=$v;
                break;
            case "f3": // title
                $title="<td><span class=file>{$v}</span></td>";
                break;
            default:
                $line.="<td>{$v}</td>";
            }
        }
        $tag['line']="{$rowid}{$title}{$line}</tr>";
        $wlog->write("line [".htmlentities($tag['line'])."]",__LINE__,__FUNCTION__,__FILE__);
        array_push($answer['tree'],$tag);
        $end=count($answer['tree'])-1;
        
//         $nRow=buildTree($tname,$column,$joinstr,$orderby,$row['rowid']);
        $nRow=buildTree($column,$from,$query_key,$orderby,$row['f1']);
        if($nRow>0) $answer['tree'][$end]['line']=str_replace("class=file","class=folder",$answer['tree'][$end]['line']);
    }
    return $rs->num_rows;
}
?>