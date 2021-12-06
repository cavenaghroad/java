$(document)
.ready(function(){
	if (typeof console === "undefined" || typeof console.log === "undefined") {
		console = {};
		console.log = function(msg) {
		     alert(msg);
		};
	}
	setlog(-1);
	if( $.cookie('saint_userid') === undefined )
		$('#userid').val('ildotaekwondo12@gmail.com');
	else $('#userid').val($.cookie('saint_userid'));
	if( $.cookie('saint_passcode') === undefined )	
		$('#passcode').val('428669');
	else $('#passcode').val($.cookie('saint_passcode'));
	$('#dvParty').dialog({
		autoOpen:false,
		resizeable:false,
		height:330,width:550,
		'open':function(e,ui){
			$.post('_xdbwork.php',{optype:'loadparty'},function(json){
				if(json['result']!='0'){
					alert(json['msg']); return false;
				}
				$('#selParty').empty().append(json['html']);
			},'json');
		},
		buttons:{
			'Select':function(){
				$(this).dialog('close');
				// find the detail of party with enteprise ID.
				$.ajax({
					data:'optype=loadparty&party='+$('#party').val(),
					datatype:"xml",	
					beforeSend:function(){
						wlog('optype=loadparty&party='+$('#party').val());
					},
					success:function(xml) {
						$('#name_kor').val($(xml).find('name_kor').text());
					}
				});

			},
			'Cancel':function(){
				$(this).dialog('close');
			}
		}
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
})
.on('keydown','#userid',function(e){
	switch( e.keyCode || e.which )	{
//	case KEY_TAB:
	case KEY_ENTER:
		$('#passcode').select();
//		return false;
	default:
//		if( isAlphaNumeric(e.keyCode) || isSymbol(e.keyCode) )	return true;
	}
	return true;
})
.on('keydown','#passcode',function(e){
	switch( e.keyCode || e.which )	{
	case KEY_ENTER:
		$('#btnLogin').click();
//		return false;
	default:
//		if( isAlphaNumeric(e.keyCode) || isSymbol(e.keyCode) )	return true;
	}
	return true;
})
.on('click','#btnLogin',function(e){
	if( $('#userid').val() == '' ) {
		alert('User ID should be filled.');
		return false;
	}
//	if( !validateEmail($('#userid').val()) ) {
//		alert('invalid_email_format');
//		return false;
//	}
	if( $('#passcode').val() == '' ) {
		alert('passcode should be given.');
		$('#passcode').focus();
		return false;
	}
	strDatetime = getDatetime();
	alert('btnLogin');
	$.post('_xdbwork.php',{
		optype:'login',userid:$('#userid').val(),passcode:$('#passcode').val(),timezone:strDatetime
	},function(json){
		if( json['result']!='0' ){
			alert(json['msg']); return false;
		}
		$.cookie('saint_userid',$('#userid').val(),{expires:10000});
		$.cookie('saint_passcode',$('#passcode').val(),{expires:10000});
		document.location = 'gateway.php';
	},'json');
	return false;
})
.on('blur','#userid1',function(){	// check duplicated userID.
	var pstr = $.trim($(this).val());
	if( pstr == '' ) return false;
	var me=$(this);
	$.ajax({
		data:'optype=duplicate&userid='+pstr,
		datatype:'text',
		beforeSend:function(){
			wlog('optype=duplicate&userid='+pstr); 
		},
		success:function(_return){
			if( _return != '' ){
				alert(_return);
				me.select();
				setTimeout(function(){me.val('').focus();},2000);
			}
		}
	});
})
.on('click','#userid,#passcode',function(){
	this.select();
});

$('#btnFind').on('click',function(){
	$('#dvParty').dialog('open');
});

$('#passcode2').on('keydown',function(){
	if( $('#passcode1').val() == $('#passcode2').val() ) $('#lblPasscode').text('Equal!');
	else $('#lblPasscode').text('');
});

$('#selParty').on('click',function(){
	$('#party').val($(this).find('option:selected').text());
	$('#party').val($(this).find('option:selected').val());
});
$('#btnEmpty').on('click',function(){
	$('#party').val('');
	$('#address,#postcode,#phone,#homepage,#facebook,#selSector').val('').prop('readonly','');
	$('#name_kor').val('').select();
});
$('#btnRegister').click(function(){
	if( $.trim($('#userid1').val()) == '' ) {
		alert('[userid] should be given.');
		return false;
	}
	if( $('#userid1').val() != $('#userid2').val() ){
		alert('Please verify UserID.');
		return false;
	}
	if( $.trim($('#passcode1').val()) == '' ) {
		alert('[password should be given.');
		return false;
	}
	if( $('#passcode1').val() != $('#passcode2').val() ){
		alert('Please verify Password.');
		return false;
	}
	if( $.trim($('#member_name').val()) == '' ) {
		alert('[member_name] should be given.');
		return false;
	}
	if( $.trim($('#birth_date').val()) == '' ) {
		alert('[email] should be given.');
		return false;
	}
	if( $.trim($('#mobile').val()) == '' ) {
		alert('[mobile Number] should be given.');
		return false;
	}
	if( $.trim($('#name_kor').val()) == '' ) {
		alert('[name] should be given.');
		return false;
	}
	if( $.trim($('#sector').val()) == '' ) {
		alert('Industry should be chosen.');
		return false;
	}
	var pstr, level=2;
	if( $('#party').val() == '' ){
		$.ajax({
			data:'optype=newparty&name_kor='+$('#name_kor').val()+'&sector='+$.trim($('#sector').val())+'&userid1='+$('#userid1').val(), 
			datatype:"text",async:false,
			beforeSend:function(){
				wlog('optype=newparty&name_kor='+$('#name_kor').val()+'&sector='+$.trim($('#sector').val())+'&userid1='+$('#userid1').val());
			},
			success:function(_return) {
				if( _return == '' ) {
					pstr ='failed to create new party.';
					alert(pstr);
					return false;
				}
				$('#party').val(_return);
				alert('party ['+_return+']');
			},
			complete:function(){
				level=0;
			}
		});
	}
	if( $('#party').val() != '' ){
		$.ajax({
			data:'optype=newmember&member_name='+$('#member_name').val()+'&mobile='+$('#mobile').val()+'&userid='+$('#userid1').val()+
				'&passcode='+$('#passcode1').val()+'&member_name='+$('#member_name').val()+'&birth_date='+$('#birth_date').val(),
			datatype:'text',async:false,
			beforeSend:function(){
				wlog('optype=newmember&member_name='+$('#member_name').val()+'&mobile='+$('#mobile').val()+'&userid='+$('#userid1').val()+
						'&passcode='+$('#passcode1').val()+'&member_name='+$('#member_name').val()+'&birth_date='+$('#birth_date').val());
			},
			success:function(_return){
				if( _return == '' ) {
					pstr = 'failed to create new member.';
					alert(pstr);
					return false;
				}
				$('#member_id').val(_return);
				alert('member_id ['+_return+']');
				pstr = 'New Member has been added.';
			},
			complete:function(){}
		});
	}
	if( $('#party').val() != '' &&  $('#member_id').val() != '' ){
		$.ajax({
			data:'optype=mem_ent&party='+$('#party').val()+'&member_id='+$('#member_id').val()+'&level='+level,
			datatype:'text',async:false,
			beforeSend:function(){
				wlog('optype=mem_ent&party='+$('#party').val()+'&member_id='+$('#member_id').val()+'&level='+level);
			},
			success:function(_return){
				if( _return == '' ){
					pstr = 'failed to add new member to party.';
					alert(pstr);
					return false;
				}
				pstr = 'complete successfully !!!';
				alert(pstr);
				document.location = 'index.php';
			},
			complete:function(){}
		});
	}
});

function setLang(pstr){
	$.cookie('lingua',pstr,{expires:10000});
	document.location=BaseFolder+'/index.php?_l='+pstr;
}

$(window).resize(function(){
	var nHeight = $(window).height();
	
	console.log('nHeight ['+nHeight+']');
	$('#dvCenter').closest('td').css({'height':(nHeight-100)+'px'});
}).load(function(){
	$(window).resize();
})
;