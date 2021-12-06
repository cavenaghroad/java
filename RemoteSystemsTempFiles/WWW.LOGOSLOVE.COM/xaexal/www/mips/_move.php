<?
include_once("include.php");
$wlog=new Logging();
$wlog->write("party [{$_SESSION['party']}]",__LINE__,__FUNCTION__,__FILE__);
if(!isset($_SESSION['party'])) {
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
$wlog->write("{$log}",__LINE__,__FUNCTION__,__FILE__);

try{
    $mysqli->autocommit(false);
    
    $rs=sqlrun(select * from i_col where par_rowid='{$_POST['tblid']}' and fname='rowid' order by seqno");
    $row=$rs->fetch_assoc();
    
    switch($_POST['step']){
    case "replace":
        $rs1=sqlrun("update {$row['tname']} set seqno={$_POST['seqno_dst']} where rowid='{$_POST['rowid_src']}'");
        $rs1=sqlrun("update {$row['tname']} set seqno={$_POST['seqno_src']} where rowid='{$_POST['rowid_dst']}'");
        break;
    case "right": // make child node
    case "left":
        $rs1=sqlrun("update {$row['tname']} set par_rowid='{$_POST['rowid_dst']}' where rowid='{$_POST['rowid_src']}'");
        
//             if($_POST['step']=="right") break;
        
        
        break;
    }
    $mysqli->commit();
    $answer['result']="0";
} catch(Exception $e){
    $mysqli->rollback();
    $wlog->write($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    $answer['msg']=$e->getMessage();
}
$mysqli->autocommit(true);
$mysqli->close();

// $wlog->write(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$wlog=null;
echo json_encode($answer);
?>