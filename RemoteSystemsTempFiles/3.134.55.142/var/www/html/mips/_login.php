<?
include_once("include.php");

// $wlog=new Logging();
$retval = "";
try{
    $rs=sqlrun("select member_id,member_name,passcode from a_member where userid='{$_POST['userid']}'",__LINE__,__FUNCTION__,__FILE__);
	if($rs->num_rows<1) throw new Exception("등록되지 않은 아이디입니다.");
	while($row=$rs->fetch_assoc()){
	    if($row['passcode']==$_POST['passcode']){
	        $_SESSION['comm_user']=$row['member_id'];
	        $_SESSION['member_id'] = $row["member_id"];
	        $_SESSION['member_name'] = $row['member_name'];
	        // startpage(a_member) should be set by default page that shows personal information to be updated.
	        break;
	    }
	}
	if(!isset($_SESSION['member_id'])) throw new Exception("비밀번호가 일치하지 않습니다.");
	
	errorlog("member [{$_SESSION['member_id']}] member_name [{$_SESSION['member_name']}] startpage [{$startpage}]",__LINE__,__FUNCTION__,__FILE__);
	
	$rs=sqlrun("select a.rowid,a.party,b.name_kor from a_mem_par a, a_party b where a.member_id='{$_SESSION['member_id']}' and a.party=b.party order by b.name_kor",__LINE__,__FUNCTION__,__FILE__);
    $answer['party']=array();
//     array_push($answer['party'],","._label("chooseone",$_SESSION['lang']));
    while($row=$rs->fetch_assoc()){
//         if($row['name_kor']!="관리화면") {
            array_push($answer['party'],$row['party'].",".$row['name_kor']);
//         }
    }
//     array_push($answer['party'],"new,"._label("brandnew",$_SESSION['lang']));
		// Need to improve the logic finding city.
// 		$pstr = "";
// 		$country = getCountry($_SERVER['REMOTE_ADDR']);
// 		$sql = "select region from a_timezone where lower(region) like '%".strtolower($country)."%'";
// 		errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
// 		if($rs===false || $crud->getCount() < 0 ) {
// 			$pstr .= "No found country. Login time is recorded with Korean standard time.";
// 			$tz = new DateTimeZone("Asia/Seoul");
// 		} else {
// 			$row = $rs->fetch_array(MYSQLI_BOTH);
// 			errorlog('region ['.$row['region'].']',__LINE__,__FUNCTION__,__FILE__);
// 			if($row['region']==""){
// 				$tz = new DateTimeZone("Asia/Seoul");
// 			} else {
// 				$tz = new DateTimeZone($row['region']);
// 			}
// 		}
// 		$date = new DateTime();
// 		$date->setTimeZone($tz);
// 		$sql = "update a_member set last_login='{$date->format('YmdHis')}',login_region='{$country}' where member_id='{$_SESSION['member_id']}'";
    $rs=sqlrun("update a_member set last_login=date_format(now(),'%Y%m%d %H%i%s') where member_id='{$_SESSION['member_id']}'",__LINE__,__FUNCTION__,__FILE__);
	if($mysqli->error!="") throw new Exception($mysqli->error);
	
	$mysqli->commit();
	$answer['result']="0";
} catch(Exception $e){
	$mysqli->rollback();
	$answer['msg']=$e->getMessage();
	errorlog("{$answer['msg']} -> [{$sql}]",__LINE__,__FUNCTION__,__FILE__);
}
errorlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$mysqli->close();
$wlog=null;
echo json_encode($answer);
?>