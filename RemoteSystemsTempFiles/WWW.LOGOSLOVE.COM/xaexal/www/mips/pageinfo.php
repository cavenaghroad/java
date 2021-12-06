<?
include_once("include.php");
wlog("pageinfo.................",__LINE__,__FUNCTION__,__FILE__);
$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$_now= date('YmdHis'.$micro);

wlog("party [{$_SESSION['party']}]",__LINE__,__FUNCTION__,__FILE__);

if(!isset($_SESSION['party'])) {
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	$mysqli->close();
}
if( isset($_POST['optype']) ) 	$_optype = $_POST['optype'];
else if( isset($_GET['optype']) ) $_optype = $_GET['optype'];
else exit;

$log="";
switch($_POST['optype']){
case "newest": case "check-expiry":
	break;
default:
	foreach($_POST as $key=>$value)	{
		if($key=="optype") {
			$log.="{$key} [<font color=red>{$value}</font>]";
		} else{
			$log.="{$key} [{$value}] ";
		}
	}
	wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);
}

try {
	$_p=$_POST['_p'];	// first page 
	$_e=$_POST['_e'];	// party ID.
	/*
	 * pageinfo
	 * gridinfo
	 * colinfo
	 * linkinfo
	 */
	$bMember = chkMemShip($_e);
	wlog("pageinfo [{$_p}/{$_e}] bMember [{$bMember}] substr [".substr($p,0,1)."/{$_p}]",__LINE__,__FUNCTION__,__FILE__);
	if($bMember==-1) exit;

	$sql = "select * from i_page where rowid='{$_p}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($rs->num_rows<1) throw new Exception("No record on I_PAGE.");

	$xmlcls=new xmlCls();
	$pageinfo=$xmlcls->createLeaf("","pageinfo");
	$child=$xmlcls->createLeaf($pageinfo,"attrinfo");
	while($row=$rs->fetch_assoc()){
		for( $i = 0; $i < $rs->field_count; $i++) {
			$rs->field_seek($i);
			$finfo=$rs->fetch_field();
			$occ = $xmlcls->createLeaf($child, $finfo->name, $row[$finfo->name]);
		}
	}
	
	$sql="select * from i_grid where par_rowid='{$_p}' order by seqno";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($rs->num_rows<1) throw new Exception("No record on I_GRID.");

	$gridinfo=$xmlcls->createLeaf($pageinfo,"gridinfo");
	while($row=$rs->fetch_assoc()){
		$child=$xmlcls->createLeaf($gridinfo,"attrinfo");

		switch($row['viewtype'])
		{
		case "TREE":	$_template="TREE"; break;
		case "DIAL":	$_template="DIAL"; break;
		case "VERT":	$_template="VERT"; break;
		case "HRZN":	
		default:
			$_template="HRZN"; break;
		}
		$_template=getOnlyField("a_template","htmltext","code",$_template);
		wlog("template [".htmlspecialchars($_template)."]",__LINE__,__FUNCTION__,__FILE__);
		$_template = str_replace("{{toollist}}","toolbar{$row['rowid']}",$_template);
		wlog("template [".htmlspecialchars($_template)."]",__LINE__,__FUNCTION__,__FILE__);
		$_template = str_replace("{{tbl_list}}","tbl{$row['rowid']}",$_template);
		wlog("template [".htmlspecialchars($_template)."]",__LINE__,__FUNCTION__,__FILE__);
		$_layout = $_template;
		
		$occ=$xmlcls->createLeaf($child,"layout",$_layout);
		
		wlog("_layout [".htmlspecialchars($_layout)."]",__LINE__,__FUNCTION__,__FILE__);
		$occ=$xmlcls->createLeaf($child,"toolbar",$htmltext);

		$token="";
		if(is_numeric($row['viewtype'])) {
			$token=$row['formhtml'];
		}
		$occ=$xmlcls->createLeaf($child,"formframe",$token);
					
		$rowid=$row['rowid'];
		$arCol=array('title','title_enu','nick','dtype','digit','pixel','nullable','editable','align','element','tooltip_msg','a4update');
		$sqlCol="select ".implode(",",$arCol)." from i_col where par_rowid='{$rowid}' and inactivated!='1' order by seqno";
		wlog($sqlCol,__LINE__,__FUNCTION__,__FILE__);
		$rs1=$mysqli->query($sqlCol);
		if($rs1===false) throw new Exception($mysqli->error);
		if($rs1->num_rows<1) throw new Exception("No record on I_COL.");
		
		$child=$xmlcls->createLeaf($gridinfo,"colinfo");
		while($row1=$rs1->fetch_assoc()){
			foreach($finfo as $val){
				$occ=$xmlcls->createLeaf($child,$val->name,$row1[$val->name]);
			}
		}	

		$sql="select * from i_foreign where parent_grid='{$rowid}' order by seqno";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs2=$mysqli->query($sql);
		if($rs2===false) throw new Exception($mysqli->error);

		$child=$xmlcls->createLeaf($gridinfo,"linkinfo");
		
		while($row2=$rs2->fetch_assoc()){
			$rsMAIN=$mysqli->query("select * from i_col where par_rowid='{$row2['parent_grid']}' order by seqno");
			if($rsMAIN===false) throw new Exception("MAIN [{$mysqli->error}]");
			$rsSUB=$mysqli->query("select * from i_col where par_rowid='{$row2['child_grid']}' order by seqno");
			if($rsSUB===false) throw new Exception("SUB [{$mysqli->error}]");
			
			while($finfo=$rs->fetch_field()){
				switch($finfo->fname){
				case "parent_col":
					if(is_null($row2['parent_col'])) break;
					$n=0;
					$rsMAIN->data_seek($n);
					while($rowMAIN=$rsMAIN->fetch_assoc()){
						++$n;
						if($rsMAIN['nick']==$row2['parent_col']) break;
					}
// 						$linkinfo=array_merge($linkinfo,array("parent_col"=>strval($n)));
					$occ=$xmlcls->createLeaf($child,"parent_col",strval($n));
					break;
				case "child_col":
					if(is_null($row2['child_col'])) break;
					$n=0;
					$rsSUB->data_seek($n);
					while($rowSUB=$rsSUB->fetch_assoc()){
						++$n;
						if($rsSUB['nick']==$row2['child_col']) break;
					}
// 						$linkinfo=array_merge($linkinfo,array("child_col"=>strval($n)));
					$occ=$xmlcls->createLeaf($child,"child_col",strval($n));
					break;
				case "child_sort_col":
					if(is_null($row2['child_sort_col'])) break;
					$n=0;
					$rsSUB->data_seek($n);
					while($rowSUB=$rsSUB->fetch_assoc()){
						++$n;
						if($rsSUB['nick']==$row2['child_sort_col']) break;
					}
// 						$linkinfo=array_merge($linkinfo,array("child_sort_col"=>strval($n)));
					$occ=$xmlcls->createLeaf($child,"child_sort_col",strval($n));
					break;
				}
			}
		}
// 			$answer['gridinfo']['linkinfo']=array_merge($answer['gridinfo']['linkinfo'],$linkinfo);
	}
// 		$answer['result']="0";
	$occ=$xmlcls->createLeaf("","result","0");
} catch(Exception $e){
// 		$answer['msg']=$e->getMessage();
	wlog("Exception [{$e->getMessage()}]",__LINE__,__FUNCTION__,__LINE__);
	$occ=$xmlcls->createLeaf("","msg",$e->getMessage());
}
// 	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
// 	echo json_encode($answer);
$xmlcls->sendXML();

?>