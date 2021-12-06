<?
// $link = mysqli_connect("3.134.55.142", "cavenagh", "qkrwogud99#","xaexal","3306");
 
$mysqli = new mysqli("localhost", "xaexal", "qkrwogud99#","xaexal");
if($mysqli->connect_errno){
	die('Connection error : '.$mysqli->connection_error);
}
$mysqli->query("set session character_set_connection=utf8;");
$mysqli->query("set session character_set_results=utf8;");
$mysqli->query("set session character_set_client=utf8;");


// class Logging{
//     private $logfile;
//     function __construct(){
//         global $admin_log, $comm_log;
        
//         if($_SESSION['logtype']==1){ // for Admin
//             $this->logfile=$admin_log;
//         } else {
//             $this->logfile=$comm_log;
//         }
//     }
//     function write($pstr,$ln="",$fn="",$sfile=""){
//         $fp = fopen($this->logfile,"a+") or die("Can't open log file [{$this->logfile}].");
//         // echo $this->logfile.".".$fp."<br>";
//         $sfile=str_replace(".php","",substr($sfile,strrpos($sfile,"/")+1));
//         $traceline="[<font color=red>{$ln}</font>:<font color=green>{$fn}</font>:<font color=blue>{$sfile}</font>]";
//         if(stripos($pstr,"select")===false && stripos($pstr,"update")===false && stripos($pstr,"delete")===false && stripos($pstr,"update")===false){
//         } else {
//             $pstr="<b>{$pstr}</b>";
//         }
//         fputs($fp,date("Y/m/d H:i:s")."&nbsp;{$traceline}&nbsp;{$pstr}<br>");
//         fclose($fp);
//     }
//     function reset($ln="",$fn="",$sfile=""){
//         $fp = fopen($this->logfile,"a+");
//         ftruncate($fp,0);
//         fputs($fp,"<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><meta /><title>log</title></head><body>".
//             "<style>html {font-size:14px}</style>");
//         $sfile=str_replace(".php","",substr($sfile,strrpos($sfile,"/")+1));
//         $traceline="&nbsp;[{$ln}: {$sfile}] ";
//         fputs($fp,date("Y/m/d H:i:s")."{$traceline}... ResetLog [".str_replace(".html","_".date("YmdHis").".html",$this->logfile)."]------------------------------<br>");
//         fclose($fp);
//     }
// }
// $wlog = new Logging();

$logfile="/var/www/html/o2o/err.log";
function errorlog($pstr="",$line=__LINE__,$func=__FUNCTION__,$file=__FILE__){
    global $logfile; 
    if($pstr=="") {
        $fp=fopen($logfile,"r+");
        ftruncate($fp,0);
        fclose($fp);
    } else {
        error_log(date("Y/m/d H-i-s").">>{$line}:{$func}:{$file} [{$pstr}]\n",3,$logfile);
    }
}
function getUnique(){
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $dt = new DateTime("now", new DateTimeZone('Asia/Seoul'));
    $_now= dechex($dt->format('YmdHis')).dechex($micro);
    return strtoupper($_now);
}
function getNOW(){
    $dt = new DateTime("now", new DateTimeZone('Asia/Seoul'));
    return  $dt->format('YmdHis');
}
function generateROWID($prev = "0000"){
    $str="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    $next="";
    for($i=3;$i>=0;$i--){
        $n=strpos($str,substr($prev,$i,1));
        if(++$n<62) return substr($prev,0,$i).substr($str,$n,1).$next;
        $next="0".$next;
    }
    return $next;
}
?>