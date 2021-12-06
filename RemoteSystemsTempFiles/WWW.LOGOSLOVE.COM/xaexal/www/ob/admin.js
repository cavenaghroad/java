var timer;
var oTitle = {
	inhouse:'매장에서 주문',
	takeout: '테이크아웃',
	delivery:'배달 주문',
	book: '예약 주문'
};
//var aMenu=[];
var aOrderType=[];

function resize(){
//	wlog('dvInhouse ['+$('#dvInhouse').height()+'] inhouse ['+$('.dvOrder').height()+'] window ['+$(window).height()+']');
	var pos2=$('.dvOrder').position();
	var startop=$(window).height()-Math.floor(pos2.top*3);
	var tall=startop-30;
	$('#inhouse,#delivery,#takeout,#book').css('height',tall+'px');
	$('#dvInhouse,#dvDelivery,#dvTakeout,#dvBook').css('height',tall+'px');
	$('#dvDelivery0,#dvTakeout0,#dvBook0').css('height',tall+'px');
	$('.dvOrder>div').css('height',startop+'px');	
}

$(window).resize(function(){
	resize();
});

function _init(){
//	resetlog()
	var party;
	if(gParty!='') {
		party=gParty;
	} else {
		party=getCookie('party');
	}
	if(isEmpty(party)) return false;

	getPosition();
	
	$('.dvOrder').tabs({
//		heightStyle:'fill',
		active:tActive,
		activate:function(e,ui){
			tActive=ui.newTab.index();
			wlog('tActive ['+tActive+']');
			$.post('_tabindex.php',{
				optype:'tabindex',ndx:tActive
			},function(json){
				if(json['success']!='0'){
					alert(json['message']);
				}
			},'json');
		}
	});
	$('label[id=cWorking]').css({'background-color':cWORKING,'color':'white'});
	$('label[id=cDone]').css({'background-color':cDONE,'color':'white'});
	$('label[id=cPaid]').css({'background-color':cPAID,'color':'white'});
	$('label[id=cFail2Pay]').css({'background-color':cFAIL2PAY,'color':'black'});
	resize();
	
	$('.dvOrder ul li a').each(function(){
		switch($.trim($(this).text())){
		case "매장주문":	aOrderType.push('inhouse'); break;
		case "배달주문":	aOrderType.push('delivery'); break;
		case "테이크아웃주문":	aOrderType.push('takeout'); break;
		case "좌석예약":	aOrderType.push('book'); break;
		}
	});
	getOrderQueue();
	var timer=setInterval(getOrderQueue,5000);
	timer_Expiry=setInterval(function(){document.location='admin.php';},24*60*60*1000);
	
	$('#dvMenu').tabs();
}

