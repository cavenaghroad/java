let today={};
$(document)
.ready(function(){
	let dt=new Date();
	today.year=dt.getFullYear();
	today.month=dt.getMonth()+1;
	today.date=dt.getDate();
	today.hour=dt.getHours();
	today.minute=dt.getMinutes();
	
	$.post('/bbs/_list.php',{optype:'notice'},function(answer){
		console.log(answer);
		if(parseInt(answer['result'])==-1) return false;
		let pstr='';
		$.each(answer['data'],function(ndx,rec){
			pstr+=`<tr style="height:24px;" rowid=${rec['rowid']}><td>${showCreated(rec['created'])}</td>`+
			`<td>${rec['title']}</td><td>${rec['writer']}</td><td>${rec['good']}</td><td>${rec['readcount']}</td></tr>`;
		});
		$('#bbs0').append(pstr);
	},'json');
	$.post('/bbs/_list.php',{optype:'free'},function(answer){
		console.log(answer);
		if(parseInt(answer['result'])==-1) return false;
		
		let pstr='';
		$.each(answer['data'],function(ndx,rec){
			pstr+=`<tr style="height:24px;" rowid=${rec['rowid']}><td>${showCreated(rec['created'])}</td>`+
			`<td>${rec['title']}</td><td>${rec['writer']}</td><td>${rec['good']}</td><td>${rec['readcount']}</td></tr>`;
		});
		$('#bbs1').append(pstr);
	},'json');
	$.post('/bbs/_list.php',{optype:'qna'},function(answer){
		console.log(answer);
		if(parseInt(answer['result'])==-1) return false;
		
		let pstr='';
		$.each(answer['data'],function(ndx,rec){
			pstr+=`<tr style="height:24px;" rowid=${rec['rowid']}><td>${showCreated(rec['created'])}</td>`+
			`<td>${rec['title']}</td><td>${rec['writer']}</td><td>${rec['good']}</td><td>${rec['readcount']}</td></tr>`;
		});
		$('#bbs2').append(pstr);
	},'json');
	$.post('/bbs/_list.php',{optype:'qna'},function(answer){
		console.log(answer);
		if(parseInt(answer['result'])==-1) return false;
		
		let pstr='';
		$.each(answer['data'],function(ndx,rec){
			pstr+=`<tr style="height:24px;" rowid=${rec['rowid']}><td>${showCreated(rec['created'])}</td>`+
			`<td>${rec['title']}</td><td>${rec['writer']}</td><td>${rec['good']}</td><td>${rec['readcount']}</td></tr>`;
		});
		$('#bbs3').append(pstr);
	},'json');
})
.on('click','#btnReset',function(){
	$('#mobile,#passcode').val('');
	return false;
})
.on('click','#btnLogin',function(){
	if($.trim($('#mobile').val())=='') alert('mobile is empty');
	else if($.trim($('#passcode').val())=='') alert('passcode is empty');
	else {
/*		$.post("gateway.php",{optype:'login',mobile:$('#mobile').val(),passcode:$('#passcode').val()},function(answer){
			console.log(answer);
			if(answer['result']!="0"){
				alert(answer['msg']); return false;
			}
			document.location=answer['url'];
		},'json');
*/	
		frmLogin.submit();
	}
	return false;
})
.on('click','#showFlow',function(){
	$('#divFlow').show();
	return false;
})
.on('click','#divFlow',function(){
	$(this).hide();
	return false;
})
.on('click','#bbs0 tr,#bbs1 tr,#bbs2 tr,#bbs3 tr',function(){
	document.location='bbs/view.php?rowid='+$(this).attr('rowid');
	return false;
})
;
function showCreated(pstr){
	let n=today.year-parseInt(pstr.substr(0,4));
	if(n!=0) return n+'년전';
	n=today.month-parseInt(pstr.substr(4,2));
	if(n!=0) return n+'달전';
	n=today.date-parseInt(pstr.substr(6,2));
	if(n!=0) return n+'일전';
	n=today.hour-parseInt(pstr.substr(8,2));
	if(n!=0) return n+'시간전';
	return pstr.substr(10,2)+':'+pstr.substr(12,2);
}