<?php 
if($_GET['tname']=="" || $_GET['fname']=="" ){
	echo "tname [".$_GET['tname']."] fname [".$_GET['fname']."]<br>";
	exit;
}
$link = mysql_connect("localhost", "xaexal", "wogud99");
mysql_select_db("xaexal", $link);

$nDate=date("YmdHis");
$newid=strtoupper(dechex($nDate));
$sql="update ".$_GET['tname']." set ".$_GET['fname']."='".$newid."' where ".$_GET['fname']." is null or ".$_GET['fname']."='' limit 1";
$result=mysql_query($sql,$link);
echo $sql."<br>";
$nCount=mysql_affected_rows($link);
while($nCount>0){
	$nDate++;
	$newid=strtoupper(dechex($nDate));
	$sql="update ".$_GET['tname']." set ".$_GET['fname']."='".$newid."' where ".$_GET['fname']." is null or ".$_GET['fname']."='' limit 1";
	$result=mysql_query($sql,$link);
	echo $sql."<br>";
	$nCount=mysql_affected_rows($link);
}
echo "<br>finished...<br>";
?>