$(document)
.tooltip()
.ready(function(){
	_init();
})
// 테이블 위치 '초기화'버튼 눌렀을 때.
.on('click','.reset-position',function(){
	$.post('work_write.php',{optype:'reset-position'},function(json){
		if(json['success']=='0')	getPosition();
		else {
			alert(json['message']);
		}
	},'json');
})
.on('mouseenter','#tblinhouse0>tbody tr,#tbltakeout0>tbody tr,#tbldelivery0>tbody tr,#tblbook0>tbody tr',function(){
	$(this).css('background-color',cHOVER);
})
.on('mouseleave','#tblinhouse0>tbody tr,#tbltakeout0>tbody tr,#tbldelivery0>tbody tr,#tblbook0>tbody tr',function(){
	$(this).css('background-color','');
})
.on('click','#tblinhouse0>tbody tr,#tbltakeout0>tbody tr,#tbldelivery0>tbody tr,#tblbook0>tbody tr',function(){
	wlog('click '+this.id);
	var my=$(this), myID=this.id;
	var tblname=$(this).closest('table').attr('id');
	tblname=tblname.replace(/tbl/g,''); tblname=tblname.replace(/0/g,'');

	$.post('_admin.php',{optype:'done',ordertype:tblname,rowid:myID},function(json){
		if(json['success']!='0'){
			alert('주문처리 실패.');
			return false;
		}
		wlog('order_id ['+json['order_id']+'] tblname ['+tblname+']');
		if(json['order_id']!=''){
			$('#'+tblname+' tbody tr').filter(function(){
				return this.id==json['order_id'];
			}).css({'color':'white','background-color':cDONE});
		}
		my.remove();
	},'json');
	return false;
})
.on('click','#tbltakeout > tbody tr td,#tbldelivery > tbody tr td,#tblbook > tbody tr td',function(){	// click a post to view the post content.
	var ordertype=$(this).closest('table').attr('id');
	ordertype=ordertype.substr(3);
	var order_id=$(this).parent('tr').attr('id');
	var status=$(this).parent('tr').attr('status');

//	$(this).parent().css('background-color',cHOVER);
//	$(this).closest('table').find('tbody tr').not($(this).parent()).css('background-color','');

	var ndx=$(this).index();	// 표시된 칼럼의 순서인덱스.
	if(status=='paid'){
		showPaid(order_id);
		return false;
	}
	showMenuDlg(ordertype,order_id,'');
	return false;
})
.on('click','.chkPosition',function(){	// 테이블 위치 변경 by Drag&Drop
	if($(this).attr('checked')){
		$('.reset-position').show();
		$('div[name=mesa]').draggable();
		$('div[name=mesa]').resizable({
			helper: "ui-resizable-helper",
			animate:false,
			grid:10,
			width:400,height:300
		});
	} else {
		$('.reset-position').hide();
		$('div[name=mesa]').draggable('destroy');
		$('div[name=mesa]').resizable('destroy');
		var pstr='';
		$('div[name=mesa]').each(function(){
			var offset=$(this).position();
			if(pstr!='') pstr+=';';
			pstr+=$(this).text()+','+offset.left+','+offset.top+','+$(this).width()+','+$(this).height();
		});
		$.post('work_write.php',{optype:'table',position:pstr});
	}
})
// 주문내역을 보기위해 테이블을 클릭.
// click the table button to view the order history.
.on('click','div[name=mesa]',function(){	
	if($('.chkPosition').attr('checked')) {
		var answer=prompt("새로운 테이블 이름",$(this).text());
		if(answer!='' && answer!=null){
			$(this).text(answer);
		}
		return false;
	}
	var my=$(this);
	var tblno=$(this).attr('tblno');
	if(tblno=='주방' || isEmpty(tblno))	return false;

	var order_id=my.attr('order_id');
	var status=my.attr('status');
	if(status=='paid'){
		showPaid(order_id);
		return false;
	} 
	$('div[name=mesa]').each(function(){
		if($(this).attr('name')==my.attr('name')) return true;
	
		if($(this).attr('order_id')===undefined || isEmpty($(this).attr('order_id'))){
			$(this).css({'color':'black','background-color':cEMPTY});
		} else {
			$(this).css({'color':'black','background-color':cWORKING});
		}
	});
	showMenuDlg('inhouse',order_id,tblno);
	return false;
})
.on('dblclick','div[name=mesa]',function(){	
	if(!$('.chkPosition').attr('checked')) return false;
	var oldtext=$(this).text();
	var answer=prompt("새로운 테이블 이름",oldtext);
	if(answer!='' && answer!=oldtext){
		$(this).text(answer);
	}
	return false;
})
.on('click','#btnErase',function(){
	if($('#selSelected>option').length<1){
		alert('주문내역이 없습니다.'); return false;
	}
	if(!confirm('정말로 현재 주문내역을 모두 삭제하시겠습니까?')) return false;

	$('#selSelected>option').each(function(){
		if(isEmpty($(this).val()))	$(this).remove();
	});
	return false;
})
.on('click','#btnUnpaid',function(){
	if(!confirm("미수금으로 처리할까요?")) 	return false;
//	resetlog();
	var orderID=$('#dlgmenu-order-id').val();
	var ordertype=$('#dlgmenu-ordertype').val();
	$.post('work_write.php',{
		optype:'unpaid',ordertype:ordertype,order_id:orderID
	},function(json){
		if(json['success']!='0'){
			alert(json['message']); return false;
		}
		if(ordertype=='inhouse'){
			$('div[name=mesa]').filter(function(){
				return $(this).attr('order_id')==json['order_id'];
			}).css({'color':'black','background-color':cEMPTY}).attr({'status':'','order_id':''});
		} else {
			$('#tbl'+ordertype+' tbody tr').filter(function(){
				return this.id==orderID;
			}).remove();
		}
		$('#dlgMenu').dialog('close');
	},'json');
})
.on('click','#btnPayment',function(){
	var actual_price=parseFloat($('#actual_price').val())*1000;
//	wlog('payment method ['+$('input[name=payment_method]:checked').val()+'] actual_price ['+actual_price+']');
	if($('input[name=payment_method]:checked').val()=='cash'){
		if(!confirm('결제할 금액은 ['+actual_price+'] 원 입니다.')) return false;
		
		$.post('work_write.php',{
			optype:'payment',payment_type:'cash',actual_price:$('#actual_price').val(),order_id:$('#dlgmenu-order-id').val(),
			ordertype:$('#dlgmenu-ordertype').val()
		},function(json){
			if(json['success']!='0'){
				alert(json['message']); return false;
			}
			if($('#dlgmenu-ordertype').val()=='inhouse'){
				$('div[name=mesa]').filter(function(){
					return $(this).attr('order_id')==$('#dlgmenu-order-id').val();
				}).css({'color':'white','background-color':cPAID});
			} else {
				$('#tbl'+$('#dlgmenu-ordertype').val()+' tbody tr').filter(function(){
					return this.id==$('#dlgmenu-order-id').val();
				}).css('background-color',cPAID);
			}
			$('#dlgMenu').dialog('close');
		},'json');
	} else {
		var goodname=$('#selSelected option:first').text();
		var goodcount=$('#selSelected>option').length-1;
		goodname+=' 외 '+goodcount+'개 ';
		$.redirect('/nicepay/PC_LITE_PHP/payRequest_utf_logoslove.php',{
			order_id:$('#dlgmenu-order-id').val()
		});
	}
})
.on('click','#merge',function(){
	if($('#working-table').is(':visible')) $('#working-table').hide();
	else $('#working-table').show();
	return false;
})
.on('click','#working-table>option',function(){
	var curTable=$(this);
	var order_id=curTable.val();
	
	if(!confirm('테이블을 이동 또는 합칠까요 ?')) return false;
	$.post('work_write.php',{
		optype:'merge',order_id1:$('#dlgmenu-order-id').val(),order_id2:order_id,tableno:curTable.text()
	},function(json){
		if(json['success']!='0'){
			alert(json['message']);
			return false;
		}
		$('#dlgMenu').dialog('close');
		getOrderQueue();
	},'json');
	return false;
})
.on('click','#add-delivery,#add-takeout,#add-book',function(){
	showMenuDlg((this.id).substr(4),'','');
}) 
/*
 * click the menu button to add new item to ordered list.
 */
