

/* ***********************************************************************/
function fPage(){
	var me=this;
	this.arForeign=[];	// key = parent_grid
	this.arGrid=[];
	this._p=$('#_p').val();
	this.grid_count=$('.tGrid').length;
	console.log('_p ['+$('#_p').val()+']');
	$.post("_foreign.php",{_p:this._p},function(json){
		console.log(json);
		if(json['result']!="0"){
			alert(json['msg']);
			return false;
		}
		if(json['foreign'].length>0) {
			me.arForeign=json['foreign'].slice();
		}
	},'json')
	.fail(function(){
		console.log('_foreign.php has syntax error.')
	})
	.always(function(){
		var arJS=['addnew','delete','update'];
		$('table[id^=tbl]').each(function(){
			var tblid=(this.id).substr(3);
			
			tGrid=new fGrid(tblid);
			tGrid.drawGrid(false);
			me.arGrid[tblid]=tGrid;
		}); 
	});
}
/* ***********************************************************************/
function fGrid(tblid){
	this.tblid=tblid; 
	this.viewtype=$('#tbl'+this.tblid).attr('viewtype');
	this.ptr=$('#tbl'+this.tblid);
	this.start=0; 
}
/* ***********************************************************************/
fGrid.prototype={
	drawGrid:function(bRefresh){
		var me=this;
		// this.selected=this.ptr.find('tbody tr.selected');
		$.post('_drawgrid.php',{rowid:me.tblid,start:me.start},function(json){
			if(json['result']!='0'){
				alert(json['msg']); return false;
			}
			if(parseInt(json['pagesize'])==0) me.start=-1;
			else me.start+=parseInt(json['pagesize']);
			switch(me.viewtype){
			case 'HRZN':
//				var bgcolor=$('#tbl'+me.tblid).css('background-color');
//				console.log('bgcolor ['+bgcolor+']');
				me.ptr.append(json['html']);
//				me.ptr.find('tbody tr').hover(
//					function(){$(this).css('background-color','#ddd')},
//					function(){$(this).css('background-color',bgcolor)});
				break;
			case 'TREE':
				$.each(json['tree'],function(ndx,val){
					me.ptr.append(val['line']);
				});
				me.ptr.treetable({expandable:true});
				me.ptr.treetable('expandAll');
				break;
			case 'VERT':
				console.log(json['vert'])
				var i=0;
				$.each(json['vert'][0],function(ndx,val){
					console.log(val);
					me.ptr.find(' tbody tr:eq('+i+')').append(val);
					i++;
				});
			}
		},'json');
	},
	getAttr:function(pos,attrname){
		if(this.viewtype==VERT){
			return $('#tbl'+this.tblid+' tr:eq('+pos+') td:eq(0)').attr(attrname);
		} else {
			return $('#tbl'+this.tblid+' thead tr:eq(0) th:eq('+pos+')').attr(attrname);
		}
	}, 
	getChild:function(){
		var me=this;
		var retArr=[];
		$.each(curPage.arForeign,function(ndx,val){
			if(val['parent_grid']==me.tblid){
				retArr.push({'child_grid':val['child_grid'],'child_col':val['child_col'],'parent_col':val['parent_col']});
			}
		});
		return retArr;
	},
	getLine:function(){
		if(arguments.length<1) pos=this.getSelectedIndex();
		else pos=parseInt(arguments[0]);
		
		var me=this;
		var pstr='';
		switch(this.viewtype){
		case 'TREE':
			var curTR=this.ptr.find('tbody tr:eq('+pos+')');
			if(curTR.length<1) return '';

			pstr=curTR.attr('data-tt-id')+'|'+curTR.attr('data-tt-parent-id');
			curTR.find('td').each(function(){
				pstr+='|'+$(this).text();
			});
			break;
		case 'HRZN':
			var curTR=this.ptr.find('tbody tr:eq('+pos+')');
			if(curTR.length<1) return '';

			curTR.find('td:gt(0)').each(function(){
				if(pstr!='') pstr+='|';
				var chk=$(this).find('input[type=checkbox]').length;
				if(chk>0){
					if($(this).find('input[type=checkbox]').prop('checked')) {
						pstr+='1';
					} else {
						pstr+='0';
					}
				} else {
					pstr+=$.trim($(this).text());
				}
			});
			break;
		case 'VERT':
			$('#tbl'+this.tblid+' tbody tr:gt(0)').each(function(){
				if(pstr!='') pstr+='|';
				pstr+=$(this).find('td:eq('+pos+')').text();
			});
			console.log('getLine ('+this.viewtype+') ['+pstr+']');
		}
		return pstr;
	},
	getParent:function(){
		var me=this;
		var retObj={};
		//$.each(ce.arForeign,function(ndx,val){
		$.each(curPage.arForeign,function(ndx,val){
			if(val['child_grid']==me.tblid) {
				retObj['parent_grid']=val['parent_grid']; 
				retObj['parent_col']=val['parent_col'];
				retObj['child_col']=val['child_col'];
				return false;
			}
		});
		return retObj;
	},
	getRoot:function(rowid){
		var par_rowid=this.ptr.find('tbody tr[data-tt-id='+rowid+']').attr('data-tt-parent-id');
		while(par_rowid!=''){
			rowid=par_rowid;
			par_rowid=this.ptr.find('tbody tr[data-tt-id='+rowid+']').attr('data-tt-parent-id');
		}
		return rowid;
	},
	getSelectedLength:function(){
		if(this.viewtype==VERT){
			return $('#tbl'+this.tblid+' tr td:eq(0).selected').length;
		} else {
			return $('#tbl'+this.tblid+' tbody tr.selected').length;
		}
	},
	getSelectedIndex:function(){
		if(this.viewtype==VERT){
			return $('#tbl'+this.tblid+' tbody tr:eq(0) td').filter(function(){
				if($(this).class=='selected') return true;
				return false;
			}).index();
		} else {
			return $('#tbl'+this.tblid+' tbody tr.selected').index();
		}
	},
	getTDText:function(row,ndx){
		console.log('getTDText row ['+row+'] col ['+ndx+']'); 
		ndx=parseInt(ndx);
		var oRow=this.ptr.find('tbody tr:eq('+row+')');
		if(this.viewtype=='TREE'){
			switch(ndx){
			case 1:
				return oRow.attr('data-tt-id'); break;
			case 2:
				return oRowattr('data-tt-parent-id'); break;
			default:
				return oRow.find('td:eq('+(ndx-TREE_BASE)+')').text();
			}
		} 
	//		console.log('getTDText HRZN tblid ['+tblid+'] row ['+row+'] ndx ['+ndx+']');
		return oRow.find('td:eq('+ndx+')').text();
	},
	/* ***********************************************************************/
	removeNode:function(rowid){
		// console.log('tblid ['+tblid+'] rowid ['+rowid+']');
		this.ptr.find('tbody tr[data-tt-parent-id='+rowid+']').each(function(){
			this.removeNode($(this).attr('data-tt-id'));
		});
		var curTR=this.ptr.find('tbody tr[data-tt-id='+rowid+']');
		var arRecord=[];
		arRecord.push(rowid);
		arRecord.push(curTR.attr('data-tt-parent-id'));
		curTR.find('td:gt(0)').each(function(){
			arRecord.push($.trim($(this).text()));
		});
		console.log(arRecord.join('|'));
		$.post('_delete.php',{rowid:this.tblid,record:arRecord.join('|')},function(json){
			if(json['result']!='0'){
				alert(json['msg']); return false;
			}
			curTR.remove();
		},'json')
		.always(function(){
		});	
	},
	showAllNodes:function(){
		var tbl=$('#tbl'+this.tblid);
		var arChild=this.getChild();
		console.log(arChild);
		if(arChild.length<1) return ;
		console.log('selected length ['+this.getSelectedLength()+']');
		
		$.each(arChild,function(nChild,retObj){
			console.log(retObj);
			var oGrid=curPage.arGrid[retObj['child_grid']];
			oGrid.ptr.find('tbody tr').show();

			var buttons=$('#delete'+this.tblid+',#levellt'+this.tblid+',#levelrt'+this.tblid+',#levelup'+this.tblid+',#leveldn'+this.tblid);
			if(oGrid.viewtype=='TREE') {
				buttons.hide();
			}
			oGrid.showAllNodes();
		});
	},
	showNode:function(pkey){
		fShowNode(this.tblid,pkey);
	},
	update:function(curCtl,row,pos,oldtext,oldrecord){
		// console.log('fBlur ctrl type [',curCtl.prop('type'),']');
		var new_value = '';
		var ctlType=curCtl.prop('type');
		if(ctlType=='checkbox'){
			if(curCtl.is(':checked')) new_value='1';
			else new_value='0';
		} else {
			new_value=curCtl.val();
		}
		// if( new_value == null ) new_value='';
		if( oldtext == new_value) return;
		if(!this.validateData(pos,new_value)){
			curCtl.text(oldtext);
			return;
		}
		var curTD=curCtl.parent();
		var gson;
		$.post('_update.php',{tblid:this.tblid,viewtype:this.viewtype,newval:new_value,oldrecord:oldrecord,pos:pos},function(json){
			gson=json;
			console.log(json);
		},'json')
		.done(function(){
			console.log('ctlType ['+ctlType+']');
			console.log(gson);
			if(gson['result']=='0'){
			} else {
				alert(gson['msg']);
				if(ctlType!='checkbox') curTD.text(oldtext);
				return false;
			}
			if(ctlType!='checkbox') {
				curCtl.remove();
				curTD.text(new_value);
			}
		})
		.fail(function(){
			if(ctlType!='checkbox') {
				curCtl.remove();
				curTD.text(new_value);
			}
		});
	},
	validateData:function(pos,value){
		try {[].undef ()} catch (e) {
	//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
		}
		var dtype=this.getAttr(pos,'dtype');
		console.log('validateData ['+dtype+','+value+']');
		switch(dtype){
		case 'number': case 'decimal': case 'money':
			if(isNaN(value)) return false;
			break;
		case 'date':
			if(!is_date(value)) return false;
			break;
		case 'time':
			if(!is_time(value)) return false;
			break;
		case 'email':
			if(!validateEmail(value)) return false;
		}
		return true;
	}
}

function fShowNode(tblid,pkey){
	$('#tbl'+tblid+' tr[data-tt-parent-id='+pkey+']').each(function(){
		$(this).show();
		fShowNode(tblid,$(this).attr('data-tt-id'));
	});
}