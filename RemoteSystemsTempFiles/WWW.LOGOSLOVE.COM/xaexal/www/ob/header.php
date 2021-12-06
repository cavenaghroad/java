<?
@session_set_cookie_params(24*60*60*1000,"/");
@session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>xaexal</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
<link type="text/css" href="/jsfile/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.structure.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.theme.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/fullcalendar-1.4.10/fullcalendar.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/uploadify3.2.1/uploadify.css" rel="stylesheet" /> 
<link type='text/css' href="/ob/base.css" rel=stylesheet />
<link type='text/css' href="/jsfile/timepicker.css" rel=stylesheet />
</head>
<script type="text/javascript" src="/jsfile/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/jsfile/jquery-ui.min.js"></script>
<script type='text/javascript' src='/jsfile/jquery.layout.js'></script>
<script type="text/javascript" src="/jsfile/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="/jsfile/fullcalendar-1.4.10/fullcalendar.js"></script>
<script type="text/javascript" src="/jsfile/uploadify3.2.1/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="/jsfile/timepicker.js"></script>
<script type="text/javascript" src="/jsfile/jquery.redirect.js"></script>
<script type="text/javascript" src="/ob/include.js"></script>
<script language='javascript'>
gParty="<?=$_SESSION['party']?>";
</script>
<body>