.on('click','button[id^=mnu]',function(){
	wlog('click btnMenu ['+$(this).text()+']');
	var btntext=$(this).text();
	var pname=btntext.split('[');
	var price=pname[1];
	price=price.replace(/\]/g,'');
	price=parseFloat($.trim(price));
	pname=$.trim(pname[0]);
	$('#selSelected').append('<option>'+pname+'</option>');
	return false;
})
/*
 * remove ordered menu item.
 */
.on('click','#selSelected>option',function(){
//	var thisMenu=$(this);

	if(!confirm('클릭한 메뉴를 지우시겠습니까?')) return false;
	$(this).remove();
	return false;
})
.on('click','#btnsubmitMenu',function(){
	switch($('#dlgmenu-ordertype').val()){
//+	case 'inhouse':
	case 'book':
	case 'delivery':
		if($('#dlgmenu-ordertype').val()=='book'){
			if(isNaN($('#howmany').val()) || $('#howmany').val()=='') {
				alert('예약참석인원을 입력하십시오.');
				return false;
			}
		} else {
			$('#address').val($.trim($('#address').val()));
			if($('#address').val()==''){
				alert('배달지 주소를 입력하십시오.'); return false;
			}
		}
	case 'takeout':
		if($('#arrival_time').val()==''){
			alert('도착예정시각을 입력하십시오.'); return false;
		}
		if($('#mobile').val()==''){
			alert('연락처(모바일) 번호를 입력하십시오.'); return false;
		}
	}
	if($('#dlgmenu-order-id').val()!='' && $('#dlgmenu-change').val()!='1'){
		/*
		 *  when order id was generated and never update on Mobile/Arrival time/Howmany/Address,
		 *  then only submit new menu.
		 */ 
		submitMenu();
	} else {
		var oParam={};
		oParam['order_id']=$('#dlgmenu-order-id').val();
		oParam['optype']='submitOrder';
		oParam['party']=$('#party').val();
		oParam['table']=$('#lblTableno').text();
		oParam['ordertype']=$('#dlgmenu-ordertype').val();
		oParam['mobile']=$('#mobile').val();
		oParam['howmany']=$('#howmany').val();
		oParam['arrival_time']=$('#arrival_time').val();
		oParam['address']=$('#address').val();
		$.post('_submitorder.php',function(json){
			if(json['success']!='0'){
				alert(json['message']); return false;
			}
			$('#dlgmenu-order-id').val(json['order_id']);
		},'json')
		.always(function(){
			submitMenu();
		});
	}
})
.on('click','#btnRefreshSale',function(){
	$('#tblSale,#tblUnpaid,#tblWorking').html('');
//	resetlog();
	$.post('_admin.php',{
		optype:'sale'
	},function(json){
		if(json['success']!="0"){
			alert(json['message']); return false;
		}
		$('#tblSale').html(json['sale']);
		$('#tblUnpaid').html(json['unpaid']);
		$('#tblWorking').html(json['working']);
	},'json');
})
.on('click','#btnSettle',function(){
	var oSale={
		'매장':'inhouse', '테이크아웃':'takeout', '배달':'delivery', '예약':'book'
	}
	var curTR=$(this).closest('tr');
	
	$('#dlgPayment').dialog({
		title:'미수금 결제', width:400,
		open:function(e,ui){
			$('#dlgpayment-order-id').val(curTR.prop('id'));
			$('#dlgpayment-ordertype').val(oSale[curTR.find('td:nth(0)').text()]);
			$('#cert_num').val(curTR.find('td:nth(1)').text());
			$('#lblPrice').text(curTR.find('td:nth(2)').text());
			var pstr=curTR.find('td:nth(3)').html();
			$('#selList').empty();
			$.each(pstr.split('<br>'),function(ndx,txt){
				$('#selList').append('<option>'+txt+'</option>');
			});
			$('#payprice').val($('#lblPrice').text()).focus().select();
		},
		buttons:[{
			text:'확인',
			click:function(){
				var actual_price=parseFloat($('#payprice').val())*1000;
//				wlog('payment method ['+$('input[name=payment_method]:checked').val()+'] actual_price ['+actual_price+']');
				if($('input[name=paymethod]:checked').val()=='cash'){
					if(!confirm('결제할 금액은 ['+actual_price+'] 원 입니다.')) return false;
					
					$.post('work_write.php',{
						optype:'payment',payment_type:'cash',actual_price:$('#payprice').val(),order_id:$('#dlgpayment-order-id').val(),
						ordertype:$('#dlgpayment-ordertype').val()
					},function(json){
						if(json['success']!='0'){
							alert(json['message']); return false;
						}
						$('#dlgPayment').dialog('close');
						$('#btnRefreshSale').click();
					},'json');
				} else {
					var goodname=$('#selList option:first').text();
					var goodcount=$('#selList>option').length-1;
					goodname+=' 외 '+goodcount+'개 ';
					$.redirect('/nicepay/PC_LITE_PHP/payRequest_utf_logoslove.php',{
						appname:'payResult_utf_logoslove.php',
						title:'서비스 사용료 납부',
						ordertype:$('#dlgpayment-ordertype').val(),
						order_id:$('#dlgpayment-order-id').val(),
						mid:'logos0001m',mkey:'QEcOnUn+Ix90MNF5GdpWH3CQ4Bz0t4NLIEG4ZY9Rs1ZJlrIwUNM6ciIIfxmYhZ7Z9IMLUwRFvg6Fz46fQ1GPTA==',
						price:actual_price,email:'xaexal@gmail.com',goodname:goodname,nickname:'손님왕',mobile:'01026384032',
						goodcount:(goodcount+1)
					});
				}
			}
		},{
			text:'취소',
			click:function(){
				$(this).dialog('close');
			}
		}]
	});
})
.on('click','#btnUnlistOrder',function(){
	var curTR=$(this).parent('tr');
	var order_id=$(this).parent('tr').attr('id');

	$.post('_admin.php',{
		optype:'unlist',order_id:order_id
	},function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		curTR.remove(); 
	},'json');
	return false;
})
.on('change','#mobile,#howmany,#address,#arrival_time',function(e,u){
	$('#dlgmenu-change').val('1');
	return false;
})
.on('click','#selSelected>option',function(e,u){
	if(!confirm('클릭한 메뉴를 취소할까요?')) return false;

	$(this).remove();
	return false;
})
.on('click','#selOrdered>option',function(e,u){
	if(!confirm('클릭한 메뉴를 취소할까요?')) return false;

	var thismenu=$(this);
	
	var oParam={};
	oParam['optype']='removeOrder';
	oParam['order_id']=$('#dlgmenu-order-id').val();
	oParam['status']=(this.className=='done'?'done':'');
	var pstr=($(this).text()).split(']');
	var n=parseInt(pstr[0].replace('[',''));
	pstr[1]=$.trim(pstr[1]);
	
	oParam['menu']=pstr[1];
	$.post('_removeorder.php',oParam,function(json){
		console.log(json);
		if(json['success']!='0') {
			alert(json['message']); return false;
		}
		if(n<2){
			thismenu.remove();
		} else {
			n--;
			thismenu.text('['+n+'] '+pstr[1]);
		}
	},'json');
})
;

