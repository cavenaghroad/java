var ENTERPRISE=2;
var GRID_ID=1;
var SCREEN_NAME=4;
var TNAME= 6;
var SEQNO=2;

$(document)
.ready(function(){
	wlog('pageinfo.js was loaded');
})
.on('click','#mInactive',function(){
	var bStatus=$(this).prop('checked');
	wlog('bStatus ['+bStatus+']');
	if(bStatus){
		$(my.gridname+' tbody tr').each(function(){
			var rowid=$(this).prop('data-tt-id');
			
		});
	}
//	return false; this should be disabled.
})
;


//function loadJS(){
//	wlog('loadJS is invoked')
//	$('input[type=button]').button().css('height','24px');
//	wlog('objArr.length ['+objArr.length+']');
//}

garGrid[1].bef_dup=function(){
	duplication(this.gridname,this.rowid,this.container._p,'pageinfo');
}

garGrid[3].bef_dup=function(){
	duplication(this.gridname,this.rowid,this.container._p,'gridinfo');
	
}
garGrid[4].bef_dup=function(){
	duplication(this.gridname,this.rowid,this.container._p,'colinfo');
	
}

function duplication(gridname,rowid,_p,level){
	$(gridname+' tr.selected').find('td:gt(0)').each(function(){
		if(pstr!='') pstr+='^';
		pstr+=$.trim($(this).text());
	});
	pstr='optype=copy_cas&rowid='+rowid+'&_p='+_p+'&level='+level+'&_column='+pstr;
	$.ajax({
		url:'disu.php',datatype:'text',data:pstr,async:true,
		beforeSend:function(){
			wlog(pstr);
		},
		success:function(_return){
		},
		complete:function(){
		}
	});
}

garGrid[0].bef_del=function(){
	removal(this.gridname,this.rowid,'pageinfo');
}

garGrid[1].bef_del=function(){
	removal(this.gridname,this.rowid,'linkinfo');
	
}
garGrid[2].bef_del=function(){
	removal(this.gridname,this.rowid,'gridinfo');
	
}
garGrid[3].bef_del=function(){
	removal(this.gridname,this.rowid,'colinfo');
	
}

function removal(gridname,rowid,level){
	$(gridname+' tr.selected').find('td:gt(0)').each(function(){
		if(pstr!='') pstr+='^';
		pstr+=$.trim($(this).text());
	});
	pstr='optype=del_cas&rowid='+rowid+'&level='+level+'&_column='+pstr;
	$.ajax({
		url:'disu.php',datatype:'text',data:pstr,async:true,
		beforeSend:function(){
			wlog(pstr);
		},
		success:function(_return){
		},
		complete:function(){
		}
	});
}

objArr[0].befDuplicate=function(){
	var my=this;
	var tbl=$(my.gridname).DataTable();
	var row=tbl.rows('.selected').indexes();
	var rowid=tbl.cell(row[0],GRID_ID).data();
	var pstr='optype=copy_cas&grid_id='+rowid;
	$.ajax({
		url:'disu.php',datatype:'text',data:pstr,async:true,
		beforeSend:function(){
			wlog(pstr);
		},
		success:function(_return){
			if(_return!='0') alert(_return);
		},
		complete:function(){}
	});
}

objArr[0].befRemove=function(){
//	setlog(-1);
	var my=this;
	var tbl=$(my.gridname).DataTable();
	var ndx=tbl.rows( '.selected' ).indexes();
	console.log(ndx );
	var grid_id=tbl.cell(tbl.rows('.selected').indexes()[0],GRID_ID).data();

	wlog('grid_id ['+grid_id+'] rowdata ['+tbl.row(ndx[0]).data()+']');
	my.resultRemove=false;
	if(!confirm('Do you want to remove all children records as well ?')){
		alert('Cancelled it.');
		return NextCancel;
	}
	var pstr = 'optype=del_cas&grid_id='+grid_id;
//	return false;
	$.ajax({
		url:'disu.php',datatype:'text',data:pstr,cache:false,async:false,type:'POST',
		beforeSend:function(){wlog(pstr);},
		success:function(_return){
			if(_return=='1'){
				alert('failed');
			} else {
				tbl.row(ndx[0]).remove().draw();
				alert('successfully deleted.');
			}
		},
		complete:function(){
			return NextCancel;
		}
	});
}
objArr[2].befCreate=function(){
	var my = this;
	var tbl=$(my.gridname).DataTable();
	var maxval = tbl.column(2).data().sort().reverse()[0];
	wlog('maxval ['+maxval+']');
	var grid_id=$(objArr[0].gridname+' tr.selected td:eq('+GRID_ID+')').text();
	rowtext='optype=flirt&grid_id='+grid_id;
	$.ajax({
		data:rowtext,url:'disu.php',async:false,
		beforeSend:function(){wlog(rowtext);},
		success:function(xml){
			objArr[1].load2table();
		},
		complete:function(){
			
		}
	});
	return false;
}

objArr[3].aftLoad2table=function(){
	var my=this;
	var ROWID=0;
	
	$(my.gridname+' tbody tr').each(function(){
		var rowid=$(this).find('td:eq('+ROWID+')').text();
		var fname=$(this).find('td:eq('+FNAME+')').text();
		
	});
}