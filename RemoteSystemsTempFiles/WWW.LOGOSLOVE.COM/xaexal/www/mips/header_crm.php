<?php 
include_once("comfunc.php");
?>
<html>
<head>
<title>Academy CRM</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
<link href="<?=$BaseFolder ?>/base.css" rel='stylesheet' />
<link type="text/css" href="/jsfile/jquery-ui.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.structure.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.theme.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/fullcalendar-1.4.10/fullcalendar.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/uploadify/uploadify.css" rel="stylesheet" /> 
<!-- <link rel="stylesheet" href="/jsfile/elrte-1.3/css/elrte.min.css" type="text/css" media="screen" charset="utf-8"> -->
<link rel='stylesheet' href="/jsfile/skin/ui.dynatree.css" />
<link rel='stylesheet' href="/jsfile/jquery.treetable.css" />
<link rel="stylesheet" href="/jsfile/screen.css" />
</head>
<script language='javascript'>
var gPHPSELF = '<?=str_replace(".php","",substr(strrchr($_SERVER['PHP_SELF'],"/"),1)) ?>';
</script>
<script type='text/javascript' src='/jsfile/jquery-1.11.2.min.js'></script>
<script type='text/javascript' src="/jsfile/jquery-ui.min.js"></script>
<script type='text/javascript' src='/jsfile/fullcalendar-1.4.10/fullcalendar.js'></script>
<script type='text/javascript' src='/jsfile/uploadify/swfobject.js'></script>
<!-- <script type='text/javascript' src='/jsfile/uploadify/jquery.uploadify.v2.1.4.js'></script> -->
<script type='text/javascript' src='/jsfile/uploadify/jquery.uploadify.v2.1.4.min.js'></script>
<script type='text/javascript' src='/jsfile/jquery-barcode-2.0.2.js'></script>
<!-- <script type='text/javascript' src='/jsfile/elrte-1.3/js/elrte.min.js'></script> -->
<script type='text/javascript' src='/jsfile/jquery.cookie.js'></script>
<script type='text/javascript' src='/jsfile/jquery.dynatree.js'></script>
<script type='text/javascript' src='/jsfile/jquery.layout.js'></script>
<script type='text/javascript' src='/jsfile/jquery.treetable.js'></script>
<!-- <script type='text/javascript' src='/jsfile/jquery.jeditable.js'></script> -->
<script type="text/javascript" src="<?=$BaseFolder ?>/include.js"></script>
<script type="text/javascript" src="<?=$BaseFolder ?>/comlib.js"></script>
<script type="text/javascript" src="<?=$BaseFolder ?>/grid_table.js"></script>
<script type="text/javascript" src="<?=$BaseFolder ?>/appendix.js"></script>
<body id=bodyback onselectstart='return false;'>
<input type=hidden id=loggeduserid name=loggeduserid value="<?=$_SESSION['userid']; ?>" />
<? include_once("comdiv.php"); ?>
<script language='javascript'>
<?
if( isset($_SESSION['member']) ) {
	if( isset($_POST['enterprise']) ) {
		$_SESSION['enterprise'] = $_POST['enterprise'];
	}
?>
	var gPHPSELF = '<?=$_POST['phpname'] ?>';
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
	   <td valign=bottom width=200px><label id=txtNameHeader name=txtNameHeader></label></td>
	    <td colspan=2 align=center valign=top><font color=blue size=5>
<?php 
$pTitle = "Academy CRM";
$sql = "select name_kor from a_enterprise where enterprise='{_SESSION['enterprise']}'";
$rs=$mysqli->query($sql);
if($rs!==false && $rs->num_rows> 0 ) {
	$row = $rs->fetch_array(MYSQLI_BOTH);
	$pTitle = $row[0];
}
echo $pTitle;
?></font></td>
		<td align=right  valign=bottom width=200px>
		<input type=hidden name=g_enterprise id=g_enterprise value='<?php echo $_SESSION['enterprise']; ?>'>
		<input type=hidden name=g_phpself id=g_phpself value='<?=str_replace(".php","",substr(strrchr($_SERVER['PHP_SELF'],"/"),1)) ?>' />
		<input type=hidden name=g_position id=g_position value='<?php if( $_SESSION['userid'] == "xaexal@gmail.com" ) {echo "controller";} else {echo "customer";} ?>'>
		<button name=btnPersonal id=btnPersonal class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" style='height:20px'><?php echo $_SESSION['member_name'] ?></button>&nbsp;&nbsp;
		<button name="btnLogout" id="btnLogout" class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all">LogOut</button>
		</td>
	</tr>
	</table> 
</div>
<div id=c_menu name=c_menu class=ui-layout-west>
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