var gInterval=null;
let p_info={};

$(document)
.ready(function(){
	$('td[id^=tbls]').each(function(){
		$(this).find('.lu').prepend(this.id.substr(1));
	});
	let oParam={optype:'init',class:$('#classid').val()};
	console.log(oParam);
	$.post('_ncs_sv.php',oParam,function(json){
		console.log(json);
		if(json['result']!='0'){
			alert(json['msg']);
			return false;
		}
		$.each(json['data'],function(k,v){
			//v=value.split(':');
			pstr="style='color:grey;";
			if(v['birth']=='1') pstr="style='color:red;";
			pstr+="font-weight:bold;font-size:20px;'";
			pstr="<tr><td "+pstr+" class='palm'>"+v['name']+"</td>"+
					"<td align=right style='font-size:6px;color:blue'><font size=2 color=blue>"+v['birth']+"</font>&nbsp;<label id=lblSeat>"+v['seq']+"</label></td></tr>"+
					"<tr><td></td><td align=right><a style='display:none;' id=btnDone>완료</a>&nbsp;<a style='display:none;' id=btnReset>취소</a></td></tr>"+
					"<tr><td colspan=2 align=right valign=bottom>&nbsp;</td></tr>";
			$('#tbls'+v['seq']).append(pstr);
			p_info[v['name']]={school:v['school'],address:v['address']};
		});	
	},'json');
	drillList();
	let dt=new Date();
	return false;
})
.on('change','#selDrill',function(){
	if(gInterval) clearInterval(gInterval);

	title=$('#selDrill').val();
	if(title=='-') {
		$('#selDrill').val('');
		return false;
	}
	$('#txtDrill').val(title);
	getDrill();

	gInterval=setInterval(getDrill,5000);
	return false;
})
.on('click','table[id^=tbls] td',function(){
	var col=$(this).index();
	var row=$(this).parent().index();

	if(row==0 && col==0 ){ //출석체크/취소
		var me=$(this);
		$.post('_ncs_sv.php',{optype:'attend',name:$(this).text()},function(json){
			switch(json['result']){
			case 'Y':
				me.css('color','red');break;
			case 'N':
				me.css('color','grey'); break;
			default:
			}
		},'json');
	}
	return false;
// })
// .on('click','td',function(){
//	return false;
})
.on('click','#btnAdd',function(){
	$.ajax({
		url:'_ncs_sv.php',dataType:'json',method:'post',
		data:'optype=add&name='+$('#txtDrill').val()+'&class='+$('#classid').val(),
		beforeSend:function(){
			$('#txtDrill').val($.trim($('#txtDrill').val()));
			if($('#txtDrill').val()=='') return false;
		},
		success:function(json){
			if(json['result']!='0') {
				alert(json['msg']);
				$('#txtDrill').val(''); 
				return false;
			}
			$('#selDrill').append('<option>'+$('#txtDrill').val()+'</option>');
			$('#txtDrill').val('');
		}
	});
	return false;
})
.on('click','#btnDel',function(){
	if(!confirm("삭제할까요?")) {
		return false;
	}
	$.post('_ncs_sv.php',{optype:'delete',name:$('#txtDrill').val(),class:$('#classid').val()},function(json){
		if(json['result']!='0'){
			alert(json['msg']); 
			$('#txtDrill').val('');
			return false;
		}
		drillList();
		$('#txtDrill').val('');
	},'json');
	return false;
})
.on('click','#btnDone',function(){
	var me=$(this);
	var student=me.closest('table').find('tr:eq(0) td:eq(0)').text();
	$.post('_ncs_sv.php',{optype:'done',student:student,drill:$('#selDrill').val(),class:$('#classid').val()},function(json){
		if(json['result']!='0'){
			alert(json['msg']); 
			return false;
		}
		me.parent().prev().text('Done').addClass('done');
	},'json');
})
.on('click','#btnReset',function(){
	var me=$(this);
	var student=me.closest('table').find('tr:eq(0) td:eq(0)').text();
	$.post('_ncs_sv.php',{optype:'setmission',status:'reset',student:student,drill:$('#selDrill').val(),class:$('#classid').val()},function(json){
		if(json['result']!='0'){
			alert(json['msg']); 
			return false;
		}
		me.parent().prev().text('작업중').addClass('working').removeClass("done submitted");
	},'json');
})
.on('mouseover','.palm',function(e,u){
	let sname=$(this).text();
	$('#p_info').html(p_info[sname]['school']+'<br><br>'+p_info[sname]['address']).show();
	$('#p_info').css({left:e.clientX+50,top:e.clientY-10});
	return false;
})
.on('mouseout','.palm',function(){
	$('#p_info').empty().hide();
	return false;
})
.on('click','#lblSeat',function(){
	var name=$(this).parent().parent().find('td:nth-child(1)').text();
	var oldseat=$(this).text();
	var str=prompt('새 좌석번호',$(this).text());
	if(str==null) return false;
	if(!$.isNumeric(str)){
		alert('좌석번호는 숫자이어야 합니다.');
		return false;
	}
	if(parseInt(str)==parseInt($(this).text())) return false;
	$.post('_ncs_sv.php',{optype:'changeseat',name:name,oldseat:oldseat,newseat:str,class:$('#classid').val()},function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
	},'json');
	return false;
})
.on('blur','input[name=ipaddr]',function(){
    var pstr=$(this).closest('table').attr('id');
    console.log(pstr);
    pstr=pstr.substr(1);
    var ipaddr=$.trim($(this).val());
    $.ajax({
        url:'_ncs_sv.php',
        data:'optype=ipaddr&seq='+pstr+'&ipaddr='+$(this).val()+'&class='+$('#classid').val(),
        method:'post',
        dataType:'json',
        beforeSend:function(){
            console.log(this.data);
        },
        success:function(json){
            if(json['result']!='0'){
                alert(json['msg']); return false;
            }
        }
    });
    return false;
})
.on('dblclick','.student',function(){
	let name=prompt('이름을 입력하시오.','');
	if(name=='') return false;
	oldseat=-1;
	newseat=$(this).find('table').attr('id');
	newseat=newseat.substr(4);
	let oParam={optype:'changeseat',name:name,oldseat:oldseat,newseat:newseat,class:$('#classid').val()};
	console.log(oParam);
	$.post('_ncs_sv.php',oParam,function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
	},'json');
	return false;
})

