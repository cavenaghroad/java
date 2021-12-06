let glov=1;

$(document)
.ready(function(){
	console.log('ready');
//	$.post(url_ctl_drill,{optype:'getlist'},function(answer){
//		console.log(answer);
//		if(parseInt(answer['result'])<0){
//			alert(answer['msg']);
//			return false;
//		}
//		$('#selClass').empty();
//		$.each(answer['data'],function(ndx,rec){
//			$('#selClass').append("<option value='"+rec['classcode']+"'>"+rec['title']+"</option>");
//		});
//	},'json');	
})
.on('click','#selClass option',function(){
	$('#classid').val($(this).val());
	let oParam={optype:'selectone',rowid:$('#classid').val()};
	
	console.log(oParam);
	$.post(url_ctl_class,oParam,function(result){
		console.log(result);
		if(parseInt(result['result'])<0) return false;
		let data=result['rec'][0];
		if($('#selStudent option').length>0) $('#selStudent').empty();
		$('#classcode').val(data['classcode']);
		$('#title').val(data['title']);
		if(data['period1'].length==8){
			data['period1']=data['period1'].substr(0,4)+'-'+data['period1'].substr(4,2)+'-'+data['period1'].substr(6);
		}
		if(data['period2'].length==8){
			data['period2']=data['period2'].substr(0,4)+'-'+data['period2'].substr(4,2)+'-'+data['period2'].substr(6);
		}
		$('#period1').val(data['period1']);
		$('#period2').val(data['period2']);
		$('#seat_cnt').val(data['seat_cnt']);
		$('#col_cnt').val(data['col_cnt']);
		$('#alive').attr('checked',false);
		if(data['alive']=='1'){
			$('#alive').attr('checked',true);
		} 
	},'json');
	return false;
})
.on('click','#btnSaveClass',function(){
	let oParam={optype:'add'};
	if($('#classid').val()!='') {
		oParam['optype']='update';
		oParam['rowid']=$('#classid').val();
	}	
	$('#classcode').val($.trim($('#classcode').val()));
	let str=$('#classcode').val();
	if(str.length<1){
		alert("CLASSID is necessary."); return false;
	}
	oParam['classcode']=str;
	$('#title').val($.trim($('#title').val()));
	str=$('#title').val();
	if(str.length<1){
		alert("Title is necessary."); return false;
	}
	oParam['title']=str;
	if($('#period1').val()>=$('#period2').val()){
		alert('StartDate should be earlier than EndDate'); return false;
	}
	oParam['period1']=$('#period1').val();
	oParam['period2']=$('#period2').val();
	if(parseInt($('#seat_cnt').val())<1){
		alert('Seat Count can not be zero.'); return false;
	}
	oParam['seat_cnt']=$('#seat_cnt').val();
	if(parseInt($('#col_cnt').val())<1){
		alert('Column Count can not be zero.'); return false;
	}
	oParam['col_cnt']=$('#col_cnt').val();
	if(parseInt(oParam['seat_cnt'])%parseInt(oParam['col_cnt'])!=0){
		alert('좌석수는 열숫자의 배수여야 합니다.'); return false;
	}
	let bAlive='0';
	if($('#alive').is(':checked')){
		bAlive='1';
	}
	oParam['alive']=bAlive;
	console.log(oParam);
	$.post(url_ctl_class,oParam,function(result){
		console.log(result);
		if(parseInt(result['result'])<0) {
			alert(result['msg']);
		}
	},'json');
	return false;
})
.on('click','#btnDeleteClass',function(){
	if($('#classid').val()=='') return false;
	if(!confirm('삭제할까요?')) return false;
	let oParam={optype:'delete',rowid:$('#classid').val()};
	$.post(url_ctl_class,oParam,function(result){
		if(parseInt(result['result'])<0) {
			alert(result['msg']); return false;
		}
		$('#selClass option[value='+$('#classid').val()+']').remove();
		$('#btnResetClass').trigger('click');
	},'json');
	return false;
})
.on('click','#btnResetClass',function(){
	$('#classcode,#classid,#title,#seat_cnt,#col_cnt,#period1,#period2').val('');
	$('#alive').prop('checked',false);
	return false;
})
.on('click','#btnLink',function(){
	if($('#selClass option:selected').val()!='') {
		document.location="http://3.134.55.142:5000/ncs/xaexal/"+$('#classcode').val();
	}
	return false;
})
.on('click','#btnLoad',function(){
	console.log('btnLoad');
	if($('#classcode').val()=='') {
		alert('class should be selected.');
		return false;
	}
	let oParam={optype:'selectall',classcode:$('#classcode').val()};
	$.post(url_ctl_student,oParam,function(result){
		console.log(result);
		$('#selStudent').empty();
		$.each(result['rec'],function(ndx,rec){
			let str=`<option value="${rec['rowid']}">${rec['name']},\t${rec['birth']},\t${rec['mobile']},\t${rec['seq']},\t${rec['tvid']},\t${rec['school']},\t${rec['address']},\t${rec['alive']},\t${rec['active']}</option>`;
			$('#selStudent').append(str);
		});
	},'json');
	return false;
})
.on('click','#selStudent option',function(){
	if($(this).val()=='') return false;
	$('#studentid').val($(this).val());
    pstr=$(this).text();
	pstr=pstr.replace(/\t/g,'');
	pstr=pstr.split(',');
	$('#name').val(pstr[0]);
	$('#birth').val(pstr[1]);
	$('#mobile').val(pstr[2]);
	$('#seq').val(pstr[3]);
	$('#tvid').val(pstr[4]);
	$('#school').val(pstr[5]);
	if(pstr[7]=='1') $('#cAlive').attr('checked',true);
	else $('#cAlive').attr('checked',false);
	return false;
})
.on('click','#btnSaveStudent',function(){
	if($('#classcode').val()=='') { 
		alert('class should be selected first.');
		return false;
	}
	$('#name').val($.trim($('#name').val()));
	if($('#name').val()==''){
		alert('name should be given.'); return false;
	}
	let oParam={name:$('#name').val(),classcode:$('#classcode').val()};
	$('#school').val($.trim($('#school').val()));
	oParam['school']=$('#school').val();
	$('#mobile').val($.trim($('#mobile').val()));
	oParam['mobile']=$('#mobile').val();
	$('#anydesk').val($.trim($('#anydesk').val()));
	oParam['tvid']=$('#tvid').val();
	oParam['alive']=$('#cAlive').is(':checked')?'1':'0';
	oParam['birth']=$('#birth').val();
	oParam['seq']=$('#seq').val();
	if($('#studentid').val()!='') {
		oParam['optype']='update';
		oParam['rowid']=$('#studentid').val();	
	} else {
		oParam['optype']='add';
	}
	console.log(oParam);
	$.post(url_ctl_student,oParam,function(result){
		console.log(result);
		if(result['result']!='0') {
			alert(result['msg']); return false;
		}
		$('#btnLoad').trigger('click');
		$('#btnResetStudent').trigger('click');
	},'json');
	return false;
})
.on('click','#btnDeleteStudent',function(){
	if(!confirm('정말로 지울까요?')) return false;
	let oParam={optype:'delete',rowid:$('#studentid').val()};
	console.log(oParam);
	$.post(url_ctl_student,oParam,function(result){
		console.log(result);
		if(result['result']=='-1') {
			alert('삭제 실패'); return false;
		}
		$('#btnLoad').trigger('click');
		$('#btnResetStudent').trigger('click');
	},'json');
	return false;
})
.on('click','#btnResetStudent',function(){
	$('#studentid,#name,#birth,#mobile,#seq,#tvid,#school').val('');
	$('#cAlive').attr('checked',false);
})
;