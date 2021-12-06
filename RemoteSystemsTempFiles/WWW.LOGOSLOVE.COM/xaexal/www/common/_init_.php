<?
$mysqli = new mysqli("localhost", "xaexal", "qkrwogud99","xaexal");
if($mysqli->connect_errno){
	die('Connection error : '.$mysqli->connection_error);
}
$mysqli->query("set session character_set_connection=utf8;");
$mysqli->query("set session character_set_results=utf8;");
$mysqli->query("set session character_set_client=utf8;");

class Logging{
    private $logfile;
    function __construct(){
        global $admin_log, $comm_log;
        
        if($_SESSION['logtype']==1){ // for Admin
            $this->logfile=$admin_log;
        } else {
            $this->logfile=$comm_log;
        }
    }
    function write($pstr,$ln="",$fn="",$sfile=""){
        $fp = fopen($this->logfile,"a+") or die("Can't open log file [{$this->logfile}].");
        // echo $this->logfile.".".$fp."<br>";
        $sfile=str_replace(".php","",substr($sfile,strrpos($sfile,"/")+1));
        $traceline="[<font color=red>{$ln}</font>:<font color=green>{$fn}</font>:<font color=blue>{$sfile}</font>]";
        if(stripos($pstr,"select")===false && stripos($pstr,"update")===false && stripos($pstr,"delete")===false && stripos($pstr,"update")===false){
        } else {
            $pstr="<b>{$pstr}</b>";
        }
        fputs($fp,date("Y/m/d H:i:s")."&nbsp;{$traceline}&nbsp;{$pstr}<br>");
        fclose($fp);
    }
    function reset($ln="",$fn="",$sfile=""){
        $fp = fopen($this->logfile,"a+");
        ftruncate($fp,0);
        fputs($fp,"<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><meta /><title>log</title></head><body>".
            "<style>html {font-size:14px}</style>");
        $sfile=str_replace(".php","",substr($sfile,strrpos($sfile,"/")+1));
        $traceline="&nbsp;[{$ln}: {$sfile}] ";
        fputs($fp,date("Y/m/d H:i:s")."{$traceline}... ResetLog [".str_replace(".html","_".date("YmdHis").".html",$this->logfile)."]------------------------------<br>");
        fclose($fp);
    }
}
$wlog = new Logging();

?>