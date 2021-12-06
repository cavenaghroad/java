$(document)
.ready(function(){
	if (typeof console === "undefined" || typeof console.log === "undefined") {
		console = {};
		console.log = function(msg) {
		     alert(msg);
		};
	}	
	$.ajaxSetup({
		url:'dbwork.php', 
		cache:false,
		async:true,
		type:'POST'
	});
	$('input[type=button]').button();
	var pYearRange = (gdToday.getFullYear()-99).toString()+':'+(gdToday.getFullYear()+10).toString();
	$('#birth_date').datepicker({
		changeMonth:true, changeYear:true
		,numberOfMonth:2
		,showOn:"button"
//		,buttonImage:'image/calendar.gif', buttonImageOnly: true, buttonText: 'Select Date'
		,dateFormat:'yy-mm-dd'
		,yearRange: pYearRange
	});
	$('#tabs').tabs({
		heightStyle:'auto'
		
	});
})
.on('click','#btnFind1',function(){
	$.ajax({
		data:'optype=findid&member_name='+$('#member_name').val()+'&birth_date='+$('#birth_date').val()+'&mobile='+$('#mobile').val(),
		datatype:'text',
		beforeSend:function(){
			wlog('optype=findid&member_name='+$('#member_name').val()+'&birth_date='+$('#birth_date').val()+'&mobile='+$('#mobile').val());
		},
		success:function(_return){
			if( _return != '' ) $('#userid').val(_return);
			else {
				alert('Cannot find any User ID. Please check if any of fields is incorrect.')
			}
		}
	});
})
.on('click','#btnFind2',function(){
	$.ajax({
		data:'optype=findpassword&member_name='+$('#member_name').val()+'&birth_date='+$('#birth_date').val()+'&mobile='+$('#mobile').val()+
		'&email='+$('#email').val(),
		dataType:'text',
		beforeSend:function(){
			wlog('optype=findpassword&member_name='+$('#member_name').val()+'&birth_date='+$('#birth_date').val()+'&mobile='+$('#mobile').val()+
					'&email='+$('#email').val());
		},
		success:function(_return){
			if( _return == '' ) alert('Password was sent to the email ['+$('#email').val()+']');
			else alert(_return);
		}
	});
})
;
