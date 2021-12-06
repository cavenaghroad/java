var endtime=[];

$(document)
.ready(function(){
	refreshPage();
	 setInterval(refreshPage,5000);
})
.on('click','#tblMission tbody tr td',function(){
	if($(this).index()!=1) return false;
	me=$(this).parent();
	op={classid:$('#classid').val(),student:$('#student').val(),name:me.find('td:eq(0)').text()};
	$.post(url_setmission,op,function(result){
		if(me.find('td:eq(1)').text()=='작업중'){
			me.find('td:eq(1)').text('확인중');
		} else {
			me.find('td:eq(1)').text('작업중');
		}
	},'json');
	return false;
})
.on('click','li',function(){
	return false;
})
.on('click','#btnRefresh',function(){
	refreshPage();
	return false;
})
;

function refreshPage(){
	$.post(url_refresh,{student:$('#student').val(),classid:$('#classid').val()},function(result){
		if($.isEmptyObject(result)) return false;
		$('#tblMission tbody').empty();
		$.each(result['list'],function(ndx,rec){
			$('#tblMission tbody').append(`<tr><td style="border:1px solid yellow">${rec['name']}</td><td style="border:1px solid yellow;cursor:pointer;">작업중</td></tr>`);
		});
		$('#tblMission tbody tr').each(function(){
			$(this).find('td:eq(1)').text('작업중').removeClass('submitted done');
		});
		$.each(result['done'],function(ndx,rec){
			$('#tblMission tbody tr').each(function(){
				if($(this).find('td:eq(0)').text()==rec['name']) {
					$(this).find('td:eq(1)').text('완료').addClass('done');
					return false;
				}
			});
		});
		$.each(result['submit'],function(ndx,rec){
			$('#tblMission tbody tr').each(function(){
				if($(this).find('td:eq(0)').text()==rec['name']) {
					$(this).find('td:eq(1)').text('확인중').addClass('submitted');
					return false;
				}
			});
		});
	},'json');
	return false;
}

