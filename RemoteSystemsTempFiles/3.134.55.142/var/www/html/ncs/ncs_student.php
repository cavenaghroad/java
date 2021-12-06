<html>
<head>
<title>휴먼교육센터</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
</head>
<style>
@import url(//fonts.googleapis.com/earlyaccess/nanumgothic.css);
td.student {
    background-color:yellow;
    width:150px;height:150px;
    /* text-align:left;
    text-valign:top; */
}
span.lu {
    float: left;
    margin-left: 5px;
	margin-top: -85px;
	font-size:10px;
}
span.rd {
	flot:right;
	margin-left:100px;
	margin-top;100px;
	font-size:8px;
}
a {
	text-decoration:underline;
	cursor:pointer;
}
td.palm,a.palm {
	cursor:pointer;
}
td.submitted {
	background-color:magenta;
	color:yellow;
	font-weight:bold;
}
td.done {
	background-color:green;
	color:yellow;
	font-weight:bold;
}
td.working {
	background-color:red;
	font-weight:normal;
}
@-webkit-keyframes blink {
  50% {
    background: #cc5;
    background-size: 75px 150px;
  }
}
@-moz-keyframes blink {
  50% {
    background: #cc5;
    background-size: 75px 150px;
  }
}

.laser {
  animation: blink 2s infinite;
  -webkit-animation: blink 2s infinite;
  -moz-animation: blink 2s infinite;
}
/*tr {
    background-color:black;
    color:white;
}*/
</style>
<body>
<div align=center style='width:100%;height:100%'>
<table style='border:1px solid green;border-collapse:collapse;'>
<tr>
	<td valign=top><select id=selClass style='width:400px' size=10></select><br>
							<button id=btnLink>Link</button></td>
	<td valign=top>
		<table id=tblClass>
		<tr><td align=right>Class ID</td><td><input type='text' id=txtClassid size=10></td></tr>
		<tr><td align=right>Title</td><td><textarea id=txtTitle rows=2 cols=40></textarea></td></tr>
		<tr><td align=right>Seat Count</td><td><input type=number id=txtSeatCount value=0 min=0 style='width:40px'></td></tr>
		<tr><td align=right>Column Count</td><td><input type=number id=txtColCount value=0 min=0 style='width:40px'></td></tr>
		<tr><td align=right>Start Date</td><td><input type=date id=txtStartDate></td>
		<tr><td align=right>End Date</td><td><input type=date id=txtEndDate></td>
		<tr><td align=right>Alive</td><td><input type=checkbox id=bAlive></tr>
		<tr><td align=center colspan=2><button id=btnSave>Save</button><button id=btnDelete>Delete</button> 
			<button id=btnReset>Clear</button>
		</td></tr>
		</table>
	</td>
</tr>
<tr><td height=10px>&nbsp;</td></tr>
<tr>
	<td valign=top>
		<button id=btnLoad>Load</button><br>
		<select id=selStudent style='width:400px;' size=30></select>
	</td>
	<td valign=top>
		<table id=tblStudent>
			<tr><td align=right>이름</td><td><input type=text id=name></td></tr>
			<tr><td align=right>생년월일</td><td><input type=text id=birth></td></tr>
			<tr><td align=right>비고</td><td><input type=text id=school></td></tr>
			<tr><td align=right>모바일</td><td><input type=text id=mobile></td></tr>
			<tr><td align=right>좌석번호</td><td><input type=number id=seq></td></tr>
			<tr><td align=right>AnyDesk ID</td><td><input type=text id=tvid></td></tr>
			<tr><td align=right>&nbsp;</td><td><input type=checkbox id=cAlive>Alive</td></tr>
			<br><td colspan=2 align=center><button id=btnSave1>Save</button><button id=btnDelete1>Delete</button>
					<button id=btnReset1>Clear</button>
			<tr><td>
		</table>
	</td>
</tr>
</table>
</div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?
if($_GET['name']=="a2z4sg"){
?>
<script src="ncs_student.js"></script>
<?
}
?>
</html>