<?
$_root_path="/home/hosting_users/xaexal/www/";
include_once($_root_path."customs/adsense/ad_head_home.php");

setCountry1("210.211.111.2");
echo "[".$gCountry."]";

function setCountry1($strIP) {
	global $link;
	global $gCountry, $gMoney;

	$arIP = explode(".",$strIP);
	for( $n=0; $n < count($arIP); $n++ ) {
		$arIP[$n] = trim(sprintf("%2x",intval($arIP[$n])));
		echo $arIP[$n]."<br>";
		if( strlen($arIP[$n]) == 1 ) $arIP[$n] = "0".$arIP[$n];
	}
	$nIP = hexdec(implode(".",$arIP));
	$psql = "select country,money from ipaddr2city where ".$nIP." between start_num and end_num";
	$mResult = mysql_query($psql,$link);
	@$mCount = mysql_num_rows($mResult);
	if( $mCount > 0 ) {
		$mRow = mysql_fetch_array($mResult);
		$gCountry = $mRow['country'];
		$gMoney = $mRow['money'];
	}
}

?>