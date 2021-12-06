<?
include_once("include.php");

$wlog=new Logging();

switch($_GET['optype']){
case "write":    
    $wlog->write($_GET['pstr']);
    break;
case "reset":
    $wlog->reset();
}
?>