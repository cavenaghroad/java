<?
include_once("../common/_init_.php");
?>
<html><head><title>generateROwid</title></head>
<body>
<?
if(!isset($_GET['id'])) $_GET['id']="0000";
echo generateROWID($_GET['id']);
?>
</body></html>