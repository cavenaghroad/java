<?
@session_start();

if(!isset($_SESSION['party'])){
?>
<script language='javascript'>
	document.location='login.php';
</script>
<?
} else {
?>
<script language='javascript'>
	document.location='admin.php';
</script>
<?
}
?>