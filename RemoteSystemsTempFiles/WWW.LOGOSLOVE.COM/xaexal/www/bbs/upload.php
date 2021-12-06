<?
include_once("include.php");

wlog("upload started",__LINE__,__FUNCTION__,__FILE__);

$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$micro=strtoupper(dechex($micro));
$fileid=strtoupper(dechex(date("YmdHis")));
$fileid.=$micro;

$answer=array();
$answer['msg']="";

if ( 0 < $_FILES['pic1']['error'] ) {
    $answer['msg'] = $_FILES['pic1']['error'];
    wlog("Error [{$_FILES['pic1']['error']}'",__LINE__,__FUNCTION__,__FILE__);
} else {
	$tmp_name=$_FILES['pic1']['tmp_name'];
	$filename=$_FILES['pic1']['name'];
	$n=strrpos($filename,".");
	if($n===false) {
		$answer['msg']="Can't identify file type.";
	} else {
		$n=substr($filename,$n+1);
		$filename=$fileid.".".$n;
		wlog("filename [{$filename}]",__LINE__,__FUNCTION__,__FILE__);
    
	    if(move_uploaded_file($tmp_name, './picture/' . $filename)){
	    	$answer['file_id']=$filename;
	    	$finfo=getimagesize("./picture/".$filename);
	    	$answer['width']=$finfo[0];
	    	$answer['height']=$finfo[1];
	    }
	}
}
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);	
?>