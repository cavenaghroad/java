<?
include_once("header.php");

$lang=$_GET['_l'];
if( $lang=="" ){
	$_SESSION['lang']="en";
?>
<script language='javascript'>
var _l = $.cookie('lingua');
if( _l === undefined || _l=='' ) {
	_l = 'en';
	$.cookie('lingua',_l);
//} else {
//	alert('<?=$_SERVER['PHP_SELF']?>?_l='+_l+'&<?=$_SERVER['QUERY_STRING']?>');
}
document.location = '<?=$_SERVER['PHP_SELF']?>?_l='+_l+'&<?=$_SERVER['QUERY_STRING']?>';
</script>
<?
exit;
}
$_SESSION['lang']=$lang;
// echo function_exists("resetlog");
resetlog();
?>
<table border=0 cellpadding=0 cellspacing=0 width=100% height=150px>
<tr>
	<td width=70% align=center onclick='document.location='+BaseFolder+';'>
		<font size="24" style='text-shadow: 5px 5px 5px #ccc'><?php echo "XAEXAL";//$arTitle["eatery title"] ?></font>
	</td>
</tr>
<tr>
	<td align=center valign=top>
<? 
	echo getCountry($_SERVER['REMOTE_ADDR']);
?>		
	</td>
</tr>
</table>

<table border="0" cellspacing=1 cellpadding=0 align="center" style='width:500px;'>
<tr height=48px>
	<td colspan=3><h1><?=_label("btnLogin",$lang)?></h1></td>
</tr>
<tr>
	<td><?=_label("userid",$lang)?>&nbsp;</td>
	<td><?=_label("passcode",$lang)?>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr> 
	<td width=220px><input type=text name=userid id=userid maxlength=36 size=36 class="txt" style="ime-mode:inactive" ></input></td>
	<td><input type=password id="passcode" name="passcode" size="16" maxlength="20" class='txt' style="ime-mode:inactive">
		<input type=hidden name=regdate id=regdate /></td>
	<td><input type=button name=btnLogin id=btnLogin value=<?=_label("btnLogin",$lang)?>></td>
</tr>
<tr>
	<td>&nbsp;</td>
 	<td ><a href="./forgot.php" class=vanilla style='cursor:hand' name=forgot id=forgot><u><?=_label("forgot",$lang)?></u></a></td>
 	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan=3><hr></td>
</tr>
</table>
<table align="center" cellpadding="0" cellspacing="0" border="0" width=500px>
<tr height=50px><td>&nbsp;</td></tr>
<tr>
	<td height=48px><h1><?=_label("newaccount",$lang)?></h1>
	</td>
</tr>
</table>
<table border=0 cellpadding=0 cellspacing=0 style='box-shadow: 10px 10px 5px #888888;background-color:#C8F8F5;margin:0 auto;width:500px;'>
<tr height=10px><td>&nbsp;</td></tr>
<tr>
	<td>
		<table class=tdeco>
		<tr>
			<td align=right><font color=red>*</font><?=_label("userid1",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=text class=txt name=userid1 id=userid1 size=32 maxlength=32 /></td>
			<td rowspan=4>&nbsp;</td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("userid2",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=text class=txt name=userid2 id=userid2 size=32 maxlength=32 /></td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("passcode1",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=password class=txt name=passcode1 id=passcode1 size=20 maxlength=20 /></td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("passcode2",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=password class=txt name=passcode2 id=passcode2 size=20 maxlength=20 />
			&nbsp;<label id=lblPasscode></label></td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("member_name",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=text class=txt name=member_name id=member_name size=20 maxlength=32 /></td>
			<td rowspan=3 valign=top><?=$comment1 ?></td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("birth_date",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=text class=txt name=birth_date id=birth_date size=10 maxlength=10  />&nbsp;</td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("mobile",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=tel class=txt name=mobile id=mobile size=32 maxlength=32 /></td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("groupname",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=text class=txt name=name_kor id=name_kor size=32 maxlength=32></td>
			<td>
			<input type=button name=btnFind id=btnFind value='<?=_label("btnFind",$lang)?>' />&nbsp;
			<input type=button name=btnEmpty id=btnEmpty value='<?=_label("btnEmpty",$lang)?>'/>
			<input type=hidden name=party id=party>
			<input type=hidden id=member_id>
			</td>
		</tr>
		<tr height=40px>
			<td colspan=3 align=center valign=middle>
				<input type=button name=btnSubmit id=btnSubmit value='<?=_label("btnSubmit",$lang)?>' />&nbsp;
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<table width=100% border=0 cellpadding=0 cellspacing=0 id=tblLogout name=tblLogout width=100% <? if(!isset($_SESSION['userid'])) echo "style='display:none'";?>>
<tr>
	<td name=tdStation id=tdStation align=center>
<?
	if( isset($_SESSION['userid']) ) {
		$sql = "select b.party,c.name_kor party_name ".
			"from a_member a,a_mem_par b left outer join a_party c  on b.party=c.party ".
			"where a.userid='{_SESSION['userid']}' and a.member_id=b.member_id";
		@$nCount = runQuery($sql,$result);
		if( $nCount > 0 ) {
?>
		<table border=0 cellpadding=0 cellspacing=0>
<?
			while( $row = mysql_fetch_array($result) ) {
				if( $row['party_name']=="" && $row['party'] == "FFFFFFFFFFFF" ) $row['party_name']="SupoerVisoR";
				echo "<tr height=20px><td valign=middle><a href='crm.php?_e={row['party']}' />{$row['party_name']}</td></tr>";
			}
?>
		</table>
<?
		}
	}
?>
	</td>
</tr>
</table>
<table border="0" width="500px" align=center>
<tr height=30px><td colspan=3>&nbsp;</td></tr>
<tr>
	<td align=center><a href='javascript:setLang("en");'>English</a></td>
	<td align=center><a href='javascript:setLang("ko");'>Korean</a></td>
	<td align=center><a href='javascript:setLang("cn");'>Chinese</a></td>
	<td align=center><a href='javascript:setLang("vn");'>Vietnamnese</a></td>
</tr>
</table>
<div name=dvParty id=dvParty style='display:none'>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td align=center><?=_label("group",$lang)?></td>
</tr>
<tr>
<td align=center><select name=selParty id=selParty style='width:500px' size=12>
<option value='na' selected><?=_label("na",$lang)?></option>
</select>
</td>
</tr>
<tr>
	<td><? echo $n; ?>&nbsp;Companies</td>
</tr>
</table>
</div>
<?

include_once("footer.php");
?>