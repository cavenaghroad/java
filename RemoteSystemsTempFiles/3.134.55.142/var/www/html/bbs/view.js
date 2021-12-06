$(document)
.ready(function(){
	console.log('rowid ['+$('#rowid').val()+']');
	$.post('_view.php',{optype:'view',rowid:$('#rowid').val()},function(answer){
		console.log(answer);
		if(parseInt(answer['result'])<0){
			alert(answer['msg']);
			return false;
		}
		$.each(answer['data'],function(k,v){
			switch(k){
			case 'created':
				v=v.substr(0,4)+'-'+v.substr(4,2)+'-'+v.substr(6,2)+' '+v.substr(8,2)+':'+v.substr(10,2)+':'+v.substr(12,2);
				break;
			}
			$('#td_'+k).text(v);
		});
		if(answer['updatable']=='1'){
			$('#btnUpdate,#btnRemove').show();
		} else {
			$('#btnUpdate,#btnRemove').hide();
		}
	},'json');
	$.post('_view.php',{optype:'viewlist',rowid:$('#rowid').val()},function(answer){
		
		let cnt=parseInt(answer['turn']);
		$.each(answer['list'],function(k,v){
			let pstr=`<tr rowid=${v['rowid']}><td align=center width=30px>${cnt}</td><td align=right>${v['created']}</td>`+
						`<td>${v['title']}</td><td>${v['writer']}</td>`+
						`<td align=right>${v['readcount']}</td><td align=right>${v['good']}</td></tr>`;
			$('#tblList').append(pstr);
			cnt++;
		});
		$('#tblList tr[rowid='+$('#rowid').val()+']').css({'font-weight':'bold','background-color':'cyan'});
	},'json');
	return false;
})
.on('click','#tblList tr',function(){
	document.location='view.php?rowid='+$(this).attr('rowid');
	return false;	
})
.on('mouseover','#tblList tbody tr',function(){
	console.log('mouseover');
	$('#tblList tbody tr.clicked').removeClass('clicked');
	$(this).addClass('clicked');
	return false;
})
;