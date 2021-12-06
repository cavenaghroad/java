<?
include_once("comfunc.php");
if( isset($_POST['optype']) ) 	$_optype = $_POST['optype'];
else if( isset($_GET['optype']) ) $_optype = $_GET['optype'];
else exit;
switch($optype){
case "send":
	$utctime=gmdate("YmdHis", time());
	$sql="insert into a_chat set receiver='xaexal',sender='".$sender."',msg='".$msg."',regdate='".$utctime."'";
	@$nCount=runQuery($sql,$result,__FUNCTION__,$errno,$errText);
	if($nCount<1) echo "";
	else {
		echo $utctime;
	}
	break;
case "fetchX":
	$sql="select distinct member_id from a_chat where read!='1'";
	@$nCount=runQuey($sql,$result,__FUNCTION__,$errno,$errText);
	if($nCount<1) echo "";
	else {
		echo $utctime;
	}
	break;
case "fetch":
	$sql="select regdate,msg,sender from a_chat where receiver='".$receiver."' order by regdate desc";
	@$nCount=runQuery($sql,$result,__FUNCTION__,$errno,$errText);
	if($nCount<1) echo "";
	else {
		$self=new xmlCls();
		
		while($row=mysql_fetch_array($result)){
			$child=$self->createFirst("crlf");
			for($i=0;$i<mysql_num_fields($result);$i++){
				$occ = $self->createNode($child,mysql_field_name($result,$i), $row[$i]);
			}
		}
		$self->sendXML();
	}
	break;
} 
?>