function submitMenu(){
	var orderlist='';
	$('#selSelected>option').each(function(){
		wlog($(this).text());
		if(orderlist!='') orderlist+='^';
		orderlist+=$(this).text();
	});
	wlog('orderlist ['+orderlist+']');
	if(orderlist==''){	// even though no order selected, exit from dlgMenu.
		$('#dlgMenu').dialog('close');
		return false;
	}
	var oParam={};
	oParam['optype']='submitMenu';
	oParam['order_id']=$('#dlgmenu-order-id').val();
	oParam['order']=orderlist;
	console.log(oParam);
	$.post('_submitmenu.php',oParam,function(json){
		if(json['message']!='') {
			alert(json['message']);
			return false;
		}
		$('#tblSelected tr').each(function(){
			$(this).remove();
		});
		alert('주문입력됐습니다.');
		$('#dlgMenu').dialog('close');
	},'json');
}

function getWorking(ordertype){
	$.post('_working.php',{
		optype:'working',ordertype:ordertype,last:last[ordertype],last0:last[ordertype+'0']
	},function(json){
		if(json['success']!='0')	return false;
		switch(ordertype){
		case "inhouse":
			$('div[name=mesa]').css({'color':'black','background-color':cEMPTY}).attr({'order_id':'','status':''});	// Reset all tables.
			
			$.each(json[ordertype],function(ndx,value){
				var bcolor={};
				switch(value['status']){
				case '':	bcolor={'color':'white','background-color':cWORKING}; break;
				case 'done':	bcolor={'color':'white','background-color':cDONE}; break;
				case 'paid':		bcolor={'color':'white','background-color':cPAID}; break;
				case 'fail2pay':	bcolor={'color':'black','background-color':cFAIL2PAY}; break;
				default: bcolor={'color':'black','background-color':cEMPTY};
				}
				$('div[name=mesa]').filter(function(){
					return $(this).attr('tblno')==value['tableno'];
				}).css(bcolor)
				.attr('order_id',(value['order_id']===undefined?'':value['order_id']))
				.attr('status',value['status']);
			});
			break;
		case "takeout":
		case "delivery":
		case "book":
//			wlog('---------------------------------------------');
			/*
			 *  If TR is not exist in json data, which means order data is removed or hidden, so it should be removed as well on TR.
			 *  Finally, TR data number is larger than JSON data number. Only JSON data can have new order.
			 */
			$('#tbl'+ordertype+' tbody tr').each(function(){
				var bExist=false;
				var order_id=this.id;
				$.each(json[ordertype],function(ndx,val){
					if(val['order_id']==this.id) {
						var bcolor;
						switch(value['status']){
						case '':	bcolor={'color':'white','background-color':cWORKING}; break;
						case 'done':	bcolor={'color':'white','background-color':cDONE}; break;
						case 'paid':		bcolor={'color':'white','background-color':cPAID}; break;
						case 'fail2pay':	bcolor={'color':'black','background-color':cFAIL2PAY}; break;
						default: bcolor=cEMPTY;
						}
						if($(this).attr('status')!=val['status'])	$(this).css(bcolor);
						if($(this).find('td:eq(1)').text()!=json['price'])	$(this).find('td:eq(1)').text(json['price']);
						if($(this).find('td:eq(2)').text()!=json['mobile'])	$(this).find('td:eq(2)').text(json['mobile']);
						delete json[ordertype][ndx];
						bExist=true;
//						wlog('bExist ['+bExist+']');
						return false;
					}
				});
				if(!bExist) $(this).remove();
			});
			$.each(json[ordertype],function(ndx,val){
				var bcolor;
				switch(val['status']){
				case '':	bcolor={'color':'white','background-color':cWORKING}; break;
				case 'done':	bcolor={'color':'white','background-color':cDONE}; break;
				case 'paid':		bcolor={'color':'white','background-color':cPAID}; break;
				case 'fail2pay':	bcolor={'color':'black','background-color':cFAIL2PAY}; break;
				default: bcolor=cEMPTY;
				}
//				wlog('new ['+val['order_id']+'] fcolor ['+fcolor+']');
				
				var pstr='<tr id='+val['order_id']+' status='+val['status']+
							'><td class=font18>'+showByMinute(val['order_id'])+
							'</td><td align=right class=font18>'+val['price']+'</td><td class=font18>'+val['mobile']+'</td>';
				
				switch(ordertype){
				case 'delivery':
					pstr+='<td class=font18 style="display:none;">'+val['address']+'</td></tr>'; 
					break;
				case 'takeout':
					pstr+='<td class=font18>'+
					(val['arrival_time']!=null?val['arrival_time']:'')+'</td></tr>'; 
					break;
				case 'book':
					pstr+='<td class=font18>'+
					(val['arrival_time']!=null?val['arrival_time']:'')+'</td><td class=font18>'+(val['howmany']!=null?val['howmany']:'')+
					'</td></tr>';
					break;
				}
//				wlog('pstr ['+pstr+']');
				$('#tbl'+ordertype+' tbody').append(pstr);
				$('#tbl'+ordertype+' tbody tr:last').css(bcolor);
			});
			if(ordertype=='delivery'){
				$('#tbl'+ordertype+' tbody tr').each(function(){ $(this).find('td:last').hide(); });
			}
		}
	
		$('#tbl'+ordertype+'0 tbody tr').each(function(){
			var rowid=this.id;
			var bExist=false;
			$.each(json[ordertype+'0'],function(ndx,val){
				if(this.id==val['rowid']) {
					bExist=true; 
					delete json[ordertype+'0'][ndx];
					return false;
				}
			});
			if(!bExist)	$(this).remove();
		});
		$.each(json[ordertype+'0'],function(ndx,val){
			var where=(val['mobile']!=null?val['mobile']:'');
			if(ordertype=='inhouse') {
				where=val['tableno'];
			}
			var pstr0='<tr id='+val['rowid']+' style="font-size:18px;"><td>'+where+
							'</td><td>'+val['name']+'</td><td height=24px>'+showHM(val['rowid'])+'</td></tr>';
			$('#tbl'+ordertype+'0 tbody').append(pstr0);
		});
	},'json')
	.always(function(){
//		resize();
	});
}