function getDrill(){
	// $('a[id=btnDone]').hide(); $('a[id=btnReset]').show();
	// $('table[id^=s]').each(function(){
	// 	$(this).find('tr:eq(1) td:eq(0)').text('작업중').removeClass('done submitted').addClass('working');
	// });
	$.post('_ncs_sv.php',{optype:'getdrill',drill:$('#selDrill').val(),class:$('#classid').val()},function(json){
		var done=[]; var submit=[];
		if(json['done']!=null) done=json['done'].split(',');
		if(json['submit']!=null) submit=json['submit'].split(',');
		
		$('table[id^=tbls]').each(function(){
			var pstr=$(this).find('tr:eq(0) td:eq(0)').text();
			var ptr=$(this).find('tr:eq(1) td:eq(0)');
			
			if($.inArray(pstr,done)>-1) {
				ptr.text('완료').addClass('done').removeClass('submitted working');
				$(this).find('a[id=btnDone]').hide();
				$(this).find('a[id=btnReset]').show();
			} else if($.inArray(pstr,submit)>-1){
				ptr.text('확인요청').addClass('submitted').removeClass('done working');
				$(this).find('a[id=btnDone]').show();
				$(this).find('a[id=btnReset]').show();
			} else {
				ptr.text('작업중').addClass('working').removeClass('submitted done');
				$(this).find('a[id=btnDone]').show();
			}
		});
	},'json');		
}

function drillList(){
	$.post('_ncs_sv.php',{optype:'drill',class:$('#classid').val()},function(json){
		$('#selDrill').empty().append('<option value="-">-</option>');
		$.each(json['data'],function(k,v){
			$('#selDrill').append('<option value="'+v+'">'+v+'</option>');
		});
	},'json');
}

