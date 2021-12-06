<?
include_once("include.php");

$mysqli->autocommit(false);
try{
    $country = getCountry($_SERVER['REMOTE_ADDR']);
    $rs=sqlrun("select region from a_timezone where lower(region) like '%".strtolower($country)."%'",__LINE__,__FUNCTION__,__FILE__);
    if($rs===false || $rs->num_rows < 0 ) {
        $pstr .= "No found country. Login time is recorded with Korean standard time.";
        $tz = new DateTimeZone("Asia/Seoul");
    } else {
        $row = $rs->fetch_assoc();
        errorlog('region ['.$row['region'].']',__LINE__,__FUNCTION__,__FILE__);
        if($row['region']==""){
            $tz = new DateTimeZone("Asia/Seoul");
        } else {
            $tz = new DateTimeZone($row['region']);
        }
    }
    $date = new DateTime();
    $date->setTimeZone($tz);
    $rs=sqlrun("update a_member set last_logout=now() where member_id='{$_SESSION['member_id']}'",__LINE__,__FUNCTION__,__FILE__);
    
    session_unset();
    $mysqli->commit();
    $answer['result']=0;
} catch(Exception $e){
    $mysqli->rollback();
    $answer['msg']=$e->getMessage();
}
$mysqli->autocommit(true);
$mysqli->close();
errorlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
?>