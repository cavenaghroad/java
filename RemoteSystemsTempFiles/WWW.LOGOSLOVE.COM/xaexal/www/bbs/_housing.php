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
$t = microtime(true);
$rowid=strtoupper(dechex($now));
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$micro=strtoupper(dechex($micro));
wlog($rowid.":".$micro,__LINE__,__FUNCTION__,__FILE__);
$rowid.=$micro;

switch($_POST['optype']){
case "new":
case "modify":
	$mysqli->autocommit(false);
	try {
		if($_POST['optype']=="new"){
			$sql= "insert into bbs set rowid='{$rowid}',title='{$_POST['title']}',content='{$_POST['content']}',userid='{$_SESSION['userid']}',".
					"created='{$now}',updated='{$now}',good=0,bad=0,readcount=0,_type='{$_POST['_type']}'";
			$answer['rowid']=$rowid;
		} else {
			$sql= "update bbs set title='{$_POST['title']}',content='{$_POST['content']}',updated='{$now}' where rowid='{$_POST['rowid']}'";
			$answer['rowid']=$_POST['rowid'];
		}
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("Failed to execute SQL.");
		if($mysqli->affected_rows<1) {
		    if($_POST['optype']=="new") throw new Exception("Failed to create new post.");
		}
		if($_POST['optype']=="new"){
			$sql="insert into bbs_house set rowid='{$rowid}',housetype='{$_POST['housetype']}',".
					"roomtype='{$_POST['roomtype']}',staytype='{$_POST['staytype']}',num_room={$_POST['num_room']},".
					"housesize={$_POST['housesize']},sizeunit='{$_POST['sizeunit']}',howlong='{$_POST['howlong']}',".
					"price={$_POST['price']},deposit={$_POST['deposit']},pub_include='{$_POST['pub_include']}',".
					"cookable='{$_POST['cookable']}',loc_type='{$_POST['loc_type']}',location='{$_POST['location']}',".
					"updated='".date("YmdHis")."'";
		} else {
			$sql="update bbs_house set housetype='{$_POST['housetype']}',".
					"roomtype='{$_POST['roomtype']}',staytype='{$_POST['staytype']}',num_room={$_POST['num_room']},".
					"housesize={$_POST['housesize']},sizeunit='{$_POST['sizeunit']}',howlong='{$_POST['howlong']}',".
					"price={$_POST['price']},deposit={$_POST['deposit']},pub_include='{$_POST['pub_include']}',".
					"cookable='{$_POST['cookable']}',loc_type='{$_POST['loc_type']}',location='{$_POST['location']}',".
					"updated='".date("YmdHis")."' where rowid='{$_POST['rowid']}'";
		}
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("Failed to execute SQL.");
		if($mysqli->affected_rows<1) {
			if($_POST['optype']=="new") throw new Exception("Failed to create new post.");
		}
		
		$mysqli->commit();
	} catch(Exception $e) {
		$mysqli->rollback();
		$answer['msg']=$e->getMessage();
		wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	break;
case "delete":
	$mysqli->autocommit(false);
	try {
	
		if(!delpost($_POST['rowid'])) throw new Exception("Failed to general post.");
		
		$sql="delete from bbs_housing where rowid='{$_POST['rowid']}'";
		
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("Failed to execute SQL.");
		if($mysqli->affected_rows<1) throw new Exception("No record deleted.");
		
		$mysqli->autocommit(false);
		$mysqli->commit();
	} catch(Exception $e) {
		$mysqli->rollback();
		$answer['msg']=$e->getMessage();
		wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	break;
}
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
?>