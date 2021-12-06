$(document)
.ready(function(e,u){
	
})
.tooltip()
.on('click','#btnSignin',function(){
	$('#userid').val($.trim($('#userid').val()));
	if($('#userid').val()==''){
		alert('아이디를 입력하십시오.'); return false;
	}
	if($('#passcode').val()==''){
		alert('비밀번호를 입력하십시오.'); return false;
	}
	let oParam={optype:'signin',userid:$('#userid').val(),passcode:$('#passcode').val()};
	console.log(oParam);
	$.post('/bbs/_signin.php',oParam,function(answer){
		console.log(answer);
		if(parseInt(answer['result'])<0) {
			alert(answer['msg']); return false;
		}
		$('#tblSignin').hide();
		$('#nickname').text(answer['nickname']);
		$('#last_logout').text(answer['last_logout']);
		$('#tblInfo').show();
		$('#btnNewPost').show();
	},'json');
	return false;
})
.on('click','#btnCancel',function(){
	$('#userid,#passcode').val('');
	return false;
})
.on('click','#tdLogout',function(){
	if(!confirm('로그아웃할까요?')) return false;
	$.post('/bbs/_signin.php',{optype:'logout'},function(answer){
		console.log(answer);
		if(parseInt(answer['result'])<0) {
			alert(answer['msg']); return false;
		}
		$('#tblSignin').show();
		$('#tblInfo').hide();
		$('#btnNewPost').hide();
	})
	return false;
})
;