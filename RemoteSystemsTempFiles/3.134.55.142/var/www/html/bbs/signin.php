<?
$tblSiginin="";
$tblInfo="";
// echo "[".$_SESSION['userid']."]<br>";
if(isset($_SESSION['userid'])){
    $tblSignin="style='display:none;'";
} else {
    $tblInfo="style='display:none;'";
}
// echo "tblInfo [{$tblInfo}]<br>tblSiginin [{$tblSignin}]<br>";
?>
<table id=tblInfo <?=$tblInfo?> style='border:1px solid green;border-collapse:collapse;'>
<tr style='height:24px;'><td id=nickname style='border:1px solid green;'><?=$_SESSION['nickname']?></td></tr>
<tr style='height:24px;'><td id=last_logout style='border:1px solid green;'><?=$_SESSION['last_logout']?></td></tr>
<tr style='height:24px;'><td id=howmanymemo style='border:1px solid green;'><?=$_SESSION['region']?></td></tr>
<tr style='height:24px;'><td id=tdLogout style='border:1px solid green;'>로그아웃</td></tr>
</table>
<table id=tblSignin <?=$tblSignin?> style='border:1px solid green;border-collapse:collapse;'>
<tr style='height:24px;'>
	<td align=right style='border:1px solid green;'>아이디</td><td style='border:1px solid green;'><input type=text size=12 maxlength=12 id=userid></td>
</tr>
<tr style='height:24px;'>
	<td align=right style='border:1px solid green;'>비밀번호</td><td style='border:1px solid green;'><input type=password size=12 maxlength=12 id=passcode></td>
</tr>
<tr style='height:24px;'>
	<td align=right style='border:1px solid green;'><button id=btnSignin>로그인</button></td><td style='border:1px solid green;'><button id=btnCancel>취소</button></td>
</tr>
</table>
<script src='/bbs/signin.js'></script>
