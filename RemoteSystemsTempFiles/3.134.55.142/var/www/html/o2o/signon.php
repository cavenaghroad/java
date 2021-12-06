<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>일대일 성경공부 가입</title>
</head>
<link href="<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.css"  rel="stylesheet">
<script src='https://code.jquery.com/jquery-3.5.0.js'></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>
<body align=center style='background-color:honeydew'>
<h1>일대일 성경공부 가입</h1>
<form action='/gateway.php' method='post' id=frmLogin>
<table align=center valign=middle>
<tr>
	<td class=font10 align=right>이름</td><td><input type=text size=12 maxlength=12 id=name></td>
</tr>
<tr>
	<td class=font10 align=right>모바일번호</td><td><input type=text size=12 maxlength=12 id=mobile></td>
</tr>
<tr>
	<td class=font10 align=right>비밀번호</td><td><input type=password size=12 maxlength=12 id=passcode></td>
</tr>
<tr>
	<td class=font10 align=right>비밀번호 확인</td><td><input type=password size=12 maxlength=12 id=passcode1></td>
</tr>
<tr>
	<td class=font10 align=right>소속단체</td><td><select id=selParty style='width:150px;'><option value='0'>-</option></select></td>
</tr>
<tr>
	<td colspan=2 align=center>
		<input type=button value='가입하기' id=btnLogin>&nbsp;&nbsp;
		<input type=button value='새로 입력' id=btnReset>
	</td>
</tr>
<tr height=40px>
	<td colspan=2 style='font-size:12px' align=center><a href='./o2o/findPrivate.php'>모바일번호/로긴암호찾기</a>&nbsp;&nbsp;
		<a href='/index.php' id=doLogin>로그인</a></td>
</tr>
</table>  
</form>
</div>
</body>
</html>