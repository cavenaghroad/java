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
        <h1>일대일 성경공부</h1><b style='color:yellow'>나의 정보</b>
 	   	</td></tr>
    <tr>
    	<td id=adminpage style='color:white' width=40% valign=bottom></td>
    	<td width=20%>&nbsp;</td>
    	<td align=right width=40% valign=bottom><a href='javascript:doLogout()'>로그아웃</a></td>
    </tr>
</table>
</div>
<input type=hidden id=member_id value=<?=$_SESSION['member_id']?>>
<div id=content align=center style='width:100%;height:100%;'>
<table class='noborder' width=100%>
<tr>
	<td valign=top width=50% align=right>
		<table>
    	<tr>
        	<td valign=top>
        		<table class=lines id=tblPrivate>
        		<caption>개인정보</caption>
				<tr><td align=right>이름</td><td><input type=text id=member_name size=20 maxlength=50></td></tr>
				<tr><td align=right>모바일번호</td><td><input type=text id=mobile size=13 maxlength=20></td></tr>
				<tr><td align=right>로긴아이디</td><td><input type=text id=userid size=12 maxlength=30></td></tr>
				<tr><td align=right>비밀번호</td><td><input type=password id=passcode size=12 maxlength=12></td></tr>
				<tr><td align=right>비밀번호 확인</td><td><input type=password id=passcode1 size=12 maxlength=12></td></tr>
				<tr><td colspan=2></td></tr>
				<tr id=private><td align=right>성별</td><td><input type=radio name=gender value=m>남성&nbsp;<input type=radio name=gender value=f>여성</td></tr>
				<tr id=private><td align=right>생년월일</td><td><input type=text id=birthday size=10 maxlength=10></td></tr>
				<tr id=private><td align=right>결혼여부</td><td><select id=marriage></select></td></tr>
				<tr id=private><td align=right>국적</td><td><select id=nationality></select></td></tr>
				<tr id=private><td align=right>직업</td><td><select id=job></select></td></tr>
				<tr id=private><td align=right>회사명</td><td><input type=text id=company size=20 maxlength=32></td></tr>
				<tr id=private><td align=right>기업규모</td><td><select id=scale></select></td></tr>
				<tr id=private><td align=right>직위</td><td><select id=position></select></td></tr>
				<tr id=private><td align=right>근무지</td><td><select id=workplace></select></td></tr>
				<tr id=private><td colspan=2></td></tr>
				<tr id=private><td align=right>자택주소</td><td><textarea id=home_addr cols=32 rows=2></textarea></td></tr>
				<tr id=private><td align=right>세례년월</td><td><input type=text id=bapt_dt size=8 maxlength=10></td></tr>
				<tr id=private><td align=right>학력</td><td><select id=school></select></td></tr>
                </table>
        	</td>
		</tr>
		</table>
	</td>
	<td valign=top width=50%>
		<table>
		<tr>
			<td valign=top>
				<table class=lines>
				<caption>나의 현재 상태</caption>
				<tr>
					<td>현재까지 양육한 동반자:&nbsp;<label id=graduated>12명</label>
				</tr>
				<tr>
					<td>현재 양육중인 동반자:&nbsp;<label id=howmany>2명 (김삼순/정려원)</label></td>
				</tr>
				<tr>
					<td>
						<input type=radio name=mystatus id=wait>새로운(추가적인) 동반자를 기다립니다.<br>
						<input type=radio name=mystatus id=stop>동반자 배정을 잠시 쉬겠습니다.
							[최근 동반자 배정일:<label id=last_assign>YYYY-MM-DD (N일전)</label>]
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign=top>
                <table class=lines>
                <caption>나의 양육자</caption>
                <thead>
                	<tr>
                    	<th class=w100 >양육자명</th><th class=w30>수료여부</th><th class=w80>시작일</th><th class=w80>수료일</th>
                	</tr>
            	</thead>
        		<tbody id=tblMentor>
                </tbody>
                </table>
        	</td>
    	</tr>
    	<tr height=10px></tr>
    	<tr>
    		<td valign=top>
    			<table class=lines>
    			<caption>나의 양육자<b>반</b></caption>
    			<thead>
    				<tr>
    					<th class=w100>담당교역자</th><th class=w30>수료여부</th><th class=w80>시작일</th><th class=w80>수료일</th>
    				</tr>
    			</thead>
    			<tbody id=tblClass>
    			</tbody>
    			</table>
    		</td>
    	</tr>
		<tr>
            <td valign=top>
        		<table class=lines>
        		<caption>나의 동반자</caption>
        		<thead>
        			<tr>
        				<th class=w30>&nbsp;</th><th class=w100>이름</th>
        				<th class=w30>성별</th><th class=w80>생년월일</th>
        				<th class=w80>모바일번호</th>
        				<th class=w30>수료여부</th><th class=w80>시작일</th><th class=w80>수료일</th>
        			</tr>
        		</thead>
        		<tbody id=tblMentee></tbody>
        		</table>
        	</td>
    	</tr>
	   	<tr height=10px></tr>
    	<tr>
        	<td valign=top>
        		<table class=lines>
        		<caption>양육 일정</caption>
        		<thead>
        			<tr>
        				<th class=w80>시각</th>
        				<th class=w100>장소</th>
        				<th class=w100>진도</th>
        			</tr>
    			</thead>
        		<tbody id=tblHistory></tbody>
        		</table>
    		</td>
    	</tr>
		</table>
	</td>
</tr>
</table>
</div>
</body>
</html>
