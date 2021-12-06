<?
include_once("include.php");
include_once("header.php");

session_destroy();
$_SESSION['logtype']=1;
$wlog=new Logging();
$lang="en";
if(isset($_SESSION['superuser'])) {
?>
<script>
document.location='svctl.php';
</script>
<?
}
?>
<table border=0 cellpadding=0 cellspacing=0 width=100% height=150px>
<tr>
	<td width=70% align=center onclick='document.location='+BaseFolder+';'>
		<font size="24" style='text-shadow: 5px 5px 5px #ccc'><?php echo "EZRA 29";//$arTitle["eatery title"] ?></font>
	</td>
</tr>
<tr>
	<td align=center valign=top>
	</td>
</tr>
</table>

<table cellspacing=1 cellpadding=0 align="center" >
<tr height=48px>
	<td colspan=2>&nbsp;</td>
	<td><h1><?=_label("btnLogin",$lang)?></h1></td>
</tr>
<tr>
	<td align=right><?=_label("userid",$lang)?>&nbsp;</td><td width=10px>&nbsp;</td>
	<td><input type=text name=userid id=userid maxlength=20 size=20 class="txt" style="ime-mode:inactive" ></input></td>
</tr>	
<tr>
	<td align=right><?=_label("passcode",$lang)?>&nbsp;</td><td width=10px>&nbsp;</td>
	<td><input type=password id="passcode" name="passcode" size="12" maxlength="12" class='txt' style="ime-mode:inactive">
		<input type=hidden name=regdate id=regdate /></td>
</tr>
<tr > 
	<td colspan=2>&nbsp;</td>
	<td><input type=button name=btnLogin id=btnLogin value=<?=_label("btnLogin",$lang)?>></td>
</tr>
<tr height=48>
	<td colspan=2>&nbsp;</td>
 	<td valign=bottom><a href="./forgot.php" class=vanilla style='cursor:hand' name=forgot id=forgot><small><u><?=_label("forgot",$lang)?></u></small></a></td>
</tr>
</table>
<?
include_once("footer.php");
?>