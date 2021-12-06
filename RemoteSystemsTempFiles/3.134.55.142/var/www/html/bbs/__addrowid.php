<?
include_once("include.php");

$tname="bbs_config";
$fname="rowid";

$result=true;

while($result){
    $now=date("YmdHis");
    $t = microtime(true);
    $rowid=strtoupper(dechex($now));
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $micro=strtoupper(dechex($micro));
    wlog($rowid.":".$micro,__LINE__,__FUNCTION__,__FILE__);
    $rowid.=$micro;
    
    $sql="update {$tname} set {$fname}='{$rowid}' where rowid='' or rowid is null limit 1";
    $rs=$mysqli->query($sql);
    if($mysqli->affected_rows<1) $result=false;
}
?>