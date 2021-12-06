let glov=1;

$(document)
.ready(function(){
	console.log('ready');
	$.post('_ncs_student.php',{optype:'init'},function(answer){
		console.log(answer);
		if(parseInt(answer['result'])<0){
			alert(answer['msg']);
			return false;
		}
		$('#selClass').empty();
		$.each(answer['data'],function(ndx,rec){
			$('#selClass').append("<option value='"+rec['classcode']+"'>"+rec['title']+"</option>");
		});
	},'json');	
})
.on('click','#selClass option',function(){
	let oParam={optype:'class_get',class:$(this).val()};
	$.post('_ncs_student.php',oParam,function(result){
		if(parseInt(result['result'])<0) {
			alert(result['msg']); return false;
		}
		console.log(result);
		let data=result['data'];
		$('#txtClassid').val(data['classcode']);
		$('#txtTitle').val(data['title']);
		if(data['period1'].length==8){
			data['period1']=data['period1'].substr(0,4)+'-'+data['period1'].substr(4,2)+'-'+data['period1'].substr(6);
		}
		if(data['period2'].length==8){
			data['period2']=data['period2'].substr(0,4)+'-'+data['period2'].substr(4,2)+'-'+data['period2'].substr(6);
		}
		$('#txtStartDate').val(data['period1']);
		$('#txtEndDate').val(data['period2']);
		$('#txtSeatCount').val(data['seat_cnt']);
		$('#txtColCount').val(data['col_cnt']);
		$('#bAlive').prop('checked',false);
		if(data['alive']=='1'){
			$('#bAlive').prop('checked',true);
		}
		$('#selStudent').empty();
	},'json');
	return false;
})
.on('click','#btnSave',function(){
	let oParam={optype:'class_add'};
	$('#txtClassid').val($.trim($('#txtClassid').val()));
	console.log('txtTitle ['+$('#txtTitle').val()+']');
	$('#txtTitle').val($.trim($('#txtTitle').val()));
	console.log('txtTitle ['+$('#txtTitle').val()+']');
	let str=$('#txtClassid').val();
	if(str.length<1){
		alert("CLASSID is necessary."); return false;
	}
	oParam['class']=str;
	str=$('#txtTitle').val();
	console.log('txtTitle ['+$('#txtTitle').val()+']');
	if(str.length<1){
		alert("Title is necessary."); return false;
	}
	oParam['title']=str;
	if($('#txtStartDate').val()>=$('#txtEndDate').val()){
		alert('StartDate should be earlier than EndDate'); return false;
	}
	oParam['startdate']=$('#txtStartDate').val();
	oParam['enddate']=$('#txtEndDate').val();
	if(parseInt($('#txtSeatCount').val())<1){
		alert('Seat Count can not be zero.'); return false;
	}
	oParam['seatcount']=$('#txtSeatCount').val();
	if(parseInt($('#txtColCount').val())<1){
		alert('Column Count can not be zero.'); return false;
	}
	oParam['colcount']=$('#txtColCount').val();
	if(parseInt(oParam['seatcount'])%parseInt(oParam['colcount'])!=0){
		alert('좌석수는 열숫자의 배수여야 합니다.'); return false;
	}
	let bAlive='0';
	if($('#bAlive').is(':checked')){
		bAlive='1';
	}
	oParam['alive']=bAlive;
	$.post('_ncs_student.php',oParam,function(result){
		console.log(result);
		if(parseInt(result['result'])<0) {
			alert(result['msg']);
		}
	},'json');
	return false;
})
.on('click','#btnDelete',function(){
	let oParam={optype:'class_remove'};
	if($('#txtClassid').val()=='') return false;
	oParam['class']=$('#txtClassid').val();
	console.log(oParam);
	$.post('_ncs_student.php',oParam,function(result){
		if(parseInt(result['result'])<1) {
			alert(result['msg']); return false;
		}
		$('#selClass option[value='+$('#txtClassid').val()+']').remove();
		$('#btnReset').trigger('click');
	},'json');
	return false;
})
.on('click','#btnReset',function(){
	$('#tblClass:input').val('');
	return false;
})
.on('click','#btnLink',function(){
	if($('#selClass option:selected').val()!='') {
		document.location="http://xaexal.cafe24.com/ncs/ncs_sv.php?name=a2z4sg&class="+$('#selClass option:selected').val();
	}
	return false;
})
.on('click','#btnLoad',function(){
	console.log('btnLoad');
	if($('#txtClassid').val()=='') {
		alert('class should be selected.');
		return false;
	}
	let oParam={optype:'load_student',class:$('#txtClassid').val()};
	$.post('_ncs_student.php',oParam,function(result){
		console.log(result);
		$('#selStudent').empty();
		$.each(result['data'],function(ndx,rec){
			let str=`<option value="${rec['name']}">${rec['name']}\t${rec['birth']}\t${rec['mobile']}\t${rec['school']}</option>`;
			$('#selStudent').append(str);
		});
	},'json');
	return false;
})
.on('click','#selStudent option',function(){
	if($(this).val()=='') return false;
    $('#tblStudent:input').val('');
	let oParam={optype:'student_get',name:$(this).val(),class:$('#txtClassid').val()};
	$.post('_ncs_student.php',oParam,function(result){
		console.log(result);
		if(parseInt(result['result'])<1) {
			alert(result['msg']);
			return false;
		}
		let rec=result['data'];
		$('#name').val(rec['name']);
//		if(rec['birth'].indexOf('-')<0) {
//			rec['birth']=rec['birth'].substr(0,2)+'-'+rec['birth'].substr(2,2)+'-'+rec['birth'].substr(4);
//			if(parseInt(rec['birth'].substr(0,2))>70){
//				rec['birth']='19'+rec['birth'];
//			} else {
//				rec['birth']='20'+rec['birth'];
//			}
//		}
		$('#birth').val(rec['birth']);
		$('#mobile').val(rec['mobile']);
		$('#school').val(rec['school']);
		$('#tvid').val(rec['tvid']);
		$('#seq').val(rec['seq']);
		console.log('Alive ['+rec['alive']+']');
		$('#cAlive').prop('checked',false);
		if(rec['alive']=='1') {
			$('#cAlive').prop('checked',true);	
		}
		return false;
	},'json');
})
.on('click','#btnSave1',function(){
	if($('#txtClassid').val()=='') { 
		alert('class should be selected first.');
		return false;
	}
	$('#name').val($.trim($('#name').val()));
	if($('#name').val()==''){
		alert('name should be given.'); return false;
	}
	let oParam={optype:'student_add',name:$('#name').val(),class:$('#txtClassid').val()};
	$('#school').val($.trim($('#school').val()));
	oParam['school']=$('#school').val();
	$('#mobile').val($.trim($('#mobile').val()));
	oParam['mobile']=$('#mobile').val();
	$('#anydesk').val($.trim($('#anydesk').val()));
	oParam['tvid']=$('#tvid').val();
	oParam['alive']=$('#cAlive').is(':checked')?'1':'0';
	oParam['birth']=$('#birth').val();
	oParam['seq']=$('#seq').val();
	console.log(oParam);
	$.post('_ncs_student.php',oParam,function(result){
		console.log(result);
		if(result['result']!='0') {
			alert(result['msg']); return false;
		}
		$('#btnLoad,#btnReset1').trigger('click');
	},'json');
	return false;
})
.on('click','#btnDelete1',function(){})
.on('click','#btnReset1',function(){})
;