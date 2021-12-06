<?
define("MAX_IMAGE_FILE",5);
define("MAX_CONTACT",3);
define("MAX_SELL_IMAGE",3);
define("NO_UPDATED","00000000000000");
define("REC_NUM",34);
$ob_member = "ob_member";
$ob_menu="ob_menu";
$ob_master="ob_command";
$ob_party="ob_party";
$ob_admin="ob_admin";
$ob_order="ob_order";
$ob_login="ob_login";

$orgpath="/home/hosting_users/xaexal/www"; 
$logfile=$orgpath."/ob1/log.html";
require_once '../common/_init_.php';

// $t = microtime(true);
// $micro = sprintf("%06d",($t - floor($t)) * 1000000);
// $_now= date('YmdHis'.$micro);

$answer=array();
$answer['success']=-1;
$answer['message']="";

function showAsDate($pstr){
	if(strlen($pstr)!=14) return $pstr;
	
	return substr($pstr,0,4)."-".substr($pstr,4,2)."-".substr($pstr,6,2)." ".substr($pstr,8,2).":".substr($pstr,10,2).":".substr($pstr,12,2);
}

function showByMinute($pstr){
	if(strlen($pstr)<12) return $pstr;
	return substr($pstr,0,4)."-".substr($pstr,4,2)."-".substr($pstr,6,2)." ".substr($pstr,8,2).":".substr($pstr,10,2);
}

function showTime($pstr){
	return substr($pstr,4,2)."-".substr($pstr,6,2)." ".substr($pstr,8,2).":".substr($pstr,10,2);
}

function _phone($pstr){
	if(substr($pstr,0,3)=='010'){
		return substr($pstr,0,3)."-".substr($pstr,3,4)."-".substr($pstr,7,4);
	} else if(substr($pstr,0,2)=='02'){
		return substr($pstr,0,2)."-".substr($pstr,2);
	} else return $pstr;
}

function getTH($pstr){
	if($pstr=="") $pstr="&nbsp;";
	return "<th align=center><font color=white>{$pstr}</font></th>";
}

function getTD($pstr,$align="L"){
	if($pstr=="") $pstr="&nbsp;";
	switch($align){
		case "L":
			$align='align=left'; break;
		case "R":
			$align="align=right"; break;
		case "C":
			$align= "align=center";
	}
	return "<td {$align}>{$pstr}</td>";
}
$gCountry="";
$gCountryCode="";
$gCurrency="";
function getCountry($strIP) {
	global $gCountry,$gCurrency,$gCountryCode;
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
?>