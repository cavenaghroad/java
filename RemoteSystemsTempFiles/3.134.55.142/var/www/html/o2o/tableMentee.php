<table>
<tr>
	<td>
		<button id='btnAddMentee'>동반자 추가</button>
	</td>
	<td align=right>
		<input type=radio name=rMentee id=rAll selected style='font-size:12px;'>전체&nbsp;
        <input type=radio name=rMentee id=rGraduated style='font-size:12px;'>수료자&nbsp;
        <input type=radio name=rMentee id=rInProgress style='font-size:12px;'>과정중&nbsp;
	</td>
</tr>
<tr>
	<td colspan=2>
        <table class=lines >
        <thead><tr>
        	<th rowspan=2>&nbsp;</th><th rowspan=2>이름</th><th rowspan=2>성별</th><th rowspan=2>생년월일</th>
        	<th rowspan=2>모바일번호</th><th colspan=3>동반자반</th><th rowspan=2 style='display:none;'>&nbsp;</th>
        	<th rowspan=2 style='display:none;'>&nbsp;</th></tr>
        <tr><th class='w100px'>시작일</th><th class='w100px'>수료일</th><th>수료여부</th></tr></thead>
        <tbody id=tblMentee>
        </tbody>
        </table>
	</td>
</tr>
</table>