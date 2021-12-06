$(document)
.on('click','#tblList tr:gt(0)',function(e,u){
	document.location='post.php?_type='+$('#_type').val()+'&rowid='+this.id;
	return false;
})
.on('click','#btnDelPost',function(e,u){
//	if(!confirm('do you want to remove this post really ?')) return false;
	alert('click');
//	var btn=$(this);
//	$.post('_post.php',{
//		optype:'del-post',rowid:btn.closest('tr').attr('id')
//	},function(json){
//		if(json['msg']!=''){
//			alert(json['msg']); return false;
//		}
//		btn.closest('tr').remove();
//		alert('Removed it.');
//	},'json');
	return false;
})
.on('click','#btnModify',function(e,u){
	document.location="post.php?optype=modify&rowid="+$(this).closest('tr').attr('id');
	return false;
})
;