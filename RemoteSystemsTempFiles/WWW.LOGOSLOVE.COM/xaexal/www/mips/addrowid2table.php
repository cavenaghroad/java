<?php
include_once("include.php");

do {
	$nUpdate = 1;

	$newdate = dechex(date("YmdHis"));
	$sql = "select rowid from ".$_POST['tname']." where rowid='".$newdate."'";
	$nCount = runQuery($sql,$result);
	if( $nCount < 1 ) {
		$nUpdate = 0;
		$sql = "update ".$_GET['tname']." set rowid='".$newdate."' where rowid='' or rowid is null limit 1";
		$nUpdate = runQuery($sql,$result);
	}
	echo "newdate [".$newdate."] nCount [".$nCount."] nUpdate [".$nUpdate."]<br>";
	sleep(1);
} while( $nUpdate > 0 );

function filelog($pstr) {
	global $logfile;
	$fp = fopen($logfile,"a+");
	fputs($fp,$pstr."<br>");
	fclose($fp);
}

function runQuery($sql,&$result) {
    global $link;

    $sql = stripslashes($sql);
    $result = mysql_query($sql,$link);
    filelog($sql);
    if( mysql_error($link) != "" ) {
        filelog(mysql_error($link));	//$mysql_err() caused function name must be a string error 
        return -1;
    }
    if( strtolower(substr($sql,0,6)) == "select" )
       @$nCount = mysql_num_rows($result);
    else
        @$nCount = mysql_affected_rows($link);
    filelog("nCount [".$nCount."]");

    return $nCount; 
}
function createNode(&$doc, &$parentNode, $ndName, $ndValue="") 
{
	$child = $doc->createElement($ndName);
	$child = $parentNode->appendChild($child);
	if( $ndValue != "" ) {
// 		filelog("ndValue [".$ndValue."]");
		$value = $doc->createTextNode($ndValue);
		$value = $child->appendChild($value);
	}
	return $child;
}

/*
$n = 0;
$ndx = 0;
	$micros = microtime(true);
while( $ndx < 5 ) {
	$micros1 = microtime(true);
	if( $micros == $micros1 ) $n++;
	else	{
		 echo "microtime [".strval($micros)."] [".strval($n)."]<br>";
//		 ob_flush();
		$micros = $micros1;
		$ndx ++;
		$n=0;
	}
}
*/
?>