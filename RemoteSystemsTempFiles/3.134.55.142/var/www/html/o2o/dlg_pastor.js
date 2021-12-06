$(document)
.ready(function(){
console.log('ready');
	$('#dlgPastor').dialog({
		autoOpen:false
		,modal:true
		,resizable:false 
		,open:function(event,ui){
			$('#tblPastor_new').empty();
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
.on('keypress','#findPastor',function(e,u){
	console.log(e.keyCode);
	if(e.keyCode==13) {
		$('#btnPastor').trigger('click');	
		return false;
	}
	return true;
})
// building the pastor list on the dialog box.
.on('click','#btnPastor',function(){
	$('#findPastor').val($.trim($('#findPastor').val()));
	console.log('Pastor ['+$('#findPastor').val()+']');
	// 양육자 후보명단을 교인목록/동반자목록에서 선택할 수 있는 옵션 필요.
	$.post('_member.php',{optype:'pastor',pastor:$('#findPastor').val()},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0){
			alert(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		$('#tblPastor_new').empty();
		let n=1; 
		$.each(answer['data'],function(ndx,data){
			pstr =`<tr pastor_id='${data['pastor_id']}'><td>${n}</td><td>${data['pastor_name']}</td>`+
				`<td>${data['gender']}</td><td>${data['birthday']}</td><td>${data['mobile']}</td></tr>`;
			$('#tblPastor_new').append(pstr);
			n++;
		});
	},'json'); 
	return false;
})
// choose a pastor on the pastor list
.on('click','#tblPastor_new tr',function(){
	console.log('tblPastor_new tr clicked');
	let rowid=$(this).attr('member_id');
	let me=$(this);
	
	console.log(`pastorname [${me.find('td:eq(1)').text()}]`);
	console.log(`pastor_id [${me.attr('pastor_id')}]`);
	$('#pastor').val(me.find('td:eq(1)').text());		// pastor's name'
	$('#pastor').parent().parent().find('td:eq(8)').attr('pastor_id',me.attr('pastor_id'));
	console.log('parent ['+$('#pastor').parent().parent().html()+']');
	$('#dlgPastor').dialog('close');
	
	return false;
})
.on('click','#btnDelPastor',function(){
	console.log('btnDelPastor');
	$('#pastor').val('');
	$('#pastor').parent().parent().find('td:eq(8)').attr('pastor_id','');
	return false;
})