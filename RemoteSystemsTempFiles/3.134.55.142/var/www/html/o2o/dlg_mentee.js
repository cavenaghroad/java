$(document)
.ready(function(){
console.log('dlgMentee ready');
	$('#dlgMentee').dialog({
		autoOpen:false
		,modal:true
		,resizable:false 
		,open:function(event,ui){
			$('#tblMentee_new').empty();
		}
		,buttons:[
			{text:'close'
			,click:function(){
				$(this).dialog('close');
			}
			}
		]
	});
})
.on('keydown','#findMentee',function(e){
	if(e.keyCode==13) $('#btnMentee').trigger('click');
	return true;
})
.on('click','#btnMentee',function(){
	$('#findMentee').val($.trim($('#findMentee').val()));
	console.log('Mentee ['+$('#findMentor').val()+']');
	$('#tblMentee_new').empty();
	$.post('_member.php',{optype:'search',search:$('#findMentee').val()},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0)	{
			alert(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		let n=1;
		$.each(answer['data'],function(ndx,data){
			console.log(data);
			pstr =`<tr rowid='${data['rowid']}' mentee_id='${data['member_id']}'><td>${n}</td><td>${data['member_name']}</td>`+
					`<td>${data['gender']}</td><td>${data['birthday']}</td><td>${data['mobile']}</td></tr>`;
			$('#tblMentee_new').append(pstr);
			n++;
		});
	},'json');
	return false;
})
.on('click','#tblMentee_new tr',function(){
	let me=$(this);
	let mentee=me.attr('mentee_id');
	let rowid=me.attr('rowid');
	$.post('_mentee.php',{optype:'addnew',mentee:mentee,mentor:oBody.tblMentor.key.mentor_id},function(answer){
		$('#tblMentee').prepend(`<tr rowid=${rowid} mentee_id=${mentee}>${me.html()}<td>-</td><td>-</td><td>-</td><td style='display:none;'>삭제</td></tr>`);
		me.remove();
	},'json');
	return false;
})