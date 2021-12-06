<?
@session_start();

// $fullpath = "/home/hosting_users/xaexal/www/picture/";
$fullpath="/var/www/html/";
$orgpath=$fullpath;
include_once '../common/_init_.php';

define("PRIMARY_YMDHIS","date_format(NOW(),'%Y%m%d%H%i%s')");
define("PRIMARY_YMD","date_format(NOW(),'%Y%m%d')");

$basename = "party";
$BaseFolder="/mips";
$primary = "member";

$t_lesson = "a_lesson";
$t_party = "a_party";
$t_member = "a_member";						//saints";
$t_m2class = "a_member2class";
$t_m2lesson = "a_member2lesson";
$t_m2e = "a_member2party";
$t_attend = "a_attend";
$t_fee = "a_fee";
$t_membertobe = "a_membertobe";		//saints_newcomer";
$t_lov = "a_lov";					//listofvalue";
$t_navi = "a_navi";			//saint_navi";
$t_config = "a_config";

$sqldbg = true;

$imgdir = "/picture/";
$logfile = "/var/www/html/ob1/mips.html";
$dberror = 0;
$dberror_msg = ""; 

// XML global 
$doc; $root;

define('CLICKED_BGCOLOR','#33ff99');
define('BASE_BGCOLOR','white');
define('MOUSEOVER_BGCOLOR','yellow');

define("LONGER",150);
define("IMGDIR_MINI","/picture_mini/");

define("bbs_table","ksw_bbs");
define("bbs_config","ksw_config");
define("bbs_reply","ksw_reply");
define("F_12","FFFFFFFFFFFF");
define("APPEND",false);
define("NEWADD",true);
define("NOLOG",false);
define("VERT","0");
define("HRZN","1");
define("TREE","2");
define("DLOG","3");
define("FORM","4");
$gCountry="";
$gMoney = "";
$gCountryCode = "";

$thispage;

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
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	$gCountry="";
	if($rs===false) $gCountryCode="";
	else if($rs->num_rows<1) $gCountryCode="";
	else {
		$row=$rs->fetch_array(MYSQLI_BOTH);
		$gCountry=$row['country'];
		$gCountryCode=$row['abbr3'];
	}
	return $gCountry;
}

function setCountry($strIP) {
	global $mysqli,$gCountry, $gCountryCode,$gMoney;

	$arIP = explode(".",$strIP);
	for( $n=0; $n < count($arIP); $n++ ) {
		$arIP[$n] = trim(sprintf("%2x",intval($arIP[$n])));
		if( strlen($arIP[$n]) == 1 ) $arIP[$n] = "0{$arIP[$n]}";
	}
	$nIP = hexdec(implode(".",$arIP));
	$sql = "select country,money from ipaddr2city where {$nIP} between start_num and end_num";
	$rs = $mysqli->query($sql);
	if($rs->num_rows > 0 ) {
		$row = $mysqli->fetch_array(MYSQLI_BOTH);
		$gCountry = $row['country'];
		$gMoney = $row['money'];
	}
}

function _label($pstr,$lang){
	global $mysqli;
	$sql="select msg from a_msg where code='{$pstr}' and lang='{$lang}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false)	{
		$pstr="";
	} else if($rs->num_rows<1){
		$pstr="";
	} else {
		$row=$rs->fetch_array(MYSQLI_BOTH);
		$pstr=$row[0];
	}
	// 	$mysqli->close();
	return $pstr;
}

function sendmail($pstr)
{
	switch( $_GET['optype'] ) {
	case "appeal":
		mail($pstr,"교적사용승인 신청","사용자 [{$_GET['userid']}] 님께서 교적사용 승인요청했습니다.");
		break;
	case "empty_passcode":
		mail($_GET['userid'],"교적부 임시비밀번호입니다.",$pstr);
		break;
	}
}

?>