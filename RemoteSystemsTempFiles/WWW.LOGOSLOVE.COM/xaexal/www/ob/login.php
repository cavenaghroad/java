<?
require_once 'header.php';
require_once 'include.php';
?>
<body style='background-image: url("/images/IMG_3899a.png");background-repeat: no-repeat;
    background-attachment: fixed;
    background-position: center; background-size:cover;'>
<table width=700px align=center valign=middle>
<tr height=160px><td>&nbsp;</td></tr>
<tr height=128px>
	<td align=center><img src='/images/logo_logoslove.png'></td>
</tr>
<tr>
	<td align=center>
		<table border=0 style='background-color:white' id=tbllogin>
			<tr>
				<td colspan=2 style="font-size:24px">스마트폰</td>
			</tr>
			<tr>
				<td colspan=2><input type=text class=mobile maxlength=20 size=20 style="font-size:24px"></td>
			</tr>
			<tr>
				<td colspan=2 style="font-size:24px">비밀번호</font></td>
			</tr>
			<tr>
				<td colspan=2><input type=password class=passcode maxlength=20 size=20 style="font-size:24px"></td>
			</tr>
			<tr>
				<td colspan=2><label id=lblWarn style='color:red;'></label></td>
			</tr>
			<tr height=80px>
				<td valign=bottom><a href='register.php'><button class=btnRegister style='font-size:24px'>회원가입</button></a></td>
				<td align=right valign=bottom><button id=btnLogin style="font-size:24px">로그인</button></td>
			</tr>
			<tr>
				<td align=right colspan=2>
					<a href='/ob/forgot.php'>비밀번호가 기억나지 않나요?</a>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
<div id=dvParty style='display:none;'>
<table id=tblParty>
<thead>
<tr>
	<th style='background-color:black;'><font color=white>업체/단체</font></th><th style='background-color:black;color:white;'>관리자 권한</th>
</tr>
</thead>
<tbody>
</tbody>
</table>
</div>
<script language='javascript'>
$(document)
.ready(function(){
// 	alert(';j;lkj');
})
.on('focus','input[type=text],input[type=password]',function(){
	$(this).select();
	return false;
})
.on('keydown','.passcode,.mobile',function(e){
	if(e.keyCode==13 && $.trim($('.mobile').val()) !='' && $.trim($('.passcode').val())!='') {
		$('#btnLogin').click();
	}
	$('#lblWarn').html('');
})
.on('keydown','.mobile,.passcode1,.passcode2',function(e){
	if(e.keyCode!=13) return true;

	$('#lblWarn').html('');
	if($.trim($('.mobile').val())=='') return false;
	else if($.trim($('.passcode1').val())=='') return false;
	else if($.trim($('.passcode2').val())=='') return false;
	else $('#btnLogin').click();	
})
.on('click','#btnLogin',function(){
	$.post('_login.php',{
		optype:'login',
		mobile:$('.mobile').val(),
		passcode:$('.passcode').val()
	},function(json){
// 		alert('['+json['success']+'/'+json['message']+']');
		if(json['success']=='0') {	// valid login info and service is not expired.
			document.location='admin.php';
		} else if(json['success']=='-1' && json['message']!=''){		// invalid login info.
			$('#lblWarn').html(json['message']);
			var l = 20;  
			for(var n=0;n<10;n++){
				$('#tbllogin').animate( { 
			         'margin-left': '+=' + ( l = -l ) + 'px',
			         'margin-right': '-=' + l + 'px'
			      }, 50);  
			}
			return false;
		} else if(json['success']=='2'){
			// If the user belongs to two or more parties, this dialog is shown for the user to choose one of parties to login. 
			$('#dvParty').dialog({
				open:function(e,ui){
					$('#tblParty>tbody').empty();
					var pstr='';
					$.each(json['record'],function(key,value){
						pstr+='<tr>';
						switch(key){
						case 'rowid':
							pstr+='<td style="display:none;">'+value+'</td>'; break;
						case 'party':	
							pstr+='<td>'+value+'</td>'; break;
						case 'admin_level':
							pstr+='<td>'+value+'</td>'; break;
						}
						pstr+='<td align=center><button id=btnParty>선택</button></td></tr>';
					});
					$('#tblParty>tbody').append(pstr);
					return false;
				},
				close:function(e,ui){
					$(this).dialog('close');
				},
				buttons:[{
					text:'닫기',
					click:function(){
						$(this).dialog('close');
					}
				}]
			});
			
		} else {	// valid login info but service was expired.
			document.location='service_expiry.php';
			return false;
		} 
	},'json');
	return true;
})
.on('click','#btnParty',function(){
	var curTR=$(this).closest('tr');
	var rowid=curTR.find('td:eq(0)').text();
	wlog('rowid [',rowid,']');
	$.post('_login.php',{
		optype:'choose-party',rowid:curTR.find('td:eq(0)').text(), 
		partyname:curTR.find('td:eq(1)').text(), admin_level:curTR.find('td:eq(2)').text()
	},function(json){
		if(json['success']=='0'){
			document.location='admin.php';
		} else {
			alert(json['message']);
		}
	},'json');
	return false;
});
</script>