<input type=hidden id=rowid value='<?=$_GET['rowid']?>'>
<table style='width:100%;' id=tblList>
<thead>
	<tr style='height:24px;'>
		<th style='width:30px;'>번호</th><th style='width:60px;'>시각</th><th style='width:180px;'>제목</th><th style='width:100px;'>작성자</th><th>조회수</th><th>추천</th>
	</tr>
</thead>
<tbody id=tblList></tbody>
</table>
<style>
#tblList tbody tr {
    cursor:pointer;
}
.clicked {
    background-color:yellow;
}
</style>