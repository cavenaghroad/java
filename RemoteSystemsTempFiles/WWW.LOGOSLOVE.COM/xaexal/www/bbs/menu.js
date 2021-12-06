$(document)
.ready(function(){
	$('#dvGuide').dialog({
		autoOpen:false,
		closeOnEscape:true
	});
})
.on('click','#doLogout',function(){
	if(!confirm('정말로 로그아웃하시겠습니까?'))	return false;
	$.post('dbwork.php',{
		optype:'logout'
	},function(json){
		if(json['msg']!=''){
			alert(json['msg']); return false;
		}
		alert('로그아웃 되었습니다.');
		document.location='/bbs';
	},'json');
})
.on('click','#doLogin',function(){
	$.post('dbwork.php',{
			optype:'login', userid:$('#userid').val(), passcode:$('#bibeon').val()
	},
	function(json){
		if(json['msg']!=''){
			alert(json['msg']);
			return false;
		}		
		location.reload();
	},'json');
})
.on('keydown','#userid,#bibeon',function(e,u){
	if(e.keyCode==13){
		$('#doLogin').click();
		return false;
	}
})
;

function showGuide(){
	$('#dvGuide').dialog('open');
}
