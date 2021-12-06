let oBody={
	tblSaint:{
		key:{
			member_id:null
		}
		,btn:{
			change:8
			,delete:9
		}
		,inputs:{
			name:'1'
			,gender:'2s'
			,birthday:'3'	// s: select tag
			,mobile:'4'
			,o2o_apply:'5'	// a: attribute ( .attr(x))
			,regdate:'6'
			,retire_dt:'7'
		}
		,option:{
			gender:{m:'남',f:'여'}
		}
		,values:{}
	}
};
$(document)
.ready(function(){
	$.post('_member.php',{optype:'saint'},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0) {
			console.log(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		let n=0;
		$.each(answer['data'],function(ndx,rec){
			n++;
			pstr=`<tr member_id='${rec['member_id']}' title='${rec['member_id']}'><td align=center>${n}</td>`+
			`<td>${rec['member_name']}</td><td align=center>${rec['gender']}</td><td>${rec['birthday']}</td>`+
            `<td>${rec['mobile']}</td><td>${rec['o2o_apply']}</td><td>${rec['regdate']}</td>`+
			`<td>${rec['retire_dt']}</td>`+
			`<td style='display:none'>수정</td><td style='display:none'>삭제</td></tr>`;
			$('#tblSaint').append(pstr);
			if(n<1){
				pstr=`<tr><td colspan=7)<h1>${answer['msg']}</h1></td></tr>`;
				$('#tblSaint').append(pstr);
			}
		});
	},'json');
})
.on('click','#tblSaint td',function(){
	let ndx=$(this).index();
	let me=$(this);
	let _tr=$(this).parent();
	let tname=_tr.parent().attr('id');
	let otbl=oBody[tname];
	otbl.key.member_id=_tr.attr('member_id');
	
	if(ndx==otbl.btn.change){
		switch(me.text()){
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
					let pstr='<select id='+key+'>';
					$.each(otbl.option[key],function(k,v){
						pstr+='<option value='+k+'>'+v+'</option>';
					});
					pstr+='</select>';
					console.log(pstr);
					_tr.find('td:eq('+col+')').html(pstr);
					setTimeout(function(){
//						$('#'+key).val(otbl.values[key]);
						$('#'+key+' option').filter(function(){
							return $(this).text()==otbl.values[key];
						}).prop('selected',true);
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
			if(!isDate($('#birthday').val())){
				alert('생년월일의 날짜가 잘못된 형식입니다.');
				return false;
			}
			console.log(otbl);
			if(otbl.key.member_id === undefined){
				$.post('_member.php',{optype:'addnew',name:otbl.values['name'],gender:otbl.values['gender'],
						birthday:otbl.values['birthday'],mobile:otbl.values['mobile'],regdate:otbl.values['regdate'],
						retire_dt:otbl.values['retire_dt'],o2o_apply:otbl.values['o2o_apply']},function(answer){
					let retval=parseInt(answer['result']);
					if(retval<0){
						alert(answer['msg']);
						if(retval==-99) document.location='/';
					}
					$.each(otbl.inputs,function(key,token){
						$('#'+key).remove();
						if(isNaN(token)){
							if(token.substr(-1)=='s'){
								_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.option[key][otbl.values[key]]);
							}
						} else {
							_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);						
						}
					});
					me.text('수정');
					_tr.find('td:eq('+otbl.btn.delete+')').text('삭제'); 
				},'json');	
			} else {
				$.post('_member.php',{optype:'change',member_id:otbl.key.member_id,
						gender:otbl.values['gender'],birthday:otbl.values['birthday'],
						name:otbl.values['name'],o2o_apply:otbl.values['o2o_apply'],
						mobile:otbl.values['mobile'],regdate:otbl.values['regdate'],
						retire_dt:otbl.values['retire_dt']},function(answer){
					let retval=parseInt(answer['result']);
					if(retval<0){
						alert(answer['msg']);
						if(retval==-99) document.location='/';
					} 
					$.each(otbl.inputs,function(key,token){
						$('#'+key).remove();
						if(isNaN(token)){
							if(token.substr(-1)=='s'){
								_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.option[key][otbl.values[key]]);
							}
						} else {
							_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);						
						}
					});
					me.text('수정');
					_tr.find('td:eq('+otbl.btn.delete+')').text('삭제'); 
				},'json');
			}
		}
		return false;
	}
	if(ndx==otbl.btn.delete){
		switch(me.text()){
		case '삭제':
			if(!confirm('삭제할까요?'))	return false;
			$.post('_member.php',{optype:'remove',member_id:_tr.attr('member_id')},function(answer){
				let retval=parseInt(answer['result']);
				if(retval>0){
					_tr.remove();
					otbl.key.member_id=null;
				}
				alert(answer['msg']);
				if(retval==-99) document.location='/';
				return false;			
			},'json');
			break;
		case '취소':
			if(_tr.attr('member_id')==undefined){
				_tr.remove();
				return false;
			}
			$.each(otbl.inputs,function(key,token){
				$('#'+key).remove();
				_tr.find('td:eq('+token.replace(/[a-zA-Z]/g,'')+')').text(otbl.values[key]);
			});
			$(this).text('삭제'); _tr.find('td:eq('+(otbl.btn.change)+')').text('수정');
			break;
		}
		return false;
	}
	let prev=_tr.parent().find('tr.click_bg');
	prev.removeClass('click_bg');
	prev.find('td:gt(-3)').hide();
	_tr.addClass('click_bg').find('td:gt(-3)').show();
	return false;
})

.on('click',':radio[name=filters]',function(){
	let token=this.id;
	switch(token) {	// show all mentors
	case 'all':
		$('#tblSaint tr:hidden').each(function(){
			$(this).show();
		});
//		countPerson('Mentor');
		break;
	case 'today':
		today=getToday();
		$('#tblSaint tr').each(function(){
			if($(this).find('td:eq(6)').text()==today) $(this).show();
			else $(this).hide();
		});
		break;
	case 'latest':
		latest='00000000';
		$('#tblSaint tr').each(function(){
			if($(this).find('td:eq(6)').text()>latest) latest=$(this).find('td:eq(6)').text();
		});
		$('#tblSaint tr').each(function(){
			if($(this).find('td:eq(6)').text()==latest) $(this).show();
			else $(this).hide();
		});
	}
	return true;
})
.on('click','#btnAddNewbie',function(){
	let _tr=$('#tblSaint tr:eq(0)').html();
	console.log(_tr);
	let pstr='';
	let m,n;
	m=_tr.indexOf('<');
	let ndx=0;
	while(m>-1){
		n=_tr.indexOf('>',m);
		pstr+=_tr.substr(m,n-m+1);
		ndx++;
		if(ndx>oBody.tblSaint.btn.change*2-1) {
			pstr+=_tr.substr(n);
			break;	
		}
		m=_tr.indexOf('<',n);
	}
	console.log(pstr);
	$('#tblSaint').prepend('<tr>'+pstr+'</tr>');
	$('#tblSaint tr:eq(0) td:eq(0)').trigger('click');
//	$('#tblSaint tr:eq(0) td:gt('+(oBody.tblSaint.btn.change-1)+')').show();
	$('#tblSaint tr:eq(0) td:eq('+oBody.tblSaint.btn.change+')').trigger('click');
	return false;
})
;