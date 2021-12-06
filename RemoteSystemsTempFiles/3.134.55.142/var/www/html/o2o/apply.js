$(document)
.ready(function(){
	$.post('_member.php',{optype:'member'},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0){
			alert(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		let n=1;
		$.each(answer['data'],function(ndx,rec){
			pstr=`<tr member_id=${rec['member_id']}><td align=center>${n}</td><td>${rec['member_name']}</td>`+
				`<td align=center>${rec['gender']}</td><td>${rec['birthday']}</td><td>${rec['mobile']}</td>`+
				`</tr>`;
			n++;
			$('#tblMember').append(pstr);
		});
	},'json');
	$.post('_member.php',{optype:'mentee'},function(answer){
		let n=1;
		$.each(answer['data'],function(ndx,rec){
			pstr=`<tr member_id=${rec['member_id']}><td align=center>${n}</td><td>${rec['member_name']}</td>`+
				`<td align=center>${rec['gender']}</td><td>${rec['birthday']}</td><td>${rec['mobile']}</td>`+
				`<td>${rec['o2o_apply']}</td></tr>`;
			n++;
			$('#tblMentee').append(pstr);
		});
	},'json');
	return false;
})
.on('dblclick','#tblMember tr',function(){
	let me=$(this);
	$.post('_member.php',{optype:'addMentee',member_id:me.attr('member_id')},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0) {
			alert(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		let name=me.find('td:eq(1)').text();
		let pstr=`<tr member_id=${me.attr('member_id')}>${me.html()}<td>YYYY-MM-DD</td></tr>`;
		let appended=false;
		$('#tblMentee tr').each(function(){
			if($(this).find('td:eq(1)').text()>name){
				$(this).before(pstr);
				appended=true;
				return false;
			}
		});
		if(!appended) $('#tblMentee').append(pstr);
		me.remove();
	},'json');
	return false;
})
.on('dblclick','#tblMentee tr',function(){
	let me=$(this);
	$.post('_member.php',{optype:'removeMentee',member_id:me.attr('member_id')},function(answer){
		let retval=parseInt(answer['result']);
		if(retval<0) {
			alert(answer['msg']);
			if(retval==-99)	document.location='/';
			return false;
		}
		let name=me.find('td:eq(1)').text();
		let pstr=`<tr member_id=${me.attr('member_id')}>${me.html()}<td>YYYY-MM-DD</td></tr>`;
		let appended=false;
		$('#tblMember tr').each(function(){
			if($(this).find('td:eq(1)').text()>name){
				$(this).before(pstr);
				appended=true;
				return false;
			}
		});
		if(!appended){
			$('#tblMember').append(pstr);
		}
		me.remove();
	},'json');
	return false;
})
;