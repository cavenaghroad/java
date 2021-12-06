<?
session_start();
if(!isset($_SESSION['member_id'])){
    echo "<script>document.location='/index.php';</script>";
    exit;
}
include_once '../common/_init_.php';
?>
<html>
<head>
	<title>관리화면</title>
</head>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300&display=swap" rel="stylesheet">
<link href="base.css" rel="stylesheet">
<script src='https://code.jquery.com/jquery-3.5.0.js'></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src='common.js'></script>
<script src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>
<body>
<div id=header>
	<table style='width:100%;height:100%;'>
    <tr>
        <td align=center>
        <h1>일대일 성경공부 관리</h1><b style='color:yellow'>양육자/동반자 관리</b>
 	   	</td></tr>
    <tr>
    	<td>
<? include_once("admin_menu.php"); ?>    	
    	</td>
    </tr>
</table>
</div>
<div id=content align=center valign=top>
<table>
<tr>
	<td align=right valign=top>
<?
    include_once("tableMentor.php");
?>
	</td>
	<td valign=top>
<?
    include_once("tableMentee.php");
?>    
	</td>
	<td valign=top>
<?    
    include_once("tableHistory.php");
?>
	</td>
</tr>
</table>
</div>
<? 
include_once "dlg_mentor.php";
include_once "dlg_mentee.php" ;
include_once "dlg_pastor.php";
?>
</body>
</html>