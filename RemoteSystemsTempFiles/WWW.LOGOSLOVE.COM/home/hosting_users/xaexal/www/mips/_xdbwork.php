<?
include_once("include.php");

$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$_now= date('YmdHis'.$micro);

$answer=array();
$answer['result']=-1;
$answer['msg']="";

$log="";
switch($_POST['optype']){
case "newest": case "check-expiry":
	break;
default:
	foreach($_POST as $key=>$value)	{
		if($key=="optype") {
			$log.="{$key} [<font color=red>{$value}</font>]";
		} else{
			$log.="{$key} [{$value}] ";
		}
	}
	wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);
}
switch( $_POST['optype'] ) {
case "login":
	resetLog();
	$retval = "";
	try{
		$sql = "select member_id,member_name from a_member where userid='{$_POST['userid']}' and passcode='{$_POST['passcode']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false)	throw new Exception($mysqli->error);
		if($rs->num_rows<1) throw new Exception("Cannot find this user ID or Please check if userID/password is correct.");
		
		$row = $rs->fetch_array(MYSQLI_BOTH);
		$_SESSION['member_id'] = $row["member_id"];
		$_SESSION['member_name'] = $row['member_name'];
		wlog("member [{$_SESSION['member_id']}] member_name [{$_SESSION['member_name']}]",__LINE__,__FUNCTION__,__FILE__);
		$pstr = "";
		$country = getCountry($_SERVER['REMOTE_ADDR']);
		$sql = "select region from a_timezone where lower(region) like '%".strtolower($country)."%'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false || $rs->num_rows < 0 ) {
			$pstr .= "No found country. Login time is recorded with Korean standard time.";
			$tz = new DateTimeZone("Asia/Seoul");
		} else {
			$row = $rs->fetch_array(MYSQLI_BOTH);
			wlog('region ['.$row['region'].']',__LINE__,__FUNCTION__,__FILE__);
			if($row['region']==""){
				$tz = new DateTimeZone("Asia/Seoul");
			} else {
				$tz = new DateTimeZone($row['region']);
			}
		}
		$date = new DateTime();
		$date->setTimeZone($tz);
		$sql = "update a_member set last_login='{$date->format('YmdHis')}',login_region='{$country}' where member_id='{$_SESSION['member_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error." (Fail to set login time for this member.)");
		
		$mysqli->commit();
		$answer['result']="0";
	} catch(Excpetion $e){
		$mysqli->rollback();
		$answer['msg']=$e->getMessage();
		wlog("{$answer['msg']} -> [{$sql}]",__LINE__,__FUNCTION__,__FILE__);
	}
	echo json_encode($answer);
	break;
case "loadparty":
	$sql = "select party,name_kor,phone,postcode,address,sector from a_party a order by name_kor";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try {
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		$answer['html']="";
		while($row=$rs->fetch_array(MYSQLI_BOTH)){
			$answer['html'].="<option value={$row['party']}>".str_pad($row['name_kor'],33,' ').str_pad($row['sector'],20,' ').
			str_pad($row['phone'],21,' ').str_pad($row['postcode'],9,' ').str_pad($row['address'],40,' ')."</option>";
		}
		$answer['result']="0";
	} catch(Exception $e){
		$answer['msg']=$e->getMessage();
	}
	echo json_encode($answer);
	break;
}
$mysqli->close();
?>