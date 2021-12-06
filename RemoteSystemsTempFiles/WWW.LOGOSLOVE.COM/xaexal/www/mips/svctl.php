<?
include_once("include.php");
include_once("header.php");
include_once('dialog.php');

$_SESSION['logtype']=1;
$wlog=new Logging();
$wlog->reset();

$cntry= getCountry($_SERVER['REMOTE_ADDR']);
if($cntry!="REPUBLIC OF KOREA") {
    ip_country($cntry);
}
if(!isset($_SESSION['superuser'])){
?>
<script>
document.location='sv.php';
</script>
<?
}
$_p="124F1828F9C200000";
$_p=$_GET['_p'];

$wlog->write(">>> PageID [{$_p}]",__LINE__,__FUNCTION__,__FILE__,1);
$log="";
foreach($_SESSION as $key=>$value)	{
    $log.="{$key} [{$value}]\n";
}
$wlog->write(">>> SESSION variables \n{$log}",__LINE__,__FUNCTION__,__FILE__,1);

define("SELF_INFO_PAGE","'124EF3FA72DD00000'");

$menu_id=$_p;
?>
<div id=c_header name=c_header class=ui-layout-north>
	<table align=center width=100% height=100%>
	<tr>
		<td width=30% valign=bottom>
		</td>
	    <td align=center valign=top><font color=blue size=5><?=showFieldFromTable("name_kor","a_party","party","'".$_SESSION['party']."'")?></font></td>
		<td align=right  valign=bottom width="30%">
			<img src='img/chat.png' width="30px" height="30px" alt="" title="Chat with Support team" style="cursor:pointer" id=btnChat name=btnChat/>
			<input type=hidden name=_p id=_p value='<?=$_p?>' />
			<button name=btnPersonal id=btnPersonal class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all"><?php echo $_SESSION['member_name'] ?></button>&nbsp;&nbsp;
			<button name="btnLogout" id="btnLogout" class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all" >LogOut</button>
		</td>
	</tr>
	</table> 
</div>
<div id=c_menu name=c_menu class=ui-layout-west>
<?echo MainMenu(SUPER_PARTY); ?>
</div>
<div id=c_bottom name=c_bottom class=ui-layout-south> </div>
<div id=c_right name=c_right class=ui-layout-east> </div>
<div id=c_body name=c_body class=ui-layout-center style='overflow:auto;' valign=top>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr height=20px>
	<td align=left><label id=screen_name name=screen_name style='font-family:Tahoma;font-size:11px'></label></td>
</tr>
</table>
<? drawLayout($_p); ?>
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
<?
include_once("footer.php");
?>