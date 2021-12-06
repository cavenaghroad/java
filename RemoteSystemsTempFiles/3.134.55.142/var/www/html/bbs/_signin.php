<?
@session_start();

$answer=array();
$answer['result']=-1;
$answer['msg']="";
$debug=true;
include_once '../common/_init_.php';
foreach($_POST as $key=>$post ){
    errorlog("{$key} [{$post}]");
}
   
$now=date("YmdHis");
$t = microtime(true);
$rowid=strtoupper(dechex($now));
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$micro=strtoupper(dechex($micro));
errorlog($rowid.":".$micro,__LINE__,__FUNCTION__,__FILE__);
$rowid.=$micro;

switch($_POST['optype']){
case "signin":
    try{
        $sql="select user_rowid,nickname,last_logout from a_member where userid='{$_POST['userid']}' and passcode='{$_POST['passcode']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        if($rs->num_rows<1) throw new Exception("존재하지 않는 사용자 또는 잘못된 비밀번호 입니다.");
        $row=$rs->fetch_assoc();
        $_SESSION['user_rowid']=$row['user_rowid'];
        $_SESSION['userid']=$_POST['userid'];
        $_SESSION['nickname']=$row['nickname'];
        $_SESSION['last_logout']=$row['last_logout'];
        $_SESSION['region']=ucwords(strtolower(getCountry($_SERVER['REMOTE_ADDR'])));
        $answer['nickname']=$row['nickname'];
        $answer['last_logout']=$row['last_logout'];
        $answer['region']=$_SESSION['region'];
        $answer['result']=0;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "logout":
    try {
        $region="Asia/Seoul";
        if($_SESSION['region']=="Republic Of Korea"){
        }
        $dt = new DateTime("now", new DateTimeZone($region));
        
        $sql="update a_member set last_logout='".$dt->format('Y-m-d H:i')."' where userid='{$_SESSION['userid']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        if($mysqli->affected_rows<1) throw new Exception("존재하지 않는 사용자입니다.");
        $answer['result']=0;
    } catch(Exception $e){
//         $answer['msg']=$e->getMessage();
//         if($debug) $answer['msg'].=" [{$sql}]";
//         errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
        errorlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
        session_destroy();
    }
    break;
}
errorlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);


function getCountry($strIP) {
    global $gCountry,$gMoney,$gCountryCode;
    global $mysqli;
    
    $arIP = explode(".",$strIP);
    for( $n=0; $n < count($arIP); $n++ ) {
        $arIP[$n] = trim(sprintf("%2x",intval($arIP[$n])));
        if( strlen($arIP[$n]) == 1 ) $arIP[$n] = "0{$arIP[$n]}";
    }
    $nIP = hexdec(implode(".",$arIP));
    $sql = "select country,abbr3 from ipaddr where '{$nIP}' between start_num and end_num";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    $gCountry="";
    if($rs===false) $gCountryCode="";
    else if($rs->num_rows<1) $gCountryCode="";
    else {
        $row=$rs->fetch_assoc();
        $gCountry=$row['country'];
        $gCountryCode=$row['abbr3'];
    }
    return $gCountry;
}
?>