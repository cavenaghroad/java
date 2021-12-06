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

$arCol=explode("|",$_POST['record']);
$wlog->write("rowid [{$_POST['rowid']}] table [{$_POST['rowid']}]",__LINE__,__FUNCTION__,__FILE__);
$mysqli->autocommit(false);
try {
    $sql="select * from i_col where par_rowid='{$_POST['rowid']}' and inactivated='0'  order by seqno";
    $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
    
    if($rs->num_rows<1) throw new Exception("No iCol info");
   
    foreach($rs as $i=>$row){
        if($row['unique_key']!='1') continue;

        switch(getDtype($row['dtype'])){
        case "string":
        case "html":
            $pkey="'".trim($arCol[$i])."'";
            break;
        default:    // number
            $pkey=trim($arCol[$i]);
        }
        $wlog->write("tname [{$row['tname']}] fname [{$row['fname']}] string pkey [{$pkey}]",__LINE__,__FUNCTION__,__FILE__);
        $sql="delete from {$row['tname']} where {$row['fname']}={$pkey}";
        $rs=sqlrun($sql,__LINE__,__FUNCTION__,__FILE__);
    }
    $mysqli->commit();
    $answer['result']=0;
//     $mysqli->rollback();
} catch(Exception $e){
    $mysqli->rollback();
    $answer['msg']=$e->getMessage();
    $wlog->write("msg [{$answer['msg']}]",__LINE__,__FUNCTION__,__FILE__);
}
$mysqli->autocommit(true);
$mysqli->close();

$wlog->write(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$wlog=null;
echo json_encode($answer);
?>