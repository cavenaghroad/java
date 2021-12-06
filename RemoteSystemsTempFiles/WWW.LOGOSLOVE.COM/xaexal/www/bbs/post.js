$(document)
.on('click','#btnModify',function(e,u){ 
	document.location='post.php?_type='+$('#_type').val()+'&optype=modify&rowid='+$('#rowid').val();
	return false;
})
.on('change','#pic1',function(e){
	e.preventDefault();
	
	var file_data = $('#pic1')[0].files[0];  
	if(file_data.size>3000000){
		alert('The image file that is more than 3MB cannot be uploaded.');
		return false;
	}
    var form_data = new FormData();                  
    form_data.append('pic1', file_data);
    $.ajax({
        url: 'upload.php', // point to server-side PHP script 
        dataType: 'json',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,     
        mimeType: 'multipart/form-data',
        contentType:false,
        type: 'post',
        success: function(json){
            if(json['msg']!=''){
            	alert(json['msg']); return false;
            }
            var h1=120;
            var w=parseInt(json['width']); 
            var h=parseInt(json['height']);
        	w=h1*w/h;
        	var w_total;
        	w_total+=w;
        	$('#imglist').append('<div id=dv'+json['file_id']+' align=left><input type=button id=del'+json['file_id']+' value=삭제><br><img src="./picture/'+json['file_id']+'" id="'+json['file_id']+'" style="height:'+h1+'px;width:'+w+'px;" hspace=5></div>');
        }
     });
})
.on('click','#btnReplyAdd',function(e,u){
	$('#txtReply').val($.trim($('#txtReply').val()));
	if($('#txtReply').val()==''){
		alert('댓글을 입력하십시오.');
		return false;
	}
	var rowid='', created='';
	$.post('_post.php',{
		optype:'add-reply',par_rowid:$('#rowid').val(),content:$('#txtReply').val(),_type:$('#_type').val()
	},function(json){
		if(json['msg']!='') {
			alert(json['msg']); return false;
		}
		rowid=json['rowid'];
		created=json['created'];
		$('#tblReplies').append('<tr><td class=td1>&nbsp;</td><td class=td2><table width=100%><tr><td valign=top><br>'+created+
				'<br><input type=button id='+created+' class=delReply value=\'삭제\'></td><td valign=top class=td2>'+$('#txtReply').val()+'<td></tr></table></td></tr>');
		$('#txtReply').val('');
	},'json')
	.always(function(){
		if(rowid=='') return false;
			
	});

})
.on('click','.delReply',function(e,u){
	if(!confirm('댓글을 지울까요?'))	return false;
	var rowid=this.id;
	var me=$(this);
	$.post('_post.php',{
		optype:'del-reply',rowid:rowid,_type:$('#_type').val()
	},function(json){
		if(json['retval']=='0'){
			me.closest('table').closest('tr').remove();
		}
		alert(json['msg']);
	})
})
.on('click','input[type=button][id^=del]',function(e,u){
	$.post('_post.php',{
		optype:'del-image',fileid:this.id
	},function(json){
		if(json['msg']!=''){
			alert(json['msg']); return false;
		}
		var pstr=this.id;
		pstr=pstr.substr(3);
		$('div[id=dv'+pstr+']').remove();
	},'json');
})
.on('click','#btnLike',function(e,u){
	$.post('_post.php',{
		optype:'like',like:'yes',rowid:$('#rowid').val()
	},function(json){
		if(json['msg']!=''){
			alert(json['msg']); return false;
		}
	});
})
.on('click','#btnHate',function(e,u){
	$.post('_post.php',{
		optype:'like',like:'no',rowid:$('#rowid').val()
	},function(json){
		if(json['msg'])=='+1') {
		} else if(json['msg']=='-1'){
			
		} else if(json['msg']!=''){
			alert(json['msg']); return false;				
		}
	});
})
;

function generalFields(){
	$('#title').val($.trim($('#title').val()));
	$('#content').val($.trim($('#content').val()));
	if($('#title').val()==''){
		alert('[제목]을 입력하십시오.');
		return false;
	}
	if($('#content').val()==''){
		alert('[내용]을 입력하십시오.');
		return false;
	}
	var result=true;
	var filename='';
	$('#imglist0 input[type=checkbox]').each(function(ndx,val){
		if(!$(this).is(':checked')) return true;
		if($.trim(this.id)=='') return true;
		if(filename!='') filename+=',';
		filename+=$.trim(this.id);
	});
		alert(filename);
	if(filename=='') return true;	
	$.post('_post.php',{
		optype:'del-image',fileid:filename
	},function(json){
		if(json['msg']!=''){
			alert(json['msg']);
		}
	});
	return result;
}

function delFunction(par_rowid){
	alert('par_rowid ['+par_rowid+']');
	$.post('_housing.php',{
		optype:'delete',rowid:par_rowid
	},function(json){
		if(json['msg']!=''){
			alert(json['msg']); return false;
		}
		alert('삭제되었습니다.');
	});
}