function getOrderQueue(){
	$.each(aOrderType,function(ndx,val){
		getWorking(val);
	});
	pertainSession();
}
/*
 * Display the positions of table.
 */
function getPosition(){
	var pstr='';
	var table_count=0;
	$.post('work_read.php',{
		optype:'table'
	},function(json){
		if(json['success']!='0'){
			alert(json['message']);
			return false;
		}
		table_count=parseInt(json['table_count']);
		$('div[name^=mesa]').remove(); 		// clear all tables.
		if(isEmpty(json['table_position'])) {	// if there is no table position data built-up.
			for(n=0;n<table_count;++n){
				pstr+='<div class=desk style="left:250px;top:50px;width:80px;height:50px;" name=mesa tblno='+n+'>'+n+'</div>';
			}
		} else if(json['table_position']!=null && json['table_position']!=''){
			var arDiv=json['table_position'].split(';');
			$.each(arDiv,function(index,val){
				var ar=val.split(',');					// text,left,top,width,height
				pstr+='<div class=desk style="left:'+ar[1]+'px;top:'+ar[2]+'px;width:'+ar[3]+'px;height:'+ar[4]+
					'px;" name=mesa tblno='+ar[0]+'>'+ar[0]+'</div>';
			});
		}
		$('#dvMesa').append(pstr);
	},'json');
}

