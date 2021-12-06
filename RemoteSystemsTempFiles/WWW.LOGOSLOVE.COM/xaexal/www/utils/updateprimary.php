<?
if(count($_GET)==0){
	die("updateprimary.php?tname=&fname=&hex=[Y/N]");
}
$mysqli = new mysqli("localhost", "xaexal", "wogud99#","xaexal");
if($mysqli->connect_errno){
	die('Connection error : '.$mysqli->connection_error);
}
$mysqli->query("set session character_set_connection=utf8;");
$mysqli->query("set session character_set_results=utf8;");
$mysqli->query("set session character_set_client=utf8;");

$sql = "select max({$_GET['fname']}) from {$_GET['tname']} where {$_GET['fname']}!='' and {$_GET['fname']} is not null";
$rs=$mysqli->query($sql);
if($rs===false){
	die($mysqli->error);
}
if( $rs->num_rows < 1 ) {
	$newdate = floatval(date("YmdHis"));
} else {
	$row=$rs->fetch_array(MYSQLI_BOTH);
	if($row[$_GET['fname']]=="") $newdate=floatval(date("YmdHis"));
	else	$newdate=floatval($row[$_GET['fname']]);
}
do {
	$newdate++;
	$_new=$newdate;
	if($_GET['hex']=="Y") $_new=strtoupper(dechex($_new));
	$sql = "update {$_GET['tname']} set {$_GET['fname']}='{$_new}' where {$_GET['fname']}='' or {$_GET['fname']} is null limit 1";
	$rs = $mysqli->query($sql);
	echo "newdate [{$_new}] nCount [{$mysqli->affected_rows}]<br>";
} while( $mysqli->affected_rows > 0 );
?>