<?
include_once("header.php");
$lang=$_SESSION['lang'];
?>
<table border="0" align="center">
<tr><td align="center">
<div id=tabs style="width:450px;height:240px;">
<ul>
	<li><a href=#tab1><?=_label("find_id",$lang)?></a></li>
	<li><a href=#tab2><?=_label("find_password",$lang)?></a></li>
</ul>
<div id=tab1 >
	<table class="tdeco" border="0" cellpadding="0" cellspacing="0">
	<tr height="24px">
		<td align="right"><?=_label("member_name",$lang)?>
		&nbsp;</td>
		<td>&nbsp;<input type=text name=member_name id=member_name size=32 maxlength=32 class="txt">
		</td>
	</tr>
	<tr height="24px">
		<td align="right"><?=_label("birth_date",$lang)?>
		&nbsp;</td>
		<td>&nbsp;<input type=text name=birth_date id=birth_date size=10 maxlength=10 class="txt">
		</td>
	</tr>
	<tr height="24px">
		<td align="right"><?=_label("mobile",$lang)?>
		&nbsp;</td>
		<td>&nbsp;<input type=text name=mobile id=mobile size=20 maxlength=20 class="txt">
		&nbsp;<input type=button name=btnFind1 id=btnFind1 value='<?=_label("btnFind",$lang)?>'></td>
	</tr>
	<tr height=40px valign=bottom>
		<td align="right"><?=_label("userid",$lang)?></td>
		<td>&nbsp;<input type=text class=txt name=userid id=userid maxlength=32 size=32></td>
	</tr>
	</table>
</div>
<div id=tab2>
	<table class="tdeco" border="0" cellpadding="0" cellspacing="0">
	<tr height="24px" valign=bottom>
		<td align="right"><?=_label("userid",$lang)?></td>
		<td>&nbsp;<input type=text class=txt name=userid id=userid maxlength=32 size=32></td>
	</tr>
	<tr height="24px">
		<td align="right"><?=_label("member_name",$lang)?>
		&nbsp;</td>
		<td>&nbsp;<input type=text name=member_name id=member_name size=32 maxlength=32 class="txt">
		</td>
	</tr>
	<tr height="24px">
		<td align="right"><?=_label("birth_date",$lang)?>
		&nbsp;</td>
		<td>&nbsp;<input type=text name=birth_date id=birth_date size=10 maxlength=10 class="txt">
		</td>
	</tr>
	<tr height="24px">
		<td align="right"><?=_label("mobile",$lang)?>
		&nbsp;</td>
		<td>&nbsp;<input type=text name=mobile id=mobile size=20 maxlength=20 class="txt"></td>
	</tr>
	<tr height="24px">
		<td align="right"><?=_label("remail",$lang)?>&nbsp;</td>
		<td>&nbsp;<input type=email class=txt name=email id=email size=32 maxlength=32></td>
	</tr>
	<tr height="40px">
		<td colspan=2 align="center"><input type=button name=btnFind2 id=btnFind2 value='<?=_label("btnFind",$lang)?>'>
	</tr>
	</table>
</div>
</div>
</td></tr>
<tr height="40px" valign="bottom">
<td align="center">
	<a href='/enterprise'>Go Back to Login Page</a>
</td>
</tr>
</table>

<?
include_once("footer.php");
?>