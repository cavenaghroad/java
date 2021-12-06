<?
require_once 'common.php';
require_once $orgpath.'/android/include.php';

foreach($_GET as $key=>$value)	wlog("{$key} [{$value}]",__LINE__,__FUNCTION__,__FILE__);

$response=array();

$_where="";
if($_GET['where']!=""){
	foreach(explode(",",$_GET['where']) as $k=>$v){
		$_where.=($_where=="")?" where ":" and ";
		$_where.="{$v}=";
		
		if(substr($_GET[$v],0,1)=="_") $_where.="'".substr($_GET[$v],1)."'";
		else $_where.=$_GET[$v];
		wlog("k [{$k}] v [{$v}] where [{$_where}]",__LINE__,__FUNCTION__,__FILE__);
	}
}
wlog("where [{$_where}]",__LINE__,__FUNCTION__,__FILE__);

if($_GET['orderby']!="") $_orderby="order by {$_GET['orderby']}";
else $_orderby="";

if($_GET['count']!="")	$limit=" limit 0,{$_GET['count']}";
else $limit="";

$sql="select {$_GET['fld']} from {$_GET['tbl']} {$_where} {$_orderby}{$limit}";

wlog($sql,__LINE__,__FUNCTION__,__FILE__);
$rs=$mysqli->query($sql);
if($rs===false) {
	$response['success']=-1;
	$response['return']="no menu found";
} else{
	$response['success']=0;
	$response['return']=array();
	while($row=$rs->fetch_array(MYSQLI_BOTH)){
		$columns=array();
		foreach(explode(",",$_GET['fld']) as $k=>$v){
			$columns[$v]=$row[$v];
		}
		array_push($response['return'],$columns);
	}
}
$mysqli->close();
wlog(json_encode($response),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($response);
?>