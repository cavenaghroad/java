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
</style>
<body>
<table >
<tr>
	<td colspan=2 align=center>
<?
include_once '../common/_init_.php';
$sql="select * from ncs_config";
$rs=$mysqli->query($sql);
if($mysqli->error) {
	$mysqli->close();
	die("Connection failed to DB");
}
$row=$rs->fetch_assoc();
$mysqli->close();	
$today=date('Ymd');
$days=intval($row['days']);

$year=intval(substr($row['period1'],0,4));
$month=intval(substr($row['period1'],4,2));
$date=intval(substr($row['period1'],6,2));
$dow=date('w',mktime(0,0,0,$month,$date,$year));
$begin=date('Ymd',mktime(0,0,0,$month,$date,$year));

// echo $finish;
while($begin<$today){
	if($dow==0 || $dow==6) {}
	else {
		$days--;
	}
	$date++;
	$dow=date('w',mktime(0,0,0,$month,$date,$year));
	$begin=date('Ymd',mktime(0,0,0,$month,$date,$year));
}
echo "<h1>{$row['title']}&nbsp;({$row['period1']}~{$row['period2']},&nbsp;{$days}/{$row['days']})</h1>";
?>	
	</td>	
</tr>
<tr><td valign=top>
	<table width=100%>
	<tr>
		<td colspan=2><a href='ncs_student.php'>학생명단관리</a></td>
	</tr>
	<tr><td colspan=2>
		<select id=selDrill style='width:200px' size=24></select>
	</td></tr>
	<tr><td colspan=2>
		<label>과제명</label><br>
		<input type=text id=txtDrill size=24><br>
		<label><sub>과제명에는 반드시 시작일자를 적을것</sub></label>
	</td></tr>	
	<tr>
	<td><input type=button id=btnAdd value='Add'></td><td align=right>
	<input type=button id=btnDel value='Delete'></td>
	</tr>
	</table>
</td>
<td valign=top>
	<table align=center>
<?
	$seat_cnt=intval($row['seat_cnt']);
	$col_cnt=intval($row['col_cnt']);
	$pstr="";
	if( $seat_cnt % $col_cnt==0){
		for($i=1;$i<=$seat_cnt;$i++){
			if($i%$col_cnt==1) $pstr="</tr>".$pstr;
			$pstr="<td class=student valign=top><table width=100% height=100% id=s".$i."></table></td>".$pstr;
			if($i%$col_cnt==0) $pstr="<tr>".$pstr;
			else $pstr="<td width=10px>&nbsp;</td>".$pstr;
		}	
		echo $pstr;
	} else {
		die("배수가 아닙니다 [좌석수:".$row['seat_cnt'].", 열수:".$row['col_cnt']."]");
	}
?>		
<!--	<tr><td colspan=<?echo $row['col_cnt'] ?> valign=top>
		<select id=selMsg style="width:760px;" size=7></select>&nbsp;&nbsp;
 		<button id=btnRmMsg>지우기</button></td></tr> -->
	</table>
</td></tr></table><br>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?
if($_GET['name']=="a2z4sg"){
?>
	<script src='ncs_sv.js'></script>
<?	
}
?>
<div id=p_info style='display:none;position:absolute;background-color:aquamarine;border:1px solid cyan;font-size:10px;'></div>
</body>
</html>
