<?
include_once 'include.php';

$now=date("YmdHis");

$answer=array();
$answer['msg']="";

$pstr="POST ";
foreach($_POST as $key => $value){
	$pstr.=" {$key}=>[{$value}],";
}
wlog($pstr,__LINE__,__FUNCTION__,__FILE__);

switch($_POST['optype']){
case "logout":
	$sql="update bbs_member set last_logout='{$now}' where userid='{$_SESSION['userid']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try{
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("로그아웃 기록실패 ");
		if($mysqli->affected_rows<1) throw new Exception("No member");
		
		session_unset();
		session_destroy();
		
	} catch(Exception $e){
		wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
		$answer['msg']=$e->getMessage();
	}
	break;
case "login":
	$sql = "select nick from bbs_member where userid='{$_POST['userid']}' and passcode='{$_POST['passcode']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try{
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("fail to query");
		wlog("num_rows [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs->num_rows<1) throw new Exception("no member");
		$row=$rs->fetch_assoc();
		$last_login=date("YmdHis");
		$sql="update bbs_member set last_login='{$last_login}' where userid='{$_POST['userid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("can't update last login time.");
		if($mysqli->affected_rows<1) throw new Exception("no update for last login time");
		$_SESSION['userid']=$_POST['userid'];
		$_SESSION['nick']=$row['nick'];
	} catch(Exception $e){
		$answer['msg']=$e->getMessage();		
	}
	break;
case "signin":
	$rowid=strtoupper(dechex($now));
	$sql="insert into bbs_member set rowid='{$rowid}',userid='{$_POST['loginid']}', passcode='{$_POST['passcode']}', nick='{$_POST['nick']}',created='{$now}',updated='{$now}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try{
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("가입 작업실패 ");
		if($mysqli->affected_rows<1) throw new Exception("가입 내역 없음.");
		$answer['msg']="";
	} catch(Exception $e){
		wlog($e->getMessage,__LINE__,__FUNCTION__,__FILE__);
		$answer['msg']=$e->getMessage();			
	}
	break;
}
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
?>