$(document)
.ready(function(){
	if (typeof console === "undefined" || typeof console.log === "undefined") {
		console = {};
		console.log = function(msg) {
		     alert(msg);
		};
	}
//	setlog(-1);
	if( $.cookie('saint_userid') === undefined )
		$('#userid').val('ildotaekwondo12@gmail.com');
	else $('#userid').val($.cookie('saint_userid'));
	if( $.cookie('saint_passcode') === undefined )	
		$('#passcode').val('428669');
	else $('#passcode').val($.cookie('saint_passcode'));
	var pYearRange = (gdToday.getFullYear()-99).toString()+':'+(gdToday.getFullYear()+10).toString();
	$('#birth_date').datepicker({
		changeMonth:true, changeYear:true
		,numberOfMonth:2
		,showOn:"button"
//		,buttonImage:'image/calendar.gif', buttonImageOnly: true, buttonText: 'Select Date'
		,dateFormat:'yy-mm-dd'
		,yearRange: pYearRange
	});
	$('#divBrandnew').dialog({
		autoOpen:false
		,title:'New Party',
		open:function(e,u){
			$('#partyname').empty();
			$('#selSector').val('');
		}
		,buttons:[{
				text:'Add New',
				click:function(){
					$.post('_addnewparty.php',{partyname:$('#partyname').val(),sector:$('#selSector').val()},function(json){
						console.log(json);
						if(json['result']!='0'){
							alert(json['msg']); return false;
						}
						alert(json['msg']);
						$('#selParty').append('<option value='+json['party']+'>'+$('#partyname').val()+'</option>');
						alert(json['party']);
						$('#selParty').val(json['party']).trigger('change');
					},'json');
					$(this).dialog('close');
				}
			},{
				text:'Cancel',
				click:function(){
					$(this).dialog('close');
				}
			}
		]
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
/****************************************************************/
/**** Login                                                                                               */
/****************************************************************/
.on('blur','#partyname',function(e){
	
})
/****************************************************************/
/**** Login                                                                                               */
/****************************************************************/
.on('click','#btnLogin',function(e){
	if( $('#userid').val() == '' ) {
		alert('User ID should be filled.');
		return false;
	}
	if( $('#passcode').val() == '' ) {
		alert('passcode should be given.');
		$('#passcode').focus();
		return false;
	}
	strDatetime = getDatetime();
	$.post('_login.php',{
		userid:$('#userid').val(),passcode:$('#passcode').val(),timezone:strDatetime
	},function(json){
		console.log(json);
		if( json['result']!='0' ){
			alert(json['msg']); return false;
		}
		$.cookie('saint_userid',$('#userid').val(),{expires:10000});
		$.cookie('saint_passcode',$('#passcode').val(),{expires:10000});
		$('#selParty').empty();
		$.each(json['party'],function(ndx,val){
			var pstr=val.split(',');
			$('#selParty').append('<option value='+pstr[0]+'>'+pstr[1]+'</option>');
		});
//		$('#selParty').append('<option value=new>&gt;&gt;&gt;&nbsp;새로 가입</option>');
		$('#selParty').fadeIn();
	},'json');
	return false;
})
/****************************************************************/
/**** Party change                                                                                    */
/****************************************************************/
.on('change','#selParty',function(){
	if($(this).val()=='') return false;
	if($(this).val()!='new'){
		var me=$(this);
		// alert('party ['+me.val()+']');
		$.post('_crmctl.php',{_e:me.val()},function(json){
			if(json['result']!='0'){
				alert(json['msg']); return false;
			}
//			alert('crmctl.php?_p='+json['firstpage']);
			document.location='crmctl.php?_p='+json['firstpage'];
		},'json');
	} else {
		$('#divBrandnew').dialog('open');
	}
	return false;
})
/****************************************************************/
/**** leave from UserID1                                                                           */
/****************************************************************/
.on('blur','#userid1',function(){	// check duplicated userID.
	var pstr = $.trim($(this).val());
	if( pstr == '' ) return false;
	var me=$(this);
	$.post('dbwork.php',{optype:'duplicate',userid:$.trim(me.val())},function(json){
		if(json['result']!='0'){
			alert(json['msg']); 
			me.val('').focus();
			return false;
		}
		$('#check_userid').val('1');
	},'json');
	return false;
})
/****************************************************************/
/**** Mouse click on UserID or Passcode field                                              */
/****************************************************************/
.on('click','#userid,#passcode',function(){
	this.select();
});

$('#btnFind').on('click',function(){
});

$('#btnSubmit').click(function(){
	if( $.trim($('#userid1').val()) == '' ) {
		alert('[userid] should be given.');
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
//	if( $.trim($('#member_name').val()) == '' ) {
//		alert('[member_name] should be given.');
//		return false;
//	}
	$.post('dbwork.php',{optype:'newmember',userid:$('#userid1').val(),passcode:$('#passcode1').val()},function(json){
		console.log(json);
		if(json['result']!='0'){
			alert(json['msg']); return false;;
		}
		alert(json['msg']);
		document.location='/';
	},'json');
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