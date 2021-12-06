<?
@session_start();

// $fullpath = "/home/hosting_users/xaexal/www/picture/";
$fullpath="/home/hosting_users/xaexal/www/";
$homepath="/bbs";
$orgpath=$fullpath;

$admin_log=$fullpath."bbs/adm.html";
$comm_log=$fullpath."bbs/log.html";

include_once '../common/_init_.php';

define("PRIMARY_YMDHIS","date_format(NOW(),'%Y%m%d%H%i%s')");
define("PRIMARY_YMD","date_format(NOW(),'%Y%m%d')");

$basename = "party";
$BaseFolder="/mips";
$primary = "member";


$sqldbg = true;

$imgdir = "/picture/";
$logfile = "/home/hosting_users/xaexal/www/ob1/bbs.html";
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
	global $mysqli,$wlog;

	$arIP = explode(".",$strIP);
	for( $n=0; $n < count($arIP); $n++ ) {
		$arIP[$n] = trim(sprintf("%2x",intval($arIP[$n])));
		if( strlen($arIP[$n]) == 1 ) $arIP[$n] = "0{$arIP[$n]}";
	}
	$nIP = hexdec(implode(".",$arIP));
	$sql = "select country,abbr3 from ipaddr where '{$nIP}' between start_num and end_num";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
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
	global $mysqli,$wlog;
	
	$sql="select msg from a_msg where code='{$pstr}' and lang='{$lang}'";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
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

function timegap($pstr){
	$now=date("YmdHis");
	if(!is_numeric($pstr)) {
	    if(strlen($pstr)>12) $pstr=substr($pstr,0,12);
	    $pstr=hexdec($pstr);
	}
	$time1=date_create(substr($pstr,0,4)."-".substr($pstr,4,2)."-".substr($pstr,6,2));
	$time2=date_create(substr($now,0,4)."-".substr($now,4,2)."-".substr($now,6,2));
	$interval=date_diff($time1,$time2);
	if($interval->days>1){
		return substr($pstr,0,4)."-".substr($pstr,4,2)."-".substr($pstr,6,2);
	} else if($interval->days>0){
		return "어제".substr($pstr,8,2).":".substr($pstr,10,2);
	} else {
		return "오늘 ".substr($pstr,8,2).":".substr($pstr,10,2);
	}
}

function getNewRowid(){
    global $wlog;
    
    $now=date("YmdHis");
    $t = microtime(true);
    $rowid=strtoupper(dechex($now));
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $micro=strtoupper(dechex($micro));
    $wlog->write($rowid.":".$micro,__LINE__,__FUNCTION__,__FILE__);
    $rowid.=$micro;
    return $rowid;
}
?>