var endtime=[];

$(document)
.ready(function(){
	if($('#student').val()!=""){
		$.cookie("studentname",$('#student').val());
	} else {
		$('#student').val($.cookie("studentname"));		
		$('#studentname').html('<h1><b>'+$('#student').val()+'</b></h1>');
	}
	refreshPage();
	 setInterval(refreshPage,5000);
	$('#msg2student').dialog({modal:true,autoOpen:false,
		buttons: [
		    {
		      text: "확인",
		      icon: "ui-icon-heart",
		      click: function() {
		        $( this ).dialog( "close" );
		      }
		    }
		  ]});
})
/*.on('blur','#tvid,#tvkey',function(){
	$.post('_ncs_sv.php',{optype:'tv',id:this.id,tvid:$('#tvid').val(),tvkey:$('#tvkey').val(),name:$('#student').val()},function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
	},'json');
	
})*/
.on('click','li',function(){
	console.log($(this).text());
	return false;
})
.on('click','#btnRefresh',function(){
	refreshPage();
	return false;
})
.on('click','#whether',function(){
	var status;
	var me=$(this);
	switch($(this).text()){
	case "완료":
		if(!confirm('완료상태를 작업중으로 바꾸시겠습니까?')) return false;
		status='done'; break;
	case "확인중":
		status='checking'; break;
	default:
		status='working'; break;		
	}
	$.post('_ncs_sv.php',{optype:'setmission',status:status,class:$('#classid').val(),student:$('#student').val(),drill:$(this).prev().text()},function(json){
		console.log(json);
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		me.removeClass(status);
		me.addClass(json['data']);
		switch(json['data']){
		case 'working': me.text('작업중');break;
		case 'checking': me.text('확인중'); break;
		case 'done': me.text('완료');
		}
	},'json');
	return false;
})
;

function refreshPage(){
	let oParam={optype:'refresh',student:$('#student').val(),class:$('#classid').val()};
	console.log(oParam);
	$.post('_refresh.php',oParam,function(json){
		$('#tblMission tbody').empty().append(json['data']);
		console.log(json);
		if(json['msg2student']=='') return false;
		$('#msg2student').dialog({title:json['msg2student']}).dialog('open');
	},'json');
	return false;
}

