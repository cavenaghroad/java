<?
session_start();
if(!isset($_SESSION['member_id'])){
    echo "<script>document.location='/index.php';</script>";
    exit;
}
if(isset($_GET['level'])) {
    $_SESSION['level']=$_GET['level'];
}
if(isset($_GET['party'])) {
    $_SESSION['party']=$_GET['party'];
}
include_once '../common/_init_.php';
?>
<html>
<head>
	<title>일대일 성경공부</title>
</head>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300&display=swap" rel="stylesheet">
<link href="base.css" rel="stylesheet">
<script src='https://code.jquery.com/jquery-3.5.0.js'></script>
<script src='common.js'></script>
<script src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>
<body>
<div id=header style='position:relative;'>
    <table style='width:100%;height:100%;'>
    <tr>
        <td align=center colspan=3>
        <h1>일대일 성경공부</h1><b style='color:yellow'>동반자반 지원</b>
 	   	</td></tr>
    <tr>
    	<td>
<? include_once("admin_menu.php"); ?>    	
    	</td>
    </tr>
</table>
</div>
<input type=hidden id=member_id value=<?=$_SESSION['member_id']?>>
<div id=content align=center style='width:100%;height:100%;'>
<table>
<tr>
	<td valign=top>
		<div style='overflow:auto;height:700px;width:500px;'>
    		<table class=lines>
    		<caption>동반자반 지원명단</caption>
    		<thead>
    			<tr>
    				<th class=w30>&nbsp;</th><th class=w100>이름</th><th class=w30>성별</th>
    				<th class=w80>생년월일</th><th class=w80>모바일번호</th><th class=w80>신청일자</th>
    			</tr>
    		</thead>
    		<tbody id=tblMentee></tbody>
    		</table>
		</div>
	</td>
	<td style='width:10px;'>&nbsp;</td>
	<td valign=top>
		<div style='overflow:auto;height:700px;width:500px;'>
    		<table class=lines>
    		<caption>등록교인 명단</caption>
    		<thead>
    			<tr>
    				<th class=w30>&nbsp;</th><th class=w100>이름</th><th class=w30>성별</th>
    				<th class=w80>생년월일</th><th class=w80>모바일번호</th>
    			</tr>
    		</thead>
    		<tbody id=tblMember></tbody>
    		</table>
    	</div>
	</td>
</tr>
</table>
</div>
</body>
</html>