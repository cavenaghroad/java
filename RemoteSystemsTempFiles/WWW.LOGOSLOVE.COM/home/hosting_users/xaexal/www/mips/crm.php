<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?
include_once("include.php");
?>
<html>
<head>
<title>Academy CRM</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
<link href="<?=$BaseFolder ?>/base.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.structure.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.theme.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/fullcalendar-1.4.10/fullcalendar.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/uploadify/uploadify.css" rel="stylesheet" /> 
<!--<link rel="stylesheet" type="text/css" href="/jsfile/DataTables-1.10.7/media/css/jquery.dataTables.css">-->
<!--<link rel="stylesheet" type="text/css" href="/jsfile/DataTables-1.10.7/extensions/TableTools/css/dataTables.tableTools.css">-->
<!--<link rel="stylesheet" type="text/css" href="/jsfile/Editor/css/editor.dataTables.min.css">-->
<link rel="stylesheet" href="/jsfile/skin/ui.dynatree.css" />
<link rel="stylesheet" href="/jsfile/jquery.treetable.css" />
<link rel="stylesheet" href="/jsfile/jquery.treetable.theme.default.css" />
<link rel="stylesheet" href="/jsfile/screen.css" />
<link rel="stylesheet" href="/jsfile/jquery.contextMenu.css" />
</head>
<script type="text/javascript" src="/jsfile/jquery-1.11.2.min.js"></script>
<script type="text/javascript" src="/jsfile/jquery-ui.min.js"></script>
<script type="text/javascript" src="/jsfile/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="/jsfile/fullcalendar-1.4.10/fullcalendar.js"></script>
<script type="text/javascript" src="/jsfile/uploadify/swfobject.js"></script>
<script type="text/javascript" src="/jsfile/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script type="text/javascript" src="/jsfile/jquery-barcode-2.0.2.js"></script>
<script type="text/javascript" src="/jsfile/jquery.cookie.js"></script>
<script type="text/javascript" src="/jsfile/jquery.dynatree.js"></script>
<script type="text/javascript" src="/jsfile/jquery.layout.js"></script>
<script type="text/javascript" src="/jsfile/jquery.treetable.js"></script>
<script type="text/javascript" src="/jsfile/jquery.contextMenu.js"></script>
<script type="text/javascript" src="<?=$BaseFolder ?>/include.js"></script>
<script type="text/javascript" src="<?=$BaseFolder ?>/comlib.js"></script>
<script type="text/javascript" src="<?=$BaseFolder ?>/grid_table.js"></script>
<script type="text/javascript" src="<?=$BaseFolder ?>/appendix.js"></script>
<body id=bodyback onselectstart="return false;">
<script language='javascript'>
member_id='<?=$_SESSION['member_id'] ?>';
wlog('crm.php ['+member_id+']');
<?
$_SESSION['level']=$_GET['_l'];
$_SESSION['party']=$_GET['_e'];
if( isset($_SESSION['member_id']) ) {
	$sql="select level from a_mem_par where member_id='{$_SESSION['member_id']}' and level!={$_SESSION['level']}";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs!== false && $rs->num_rows>0){
		$row=$rs->fetch_array(MYSQLI_BOTH);
		$_SESSION['level']=$row[0];
	}
?>
	var gPHPSELF = '<?=$_GET['_p'] ?>';
	var gLevel='<?=$_SESSION['level']?>';
//	gMemberID='<?=$_SESSION['member_id']?>';
<?
} else {
?>
	document.location = './index.php';
<?
}
?>
</script>
<div id=c_header name=c_header class=ui-layout-north>
	<table align=center width=100% height=100%>
	<tr>
	   <td valign=bottom width=300px><label id=txtNameHeader name=txtNameHeader></label>
<?
$sql = "select a.party,b.name_kor from a_mem_par a,a_party b where a.member_id='{$_SESSION['member_id']}' and b.party<>'{$_GET['_e']}'".
		" and a.party=b.party order by b.name_kor";
wlog($sql,__LINE__,__FUNCTION__,__FILE__);
$rs=$mysqli->query($sql);
if($rs!== false && $rs->num_rows>0){
	$pstr="<select id=selParty name=selParty>";
	while($row=$rs->fetch_array(MYSQLI_BOTH)){
		$pstr.="<option value={$row['party']}>{$row['name_kor']}</option>";
	}
	$pstr.="</select>";
	echo $pstr;
}
?>	   
	   </td>
	    <td align=center valign=top><font color=blue size=5>
<?
$pTitle = "Academy CRM";
$sql = "select name_kor from a_party where party='{$_GET['_e']}'";
wlog($sql,__LINE__,__FUNCTION__,__FILE__);
$rs=$mysqli->query($sql);
if($rs!== false && $rs->num_rows>0){
	$row = $rs->fetch_array(MYSQLI_BOTH);//($result);
	$pTitle = $row[0];
}
echo $pTitle;
?>
</font></td>
		<td align=right  valign=bottom width="300px">
		<img src='img/chat.png' width="30px" height="30px" alt="" title="Chat with Support team" style="cursor:pointer" id=btnChat name=btnChat/>
		<input type=hidden name=_e id=_e value='<?=$_GET['_e']; ?>'>
		<input type=hidden name=_p id=_p value='<?=$_GET['_p']?>' />
		<button name=btnPersonal id=btnPersonal class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all"><?php echo $_SESSION['member_name'] ?></button>&nbsp;&nbsp;
		<button name="btnLogout" id="btnLogout" class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all logout" >LogOut</button>
		</td>
	</tr>
	</table> 
</div>
<div id=c_menu name=c_menu class=ui-layout-west>
<!--<table id=tMenu name=tMenu class=treetable border=1><thead><tr><th>Menu</th></tr><thead></table>-->
</div>
<div id=c_bottom name=c_bottom class=ui-layout-south>
</div>
<div id=c_right name=c_right class=ui-layout-east>
</div>
<div id=c_body name=c_body class=ui-layout-center align=center valign=top>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr height=20px>
<td align=left><label id=screen_name name=screen_name style='font-family:Tahoma;font-size:11px'></label></td>
</tr>
</table>
<table border=0 cellpadding="0" cellspacing="0"  valign=top id='<?=$_GET['_p'] ?>' name='<?=$_GET['_p'] ?>'>
</table>
</div>
</body>
<div id=dvLoading name=dvLoading style='display:none;filter:alpha(opacity=50); opacity:0.5;'>
<img src='<?=$BaseFolder ?>/img/spinner.gif' width:60px height:60px><b>Loading...</b></img>
</div>
<div id=dvTable name=dvTable style='overflow:auto'></div>
<ul id="crmMenu" class="contextMenu">
	<li class="edit"><a href="#edit">Edit</a></li>
</ul>
</html>