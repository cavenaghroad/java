<div id=dvAuthor style='display:none;'>
<input type=hidden id=txtUserid>
<table>
<tr class=trbd>
	<td id=viewFullList >작성글 전체 보기 </td>
</tr>
<tr class=trbd>
	<td id=writeMemo>쪽지보내기</td>
</tr>
</table>
</div>
<script language='javascript'>
$(document)
.on('click','#viewFullList',function(e,u){
	document.location='list.php?author='+$('#txtUserid').val();
})
.on('click','#writeMemo',function(e,u){
	document.location='listmemo.php?user='+$('#txtUserid').val();
});
</script>