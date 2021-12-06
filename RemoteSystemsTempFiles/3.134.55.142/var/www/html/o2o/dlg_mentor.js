$(document)
.ready(function(){
console.log('dlgmentor ready');
	$('#dlgMentor').dialog({
		autoOpen:false
		,modal:true
		,resizable:false 
		,width:400
		,open:function(event,ui){
			$('#tblMentor_new').empty();
			$.post('_member.php',{optype:'search'},function(answer){
				let retval=parseInt(answer['result']);
				if(retval>100) return false;
				if(retval==-99) {
					alert(answer['msg']);
					document.location='/';
					return false;
				}
				drawMentor(answer);
			},'json');
		}
		,buttons:[
			{text:'닫기'
			,click:function(){
				$(this).dialog('close');
			}
			}
		]
	});
})
.on('keypress','#findMentor',function(e,u){
	console.log(e.keyCode);
	if(e.keyCode==13) {
		$('#btnMentor').trigger('click');	
		return false;
	}
	return true;
})
// building the mentor list
.on('click','#btnMentor',function(){
	$('#findMentor').val($.trim($('#findMentor').val()));
	console.log('Mentor ['+$('#findMentor').val()+']');
	// 양육자 후보명단을 교인목록/동반자목록에서 선택할 수 있는 옵션 필요.
	$.post('_member.php',{optype:'search',search:$('#findMentor').val()},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0){
			alert(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		if(retval>100 && $('#findMentor').val()==""){
			alert(answer['msg']);
			return false;
		}
		$('#tblMentor_new').empty();		// this can't be in drawMentor().
		drawMentor(answer);
	},'json'); 
	return false;
})
// choose a mentor on the mentor list
.on('click','#tblMentor_new tr',function(){
	let rowid=$(this).attr('mentor_id');
	let me=$(this);
	$.post('_mentor.php',{optype:'addnew',mentor:rowid},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0) {
			alert(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		$('#tblMentor').prepend("<tr mentor_id="+rowid+">"+me.html()+
			"<td>-</td><td>-</td><td>-</td><td>-</td><td style='display:none'>수정</td>"+
			"<td style='display:none'>삭제</td></tr>");
		me.remove();	
	},'json');
	return false;
})
;

function drawMentor(o){
	let n=1; 
	$.each(o['data'],function(ndx,data){
		pstr =`<tr mentor_id='${data['member_id']}'><td>${n}</td><td>${data['member_name']}</td>`+
			`<td>${data['gender']}</td><td>${data['birthday']}</td><td>${data['mobile']}</td></tr>`;
		$('#tblMentor_new').append(pstr);
		n++;
	});
}