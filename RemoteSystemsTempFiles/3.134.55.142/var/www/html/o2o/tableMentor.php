<table style='width:720px'>
<tr>
	<td colspan=2>
		<table style='border:1px solid green;width:100%'>
		<tr>
			<td id=filter style='font-size:14px'>
        		<input type=radio name=filters id=feed>양육중&nbsp;
        		<input type=radio name=filters id=wait>동반자 기다림&nbsp;
        		<input type=radio name=filters id=stop>휴식중&nbsp;
        		<input type=radio name=filters id=inclass>양육자반 진행중&nbsp;
        		<input type=radio name=filters id=graduated>양육자반 수료&nbsp;
        		<input type=radio name=filters id=all checked>전체보기&nbsp;
			</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>
        <input type=hidden id=member_id values=<?=$_SESSION['member_id']?>>
        <button id='btnAddMentor'>양육자 추가</button>
	</td>
	<td align=right>
		<label id=NumMentor></label>
	</td>
</tr>
<tr>
	<td colspan=2>
        <table class=lines>
        <thead>
        	<tr>
            	<th rowspan=2 class=w30>&nbsp;</th>
            	<th rowspan=2  class=w100>이름</th>
            	<th rowspan=2 class=w30>성별</th>
            	<th rowspan=2 class=w80>생년월일</th>
            	<th rowspan=2 class=w80>모바일번호</th>
            	<th colspan=4>양육자반</th>
            	<th rowspan=2 style='display:none;'>&nbsp;</th>
            	<th rowspan=2  style='display:none;'>&nbsp;</th>
        	</tr>
            <tr>
            	<th class=w80>시작일</th>
            	<th class=w80>수료일</th>
            	<th class=w30>수료여부</th>
        		<th class=w100>담당목사</th>
        	</tr>
    	</thead>
        <tbody id=tblMentor></tbody>
        </table>
	</td>
</tr>
</table>