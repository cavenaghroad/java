<?
	if(isset($_GET['admin'])){
?>
<script language='javascript'>
	document.location='admin.php';
</script>
<?
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Page Title</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" src="base.css" />
	<script type='text/javascript' src="/jsfile/jquery-1.11.2.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<script type="text/javascript" src="/ob/include.js"></script>
</head>
<body>

<?
require_once 'branch.php';
require_once 'verify.php';
require_once 'menu.php';
require_once 'inhouse.php';
require_once 'delivery.php';
require_once 'takeout.php';
require_once 'showhistory.php';
require_once 'book.php';
?>
</body>
<script type='text/javascript' src='index.js'></script>
</html>