function soundplay(tblname){
	if(!gSoundPlay) return;

	switch(tblname){
	case 'inhouse':		
		var audio=new Audio('./sound/ding-dong.wav'); 
		audio.play();
		audio=null;
		break;
	case 'delivery':			
		var audio=new Audio('./sound/ding-dong.wav'); 
//		var audio=new Audio('./sound/motorbike-horn.wav'); 
		audio.play();
		audio=null;
		break;
	case 'takeout':		
		var audio=new Audio('./sound/ding-dong.wav'); 
//		var audio=new Audio('./sound/bike-horn.wav'); 
		audio.play();
		audio=null;
		break;
	case 'book':		
		var audio=new Audio('./sound/ding-dong.wav'); 
//		var audio=new Audio('./sound/classic-guitar.wav'); 	
		audio.play();
		audio=null;
	}
}

function showMenuDlg(ordertype,order_id,tblno){
	$('#dlgMenu').dialog({
		title:oTitle[ordertype],width:$(window).width(),height:$(window).height()
		,create:function(){
			$('#arrival_time').timepicker();
			$('#tabOrder').tabs({
				beforeActivate:function(e,u){
					switch(u.newTab[0].id){
					case 'liSelected':
						break;
					case 'liOrdered':
						$('#selOrdered').empty();
						if(!isEmpty($('#dlgmenu-order-id').val())){
							$.post('_getorder.php',{
								optype:'getOrder',order_id:$('#dlgmenu-order-id').val()
							},function(json){
								if(json['message']!='') {
									alert(json['message']); return false;
								}
								$.each(json['order'],function(ndx,val){
									var pstr='<option class='+(val['status']==''?'ordered':'done')+'>['+val['qty']+'] '+val['name']+'</option>';
									$('#selOrdered').append(pstr);
								});
							},'json');
						}
						break;
					}
				}
			});
			return false;
		}
		,open:function(e,ui){
			$('#dlgmenu-change').val('0');
			$('#selSelected').empty();
			$('#lblTotal').text(''); 
			$('#actual_price').val('');
			$('#dlgmenu-order-id').val(order_id);
			$('#dlgmenu-ordertype').val(ordertype);
			$('#lblTableno').text('').hide();
			$('#working-table').empty().hide();
			$('#pnMobile,#pnArrival_time,#pnHowmany,#pnAddress').hide();
			$('#mobile,#arrival_time,#howmany,#address').val('');
/*
 			----------------------------------------------------------------------------
 			               Mobile   ArrivalTime Howmany  Address   TableNo
 			----------------------------------------------------------------------------
 			Inhouse                                                                   O
 			----------------------------------------------------------------------------
 			Takeout      O               O
 			----------------------------------------------------------------------------
 			Delivery      O               O                            O
 			----------------------------------------------------------------------------
 			Book          O               O             O
 			----------------------------------------------------------------------------
 */			
			switch(ordertype){
			case 'inhouse':
				$('#lblTableno').show();
				$('#merge').val('이동');
				break;
			case 'delivery':
			case 'book':
				if(ordertype=='delivery'){
					$('#pnAddress').show();
				} else {
					$('#pnHowmany').show();		// no break;
					$('#merge').val('좌석지정');
				}
			case 'takeout':
				$('#pnMobile').show();
				$('#pnArrival_time').show();
			}
			var jsn={};
			if(isEmpty($('#dlgmenu-order-id').val())) return false;
			
			$.post('_getinfo.php',{
				optype:'getInfo',order_id:$('#dlgmenu-order-id').val()
			},function(json){
				console.log(json);
				if(json['success']!='0'){
					alert(json['message']); return false;
				}
				$.each(json['order'][0],function(ndx,val){
//						if(ndx=='message'||ndx=='success') return true;
					jsn[ndx]=val;
				});
				console.log(jsn);
			},'json')
			.always(function(){
				wlog('ordertype ['+ordertype+']')
				// make the list of table to be used to move order.
				$('div[name=mesa]').each(function(){
					var tableno=$(this).attr('tblno');
					var status=$(this).attr('status');
					if(status=='paid' || status=='fail2pay') return true;
					if(tableno=='주방' || isEmpty(tableno)) return true;
					var orderID = $(this).attr('order_id');
					
					if(orderID===undefined) orderID='';
					if(order_id==orderID && orderID!='') return true;
					var sColor='';
					if(orderID!='')	{
						if(status=='done') sColor='color:'+cDONE; 
						else sColor='color:'+cWORKING;
					}
					// working-table is the table list to which the order is moved.
					// When 'Merge' is clikced this 'working-table' list is shown.
					$('#working-table').append('<option style="font-size:18px;'+sColor+'" value='+orderID+'>'+tableno+'</option>');
				});
				switch(ordertype){
				case 'inhouse':
					$('#lblTableno').text(tblno);
//					if(ordertype=='inhouse') break;
				case 'book':
				case 'delivery':
					if(ordertype=='delivery'){
						$('#address').val(jsn['address']);
					} else {
						$('#howmany').val(jsn['howmany']);
					}
				case 'takeout':		// no break;
					$('#mobile').val(jsn['mobile']);
					$('#arrival_time').val(jsn['arrival_time']);
				}
				$('#dlgmenu-change').val('0');
			});
			return false;
		}
		,close:function(e,ui){
			getWorking($('#dlgmenu-ordertype').val());
		}
	});	
}

