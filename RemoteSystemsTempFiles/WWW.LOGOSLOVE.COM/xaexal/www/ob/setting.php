<div id=setting>
<input type=hidden id=rowid name=rowid />
<table>
<tr>
	<td valign=top>
		<table>
		<tr>
			<td align=right>매장 공식명칭 </td>
			<td><input type=text id=party-name size=40 maxlength=40></td>
		</tr>
		<tr>
			<td align=right>우편번호 </td>
			<td><input type=text id=party-postcode size=10 maxlength=10></td>
		</tr>
		<tr>
			<td align=right valing=top>주소 </td>
			<td><textarea id='party-addr' rows=2 cols=40></textarea></td>
		</tr>
		<tr>
			<td align=right>매장 유선 전화번호 </td>
			<td><input type=tel id=party-phone size=20 maxlength=20></td>
		</tr>
		<tr>
			<td align=right>매장대표자 전화번호 </td>
			<td><input type=tel id=party-mobile size=20 maxlength=20></td>
		</tr>
		<tr>
			<td align=right>최근 로고업로드 시각</td>
			<td><label id=logo_updated></label></td>
		</tr>
		<tr>
			<td align=right>매장 대표로고 등록</td>
			<td><input type=file id=logofile accept='.jpg,.png,.jpeg,.gif'><button id=btnUpload style='display:none;'>Upload</button></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<table style='height:200px;'>
				<tr>
					<td>
						<img id=imgLogo src='./logo/logonotfound.png' style='width:200px;height:200px;'>
					</td>
					<td valign=top>
						<button id=btnRemoveImage>로고 지우기</button>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		</table>
	</td>
	<td valign=top>
		<table>
		<tr>
			<td align=right>와이파이 아이디 </td>
			<td><input type=text id=wifi maxlength=256 size=20></td>
		</tr>
		<tr>
			<td align=right>영업시작 시각</td>
			<td><input type=text id=business_start size=5></td>
		</tr>
		<tr>
			<td valign=top align=right>주문접수 방식</td>
			<td>
				<table border=1>
				<tr>
					<td>
						<table border=0>
						<tr>
							<td width=150px valign=top><input type=checkbox id=chkInhouse>매장내 주문</input></td>
							<td><label id=_inhouse style='display:none'><!-- <input type=checkbox id=chkInhousepay>주문시 결제가능</input>&nbsp;&nbsp; -->
								테이블 수&nbsp;
								<input type=number id=party-tablecount size=3 maxlength=3 style='width:40px;text-align:right;' min=0 value=0>&nbsp;개</label></td>
					</tr></table>
					</td>
				</tr>
				<tr>
					<td>
						<table border=0>
						<tr>
							<td width=150px valign=top><input type=checkbox id=chkDelivery>배달 주문</input></td>
							<td><label id=_delivery style='display:none'><!-- <input type=checkbox id=chkDeliverypay>주문시 결제가능</input>&nbsp;&nbsp; -->
								배송비용&nbsp;
								<input type=number id=delivery_fee name=delivery_fee size=5 maxlength=5 style='width:60px;text-align:right;' min=0 value=0>&nbsp;원</label>
							</td>
						</tr></table>
					</td>
				</tr>
				<tr>
					<td>
						<table border=0>
						<tr>
							<td width=150px><input type=checkbox id=chkTakeout>테이크아웃 주문</input></td>
							<td><label id=_takeout style='display:none'><!-- <input type=checkbox id=chkTakeoutpay>주문시 결제가능</input> --></label>&nbsp;</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border=0>
						<tr>
							<td width=150px><input type=checkbox id=chkBook>예약 주문</input></td>
							<td><label id=_book style='display:none'><!-- <input type=checkbox id=chkBookpay>주문시 결제가능</input> --></label>&nbsp;</td>
						</tr></table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr height=20px><td colspan=2>&nbsp;</td></tr>
		<tr>
			<td colspan=2 align=center><button id=btn_submit>적용</button>&nbsp;<button id=btn-cancel>취소</button>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</div>
