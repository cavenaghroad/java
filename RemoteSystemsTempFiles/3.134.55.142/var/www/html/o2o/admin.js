let oBody={
	tblMentor:{
		key:{
			mentor_id:null
		}
		,btn:{
			change:9
			,delete:10
		}
		,inputs:{
			start_dt:'5'
			,end_dt:'6'
			,graduated:'7s'	// s: select tag
			,pastor:'8'
			,pastor_id:'8a'	// a: attribute ( .attr(x))
		}
		,values:{}
	},
	tblMentee:{
		key:{
			mentee_id:null
			,rowid:null
		}
		,btn:{
			change:8
			,delete:9
		}
		,inputs:{
			start_dt:'5'
			,end_dt:'6'
			,graduated:'7s'
		}
		,values:{}
	},
	tblHistory:{
		key:{
			rowid:null
			,hist_id:null
		}
		,btn:{
			change:4
			,delete:5
		}
		,inputs:{
			meet_dt:'1'
			,location:'2'
			,chapter:'3'
		}
		,values:{}
	}
};

$(window).resize(function(){
	console.log(`width [${$(window).width()}] height [${$(window).height()}]`)
})
$(document)
.ready(function(){
	let h=$('#header').height();
	$('#content').height(screen.height-h-150);
	$.post('_mentor.php',{optype:'list'},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0) {
			console.log(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		let n=0;
		$.each(answer['data'],function(ndx,rec){
			n++;
			pstr=`<tr mentor_id='${rec['mentor']}' title='${rec['mentor']}'><td align=center>${n}</td>`+
			`<td>${rec['member_name']}</td><td align=center>${rec['gender']}</td><td>${rec['birthday']}</td>`+
            `<td>${rec['mobile']}</td><td>${rec['start_dt']}</td><td>${rec['end_dt']}</td>`+
            `<td align=center>${rec['graduated']}</td><td pastor_id=${rec['pastor_id']}>${rec['pastor']}</td>`+
			`<td style='display:none'>수정</td><td style='display:none'>삭제</td></tr>`;
			$('#tblMentor').append(pstr);
			if(n<1){
				pstr=`<tr><td colspan=7)<h1>${answer['msg']}</h1></td></tr>`;
				$('#tblMentor').append(pstr);
			}
			countPerson('Mentor');
		});
	},'json');
	return false;
})
//--------------------------------------------------------------------------------------
// 양육자 관리
//--------------------------------------------------------------------------------------
.on('click','#tblMentor td',function(){
	let ndx=$(this).index();
	let me=$(this);
	let _tr=$(this).parent();
	let tname=_tr.parent().attr('id');
	console.log(`tname [${tname}]`);
	let otbl=oBody[tname];
	otbl.key.mentor_id=_tr.attr('mentor_id');
	/*$('#clpbrd').val(otbl.key.mentor_id).show().select();
	document.execCommand('copy');
	$('#clpbrd').hide();
	*/
	if(ndx==otbl.btn.delete){	// click "Delete" button
		trSelect(_tr);
		console.log('Mentor ID ['+otbl.key.mentor_id+']');
		switch($(this).text()){
		case '삭제':
			if(!confirm('삭제할까요?'))	return false;
			$.post('_mentor.php',{optype:'remove',mentor:otbl.key.mentor_id},function(answer){
				let retval=parseInt(answer['result']);
				if(retval>0){
					_tr.remove();
					otbl.id=null;
				}
				alert(answer['msg']);
				if(retval==-99) document.location='/';
				return false;			
			},'json');
			break;
		case '취소':
			$.each(otbl.inputs,function(key,token){
				$('#'+key).remove();
				_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
			});
			$(this).text('삭제'); _tr.find('td:eq('+(otbl.btn.change)+')').text('수정');
			break;
		}
		return false;
	}
	if(ndx==otbl.btn.change){	// click "Modify" button
		let me=$(this);
		let _tr=$(this).parent();
		switch($(this).text()){
		case '수정':
			console.clear();
			console.log(_tr.html());
			$.each(otbl.inputs,function(key,token){
				switch(token.substr(-1)){
				case 'a':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').attr(key);
					break;	
				case 's':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').text();
					_tr.find('td:eq('+col+')').html('<select id='+key+'><option value=Y>Yes</option><option value=N>No</option></select>');
					setTimeout(function(){
						$('#'+key).val(otbl.values[key]);
					},300);
					break;
				default:
					col=token;
					otbl.values[key]=_tr.find('td:eq('+col+')').text();
					console.log('values [<input type=text id='+key+' size=10 style="width:80px;" value="'+otbl.values[key]+'">]');
					_tr.find('td:eq('+col+')').html('<input type=text id='+key+' size=10 style="width:80px;" value="'+otbl.values[key]+'">');
					if(key=="pastor"){
						setTimeout(function(){
							$('#pastor').focus(function(){
								$('#dlgPastor').dialog({
									autoOpen:true
									,modal:true
									,position:{my:'left top',at:'left bottom',of:$('#pastor')}
									});
							});
						},300);
					}
				}
			});
			console.log(otbl);
			me.text('완료');
			_tr.find('td:eq('+otbl.btn.delete+')').text('취소');
			break;
		case '완료':
			console.clear();
			console.log('>>>>> 완료 <<<<<<<<<<<<<<<<<<<');
			$.each(otbl.inputs,function(key,token){
				console.log(`key [${key}] token [${token}]`);
				switch(token.substr(-1)){
				case 'a':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').attr(key);
					break;
				case 's':
				default:
					otbl.values[key]=$('#'+key).val();
					console.log(`key [${key}]  val [${$('#'+key).val()}] values [${otbl.values[key]}]`);
				}
			});
			console.log(oBody[tname]);
			if(!isDate($('#start_dt').val())){
				alert('시작일의 날짜가 잘못된 형식입니다.');
				return false;
			}
			if(!isDate($('#end_dt').val())){
				alert('수료일의 날짜가 잘못된 형식입니다.');
				return false;
			}
			$.post('_mentor.php',{optype:'change',mentor:otbl.id,
					start_dt:otbl.values['start_dt'],end_dt:otbl.values['end_dt'],graduated:otbl.values['graduated'],pastor:otbl.values['pastor_id']},function(answer){
				let retval=parseInt(answer['result']);
				if(retval<0){
					alert(answer['msg']);
					if(retval==-99) document.location='/';
				} 
				$.each(otbl.inputs,function(key,token){
					$('#'+key).remove();
					_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
				});
				me.text('수정')
			},'json');
		}
		return false;
	} 
	// click data area.		
	console.log('#tblMentor tr ['+$(this).index()+']');
	trSelect(_tr);
	otbl.id=_tr.attr('mentor_id');
	console.log('Mentor ID ['+otbl.id+']');
	$.post('_mentee.php',{optype:'list',mentor:oBody.tblMentor.key.mentor_id},function(answer){
		$('#tblMentee').empty();
		let retval=parseInt(answer['result']);
		if(retval<0){
			if(retval==-99) {
				alert(answer['msg']);
				document.location='/';
				return false;
			}
			let n=$('#tblMentee').parent().find('thead tr th').length;
			pstr=`<tr><td colspan=${n}>${answer['msg']}</td></tr>`;
			$('#tblMentee').append(pstr);
			return false;
		}
//		console.log(answer);
		let n=0;
		$.each(answer['data'],function(ndx,rec){
			n++;
			pstr=`<tr mentee_id=${rec['member_id']} rowid=${rec['rowid']} title=${rec['member_id']}>`+
				`<td align=center>${n}</td><td>${rec['member_name']}</td>`+
				`<td align=center>${rec['gender']}</td><td>${rec['birthday']}</td><td>${rec['mobile']}</td>`+
				`<td>${rec['start_dt']}</td><td>${rec['end_dt']}</td><td align=center>${rec['graduated']}</td>`+
				`<td style='display:none;'>수정</td><td style='display:none;'>삭제</td></tr>`;
//			console.log(pstr);
			$('#tblMentee').append(pstr);
		});
		otbl['num_rows']=n; 
		if(n==0){
			let n=$('#tblMentee').parent().find('thead tr th').length;
			pstr='<tr><td colspan='+n+'>등록된 동반자가 없습니다.</td></tr>';
			$('#tblMentee').append(pstr);
			$('#NumMentee').text('');
		} else {
			countPerson('Mentee');
		}
		$('#tblHistory').empty();
	},'json');
	return false;
})
//--------------------------------------------------------------------------------------
// 동반자 관리
//--------------------------------------------------------------------------------------
.on('click','#tblMentee td',function(){
	let ndx=$(this).index();
	let me=$(this);
	let _tr=$(this).parent();
	let tname=_tr.parent().attr('id');
	console.log(`tname [${tname}]`);
	let otbl=oBody[tname];
	otbl.key.mentee_id=_tr.attr('mentee_id');
	otbl.key.rowid=_tr.attr('rowid');

//	$('#clpbrd').val(otbl.key.mentee_id+'/'+otbl.key.rowid).show().select();
//	document.execCommand('copy');
//	$('#clpbrd').hide();

	if(ndx==otbl.btn.delete){	// click "Delete" button
		trSelect(_tr);
		console.log('Mentee ID ['+otbl.key.rowid+']');
		switch($(this).text()){
		case '삭제':	
			if(!confirm('삭제할까요?'))	return false;
			
			$.post('_mentee.php',{optype:'remove',rowid:otbl.key.rowid},function(answer){
				let retval=parseInt(answer['result']);
				if(retval>0){
					_tr.remove();
					otbl.id=null;
				} else {
					alert(answer['msg']);
					if(retval==-99) {
						document.location='/';
						return false;	
					}
				}
			},'json');
			break;		
		case '취소':
			$.each(otbl.inputs,function(key,token){
				$('#'+key).remove();
				_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
			});
			$(this).text('삭제'); _tr.find('td:eq('+(otbl.btn.change)+')').text('수정');
		}
		return false;
	} 
	if(ndx==otbl.btn.change){	// click "Modify" button
		let me=$(this);
		let _tr=$(this).parent();
		switch($(this).text()){
		case '수정':
			console.clear();
			console.log(_tr.html());
			$.each(otbl.inputs,function(key,token){
				switch(token.substr(-1)){
/*				case 'a':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').attr(key);
					break; */	
				case 's':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').text();
					_tr.find('td:eq('+col+')').html('<select id='+key+'><option value=Y>Yes</option><option value=N>No</option></select>');
					setTimeout(function(){
						$('#'+key).val(otbl.values[key]);
					},300);
					break;
				default:
					col=token;
					otbl.values[key]=_tr.find('td:eq('+col+')').text();
					console.log('values [<input type=text id='+key+' size=10 style="width:80px;" value="'+otbl.values[key]+'">]');
					_tr.find('td:eq('+col+')').html('<input type=text id='+key+' size=10 style="width:80px;" value="'+otbl.values[key]+'">');
/*					if(key=="pastor"){
						setTimeout(function(){
							$('#pastor').focus(function(){
								$('#dlgPastor').dialog({
									autoOpen:true
									,modal:true
									,position:{my:'left top',at:'left bottom',of:$('#pastor')}
									});
							});
						},300);
					}*/
				}
			});
			console.log(otbl);
			me.text('완료');
			_tr.find('td:eq('+otbl.btn.delete+')').text('취소');
			break;
		case '완료':
			console.clear();
			console.log('>>>>> 완료 <<<<<<<<<<<<<<<<<<<');
			$.each(otbl.inputs,function(key,token){
				console.log(`key [${key}] token [${token}]`);
				switch(token.substr(-1)){
				/*case 'a':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').attr(key);
					break;*/
				case 's':
				default:
					otbl.values[key]=$('#'+key).val();
					console.log(`key [${key}]  val [${$('#'+key).val()}] values [${otbl.values[key]}]`);
				}
			});
			console.log(oBody[tname]);
			if(!isDate($('#start_dt').val())){
				alert('시작일의 날짜가 잘못된 형식입니다.');
				return false;
			}
			if(!isDate($('#end_dt').val())){
				alert('수료일의 날짜가 잘못된 형식입니다.');
				return false;
			}
			$.post('_mentee.php',{optype:'change',mentee:otbl.id,
					start_dt:otbl.values['start_dt'],end_dt:otbl.values['end_dt'],graduated:otbl.values['graduated'],
					mentor:oBody.tblMentor.key.mentor_id},function(answer){
				let retval=parseInt(answer['result']);
				if(retval<0){
					alert(answer['msg']);
					if(retval==-99) document.location='/';
					return false;
				} 
				$.each(otbl.inputs,function(key,token){
					$('#'+key).remove();
					_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
				});
				me.text('수정')
			},'json');
		}
		return false;		
	} 
	// click data area.
	console.log('#tblMentee tr ['+$(this).index()+']');
	trSelect(_tr);
	otbl.id=_tr.attr('mentee_id');
	console.log('Mentee ID ['+otbl.id+']');
	$.post('_history.php',{optype:'list',rowid:oBody.tblMentee.key.rowid},function(answer){
		$('#tblHistory').empty();
		let retval=parseInt(answer['result']);
		if(retval<0){
			if(retval==-99) {
				document.location='/';
			} else {
				let n=$('#tblMentee').parent().find('thead tr th').length;
				pstr=`<tr><td colspan=${n}>${answer['msg']}</td></tr>`;
				$('#tblHistory').append(pstr);
			}
			return false;
		}
		let n=0;
		console.log(answer);
		$.each(answer['data'],function(ndx,rec){
			n++;
			pstr=`<tr hist_id=${rec['hist_id']} rowid=${rec['rowid']} title=${rec['member_id']}><td>${n}</td>`+
				`<td>${rec['meet_dt']}</td><td>${rec['location']}</td><td>${rec['chapter']}</td>`+
				`<td style='display:none;'>수정</td><td style='display:none;'>삭제</td></tr>`;
			$('#tblHistory').append(pstr);
		});
		otbl['num_rows']=n; 
		if(n==0){
			let n=$('#tblHistory').parent().find('thead tr th').length;
			pstr='<tr><td colspan='+n+'>추가한 일정이 없습니다.</td></tr>';
			$('#tblHistory').append(pstr);
		}
	},'json');
	return false;
})
//--------------------------------------------------------------------------------------
// 일정관리
//--------------------------------------------------------------------------------------
.on('click','#tblHistory td',function(){
	let ndx=$(this).index();
	let me=$(this);
	let _tr=me.parent();
	let tname=_tr.parent().attr('id');
	let otbl=oBody['tblHistory'];
	otbl.key.rowid=_tr.attr('rowid');
	otbl.key.hist_id=_tr.attr('hist_id');
/*	$('#clpbrd').val(otbl.key.rowid).show().select();
	document.execCommand('copy');
	$('#clpbrd').hide();
*/	console.log(`ndx [${ndx}] text [${me.text()}]`);
	if(ndx==otbl.btn.delete){	//click "취소"/"삭제"
		trSelect(_tr);
		console.log('rowid ['+otbl.key.rowid+']');
		switch($(this).text()){
		case "삭제":
			if(!confirm('정말로 삭제할까요?')) return false;			
			$.post('_history.php',{optype:'remove',hist_id:_tr.attr('hist_id')},
				function(answer){
					let retval=parseInt(answer['result']);
					if(retval<0){
						alert(answer['msg']); 
						if(retval==-99)	document.location='/';
						return false;
					}
					_tr.remove();
				},'json');
			break;
		case "취소":
			/*_tr.remove();*/
			$.each(otbl.inputs,function(key,token){
				$('#'+key).remove();
				_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
			});
			me.text('삭제'); _tr.find('td:eq('+otbl.btn.change+')').text('수정');
			break;
		}
		return false;
	} 
	if(ndx==otbl.btn.change){	// click "등록"/"완료"
		switch($(this).text()){
		case "수정":
			console.clear();
			console.log(_tr.html());
			$.each(otbl.inputs,function(key,token){
				switch(token.substr(-1)){
				case 'a':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').attr(key);
					break;	
				case 's':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').text();
					_tr.find('td:eq('+col+')').html('<select id='+key+'><option value=Y>Yes</option><option value=N>No</option></select>');
					setTimeout(function(){
						$('#'+key).val(otbl.values[key]);
					},300);
					break;
				default:
					col=token;
					otbl.values[key]=_tr.find('td:eq('+col+')').text();
					console.log('values [<input type=text id='+key+' size=10 style="width:80px;" value="'+otbl.values[key]+'">]');
					_tr.find('td:eq('+col+')').html('<input type=text id='+key+' size=10 style="width:80px;" value="'+otbl.values[key]+'">');
				}
			});
			console.log(otbl);
			me.text('등록'); _tr.find('td:eq('+otbl.btn.delete+')').text('취소');
			break;
		case "등록":
			console.clear();
			console.log('>>>>> 완료 <<<<<<<<<<<<<<<<<<<');
			$.each(otbl.inputs,function(key,token){
				console.log(`key [${key}] token [${token}]`);
				switch(token.substr(-1)){
				case 'a':
					col=token.substr(0,token.length-1);
					otbl.values[key]=_tr.find('td:eq('+col+')').attr(key);
					break;
				case 's':
				default:
					otbl.values[key]=$('#'+key).val();
					console.log(`key [${key}]  val [${$('#'+key).val()}] values [${otbl.values[key]}]`);
				}
			});
			if(!isDate($('#meet_dt').val())){
				alert('모임시각의 날짜가 잘못된 형식입니다.');
				return false;
			}
			if(otbl.key.hist_id==null){
				$.post('_history.php',{optype:'addnew',rowid:otbl.key.rowid,
					meet_dt:otbl.values['meet_dt'],location:otbl.values['location'],chapter:otbl.values['chapter']},
					function(answer){
						if(answer['errcode']=="1062"){
							alert('같은 날짜에 이미 등록된 일정이 있습니다.');
							return false;
						}
						let retval=parseInt(answer['result']);
						if(retval<0){
							alert(answer['msg']);
							if(retval==-99) document.location='/';
							return false;
						} 
						$.each(otbl.inputs,function(key,token){
							$('#'+key).remove();
							_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
						});
						me.text('수정'); _tr.find('td:eq('+otbl.btn.delete+')').text('삭제');
					},'json');
			} else {
				$.post('_history.php',{optype:'change',hist_id:otbl.key.hist_id,
					meet_dt:otbl.values['meet_dt'],location:otbl.values['location'],chapter:otbl.values['chapter']},
					function(answer){
						if(answer['errcode']=="1062"){
							alert('같은 날짜에 이미 등록된 일정이 있습니다.');
							return false;
						}
						let retval=parseInt(answer['result']);
						if(retval<0){
							alert(answer['msg']);
							if(retval==-99) document.location='/';
							return false;
						} 
						$.each(otbl.inputs,function(key,token){
							$('#'+key).remove();
							_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
						});
						me.text('수정'); _tr.find('td:eq('+otbl.btn.delete+')').text('삭제');
					},'json');
			}
			break;
		}
		return false;
	} 
	trSelect(_tr);
	return false;
})
.on('click','#btnAddMentor',function(){
	let _tr=$('#tblMentor tr.click_bg');
	if($('#start_dt').length>0){
		 _tr.find('td:eq('+(oBody['tblMentor'].btn.delete)+')').trigger('click');
	}
	_tr.find('td:gt('+(oBody['tblMentor'].btn.change-1)+')').hide();
	$('#dlgMentor').dialog('open');
})
.on('click','#btnAddMentee',function(){
	console.log('['+oBody['tblMentor']['id']+']');
	if(oBody['tblMentor']['id']==null){
		alert('양육자를 먼저 선택하십시오.');
		return false;
	}
	let _tr=$('#tblMentee tr.click_bg');
	if($('#start_dt').length>0){
		tr.find('td:eq('+(oBody['tblMentee'].btn.delete)+')').trigger('click');
	}
	_tr.find('td:gt('+(oBody['tblMentee'].btn.change-1)+')').hide();
	$('#dlgMentee').dialog('open');
})
.on('click','#btnAddHistory',function(){
	let otbl=oBody['tblHistory'];
	
	if($('#tblMentee tr.click_bg').length==0){
		alert('일정을 등록할 동반자를 먼저 선택하십시오.');
		return false;
	}
	let _tr=$('#tblMentee tr.click_bg');
	let rowid=_tr.attr('rowid');
	let mentee=_tr.attr('mentee_id');
	console.log(`rowid [${rowid}] mentee [${mentee}]`);
	if($('#tblHistory tr').length==1 && $('#tblHistory tr:eq(0) td').length==1) {
		$('#tblHistory tr:eq(0)').remove();
	}
	$('#tblHistory tr.click_bg td:gt('+(otbl.btn.change-1)+')').hide();
	
	otbl.key.rowid=rowid;
	otbl.key.hist_id=null;
	let pstr='<tr rowid='+rowid+' title='+rowid+'><td align=center>*</td><td><input type=text size=12 maxlength=12 style="width:100px;" id=meet_dt></td>'+
		'<td><input type=text size=12 style="width:120px;" id=location></td><td><input type=text size=20 style="width:150px;" id=chapter></td><td>등록</td><td>취소</td></tr>';
	$('#tblHistory').append(pstr);
	setTimeout(function(){
		$('#meet_dt').focus();
	},300);
	return false;
})
.on('click','#rAll',function(){
	$('#tblMentee  tr').show();
})
.on('click','#rGraduated',function(){
	$('#tblMentee tr').each(function(ndx,html){
		if($(this).find('td:eq(7)').text()=='Y')	$(this).show();
		else $(this).hide();
	})
})
.on('click','#rInProgress',function(){
	$('#tblMentee tr').each(function(ndx,html){
		if($(this).find('td:eq(7)').text()=='Y')	$(this).hide();
		else $(this).show();
	})
})
.on('click',':radio[name=filters]',function(){
	let token=this.id;
	if(token=='all') {	// show all mentors
		$('#tblMentor tr:hidden').each(function(){
			$(this).show();
		});
		countPerson('Mentor');
	} else {
		$.post('_mentor.php',{optype:'filter',filter:token},function(answer){
			let retval=parseInt(answer['result']);
			if(retval<0){
				alert(answer['msg']);
				if(retval==-99) document.location='/';
				return false;
			}
			$('#tblMentor tr').hide();
			$.each(answer['data'],function(ndx,mentor){
				$('#tblMentor tr[mentor_id='+mentor+']').show();
			});
			countPerson('Mentor');
		},'json');
	}
	return true;
})
.on('click',':radio[name=rMentee]',function(){
	let token=this.id;
	switch(token){
	case 'all':
		$('#tblMentee tr:hidden').each(function(){
			$(this).show();
		});
		break;
	case 'graduated':
		$('#tblMentee tr').each(function(){
			if($(this).find('td:eq(7)').text()=='Y') $(this).show();
			else $(this).hide();
		});
		break;
	case 'inclass':
		$('#tblMentee tr').each(function(){
			if($(this).find('td:eq(7)').text()=='Y') $(this).hide();
			else $(this).show();
		});
		break;
	}
	countPerson('Mentee');
	return true;
})
;

function hasNumeric(pstr){
	for(i=0;i<pstr.length;i++){
		if(!isNaN(pstr[i])) return true;
	}
	return false;
}

function trSelect(_tr){
	let tbl=_tr.parent().attr('id');
	let n=oBody[tbl]['btn']['change']-1;
	let prevTR=_tr.parent().find('tr.click_bg');
	prevTR.find('td:gt('+n+')').hide();
	prevTR.removeClass('click_bg');
	_tr.addClass('click_bg');
	_tr.find('td:gt('+n+')').show();
	$.each(oBody[tbl]['key'],function(ndx,value){
		console.log(`ndx [${ndx}] value [${value}] attr [${_tr.attr(ndx)}]`);
		oBody[tbl]['key'][ndx]=_tr.attr(ndx);
		console.log(`ndx [${ndx}] value [${oBody[tbl]['key'][ndx]}]`);
	})
}
function countPerson(str){
	let cnt=0;
	$('#tbl'+str+' tr').each(function(){
		if($(this).is(':visible')) cnt++;
	});
	$('#Num'+str).text(cnt+' 명');
}