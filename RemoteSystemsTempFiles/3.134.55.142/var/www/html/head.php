<?
$_root_path="/home/hosting_users/xaexal/www/";

$_zb_url = "http://www.xaexal.com/bbs/";
$_zb_path="/home/hosting_users/xaexal/www/bbs/";

include_once( $_zb_path."outlogin.php" );

include_once("customs/comlib/class_stuff.php");
include_once("customs/comlib/custom.lib.php");
include_once("customs/comlib/class_def.php");
include_once("customs/comlib/class_def1.php");
/*include_once  $_zb_path."outlogin.php";*/
include_once($_zb_path."_head.php");

$table_width = 1040;

?>

<style>
td , select ,input {font-family:tahoma; font-size:11px; color:#000000}
.w_title1 {font-family:Verdana; font-size:11px; color:#000000}
</style>
<table align="center" cellpadding="0" cellspacing="0"  border=0>
<tr>
<td colspan=3 height=40px><a href="/"><font family='Verdana' size=36px  bgcolor='#FF0000'><?php echo $_SERVER['PHP_SELF']?></font></a></td>
</tr>
<td  id=leftpanel align=left valign=top width=170px class='w_title1'>&nbsp;
<?//= print_outlogin("default",1,10); ?>
<?= ezCalendar(); ?>
<?= CalMenu(); ?>
<?= LeftMenu(); ?>
<table border=0 cellpadding=0 cellspacing=0><tr><td height=400px>&nbsp;</td></tr></table>
<?= xRate(); ?>
<?= kWeather();?>

</td>
<td align=center valign=top width=700px><div id=dvMain>
