var curPage;
var curTBL;
var curTR;
var curTD;
var eobj={};
var TREE_BASE=3;

$(document)
/* ***********************************************************************/
.resize(function(){
//	win_resize();
})
/* ***********************************************************************/
.ready(function(){
//	setlog(-1); 
	win_resize();
	$.getScript( "grid.js" )
	  .done(function( script, textStatus ) {
//		init_screen();

		//  in case of the web browser does not support debug window such as IE9.
		if (typeof console === 'undefined' || typeof console.log === 'undefined') {
			console = {};
			console.log = function(msg) {/*alert(msg)*/;};
			console.clear = function(){}
		}
//		$('button').button().css('height','20px');
		curPage=new fPage();
//		console.log('grid_count ['+curPage.grid_count+']');
		$('#mainmenu').treeview({
			collapsed: false,
	        animated: "medium",
	        control:"#sidetreecontrol",
	        persist: "location"
		});
	  })
	  .fail(function( jqxhr, settings, exception ) {
	    console.log('failed to load js');
	});
})
/* ***********************************************************************/
.on('click','#btnPersonal', function() {
//	document.location = 'crm.php?_p=124EF3FA72DD&_e='+$('#_e').val();
	document.location='svctl.php?_p=124EF3FA72DD00000';
})
/* ***********************************************************************/
.tooltip()
/* ***********************************************************************/
.on('click','label[id^=reload]',function(){
	var tblid=(this.id).substr(6);
	alert('.....')
	var oGrid=curPage.arGrid[tblid];
	oGrid.start=0;
	oGrid.ptr.find('tbody').empty();  
	oGrid.drawGrid(true);
	if(oGrid.viewtype=='TREE'){
		
	}
	return false;
})
/***** ADD NEW **********************************************************/
.on('click','label[id^=addnew]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}
	console.log('AddNew');
	var tblid=(this.id).substr(6);
	var oGrid=curPage.arGrid[tblid];
	
	// var viewtype=$('#tbl'+tbl).attr('viewtype');
	// wlog('rowid ['+rowid+']');
	var parcol=0;
	var parvalue='';	// the value of parent record in the parent grid
	var sametable='N';
	var childCol=-1;
	var curForeign=curPage.arForeign;
	var row=oGrid.getSelectedIndex();

	console.log('viewtype ['+oGrid.viewtype+']');
	switch(oGrid.viewtype){
	case 'HRZN':
		var retObj=oGrid.getParent();
		console.log(retObj)
		console.log(curPage.arGrid);
		var childCol=-1;
		if(!$.isEmptyObject(retObj)){	// No parent
			// var selectedRow=$('#tbl'+oGrid.tblid+' tbody tr.selected');
			
			var parentGrid=curPage.arGrid[retObj['parent_grid']];
			// if(selectedRow.length<1){
			if(parentGrid.getSelectedLength()<1){
				alert('No parent record selected.');
				return false;
			}
			childCol=retObj['child_col'];
			parvalue=parentGrid.getTDText(parentGrid.getSelectedIndex(),retObj['parent_col']);
			console.log('parvalue ['+parvalue+']');
		}
		console.log('parcol ['+retObj['parent_col']+'] parvalue ['+parvalue+'] childCol ['+retObj['child_col']+'] viewtype ['+oGrid.viewtype+']');
		$.post('_addnew.php',{tblid:oGrid.tblid,parvalue:parvalue,childcol:childCol},function(json){
			console.log(json);
			if(json['result']!='0'){
				alert(json['msg']);
				return false;
			}
			oGrid.ptr.prepend(json['html']);
		},'json');
		break;
	case 'TREE':
		console.log('selectedLength ['+oGrid.getSelectedLength()+']');
		if(oGrid.getSelectedLength()>0){	// find selected row in itself.
			parvalue=oGrid.selected.attr('data-tt-id');
			console.log('parvalue ['+parvalue+']');
			sametable='Y';	// parent rowid exists in the other record on the same grid.
		} else {	// 
			var retObj=oGrid.getParent();
			console.log(retObj);
			var parentGrid=curPage.arGrid[retObj['parent_grid']];
			console.log('parent SelectedLength ['+parentGrid.getSelectedLength()+']');
			if(parentGrid.getSelectedLength()>0){
				row=parentGrid.getSelectedIndex();
				parvalue=parentGrid.getTDText(row,retObj['parent_col']);
			}
			sametable='N';	// parent rowid is in the parent table.
		}
		console.log('parvalue ['+parvalue+'] tblid ['+oGrid.tblid+']');
		console.log(retObj);
		$.post('_addnew.php',{tblid:oGrid.tblid,parvalue:parvalue,sametable:sametable},function(json){
			console.log(json);
			if(json['result']!='0'){
				alert(json['msg']);
				return false;
			}
			if(parvalue==''){
				oGrid.treetable("loadBranch",null,json['html']);
			} else {
				var pnode=$('#tbl'+oGrid.tblid).treetable('node',parvalue);
				oGrid.treetable("loadBranch",pnode,json['html']);
			}
		},'json');
		break;
	case 'VERT':
	}
})
/* ***********************************************************************/
.on('click','.duplicate',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	var my=garGrid[$(this).index('.duplicate')];
	var pstr=[];
	
	if($.isFunction(my.bef_dup)) if(!my.bef_dup()) return false;
	
	my.duplicateRow();
	return false;
})
/* ***********************************************************************/
.on('click','label[id^=delete]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	/*
	 * based on the cascade delete.
	 */
	var me=$(this);
	var tblid=(this.id).substr(6);
	var oGrid=curPage.arGrid[tblid];

	$.ajax({
		url:'_remove.php',method:'post',dataType:'json',
		data:'tblid='+tblid+'&record='+oGrid.getLine(),
		beforeSend:function(){
			console.log(this.data);
		},
		success:function(json){
			if(json['result']=='0'){
			} else {
			}
		}
	});
	/*	
	switch(oGrid.viewtype){
	case 'TREE':
		if(!confirm('선택된 항목에 속한 하위항목이 있으면 모두 삭제됩니다. 모두 삭제할까요 ?')) return false;	// confirm() does not work only on the Chrome.

		oGrid.remove(oGrid.selected.attr('data-tt-id'));
		oGrid.removeNode(oGrid.selected.attr('data-tt-id'));
		if(oGrid.getSelectedLength()<1) {
			var buttons=$('#delete'+oGrid.tblid+',#levellt'+oGrid.tblid+',#levelrt'+oGrid.tblid+',#levelup'+oGrid.tblid+',#leveldn'+oGrid.tblid);
			buttons.hide();
		}
		break;
	default:
		if(!confirm('정말로 삭제할까요 ?')) return false;	// confirm() does not work only on the Chrome.
	
		oGrid.remove(oGrid.getLine());

		oGrid.ptr.find('tbody tr').each(function(){
			var curTR=$(this);

			if(!curTR.find('td:first').hasClass('crossed')) return true;
	
			var arRecord=[];
			curTR.find('td:gt(0)').each(function(){
				arRecord.push($.trim($(this).text()));
			});
			wlog(arRecord.join('|'));
			var skip=true;
			$.post('_delete.php',{rowid:oGrid.tblid,record:arRecord.join('|')},function(json){
				console.log(json);
				if(json['result']!='0'){
					alert(json['msg']); skip=false; return false;
				}
				curTR.remove();
				me.hide();
			},'json');
		});
	}
	*/
	return false;
})
/* ***********************************************************************/
.on('click','label[id^=find]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	var oGrid=curPage.arGrid[tblid];
	var pstr=$.trim($('#srch'+oGrid.tblid).val());
//	console.log(pstr);
	if(pstr=='') {
		oGrid.ptr.find('tbody tr').show();
		return false;
	}
	switch(oGrid.viewtype){
	case 'TREE':
		propname='td'; break;
	default:
		propname='td:gt(0)'; break;
	}
	oGrid.ptr.find('tbody tr').each(function(){
		var skip=true;
		$(this).find(propname).each(function(){
			if(new RegExp(pstr,'i').test($(this).text())){
				skip=false;
				return false;
			}
		});
		if(skip){
			$(this).hide();
		}
	});
	return false;
	
})
/* ***********************************************************************/
.on('keydown','input[id^=srch]',function(e){
	if(e.which==KEY_ENTER) {
		$('#find'+(this.id).substr(4)).click();
		return false;
	}
})
/* ***********************************************************************/
.on('click','input[id^=chk]',function(){
	var tblid=(this.id).substr(3);
	if($(this).is(':checked')){
		$('#tbl'+tblid+' tbody tr').each(function(){
			$(this).find('td:first').addClass('crossed');
		});
		$('#delete'+tblid).show();
	} else {
		$('#tbl'+tblid+' tbody tr').each(function(){
			$(this).find('td:first.crossed').removeClass('crossed');
		});
		$('#delete'+tblid).hide();
	}
	return true;
})
/* ***********************************************************************/
.on('mousedown','table[id^=tbl] tbody td',function(e){
	eobj['x']=e.PageX;
	eobj['y']=e.pageY;
	eobj['now']=e.timeStamp;
})
/* ***********************************************************************/
.on('click','table[id^=tbl] tbody td input:checkbox',function(e){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}
	var me=$(this);
	var tbl=$(this).closest('table');
	var tblid=(tbl.attr('id')).substr(3);
	var oGrid=curPage.arGrid[tblid];

	var row=me.parent().parent().index();
	var col=me.parent().index();
//	console.log('this.checked ['+this.checked+']');
	
	var oldrecord=oGrid.getLine(row);
//	console.log('row ['+row+'] col ['+col+'] oldrecord ['+oldrecord+']');
	oGrid.update($(this),row,col,(this.checked?'0':'1'),oldrecord);
	
	// return false;
})
/* ***********************************************************************/
.on('mouseup','table[id^=tbl] tbody td',function(e){
//	console.log('mouseup');
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}
	
	var me=$(this);
	
	var col=me.index();
	var curTR=me.parent();
	var row=curTR.index();
	var tbl=me.closest('table');
	var tblid=(tbl.attr('id')).substr(3);
	var oGrid=curPage.arGrid[tblid];
	
	/*
	 * if gap<.3 seconds then select else edit
	 */
	if(eobj['x']!=e.PageX || eobj['y']!=e.pageY || e.timeStamp-eobj['now']<300) {	// for edit

		/*----------------------------------------------------------------*/
		/* Check for delete record in HZRN table                    */
		/*----------------------------------------------------------------*/
		if((oGrid.viewtype=="HRZN" && col==0) || (oGrid.viewtype=="VERT" && row==0)){
			$(this).toggleClass('crossed');
			if(oGrid.viewtype=="HRZN"){
				ptr="td:eq(0)";
			} else {
				ptr="tr:eq(0) td";
			}
			if(oGrid.ptr.find(ptr).hasClass('crossed')){
				$('#delete'+tblid).show();
				$('#chk'+tblid).attr('checked',true);
			} else {
				$('#delete'+tblid).hide();
				$('#chk'+tblid).attr('checked',false);
			}
			return false;
		}
		/*----------------------------------------------------------------*/
		/* Click parent record and Show only child records      */
		/*----------------------------------------------------------------*/
		if(oGrid.viewtype=='VERT'){
			$('#tbl'+oGrid.tblid+' tbody tr').each(function(){
				$(this).find('td:eq('+col+')').toggleClass('selected');
			});
		} else {
			$('#tbl'+oGrid.tblid+' tr.selected').not(curTR).removeClass('selected');
			curTR.toggleClass('selected');
			
			if(oGrid.getSelectedLength()<1){
				oGrid.showAllNodes();
			} else {
				var buttons=$('#delete'+tblid+',#levellt'+tblid+',#levelrt'+tblid+',#levelup'+tblid+',#leveldn'+tblid);
				buttons.show();
				var arChild=oGrid.getChild();
				if(arChild.length<1) return ; // no record in the child grid
	
				// var row=oGrid.getSelectedIndex();
				$.each(arChild,function(nChild,valObj){
					$('#tbl'+valObj['child_grid']+' tbody tr').hide();
					var nFirst=-1;
					var pkey=oGrid.getTDText(row,valObj['parent_col']);
					var childGrid=curPage.arGrid[valObj['child_grid']];
					switch(childGrid.viewtype){
					case 'HRZN':
						childGrid.ptr.find('tbody tr').each(function(){
							if($(this).find('td:eq('+valObj['child_col']+')').text()==pkey){
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
							}
						});
						childGrid.ptr.find('tbody tr:eq('+nFirst+') td:eq(1)').trigger('mouseup');
						break;
					case 'TREE':
						switch(valObj['child_col']){
						case 1:
							childGrid.ptr.find('tbody tr[data-tt-id='+pkey+']').each(function(){
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
								childGrid.showNode(pkey);
							});
							break;
						case 2:
							childGrid.ptr.find('tbody tr[data-tt-parent-id='+pkey+']').each(function(){
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
								childGrid.showNode($(this).attr('data-tt-id'));
							});
							break;
						default:
							var ndx=parseInt(valObj['child_col'])-3;
							childGrid.ptr.find('tbody tr').each(function(){
								if($(this).find('td:eq('+ndx+')').text()!=pkey) return true;
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
								// oGrid.showNode($(this).attr('data-tt-id'));
							});
						}
					}
				});
			}
		}
		return false;
	}
	
	// procedure for edit, not select
	var oldtext = me.text();
	var oldrecord=oGrid.getLine(row);
	var pstr = '';
	var strCtl='';
	var num_input='';

	dtype=oGrid.getAttr(col,'dtype');
	switch(dtype){
		case 'gender':                                                
		case 'bool':
		// case 'align':
		// case 'valign':
			if(dtype=='gender'){
				pstr='<select id=seltext name=seltext><option value=M>남</option><option value=F>여</option></select>';
			} else if(dtype=='bool'){
				pstr='<select id=seltext name=seltext><option value=1>1</option><option value=0>0</option></select>';
			// } else if(dtype=='align'){
			// 	pstr='<select id=seltext name=seltext><option value="left">left</option><option value="center">center</option><option value="right">right</option></select>';
			// } else if(dtype=='valign'){
			// 	pstr='<select id=seltext name=seltext><option value="top">top</option><option value="middle">middle</option><option value="bottom">bottom</option></select>';
			}
			me.html(pstr);
			
			setTimeout(function(){$('#seltext').focus()},100); 
			$('#seltext')
			.on('keydown',function(e){
				switch( e.keyCode||e.which ){
				case KEY_ESCAPE:
					me.text(oldtext);
					return false;
				case KEY_TAB: case KEY_ENTER:
					me.trigger('blur');
					break;
				}
			})
			.on('blur',function(e){
				try {[].undef ()} catch (e) {
					wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
				}
				oGrid.update($(this),row,col,oldtext,oldrecord);
			});
			break;
		case 'radio':
			var strList=$('#tbl'+oGrid.tblid+' thead tr:eq(0) th:eq('+col+')').attr('list');
			pstr="<select id=seltext name=seltext><option value=''></option>";
			$.each(strList.split('`'),function(ndx,val){
				pstr+="<option value='"+val+"'";
				if(oldtext==val) pstr+=" selected";
				pstr+=">"+val+"</option>";	
			})
			pstr+="</select>";
			me.html(pstr);
			setTimeout(function(){$('#seltext').focus()},100);
			$('#seltext')
			.on('keydown',function(e){
				switch( e.keyCode||e.which ){
				case KEY_ESCAPE:
					wlog('key_escape');
					me.text(oldtext);
					return false;
				case KEY_TAB: case KEY_ENTER:
					me.trigger('blur');
					break;
				}
				return false;
			})
			.on('blur',function(e){
				try {[].undef ()} catch (e) {
					wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
				}
				oGrid.update($(this),row,col,oldtext,oldrecord);
				return false;
			});
			break;
		case 'select':
			strCtl="seltext";
			console.log('dtype tblid ['+tblid+'] ndx ['+col+']');
			var pstr='';
			$.post('_radio.php',{tblid:oGrid.tblid,ndx:col,oldrecord:oldrecord},function(json){
				console.log(json);
				if(json['result']!='0'){
					alert(json['msg']);
					return false;
				}
				pstr="<select id=seltext name=seltext><option value=''></option>";
				$.each(json['element'].split('`'),function(ndx,val){
					pstr+="<option value='"+val+"'";
					if(oldtext==val) pstr+=" selected";
					pstr+=">"+val+"</option>";
				});
				pstr+="</select>";
				me.html(pstr);
				setTimeout(function(){$('#seltext').focus()},100);
				$('#seltext')
				.on('keydown',function(e){
					switch( e.keyCode||e.which ){
					case KEY_ESCAPE:
						wlog('key_escape');
						me.text(oldtext);
						return false;
					case KEY_TAB: case KEY_ENTER:
						me.trigger('blur');
						break;
					}
					return false;
				})
				.on('blur',function(e){
					try {[].undef ()} catch (e) {
						wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
					}
					oGrid.update($(this),row,col,oldtext,oldrecord);
					return false;
				});
			},'json');
			break;
		case 'number':
		case 'decimal':
		case 'money':
			num_input=' class=num_input';
		case 'time':
		case 'date':
		case 'text':
		case 'time_interval':
		default:
			var nSize=32;
			var curTD=$('#tbl'+oGrid.tblid+' thead tr:eq(0) td:eq('+col+')');
			var digit=parseInt(curTD.attr('digit'));
			var pixel=parseInt(curTD.attr('pixel'));
			if(digit>nSize) {
				// when digit is grater than nSize, TEXTAREA is used as HTML tag.
				var nRow = Math.floor(digit/nSize);
				if(digit % nSize>0) nRow++;
				pstr='<textarea rows='+nRow+' cols='+nSize+' id=editctl name=editctl>'+oldtext+'</textarea>';
			} else {
				pstr='<input type='+dtype+' id=editctl name=editctl style="width:'+pixel+'px;" maxlength='+digit+
				num_input+' value="'+oldtext+'"></td></tr>';
			}
			console.log(pstr);
			strCtl='editctl';
			me.html(pstr);
			setTimeout(function(){$('#editctl').select().focus();},100);
			$('#'+strCtl)
			.keydown(function(e){
				switch( e.keyCode||e.which ){
				case KEY_ESCAPE:
					me.text(oldtext);
					return false;
				case KEY_TAB: case KEY_ENTER:
					$(this).trigger('blur');
					return false;
				}
			})
			.blur(function(e){
				try {[].undef ()} catch (e) {
					wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
				}
				oGrid.update($(this),row,col,oldtext,oldrecord);
				return false;
			});
		}
		return false;		// to stop double click to occur twice.
})
/* ***********************************************************************/
.on('click','#mainmenu li',function(){
	if(this.id==''){
		alert('No page defined.');
		return false;
	}
	document.location='./svctl.php?_p='+this.id;
})
/* ***********************************************************************/
.on('click','thead tr',function(){
	wlog('thead tr');
})
/* ***********************************************************************/
.on('scroll','div[id^=dv]',function(){
	console.log('scroll');
})
/* ***********************************************************************/
.on('click','label[id^=leveldn]',function(){	// for TREE only.
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	/*
	 * based on the cascade delete.
	 */
	var tblid=(this.id).substr(7);
	var oGrid=new fGrid(tblid);
	var row=$('#tbl'+tblid+' body tr.selected').index();

	var curNode={ rowid:curTR.attr('data-tt-id'), par_rowid:curTR.attr('data-tt-parent-id'),seqno:parseInt(curTR.find('td:eq(1)').text())};
	
	// var row=curTR.index()+1;
	// console.log('row ['+row+'] length ['+$('#tbl'+tblid+' tbody tr').length+']');
	// if(row>$('#tbl'+tblid+' tbody tr').length-1) return false;
	var nextTR=$('#tbl'+tblid+' tbody tr:eq('+row+')');
	
	var nextNode={rowid:nextTR.attr('data-tt-id'), par_rowid:nextTR.attr('data-tt-parent-id'),seqno:parseInt(nextTR.find('td:eq(1)').text())};
	
	if(curNode.par_rowid!=nextNode.par_rowid) return false;
	var paramObj={step:'replace'};
	paramObj['tblid']=tblid;
	paramObj['rowid_src']=curNode.rowid;
	paramObj['seqno_src']=curNode.seqno;
	paramObj['rowid_dst']=nextNode.rowid;
	paramObj['seqno_dst']=nextNode.seqno;
	console.log(paramObj);
	$.post('_move.php',paramObj,function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		// exchange two SEQNOs.
		var tmp=nextTR.find('td:eq(1)').text();
		nextTR.find('td:eq(1)').text(curTR.find('td:eq(1)').text());
		curTR.find('td:eq(1)').text(tmp);
		
		// exchange two TRs' html.
		var pstr='<tr data-tt-id='+curNode.rowid+' data-tt-parent-id='+curNode.par_rowid+'>'+curTR.html()+'</tr>';
		curTR.remove();
		nextTR.after(pstr);
		$('#tbl'+tblid+' tbody tr[data-tt-id='+curNode.rowid+']').addClass('selected');
	},'json');
	return false;
})
.on('click','label[id^=levelup]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	/*
	 * based on the cascade delete.
	 */
	var tblid=(this.id).substr(7);
	
	var curTR=$('#tbl'+tblid+' tbody tr.selected');
	var curNode={ rowid:curTR.attr('data-tt-id'), par_rowid:curTR.attr('data-tt-parent-id'),seqno:parseInt(curTR.find('td:eq(1)').text())};
	
	var row=curTR.index()-1;
	if(row<0) return false;
	var nextTR=$('#tbl'+tblid+' tbody tr:eq('+row+')');
	var nextNode={rowid:nextTR.attr('data-tt-id'), par_rowid:nextTR.attr('data-tt-parent-id'),seqno:parseInt(nextTR.find('td:eq(1)').text())};
	
	if(curNode.par_rowid!=nextNode.par_rowid) return false;
	var paramObj={step:'replace'};
	paramObj['tblid']=tblid;
	paramObj['rowid_src']=curNode.rowid;
	paramObj['seqno_src']=curNode.seqno;
	paramObj['rowid_dst']=nextNode.rowid;
	paramObj['seqno_dst']=nextNode.seqno;
	console.log(paramObj);
	$.post('_move.php',paramObj,function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		// exchange two SEQNOs.
		var tmp=nextTR.find('td:eq(1)').text();
		nextTR.find('td:eq(1)').text(curTR.find('td:eq(1)').text());
		curTR.find('td:eq(1)').text(tmp);
		
		// exchange two TRs' html.
		var pstr='<tr data-tt-id='+curNode.rowid+' data-tt-parent-id='+curNode.par_rowid+'>'+curTR.html()+'</tr>';
		curTR.remove();
		nextTR.before(pstr);
		$('#tbl'+tblid+' tbody tr[data-tt-id='+curNode.rowid+']').addClass('selected');
	},'json');
	return false;
})
.on('click','label[id^=levellt]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	/*
	 * based on the cascade delete.
	 */
	var tblid=(this.id).substr(7);
	
	var curTR=$('#tbl'+tblid+' tbody tr.selected');
	var curNode={ rowid:curTR.attr('data-tt-id'), par_rowid:curTR.attr('data-tt-parent-id'),seqno:parseInt(curTR.find('td:eq(1)').text())};
	
	var row=curTR.index()-1;
	if(row<0) return false;
	var nextTR=$('#tbl'+tblid+' tbody tr:eq('+row+')');
	var nextNode={rowid:nextTR.attr('data-tt-id'), par_rowid:nextTR.attr('data-tt-parent-id'),seqno:parseInt(nextTR.find('td:eq(1)').text())};
	console.log(curNode.par_rowid+'/'+nextNode.rowid);
	
	if(curNode.par_rowid!=nextNode.rowid) return false;
	var paramObj={optype:'move',step:'left'};
	paramObj['tblid']=tblid;
	paramObj['rowid_src']=curNode.rowid;
//	paramObj['seqno_src']=curNode.seqno;
	paramObj['rowid_dst']=nextNode.par_rowid;
//	paramObj['seqno_dst']=nextNode.seqno;
	// find root node
//	paramObj['rowid_root']=getRoot(tblid,curNode.rowid);
	
	console.log(paramObj);
	console.log(nextTR.html());
	var nextHTML=nextTR.html();
	var curHTML=curTR.html();
	console.log(curTR.html());
		// exchange two TRs' html.
		nextHTML=nextHTML.replace(/\<a.*\/\a\>/i,'');
		var n=nextHTML.indexOf('</span>')+7;
		curHTML=nextHTML.substr(0,n)+curHTML.substr(n);
		console.log('curHTML ['+curHTML+']');
		$('#tbl'+tblid).treetable('unloadBranch',$('#tbl'+tblid).treetable('node',curNode.rowid));
		nextTR.after(curHTML);
//		$('#tbl'+tblid+' tbody tr[data-tt-id='+curNode.rowid+']').addClass('selected');
//	},'json');
	return false;
})
.on('click','label[id^=levelrt]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	/*
	 * based on the cascade delete.
	 */
	var tblid=(this.id).substr(7);
	
	var curTR=$('#tbl'+tblid+' tbody tr.selected');
	var curNode={ rowid:curTR.attr('data-tt-id'), par_rowid:curTR.attr('data-tt-parent-id'),seqno:parseInt(curTR.find('td:eq(1)').text())};
	
	var row=curTR.index()-1;
	if(row<0) return false;
	var nextTR=$('#tbl'+tblid+' tbody tr:eq('+row+')');
	var nextNode={rowid:nextTR.attr('data-tt-id'), par_rowid:nextTR.attr('data-tt-parent-id'),seqno:parseInt(nextTR.find('td:eq(1)').text())};
	if(curNode.par_rowid!=nextNode.par_rowid) return false;
	var paramObj={step:'right'};
	paramObj['tblid']=tblid;
	paramObj['rowid_src']=curNode.rowid;
//	paramObj['seqno_src']=curNode.seqno;
	paramObj['rowid_dst']=nextNode.rowid;
//	paramObj['seqno_dst']=nextNode.seqno;
	console.log(paramObj);
	$.post('_move.php',paramObj,function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		$('#tbl'+tblid).treetable('move',curNode.rowid,nextNode.rowid);
		$('#tbl'+tblid+' tbody tr[data-tt-id='+curNode.rowid+']').addClass('selected');
	},'json');
	return false;
	
})
.on('click','#btnLogout',function(){
//	console.log('logout');
	$json='';
	$.post('_svlogout.php',{},function(json){
		console.log('result ['+json['result']+']');
		$json=json;
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		document.location = './sv.php';
	},'json');
	console.log($json);
})
;

$('div')
.scroll(function(){
//	console.log('document scroll');
})
;
