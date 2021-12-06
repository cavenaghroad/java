<?
session_start();
if(!isset($_SESSION['member_id'])){
    echo "<script>document.location='/index.php';</script>";
    exit;
}
include_once '../common/_init_.php';
?>
<html>
<head>
	<title>관리화면</title>
</head>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300&display=swap" rel="stylesheet">
<link href="base.css" rel="stylesheet">
<script src='https://code.jquery.com/jquery-3.5.0.js'></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src='common.js'></script>
<script src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>
<body align=center>
<div id=header>
	<table style='width:100%;height:100%;'>
    <tr>
        <td align=center>
        <h1>일대일 성경공부 관리</h1><b style='color:yellow'>양육자/동반자 관리</b>
 	   	</td></tr>
    <tr>
    	<td>
<? include_once("admin_menu.php"); ?>    	
    	</td>
    </tr>
</table>
</div>
<div id=content align=center valign=top style='width:100%;height:100%;'>
<table style='width:720px'>
<tr>
	<td>
        <table>
        <tr>
            <td colspan=2>
        		<table style='border:1px solid green;width:100%'>
        		<tr>
        			<td id=filter style='font-size:14px'>
                		<input type=radio name=filters id=all checked>전체보기&nbsp;
                		<input type=radio name=filters id=today>오늘 새가족반 가입&nbsp;
                		<input type=radio name=filters id=latest>최근 새가족반 가입&nbsp;
        			</td>
        		</tr>
        		</table>
        	</td>
        </tr>
        <tr>
        	<td>
                <input type=hidden id=member_id values=<?=$_SESSION['member_id']?>>
                <button id='btnAddNewbie'>새가족 추가</button>
        	</td>
        	<td align=right>
        		<label id=NumMember></label>
        	</td>
        </tr>
        <tr>
        	<td valign=top colspan=2>
        		<div style='overflow:auto;height:700px;width:720px;'>
            		<table class=lines>
            		<caption>새가족반 명단</caption>
                    <thead>
                    	<tr><th>&nbsp;</th><th>이름</th><th>성별</th><th>생년월일</th>
                    	<th>모바일번호</th><th>동반자반 지원</th><th>교회등록일</th>
                    	<th>교회탈퇴일</th></tr>
                    </thead>
                    <tbody id=tblSaint>
                    </tbody>
        			</table>
        		</div>
        	</td>
        </tr>
        </table>
	</td>
</tr>
</table>       
</div>
</body>
</html>