<script language=javascript>
$(document)
.ready(function(){
	$('#business_start').timepicker({
			'timeFormat': 'H:i'
	});
	// fetch the existing info of this party.
	$.post('work_read.php',{optype:'setting'},function(json){
		if(json['success']!='0') {
			alert(json['message']);
			return false;
		}
		
		$.each(json,function(key,value){
// 			wlog(key+' ['+value+']');
			switch(key){
			case 'rowid': $('rowid').val(value); break;
			case 'party': $('#party-name').val(value); break;
			case 'postcode': $('#party-postcode').val(value); break;
			case 'address': $('#party-addr').val(value); break;
			case 'table_count':$('#party-tablecount').val(value); break;
			case 'phone': $('#party-phone').val(value); break;
			case 'mobile': $('#party-mobile').val(value); break;
			case 'wifi': $('#wifi').val(value); break;
			case 'business_start':$('#business_start').val(value); break;
			case 'delivery_fee':$('#delivery_fee').val(value); break;
			case 'order_inhouse': 
				$('#chkInhouse').prop('checked',(value=='1')?true:false);
				break;
			case 'order_takeout':
				$('#chkTakeout').prop('checked',(value=='1')?true:false);
				break;
			case 'order_delivery':
				$('#chkDelivery').prop('checked',(value=='1')?true:false);
				break;
			case 'order_book':
				$('#chkBook').prop('checked',(value=='1')?true:false);
				break;
			case 'pay_inhouse':
				$('#chkInhousepay').prop('checked',(value=='1')?true:false);
				break;
			case 'pay_takeout':
				$('#chkTakeoutpay').prop('checked',(value=='1')?true:false);
				break;
			case 'pay_delivery':
				$('#chkDeliverypay').prop('checked',(value=='1')?true:false);
				break;
			case 'pay_book':
				$('#chkBookpay').prop('checked',(value=='1')?true:false);
				break;
			case 'logo_updated':
				if(!isEmpty(value)){
					var pstr = value.substr(0,4)+'-'+value.substr(4,2)+'-'+value.substr(6,2)+' '+value.substr(8,2)+':'+value.substr(10,2)+':'+value.substr(12,2);
					$('#logo_updated').text(pstr);
				}
				break;
			case 'logo_image':
				if(isEmpty(value)){
					$('#imgLogo').attr('src','./logo/logonotfound.png');
				} else {
					$('#imgLogo').attr('src','./logo/'+value);
				}
			}
		});
	},'json')
	.always(function(){
		if($('#chkInhouse').is(':checked')) $('#_inhouse').show();
		if($('#chkDelivery').is(':checked')) $('#_delivery').show();
		if($('#chkTakeout').is(':checked')) $('#_takeout').show();
		if($('#chkBook').is(':checked')) $('#_book').show();
		
	});
})
// Save the party info.
.on('click','#btn_submit',function(){
	$('input,textarea').each(function(){
		$(this).val($.trim($(this).val()));
	});
	if($('#party-name').val()=='') {
		alert('매장의 공식명칭을 입력하십시오');
		return false;
	}
	if($('#chkInhouse').is(':checked')){
		var tcount=$('#party-tablecount').val();
		if(tcount=='' || isNaN(tcount)){
			alert('테이블 갯수를 입력하십시오');
			return false;
		}
	}
	if($('#chkDelivery').is(':checked')){
		var dfee=$('#delivery_fee').val();
		if(dfee=='' || isNaN(dfee)){
			alert('1회당 배송비용을 입력하십시오');
			return false;
		}
	}
	$.post('_admin.php',{
		optype:'setting',
		rowid:$('rowid').val(),
		party:$('#party-name').val(), phone:$('#party-phone').val(),mobile:$('#party-mobile').val(),
		postcode:$('#party-postcode').val(), addr:$('#party-addr').val(),
		tablecount:$('#party-tablecount').val(), 
		wifi:$('#wifi').val(),
		business_start:$('#business_start').val(),
		inhouse:$('#chkInhouse').is(':checked'),
		takeout:$('#chkTakeout').is(':checked'),
		delivery:$('#chkDelivery').is(':checked'),
		book:$('#chkBook').is(':checked'),
		pay_inhouse:$('#chkInhousepay').is(':checked'),
		pay_takeout:$('#chkTakeoutpay').is(':checked'),
		pay_delivery:$('#chkDeliverypay').is(':checked'),
		pay_book:$('#chkBookpay').is(':checked'),
		delivery_fee:$('#delivery_fee').val()
	},function(json){
		if(json['success']=='0') {
			alert('적용되었습니다');
		} else {
			alert(json['message']);
		}
	},'json');
	return false;
})
.on('click','#btn-cancel',function(){
	$(':input').each(function(ndx,value){
		$(this).val('');
	});
})
.on('change','#chkInhouse,#chkDelivery,#chkTakeout,#chkBook',function(){
	var idstr=this.id;
	idstr=idstr.replace(/chk/g,'').toLowerCase(); 
	if($(this).is(':checked')) {
		$('#_'+idstr).show();
	} else {
		$('#_'+idstr).hide();
	}
})
.on('click','#btnUpload',function(){
	var file_data = $("#logofile").prop("files")[0];
	if(file_data.name!='') {   
	    var form_data = new FormData();
	    form_data.append("file", file_data);
	
	    $.ajax({
	    	url: '_upload_logo.php', // point to server-side PHP script 
	        dataType: 'json',  // what to expect back from the PHP script, if anything
	        cache: false,
	        contentType: false,
	        processData: false,
	        data: form_data,                         
	        type: 'post',
	        success: function(json){
		        if(json['success']!='0'){
			        $('#imgLogo').attr('src','./logo/logonotfound.png');
			        alert(json['message']); return false;
		        }
		        var d = new Date();
	        	$('#imgLogo').attr('src',json['filename']+'?'+d.getTime());
			}
	     });
	}
	return false;
})
.on('change','#logofile',function(){
	var file_data=$(this).prop('files')[0];
	if(file_data.name!=''){
		$('#btnUpload').click();
	}
	return false;
})
.on('click','#btnRemoveImage',function(){
	if(!confirm('로고이미지를 제거하시겠습니까?')) return false;

	$.post('work_write.php',{
		optype:'remove-logo'
	},function(json){
		if(json['success']!='0'){
			alert(json['message']); return false;
		}
		$('#imgLogo').attr('src','./logo/logonotfound.png');
	},'json');
	return false;
})
;
function loadSetting(){
	$.post('work_read.php',{
		optype:'load-setting'
	},function(txt){
	},'text');
}
</script>