<?php 
include_once("comfunc.php");

$limit = "";
$strLog = "";
$orderby = " order by a.name_kr";
$log = $_GET['optype']."::";
$maintable = $t_member;
$nMax = 0;
switch( $_GET['optype'] ) {
case "payment":
	if( $_GET['orderby'] == "" )	$_GET['orderby'] = "actual_pay_date";
	$sql = "select b.member,b.name_kr,b.name_en,a.actual_pay_date,a.pay_price".
				"from a_fee a, a_member b ".
			"where a.actual_pay_date between '".$_GET['start_date']."' and '".$_GET['end_date']."' and a.school='".$_GET['school']."' and a.member=b.member order by ".$_GET['orderby'];
	$nCount = runQuery($sql,$result);
	break;
case "schedule":
//	$sql = "select a.member,count(b.attend_date) from a_attend a left out join a_member b on a.member=b.member ".
//				"where a.school='".$_GET['school']."' and a.course='".$_GET['course']."' and a.attend_date between '".$_GET['startdate']."' and '".$_GET['enddate'].
//				"' group by a.member order by a.member";
//	$nCount = runQuery($sql,$result);
//	while( $row = mysql_fetch_array($result) ) {
//		if( $nMax < intval($row[1]) )	$nMax = intval($row[1]);
//	}
	if( $_GET['orderby'] == "" )	$_GET['orderby'] = "a.name_kr";
	$sql = "select a.member,a.name_kr,a.gender,a.nationality,a.phone_home,a.phone_mobile,a.email ".
			"from a_attend a, a_member b ".
			"where a.school='".$_GET['school']."' and a.course='".$_GET['course']."' and a.member=b.member ".
				"and a.attend_date between '".$_GET['startdate']."' and '".$_GET['enddate']."' order by ".$_GET['orderby'];
	$nCount = runQuery($sql,$result);
	break;
case "attendance":
	if( $_GET['orderby'] == "" )	$_GET['orderby'] = "a.name_kr";
	$sql = "select concat(a.member||'_'||a.attend_date) attended,a.actual_attend_date,a.actual_attend_time from a_attend a ".
				"where a.school='".$_GET['school']."' and a.course='".$_GET['course']."' and a.attend_date between '".$_GET['startdate']."' and '".$_GET['enddate']."'";
	$nCount = runQuery($sql,$result);
	break;
default:
}
$doc = new DOMDocument('1.0');
$root = $doc->createElement('root');
$root = $doc->appendChild($root);

$child = $doc->createElement('totalcount');
$child = $root->appendChild($child);
$value = $doc->createTextNode($nTotal);
$value = $child->appendChild($value);

$child = $doc->createElement('reccount');
$child = $root->appendChild($child);
$value = $doc->createTextNode($nCount);
$value = $child->appendChild($value);

$child = $doc->createElement('maxcount');
$child = $root->appendChild($child);
$value = $doc->createTextNode($nMax);
$value = $child->appendChild($value);

if( $nCount > 0 ) {
	while($row = mysql_fetch_array($result)) {
	    $occ = $doc->createElement('crlf');
	    $occ = $root->appendChild($occ);
	
	    for( $i = 0; $i < mysql_num_fields($result); $i++) {
	    	$child = $doc->createElement(mysql_field_name($result,$i));
	        $child = $occ->appendChild($child);
	        $value = $doc->createTextNode($row[$i]);
	        $value = $child->appendChild($value);
	    } // foreach
	} // while
}

header("Content-type: text/xml");
$xml_string = $doc->saveXML();
echo $xml_string;
$strLog = $_GET['optype']."::".$strLog;
makelog($sql);
?>