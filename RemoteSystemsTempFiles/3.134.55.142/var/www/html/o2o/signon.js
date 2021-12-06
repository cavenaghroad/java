$(document)
.ready(function(){
	$.post('_signon.php',{optype:'party'},function(answer){
		console.log(answer);
		let retval=parseInt(answer['result']);
		if(retval<0) {
			console.log(answer['msg']);
			if(retval==-99) document.location='/';
			return false;
		}
		let pstr;
		$.each(answer['data'],function(ndx,rec){
			pstr=`<option value='${rec['party']}'>${rec['name_kor']}</option>`;
			$('#selParty').append(pstr);
		});
	},'json');	
})
.on('click','#btnLogin',function(){
	console.log('btnlogin')
	let name=$.trim($('#name').val());
	if(name==''){
		alert('이름을 입력하십시오.');
		return false;
	}
	let mobile=$.trim($('#mobile').val());
	if(mobile==''){
		alert('모바일 번호를 입력하십시오.');
		return false;
	}
	let passcode=$('#passcode').val();
	if(passcode!=$.trim($('#passcode').val())){
		alert('비밀번호는 공란을 포함할 수 없습니다.');
		return false;
	}
	if(passcode.length<6){
		alert('비밀번호는 6자 이상이어야 합니다.')
		return false;
	}
	if(passcode!=$('#passcode1').val()){
		alert('비밀번호를 확인하고 다시 입력하십시오.');
		return false;
	}
	if($('#selParty').val()=='0'){
		alert('소속단체를 선택하십시오.');
		return false;
	}
	oParam={optype:'enter',name:name,mobile:mobile,passcode:passcode,party:$('#selParty').val()};
	console.log(oParam);
	$.post('_signon.php',oParam,function(answer){
			console.log(answer);
			if(parseInt(answer['result'])<1){
				alert('회원가입이 되지 않았습니다. 고객센터에 확인하십시오.');
				return false;
			}
			alert('가입을 환영합니다. 로그인페이지로 이동합니다.');
			document.location='/index.php';
		},'json');
	return false;
})
;