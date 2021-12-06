<?
include_once("include.php");

$wlog=new Logging();
$wlog->reset();
$wlog->write("party [{$_SESSION['party']}]",__LINE__,__FUNCTION__,__FILE__);
if(!isset($_SESSION['member_id'])) {
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	$mysqli->close();
	$wlog=null;
	exit;
}

$log="";
foreach($_POST as $key=>$value)	{
	$log.="{$key} [{$value}] ";
}
foreach($_SESSION as $key=>$value)	{
    $log.="SESSION {$key} [{$value}]\n";
}
$rs=sqlrun("select viewtype from i_grid where rowid='{$_POST['tblid']}'",__LINE__,__FUNCTION__,__FILE__);
if($rs->num_rows<1) throw new Exception("No viewtype found");
$row=$rs->fetch_assoc();
$viewtype=$row['viewtype'];

$wlog->write("{$log}",__LINE__,__FUNCTION__,__FILE__);
$oldrecord=explode("|",$_POST['oldrecord']);
$answer['element']="";
try{
    switch($viewtype){
    case "TREE":
        $ndx=intval($_POST['ndx'])+2;
        break;
    default:  
        $ndx=intval($_POST['ndx'])-1;
    }
    // Don't use the LIMIT phrase to save time to access data table.
    $sql="select * from i_col where par_rowid='{$_POST['tblid']}' and inactivated='0' order by seqno";
    $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception("No record with par_rowid={$_POST['tblid']}");
    $rs->data_seek($ndx);
    $row=$rs->fetch_assoc();

    $wlog->write("element [{$row['element']}]",__LINE__,__FUNCTION__,__FILE__);
    $sql=trim($row['element']);
    $m=preg_match('/\[.*\]/',$sql,$token);
    foreach($token as $k=>$v){
        $sch=str_replace("]","",str_replace("[","",$v));
        $n=0;
        if(strpos(".",$sch)===false){
            foreach($rs as $i=>$rec){
                if($rec['fname']==$sch) {
                    $sql=str_replace($v,$oldrecord[$n],$sql);
                    break;
                }
                $n++;
            }
        } else {
            $sch=explode(".",$sch);
            foreach($rs as $i=>$rec){
                if($rec['tname']==$sch[0] && $rec['fname']==$sch[1]){
                    $sql=str_replace($v,$oldrecrd[$n],$sql);
                    break;
                }
                $n++;
            }
        } 
    } 
    $m=preg_match('/<.*>/',$sql,$token);
    foreach($token as $k=>$v){
        $sch=str_replace(">","",str_replace("<","",$v));
        $sql=str_replace($v,$_SESSION[$sch],$sql);
    }
    $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
    $answer['element']="";
    while($row=$rs->fetch_array()){
        if($answer['element']!="") $answer['element'].="`";
        $answer['element'].=$row[0];
    }
    $answer['result']=0;
    $wlog->write($answer['element'],__LINE__,__FUNCTION__,__FILE__);
} catch(Exception $e){
    $answer['msg']=$e->getMessage();
    $wlog->write($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
}

$mysqli->close();

// $wlog->write(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$wlog=null;
echo json_encode($answer);
?>