<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?
include_once("include.php");
include_once("header.php");
include_once('dialog.php');

$_SESSION['logtype']=2;
$wlog=new Logging();
$wlog->reset();

$cntry= getCountry($_SERVER['REMOTE_ADDR']);
if($cntry<>"REPUBLIC OF KOREA") {
    ip_country($cntry);
}

$_p=$_GET['_p'];
$wlog->write(">>> PageID [{$_p}]",__LINE__,__FUNCTION__,__FILE__);
if($_p==""){
?>
<script language='javascript'>
	alert('No page ID given');
	window.history.back();
</script>
<?    
} else {
?>
<script language='javascript'>
</script>
<?
}
if( !isset($_SESSION['member_id']) ) {
?>
<script language='javascript'>
	document.location = './index.php';
</script>
<?
}
$log="";
foreach($_SESSION as $key=>$value)	{
    $log.="{$key} [{$value}]\n";
}
$wlog->write(">>> SESSION variables \n{$log}",__LINE__,__FUNCTION__,__FILE__);

define("SELF_INFO_PAGE","'124EF3FA72DD00000'");

$menu_id=$_p;
/********************************************************************************* 
 * if user is general user (not supervisor), check if this page belongs to this user's party menu.
 * ********************************************************************************/
if($_SESSION['member_id']!=SUPERVISOR && $_p!=SELF_INFO_PAGE){    // if not xaexal.
    $wlog->write(">>> This is general user.",__LINE__,__FUNCTION__,__FILE__);
    $arRow=array();
    // check if page_i is in the i_navi table.
    $rs=sqlrun("select rowid,par_rowid from i_navi where page_id='{$_p}'",__LINE__,__FUNCTION__,__FILE__);
    while($row=$rs->fetch_assoc()) {
        $par_rowid=$row['par_rowid'];
        $rowid=$row['rowid'];
        // check which party value the root menu-item has by recursive search.
        while($par_rowid!=""){
            $rs1=sqlrun("select rowid,par_rowid from i_navi where rowid='{$row['par_rowid']}'",__LINE__,__FUNCTION__,__FILE__);
            $row1=$rs1->fetch_assoc();
            $rowid=$row1['rowid'];
            $par_rowid=$row1['par_rowid'];
        }
        array_push($arRow,$rowid);
    }
    $cnt=0;
    if(count($arRow)>0){
        // check if the party of this user has 
        $rs=sqlrun("select count(*) cnt from a_mem_par where member_id='{$_SESSION['member_id']}' and party in (select party from a_party where menu_id in ('".implode("','",$arRow)."'))",__LINE__,__FUNCTION__,__FILE__);
        $row=$rs->fetch_assoc();
        $wlog->write("cnt [{$row['cnt']}]",__LINE__,__FUNCTION__,__FILE__);
        $cnt=intval($row['cnt']);
    }
    if($cnt<1){
?>
    <script language='javascript'>
    	document.location = './index.php';
    </script>
<?
    }
}
?>
<div id=c_header name=c_header class=ui-layout-north>
	<table align=center width=100% height=100%>
	<tr>
		<td width=30% valign=bottom>
<?
$wlog->write(">>> Building up Party dropdown list.",__LINE__,__FUNCTION__,__FILE__);
$rs=sqlrun("select b.party,b.name_kor from a_mem_par a,a_party b where b.party<>'".SUPER_PARTY."' and  a.member_id='{$_SESSION['member_id']}' and a.party=b.party order by a.level",__LINE__,__FUNCTION__,__FILE__);
if($rs->num_rows>1) echo "<select id=selParty>";
while($row=$rs->fetch_assoc()){
    $selected="";
    if($row['party']==$_SESSION['party']) $selected=" selected";
    echo "<option value='{$row['party']}'{$selected}>{$row['name_kor']}</option>";
}
if($rs->num_rows>1) echo "</select>";
?>		
		</td>
	    <td align=center valign=top><font color=blue size=5><?=showFieldFromTable("name_kor","a_party","party","'".$_SESSION['party']."'")?></font></td>
		<td align=right  valign=bottom width="30%">
			<img src='img/chat.png' width="30px" height="30px" alt="" title="Chat with Support team" style="cursor:pointer" id=btnChat name=btnChat/>
			<input type=hidden name=_p id=_p value='<?=$_p?>' />
			<button name=btnPersonal id=btnPersonal class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all"><?php echo $_SESSION['member_name'] ?></button>&nbsp;&nbsp;
			<button name="btnLogout" id="btnLogout" class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all logout" >LogOut</button>
		</td>
	</tr>
	</table> 
</div>
<div id=c_menu name=c_menu class=ui-layout-west>
<?echo MainMenu($_SESSION['party']); ?>
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

<div id=dvLoading border=0>
<img src='img/spinner.gif' border=0><b>Loading...</b></img>
</div>
<div id=dvTable name=dvTable style='overflow:auto'></div>
<ul id="crmMenu" class="contextMenu">
	<li class="edit"><a href="#edit">Edit</a></li>
</ul>

<ul id=custom_menu>
	<li> names</li>
</ul>
</html>
<?
$rs=sqlrun("update a_member set startpage='{$_p}' where member_id='{$_SESSION['member_id']}'",__LINE__,__FUNCTION__,__FILE__);
include_once("footer.php");
?>