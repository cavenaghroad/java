<?
include_once("include.php");
include_once("header.php");

$_SESSION['logtype']=2;
// $wlog=new Logging();
$cntry= getCountry($_SERVER['REMOTE_ADDR']);
if($cntry<>"REPUBLIC OF KOREA") {
	ip_country($cntry);
}
// resetlog(__LINE__,__FUNCTION__,__FILE__);

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
alert("....");
document.location = '<?=$_SERVER['PHP_SELF']?>?_l='+_l+'&<?=$_SERVER['QUERY_STRING']?>';
</script>
<?
exit;
}
$_SESSION['lang']=$lang;
?>
<table border=0 cellpadding=0 cellspacing=0 width=100% height=150px>
<tr>
	<td width=70% align=center onclick='document.location='+BaseFolder+';'>
		<font size="24" style='text-shadow: 5px 5px 5px #ccc'><? echo "EZRA 29";//$arTitle["eatery title"] ?></font>
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
	<td width=220px><input type=text name=userid id=userid maxlength=20 size=20 class="txt" style="ime-mode:inactive" ></input></td>
	<td><input type=password id="passcode" name="passcode" size="12" maxlength="12" class='txt' style="ime-mode:inactive">
		<input type=hidden name=regdate id=regdate /></td>
	<td><input type=button name=btnLogin id=btnLogin value=<?=_label("btnLogin",$lang)?>></td>
</tr>
<tr>
	<td>&nbsp;</td>
 	<td ><a href="./forgot.php" class=vanilla style='cursor:hand' name=forgot id=forgot><u><?=_label("forgot",$lang)?></u></a></td>
 	<td>&nbsp;</td>
</tr>
<tr>
	<td colspan=3>
		<select id=selParty style='display:none;'>
		<option value=''>占쎄퐨占쎄문占쎈릭疫뀐옙</option>
		</select>
	</td>
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
			<td>&nbsp;<input type=text class=txt name=userid1 id=userid1 size=20 maxlength=20 />
			<input type=hidden id=check_userid value='0'></td>
			<td rowspan=4>&nbsp;</td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("passcode1",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=password class=txt name=passcode1 id=passcode1 size=12 maxlength=12 /></td>
		</tr>
		<tr>
			<td align=right><font color=red>*</font><?=_label("passcode2",$lang)?>&nbsp;</td>
			<td>&nbsp;<input type=password class=txt name=passcode2 id=passcode2 size=12 maxlength=12 />
			&nbsp;<label id=lblPasscode></label></td>
		</tr>
<!-- 		<tr> 
			<td align=right><font color=red>*</font><?=_label("member_name",$lang)?>&nbsp;</td>
 			<td>&nbsp;<input type=text class=txt name=member_name id=member_name size=20 maxlength=32 /></td>
			<td rowspan=3 valign=top><?=$comment1 ?></td>
 		</tr> -->
		<tr height=40px>
			<td colspan=3 align=center valign=middle>
				<input type=button name=btnSubmit id=btnSubmit value='<?=_label("btnSubmit",$lang)?>' />&nbsp;
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<!-- <table width=100% border=0 cellpadding=0 cellspacing=0 id=tblLogout name=tblLogout width=100% <? if(!isset($_SESSION['userid'])) echo "style='display:none'";?>>
<tr>
	<td name=tdStation id=tdStation align=center>
		<table border=0 cellpadding=0 cellspacing=0>
		</table>
	</td>
</tr>
</table>
 -->
<table border="0" width="500px" align=center>
<tr height=30px><td colspan=3>&nbsp;</td></tr>
<tr>
	<td align=center><a href='javascript:setLang("en");'>English</a></td>
	<td align=center><a href='javascript:setLang("ko");'>Korean</a></td>
	<td align=center><a href='javascript:setLang("cn");'>Chinese</a></td>
	<td align=center><a href='javascript:setLang("vn");'>Vietnamnese</a></td>
</tr>
</table>
<div id=divBrandnew style='display:none;'>
<table style='border-collapse:collapse;' border=1>
<tr>
	<td align=right><?=_label("enterprisename",$lang)?></td>
	<td><input type=text id=partyname size=20 maxlength=64></td>
</tr>
<tr>
	<td align=right><?=_label("sector",$lang) ?></td>
	<td><select id=selSector style='width:200px;'>
		<option value=church>�뤃癒곗돳</option>
		<option value=company>疫꿸퀣毓�</option>
		<option value=eatery>占쎌뒄占쎈뻼占쎈씜</option>
		<option value=club>占쎈짗占쎌깈占쎌돳</option>
	</select>
	</td>
</tr>
</table>
</div>
<?
include_once("footer.php");
?>