<?
include_once 'include.php';

$answer=array();
$answer['msg']="";

$pstr=" POST ";
foreach($_POST as $key=>$value){
	$pstr.=" {$key} [{$value}]";
}
wlog($pstr,__LINE__,__FUNCTION__,__FILE__);

if(!isset($_SESSION['userid'])) {
	$answer['msg']="Login first !!!";
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	exit;
}
$now=date("YmdHis");
$rowid=strtoupper(dechex($now));
switch($_POST['optype']){
case "update_info":
	$where="";
	$pstr="";
	foreach($_POST as $key=>$value){
		if($key=="update_info") continue;
		if($key=="passcode") $where=" and passcode='{$value}'";
		
		if($pstr=="") $pstr.="set ";
		else $pstr.=",";
		$pstr.="{$key}='{$value}'";
	}
	$sql="update bbs_member {$pstr} where userid='{$_SESSION['userid']}'{$where}";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try{
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("Failed to execute SQL.");
		if($mysqli->affected_rows<1) throw new Exception("Failed to update member info.");
	} catch(Exception $e){
		wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
		$answer['msg']=$e->getMessage();
	}
}
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
?>