function showPaid(order_id){
	var ordertype='';
	$('#dlgPaid').dialog({
		title:'정산확인',modal:true, width:500,MaxHeight:$(window).height(),
		position:{my:'top',at:'top',of:window},
		open:function(e,ui){
			$(this).html('');
			$.post('_admin.php',{
				optype:'get-paid',order_id:order_id
			},function(json){
				if(json['success']!='0'){
					alert(json['message']); 
					$('#dlgPaid').dialog('close');
					return false;
				}
				var pstr='<table border=1><tr><th style="background-color:black;"><font color=white><b>주문내역</b></font></th></tr>';
				$.each(json['order'],function(ndx,value){
					pstr+='<tr><td>'+value['name']+' ('+value['price']+')</td></tr>';
				});
				pstr+='</table>';
				var sch='<table border=1><tr style="background-color:black;"><th colspan=2><font color=white><b>결제내역</b></font></tr>';
				var pTime=json['pay_time'];
				ordertype=json['ordertype'];
//				switch(json['ordertype']){
//				case 'inhouse': 
					json['ordertype']=oTitle[ordertype]; 
//					break;
//				case 'delivery': json['ordertype']='배달주무'; break;
//				case 'takeout': json['ordertype']='테이크아웃주문'; break;
//				case 'book': json['ordertype']='예약주문'; break;
//				}
				sch+='<tr><td align=right>주문형태</td><td align=center>'+json['ordertype']+'</td></tr><tr><td align=right>모바일번호</td><td align=center>'+
					json['mobile']+'</td></tr><tr><td align=right>지불금액</td><td align=right>'+json['paid']+'&nbsp;'+json['currency']+
					'</td></tr><tr><td align=right>총액</td><td align=right>'+
					json['price']+'&nbsp;'+json['currency']+'</td></tr><tr><td align=right>주문처리상태</td><td align=center>'+
					(json['status']==undefined?'':json['status'])+'</td></tr><tr><td align=right>'+
					'지불시각</td><td align=center>'+pTime.substr(0,4)+'/'+pTime.substr(4,2)+'/'+pTime.substr(6,2)+' '+pTime.substr(8,2)+':'+
					pTime.substr(10,2)+':'+pTime.substr(12,2)+' '+pTime.substr(14)+'</td></tr><tr><td align=right>지불방식</td><td align=center>'+
					json['payment_type']+
					'</td></tr><tr><td align=right>결제결과</td><td align=center>'+(json['payment_code']==undefined?'':json['payment_code'])+
					'</td></tr><tr><td align=right>처리결과메세지</td><td align=center>'+(json['payment_msg']==undefined?'':json['payment_msg'])
					+'</td></tr><tr><td align=right>Payment ID</td><td>'+
					(json['payment_id']==undefined?'':json['payment_id'].substr(0,15)+'<br>'+json['payment_id'].substr(15,15)+'<br>'+
					json['payment_id'].substr(30))+
					'</td></tr><tr><td align=right>지불자</td><td>'+(json['payer']==undefined?'':json['payer'])+
					'</td></tr><tr><td align=right>결제회사명</td><td>'+(json['company']==undefined?'':json['company'])+
					'</td></tr><tr><td align=right>할부기간</td><td align=center>'+json['allotment']+'</td></tr><tr><td align=right>영수증발행형태</td>'+
					'<td align=center>'+json['receipt']+'</td></tr><tr>'+
					'<td align=right>결제(카드/계좌)번호</td><td>'+(json['payment_number']==undefined?'':json['payment_number'])+
					'</td></tr><tr><td align=right>지급만기일</td><td align=center>'+
					(json['expect']==undefined?'':json['expect'])+'</td></tr></table>';
				$('#dlgPaid').html('<table style="font-size:12px;"><tr><td valign=top>'+pstr+'</td><td valign=top>'+sch+'</td></tr></table>');
			},'json');
			return false;
		},
		buttons: [
	          {
	        	  text:'확인',
	        	  click:function(e,ui){
	        		  $(this).dialog('close'); return false;
	        	  }
	          },
	          {
	        	  text:'완료',
	        	  click:function(e,ui){
	        		  $.post('_admin.php',{
	        			  optype:'unlist',order_id:order_id
	        		  },function(json){
	        			  if(json['success']!='0'){
	        				  alert(json['message']); return false;
	        			  }
	        			  $('#dlgPaid').dialog('close');
	        			  return false;
	        		  },'json')
	        		  .always(function(){
	        			  if(ordertype=='inhouse'){
	        				  $('div[name=mesa]').filter(function(){
	        					  return $(this).attr('order_id')==order_id;
	        				  }).css({'color':'black','background-color':cEMPTY})
	        				  .attr({'status':'','order_id':''});
	        			  } else {
	        				  $('#tbl'+ordertype+' tbody tr').filter(function(){
	        					  wlog(order_id+'/'+this.id);
	        					  return this.id==order_id;
	        				  }).remove();
	        			  }
	        		  });
	        	  }
	          }
          ]
	});

}
