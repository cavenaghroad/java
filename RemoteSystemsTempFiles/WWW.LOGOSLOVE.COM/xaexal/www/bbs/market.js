$(document)
.ready(function(){
})
.on('click','#btnWrite',function(e,u){
	var filelist='';
	var imgid=$('#imglist img');
	imgid.each(function(ndx){
		if(filelist!='') filelist+=',';
		filelist+=$(this).attr('id');
	});
	var result=generalFields();
	if(!result) return false;
	$.post('_market.php',{
		_type:$('#_type').val(),optype:$('#optype').val(),title:$('#title').val(),content:$('#content').val(),rowid:$('#rowid').val(),img:filelist	// <= Add the image file information.
	},function(json){
		if(json['msg']!=''){
			result=alert(json['msg']); 
			return false;
		}
		alert('게시물이 등록됐습니다.1');
		document.location='post.php?_type='+$('#_type').val()+'&rowid='+json['rowid'];
	},'json');
	return false;
})
.on('click','#btnDelete',function(e,u){
	if(!confirm('정말로 삭제하시겠습니까?')) return false;
	$.post('_market.php',{
		optype:'delete',rowid:$('#rowid').val()
	},function(json){
		if(json['msg']!=''){
			alert(json['msg']); return false;
		}
		alert('삭제되었습니다.');
	},'json');
	return false;
})
;
