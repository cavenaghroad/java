<div id=dlgMentor style='display: none;background-color:aquamarine;'>
<table style='border:1px solid blue;width:400px;'>
<tr>
	<td><input type=text size=20 id='findMentor'>&nbsp;<input type=button value='찾기' id='btnMentor'></td>
</tr>
<tr>
	<td>
		<div style='overflow:auto;height:300px;width:400px;'>
		<table class=lines><caption>교인 명단</caption>
		<thead>
			<tr style='background-color:black;color:white;'>
				<th>&nbsp;</th><th class=w100px>이름</th><th>성별</th><th class=w100px>생년월일</th><th class=w100px>모바일번호</th>
			</tr>
		</thead>
		<tbody id=tblMentor_new style='cursor:hand'></tbody>
		</table>
		</div>
	</td>
</tr>
</table>
</div>
<script src='dlg_mentor.js'></script>
