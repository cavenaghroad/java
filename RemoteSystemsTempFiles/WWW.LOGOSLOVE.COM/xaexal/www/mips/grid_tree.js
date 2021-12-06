$(document)
.ready(function(){
	
})
.on('click','.mkroot',function(){
	wlog('mkroot >>>>>>>>>>>>>>');
	var my = objArr[belongTo($(this),'toolbar')];
	wlog('my ['+my.gridname+'] my.myIndex ['+my.myIndex+']');
	if(my.row<0) return false;
	
	var rowid=$(my.gridname+' tr.selected').attr('data-tt-id');
//	wlog('mkroot rowid ['+rowid+']');
	var urltext='optype=upd_tree&grid_id='+my.grid_id+'&rowid='+rowid+'&par_rowid=&_e='+my._e+'&_column=';
	$.ajax({
		dataType:'text', async:false,
		data:urltext,
		beforeSend:function(){wlog(urltext);},
		success:function(_return){
			if(parseInt(_return)==0){
				$(my.gridname)
//				.treetable('move', rowid, par_rowid)
				.treetable('reveal','');
				_ret=false;
			}
		},
		error:function(e){},//
		complete:function(){}
	});
	
	return false;
})
.on('click','levelup',function(){
	wlog('levelup >>>>>>>>>>>>>>');
	var my = objArr[belongTo($(this),'toolbar')];
	wlog('my ['+my.gridname+'] my.myIndex ['+my.myIndex+']');
	if(my.row<1) return false;
	
	var rowid=$(my.gridname+' tr.selected').attr('data-tt-id');
	var par_rowid=$(my.gridname+' tr.selected').attr('data-tt-parent-id');
	var rowid_up, par_rowid_up,pHTML,pHTML_up;
	wlog('mkroot rowid ['+rowid+']');
	var n=my.row-1;
	while(n>-1){
		rowid_up=$(my.gridname+' tbody tr:eq('+n+')').attr('data-tt-id');
		par_rowid_up=$(my.gridname+' tbody tr:eq('+n+')').attr('data-tt-parent-id');
		if(par_rowid!=par_rowid_up){
			// exchange both records.
			pHTML = $(my.gridname+' tbody tr:eq('+my.row+')').html();
			break;
		}
		--n;
	}
})
//.on('click','xpndx',function(){
//	wlog('mkroot >>>>>>>>>>>>>>');
//	var my = objArr[belongTo($(this),'toolbar')];
//	$(my.grdname).treetable('expandAll');
//})
;
