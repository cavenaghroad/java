<?
require_once 'common.php';
require_once $orgpath.'/android/include.php';

resetlog();
if($_SERVER['REQUEST_METHOD']=="GET"){
	foreach($_GET as $key=>$value){
		wlog("{$key} [{$value}]",__LINE__,__FUNCTION__,__FILE__);
		$$key=$value;
		wlog("{$key} [{$$key}]",__LINE__,__FUNCTION__,__FILE__);
	}
} else {
	foreach($_POST as $key=>$value){
		wlog("{$key} [{$value}]",__LINE__,__FUNCTION__,__FILE__);
		$$key=$value;
		wlog("{$key} [{$$key}]",__LINE__,__FUNCTION__,__FILE__);
	}
}
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$order_id= date('YmdHis'.$micro);
$rowid=$order_id;
$phone=$_GET['phone'];

$answer=array();
$answer['success']=-1;
$answer['message']="";

$_now=date("YmdHis");
$n=0;
try {
	$mysqli->autocommit(false);

	// Check if the order from same user is again asked within 5 seconds.
	$sql="update {$ob_member} set updated='{$_now}' where mobile='{$phone}' ".
			"and cast({$_now} as decimal(14,0))-cast(updated as decimal(14,0))>5";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false)	throw new Exception($mysqli->error);

	if($mysqli->affected_rows>0){	// more than 5 seconds.
		if($nickname!="") {
			$sql="update {$ob_member} set nickname='{$nickname}'";
			if($address!=""){
				$sql.=",address='{$address}'";
			}
			$sql.=",updated='{$_now}' where mobile='{$phone}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);	
		}
	} else{
		// Check if it is an existing user.
		$sql="select mobile from {$ob_member} where mobile='{$phone}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows>0) throw new Exception("too often order.");	// If it is the existing user, finish and ignore order, and send message.

		// Order came from new mobile number, so it will be registered as new member.
		$sql="";
		switch($optype){
		case "inhouse":
		case "takeout":
		case "book":
			$sql="insert into {$ob_member} set mobile='{$phone}',nickname='{$nickname}',regdate='{$_now}',updated='{$_now}'";
			break;
		case "delivery":
			if($address!=""){
				$sql="insert into {$ob_member} set mobile='{$phone}',nickname='{$nickname}',address='{$address}',".
						"regdate='{$_now}',updated='{$_now}'";
			}
			break;
		}
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
	}
	$party=$_GET['party'];
	$orderlist = explode(";",$orderlist);
	foreach($orderlist as $key=>$value){
		$order=explode("^",$value);
		$sql="insert into {$ob_order} set order_id='{$order_id}',rowid='{$rowid}',name='{$order[0]}',mobile='{$phone}',".
				"price=(select price*{$order[1]} from {$ob_menu} where party='{$party}' and name='{$order[0]}')";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows!=1) throw new Exception("{$order[0]} is not inserted into Order [{$order_id}].");
		$t = microtime(true);
		$micro = sprintf("%06d",($t - floor($t)) * 1000000);
		$rowid= date('YmdHis'.$micro);
	}
	if(isset($_GET['howmany'])) $howmany=$_GET['howmany'];
	else $howmany=0;
	$sql="insert into {$ob_master} set ordertype='{$optype}',order_id='{$order_id}',party='{$party}',mobile='{$phone}',".
			"arrival_time='{$arrival_time}',howmany={$howmany},tableno='{$_GET['tableno']}',".
			"price=(select sum(price) from {$ob_order} where order_id='{$order_id}')";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($mysqli->affected_rows!=1) throw new Excpetion("Order was not added.");
	
	$sql="update {$ob_member} set address='{$address}',nickname='{$nickname}' where mobile='{$phone}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);

	$mysqli->commit();
	$answer['success']=0;
	$answer['order_id']=$order_id;
	$answer['message']="주문 완료됐습니다.";
	wlog('success:0, message:주문 완료됐습니다.');
}catch(Exception $e){
	$mysqli->rollback();
	wlog($e->getMessage(),__LINE__,__FILE__);
	$answer['success']=-1;
	$answer['order_id']="";
	$answer['message']=$e->getMessage();
}
$mysqli->autocommit(false);

$mysqli->close();
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
?>