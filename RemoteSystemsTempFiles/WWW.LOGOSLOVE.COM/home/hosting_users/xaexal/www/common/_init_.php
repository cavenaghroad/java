<?
$mysqli = new mysqli("localhost", "xaexal", "qkrwogud99","xaexal");
if($mysqli->connect_errno){
	die('Connection error : '.$mysqli->connection_error);
}
$mysqli->query("set session character_set_connection=utf8;");
$mysqli->query("set session character_set_results=utf8;");
$mysqli->query("set session character_set_client=utf8;");

function wlog($pstr,$ln="",$_fn="",$sfile="",$enable=true) {
	if(!$enable) return;
	
	global $logfile;

	$fp = fopen($logfile,"a+");
	if($ln=="" && $sfile=="") $traceline="";
	else {
		$sfile=str_replace(".php","",substr($sfile,strrpos($sfile,"/")+1));
		$traceline="[]";
		if($ln!="" || $_fn!="" || $sfile!="")		$traceline="[{$ln}: {$fn}: {$sfile}] ";
	}
// 	fputs($fp,"<font color=blue>[".date("Y/m/d H:i:s")."]</font> <font color='red'>{$traceline}</font> {$pstr}<br>");
	fputs($fp,"[".date("Y/m/d H:i:s")."] {$traceline}... {$pstr}<br>");
	fclose($fp);
}

function resetlog($_do=true){

	if(!$_do) return;

	global $logfile;
// 	unlink($logfile);   I should create new log file manually if it does not exist. not allowed to create it by PHP code.
	$fp = fopen($logfile,"r+");
	ftruncate($fp,0);
		fputs($fp,"<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><meta /><title>log</title></head>".
				"<script language='javascript'>//setTimeout(function(){document.location='';},3000);</script><body>");
	fclose($fp);
// 	chgrp($logfile,"xaexal");

}
?>