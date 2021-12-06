$(document)
.ready(function(){
	$.post('_admin.php',{optype:'checkLevel'},function(answer){
		$('#adminpage').html(answer['data']);
		return false;
	},'json');
	$.ajax({
		url:'_private.php',
		data:{optype:'private'},
		dataType:'json',method:'post',
		beforeSend:function(){
			getPicklist('nationality');
			getPicklist('position');
			getPicklist('job');
			getPicklist('scale');
			getPicklist('marriage');
			getPicklist('workplace');
			getPicklist('school');
		},
		success:function(info){
			console.log(info);
			if(info['result']<1) {
				alert(info['msg']); return false;
			}
			$('#tblPrivate input,select,textarea').each(function(){
				if(info.data[this.id]===undefined) info.data[this.id]='';
				//console.log('['+$(this).prop('tagName').toLowerCase()+'/'+$(this).prop('type')+'/'+this.id+'/'+info.data[this.id]+']');
				switch($(this).prop('tagName').toLowerCase()){
				case 'select':
					if(this.id=='nationality' && (info.data[this.id]=='' || info.data[this.id]=='South Korea') ) {
						setTimeout(function(){
							$("#nationality option").filter(function() {
						  		return $(this).text()=='REPUBLIC OF KOREA';
							}).prop('selected', true);
						},300);
					} else {
						$('#'+this.id).val(info.data[this.id]);
					}
					break;
				case 'textarea':
					$('#'+this.id).text(info.data[this.id]); break;
				default:
					switch($(this).prop('type')){
					case 'password':
					case 'text':
					case 'hidden':
						$('#'+this.id).val(info.data[this.id]); break;
					case 'radio':
					case 'checkbox':
						let name=$(this).prop('name');
						if(info.data[name]!='' && info.data[name]!=null){
							$('input[name='+name+'][value='+info.data[name]+']').attr('checked',true); 
						}
						break;
					}
				}			
			});
		}
	});
	$.post('_mentor.php',{optype:'getMentor',member_id:$('#member_id').val()},function(answer){
		console.log(answer);
		let result=parseInt(answer['result']);
		if(result== -99){
			alert(answer['msg']);
			document.location="/";
		} else if(result== -1){
			n=$('#tblMentor').siblings().find('tr th').length;
			pstr=`<tr><td colspan=${n}>${answer['msg']}</td></tr>`;
			$('#tblMentor').append(pstr);
		} else if(result>0){
			$.each(answer['data'],function(ndx,rec){
				pstr=`<tr><td>${rec['member_name']}</td><td align=center>${rec['graduated']}</td><td>${rec['start_dt']}</td><td>${rec['end_dt']}</td></tr>`;
				$('#tblMentor').append(pstr);
				$('#tblMentor').parent().show();
			});
		} 
	},'json');
	$.post('_mentor.php',{optype:'getPastor',member_id:$('#member_id').val()},function(answer){
		console.log(answer);
		let result=parseInt(answer['result']);
		if(result== -99){
			alert(answer['msg']);
			document.location="/";
		} else if(result== -1){
			n=$('#tblClass').siblings().find('tr th').length;
			pstr=`<tr><td colspan=${n}>${answer['msg']}</td></tr>`;
			$('#tblClass').append(pstr);
		} else if(result>0){
			$.each(answer['data'],function(ndx,rec){
				pstr=`<tr><td>${rec['member_name']}</td><td align=center>${rec['graduated']}</td><td>${rec['start_dt']}</td><td>${rec['end_dt']}</td></tr>`;
				$('#tblClass').append(pstr);
				$('#tblClass').parent().show();
			});
		} 
	},'json');	
	$.post('_mentor.php',{optype:'getMentee',member_id:$('#member_id').val()},function(answer){
		console.log(answer);
		let result=parseInt(answer['result']);
		if(result== -99){
			alert(answer['msg']);
			document.location="/";
		} else if(result== -1){
			n=$('#tblMenee').siblings().find('tr th').length;
			pstr=`<tr><td colspan=${n}>${answer['msg']}</td></tr>`;
			$('#tblMentee').append(pstr);
		} else if(result>0){
			let n=1;
			$.each(answer['data'],function(ndx,rec){
				pstr=`<tr rowid='${rec['rowid']}' mentee='${rec['mentee']}'>`+
					`<td align=center>${n}</td><td>${rec['member_name']}</td><td align=center>${rec['gender']}</td><td>${rec['birthday']}</td>`+
					`<td>${rec['mobile']}</td><td align=center>${rec['graduated']}</td><td>${rec['start_dt']}</td><td>${rec['end_dt']}</td></tr>`;
				n++;
				console.log(pstr);
				$('#tblMentee').append(pstr);
			});
		} 
	},'json');	
	$.post('_mentee.php',{optype:'count',member_id:$('#member_id').val()},function(answer){
		console.log(answer);
		$.each(answer['data'],function(ndx,rec){
			$('#graduated,#howmany').text('0 명');
			switch(rec['graduated']) {
			case 'Y':
				$('#graduated').text(rec['cnt']+' 명'); break;
			case 'N':
				$('#howmany').text(rec['cnt']+' 명'); break;
			}
		});
		$('#'+answer['status']).prop('checked',true);
	},'json');
/*
	$.post('_mentee.php',{optype:'list',mentor:$('#member_id').val()},function(answer){
		console.log(answer);
		let result=parseInt(answer['result']);
		if(result== -99){
			alert(answer['msg']);
			document.location="/";
		} else if(result== -1){
			n=$('#tblMentee').siblings().find('tr th').length;
			pstr=`<tr><td colspan=${n}>${answer['msg']}</td></tr>`;
			$('#tblMentee').append(pstr);
		} else if(result>0){
			let n=1;
			$.each(answer['data'],function(ndx,rec){
				pstr=`<tr rowid=${rec['rowid']} mentee=${rec['mentee_id']}><td>${n}</td><td>${rec['member_name']}</td>`+
					`<td>${rec['gender']}</td><td>${rec['birthday']}</td><td>${rec['mobile']}</td>`+
					`<td>${rec['graduated']}</td></tr>`;
				n++;
				$('#tblMentee').append(pstr);
				$('#tblMentee').parent().show();
			});
		}
	},'json');
*/	
	$.post('_history.php',{optype:'all',member_id:$('#member_id').val()},function(answer){
		console.log(answer);
		let result=parseInt(answer['result']);
		if(result== -99){
			alert(answer['msg']);
			document.location="/";
		} else if(result== -1){
			n=$('#tblHistory').siblings().find('tr th').length;
			pstr=`<tr><td colspan=${n}>${answer['msg']}</td></tr>`;
			$('#tblHistory').append(pstr);
		} else if(result>0){
			console.log(answer);
			$.each(answer['data'],function(ndx,rec){
				pstr=`<tr rowid=${rec['rowid']} style='display:none;'><td>${rec['meet_dt']}</td>`+
					`<td>${rec['location']}</td><td>${rec['chapter']}</td>`+
					`</tr>`;
				$('#tblHistory').append(pstr);
			});
		}
	},'json');
	return false;
})
.on('click','#tblMentee tr',function(){
	var rowid=$(this).attr('rowid');
	console.log(rowid);
	$('#tblHistory tr').each(function(){
		if(rowid==$(this).attr('rowid')) $(this).show();
		else $(this).hide();
	});
	return false;
})
.on('blur','#tblPrivate textarea,input[type=text],input[type=password],input[type=hidden]',function(){
	let colname=this.id;
	let colval=$(this).val();
	let trID=$(this).closest('tr').prop('id');
	if(colname=='passcode1'){
		if($.trim(colval)=='' || colval==null) return false;
		
	}
	console.log(`colname [${colname}] colval [${colval}]`);
	$.post('_private.php',{optype:'update',column:colname,value:colval,tbl:trID},function(info){
		if(parseInt(info['result'])<0){
			alert(info['msg']); return false;
		} 
	},'json');
	return true;
})
.on('change','#tblPrivate select,input[type=radio],input[type=checkbox]',function(){
	if($(this).prop('name')=='mystatus'){
		let newStatus=this.id;
		$.post('_update.php',{optype:'status',status:newStatus,member_id:$('#member_id').val()},
			function(answer){
				
			},'json');
		return false;
	}
	let colname,colval;
	let trID=$(this).closest('tr').prop('id');
	console.log(`tagName [${$(this).prop('tagName')}]`);
	if($(this).prop('tagName')=='SELECT'){
		colname=this.id;
		colval=$(this).find(':selected').text();
	} else {
		colname=$(this).prop('name');
		colval=$(':input[name='+colname+']:checked').val();
	}
	console.log(`colname [${colname}] colval [${colval}]`);
	$.post('_private.php',{optype:'update',column:colname,value:colval,tbl:trID},function(info){
		if(parseInt(info['result'])<0){
			alert(info['msg']); return true;
		} 
	},'json');
	return false;
})
.on('change','input[name=mystatus]',function(){
	let newStatus=this.id;
	console.log('newStatus ['+this.id+']');
	$.post('_mentee.php',{optype:'setStatus',member_id:$('#member_id').val(),status:newStatus},function(){},'json');
	return false;
})
;


function getPicklist(tblname){
	$.post('_admin.php',{optype:'picklist',tblname:tblname},function(info){
		$('#'+tblname).empty();
		let pstr='';
		$.each(info['data'],function(ndx,rec){
			pstr+=`<option value='${rec['name']}'>${rec['name']}</option>`;
		});
		$('#'+tblname).append(pstr);
	},'json');
}