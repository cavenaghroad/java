var curPage;
var dblclicking=false;	// true if script is in double click mode.
var eobj={};
var TREE_BASE=3;

$(document)
/* ***********************************************************************/
.resize(function(){
//	win_resize();
})
/* ***********************************************************************/
.ready(function(){
	win_resize();
	$('button').button().css('height','20px');
	//  in case of the web browser does not support debug window such as IE9.
	if (typeof console === 'undefined' || typeof console.log === 'undefined') {
		console = {};
		console.log = function(msg) {/*alert(msg)*/;};
		console.clear = function(){}
	}
	if($('#_p').val()=='')	{
		alert('No page ID given');
		window.history.back();
		return false;
	}
	$.post('./servlet/_init.php',{optype:'partylist',_p:$('#_p').val()},function(answer){
		if(answer['result']=='-99'){
			alert(answer['msg']);
			document.location='./index.php';
			return false;
		}
		$('#partylist').html(answer['data']);
	},'json');
	$.post('_mainmenu.php',{optype:'build'},function(answer){
		console.log(answer);
		if(answer['result']=="-99"){
			alert(answer['msg']); return false;
		} else if(answer['result']=="0"){
			$('#c_menu').hide();
			$('#c_menu').append(answer['data']);
			setTimeout(function(){
				$('#mainmenu').treeview({
					collapse:false
				});
				$('#c_menu').show();
			},100);
		}
	},'json');
	curPage=new fPage();
})
.on('click','.logout',function(){
	if(!confirm('정말로 로그아웃하시겠습니까?')) return false;
	$.post('_logout.php',{},function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		document.location = './index.php';
	},'json');
})
/* ***********************************************************************/
.on('click','#btnPersonal', function() {
	document.location='crmctl.php?_p=124EF3FA72DD00000';
})
/* ***********************************************************************/
.tooltip()
/* ***********************************************************************/
.on('click','label[id^=reload]',function(){
	var rowid=(this.id).substr(6);
	console.log('rowid ['+rowid+']');
	curPage.arGrid[rowid].start=0;
	$('#tbl'+rowid+' tbody').empty();  
	curPage.arGrid[rowid].drawGrid(true);
	if($('#tbl'+rowid).attr('viewtype')=='TREE'){
		
	}
	return false;
})
/*************************************************************************/
.on('change','#selParty',function(){
	var party=$(this).val();
	$.post('_crmctl.php',{_e:party},function(json){
//		console.log(json);
		if(json['result']!='0'){
			alert(json['msg']);
			return false;
		}
		document.location='crmctl.php?_p='+json['firstpage'];
	},'json');
	return false;
})
/****** ADD NEW **********************************************************/
.on('click','label[id^=addnew]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}
	rowid=(this.id).substr(6);
	var viewtype=$('#tbl'+rowid).attr('viewtype');
	wlog('rowid ['+rowid+']');
	var parcol=0;
	var parvalue='';
	var sametable='N';
	var childCol=-1;
	var curForeign=curPage.arForeign;
	var row=$('#tbl'+rowid+' tbody tr.selected').index();

	var bSkip=false;
	switch(viewtype){
	case 'TREE':
		if($('#tbl'+rowid+' tbody tr.selected').length>0){	// find selected row in itself.
			parvalue=$('#tbl'+rowid+' tbody tr.selected').attr('data-tt-id');
			sametable='Y';	// parent rowid is in same table.
		} else {	// .
			var retObj=curPage.getParent(rowid);
			console.log(retObj);
			if($('#tbl'+retObj['parent_grid']+' tbody tr.selected').length>0){
				row=$('#tbl'+retObj['parent_grid']+' tbody tr.selected').index();
				parvalue=getNodeText(retObj['parent_grid'],row,retObj['parent_col']);
			}
			sametable='N';	// parent rowid is in the parent table.
		}
		console.log('parvalue ['+parvalue+'] tblid ['+rowid+']');
		console.log(retObj);
		$.post('_addnew.php',{optype:'tree',tblid:rowid,parvalue:parvalue,sametable:sametable},function(json){
			console.log(json);
			if(json['result']!='0'){
				alert(json['msg']);
				return false;
			}
			if(parvalue==''){
				$('#tbl'+rowid).treetable("loadBranch",null,json['html']);
			} else {
				var pnode=$('#tbl'+rowid).treetable('node',parvalue);
				$('#tbl'+rowid).treetable("loadBranch",pnode,json['html']);				
//					$('#tbl'+rowid+' tbody tr[data-tt-id='+parvalue+']').after(json['html']);
			}
		},'json');
		break;
	case 'HRZN':
		var retObj=curPage.getParent(rowid);
		console.log(retObj)
		var childCol=-1;
		if(!$.isEmptyObject(retObj)){
			console.log('parent_grid ['+retObj['parent_grid']+'] length ['+$('#tbl'+retObj['parent_gird']+' tbody tr.selected').length+']')
			if($('#tbl'+retObj['parent_grid']+' tbody tr.selected').length<1){
				alert('이전 단계의 데이터가 선택되지 않았습니다.');
				return false;
			}
			if($('#tbl'+retObj['parent_grid']).attr('viewtype')=='TREE' && $('#tbl'+retObj['parent_grid']+' tbody tr.selected').attr('data-tt-parent-id')==''){
				alert('이전 단계의 데이터테이블의 표제어를 선택할 수 없습니다.');
				return false;
			}
			childCol=retObj['child_col'];
			var row=$('#tbl'+retObj['parent_grid']+' tbody tr.selected').index();
			parvalue=getNodeText(retObj['parent_grid'],row,retObj['parent_col']);
		}
//		console.log('parcol ['+retObj['parent_col']+'] parvalue ['+parvalue+'] childCol ['+retObj['child_col']+'] viewtype ['+viewtype+']');
		$.post('_addnew.php',{optype:'hrzn',viewtype:viewtype,tblid:rowid,parvalue:parvalue,childcol:childCol},function(json){
			console.log(json);
			if(json['result']!='0'){
				alert(json['msg']);
				return false;
			}
			$('#tbl'+rowid+' tbody').append(json['html']);
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
	var rowid=(this.id).substr(6);
	var ce=$(this);
	
//	if($.isFunction(preDelete)) {
//		preDelete();
//		return false;
//	}
	
	switch($('#tbl'+rowid).attr('viewtype')){
	case 'TREE':
		if(!confirm('선택된 항목에 속한 하위항목이 있으면 모두 삭제됩니다. 모두 삭제할까요 ?')) return false;	// confirm() does not work only on the Chrome.

		removeNode(rowid,$('#tbl'+rowid+' tbody tr.selected').attr('data-tt-id'));
		if($('#tbl'+rowid+' tbody tr.selected').length<1) {
			var buttons=$('#delete'+rowid+',#levellt'+rowid+',#levelrt'+rowid+',#levelup'+rowid+',#leveldn'+rowid);
			buttons.hide();
		}
		break;
	default:
		if(!confirm('정말로 삭제할까요 ?')) return false;	// confirm() does not work only on the Chrome.
	
		$('#tbl'+rowid+' tbody tr').each(function(){
			var curTR=$(this);
//			console.log(curTR.find('td:first').hasClass('crossed'));
			if(!curTR.find('td:first').hasClass('crossed')) return true;
	
			var arRecord=[];
			curTR.find('td:gt(0)').each(function(){
				arRecord.push($.trim($(this).text()));
			});
			wlog(arRecord.join('|'));
			var skip=true;
			$.post('_delete.php',{rowid:rowid,record:arRecord.join('|')},function(json){
				console.log(json);
				if(json['result']!='0'){
					alert(json['msg']); skip=false; return false;
				}
				curTR.remove();
				ce.hide();
			},'json');
		});
	}
	return false;
})
/* ***********************************************************************/
.on('click','label[id^=find]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	var rowid=(this.id).substr(4);
	var pstr=$.trim($('#srch'+rowid).val());
//	console.log(pstr);
	if(pstr=='') {
		$('#tbl'+rowid+' tbody tr').show();
		return false;
	}
	switch($('#tbl'+rowid).attr('viewtype')){
	case 'TREE':
		propname='td'; break;
	default:
		propname='td:gt(0)'; break;
	}
	$('#tbl'+rowid+' tbody tr').each(function(){
		var skip=true;
		$(this).find(propname).each(function(){
//			if(($(this).text()).test(/pstr/g) {
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
.on('keydown','label[id^=srch]',function(e){
	if(e.which==KEY_ENTER) {
		$('#find'+(this.id).substr(4)).click();
		return false;
	}
})
/* ***********************************************************************/
.on('click','button[id^=chk]',function(){
	var rowid=(this.id).substr(3);
	if($(this).is(':checked')){
		$('#tbl'+rowid+' tbody tr').each(function(){
			$(this).find('td:first').addClass('crossed');
		});
		$('#delete'+rowid).show();
	} else {
		$('#tbl'+rowid+' tbody tr').each(function(){
			$(this).find('td:first.crossed').removeClass('crossed');
		});
		$('#delete'+rowid).hide();
	}
	return true;
})
/* ***********************************************************************/
.on('mousedown','table[id^=tbl] tbody td',function(e){
	eobj['x']=e.PageX;
	eobj['y']=e.pageY;
	eobj['now']=e.timeStamp;
})
//.on('dblclick','table[id^=tbl] tbody td',function(e){
//	alert($(this).closest('table').attr('id'));
////	$('#dlg_rowid').val($('#)
//	$('#dlgEdit').dialog('open');
//})
/* ***********************************************************************/
.on('click','table[id^=tbl] tbody td input:checkbox',function(e){

	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}
	var me=$(this);
	var curTR=me.parent().parent();
	var tbl=$(this).closest('table');
	var tblid=(tbl.attr('id')).substr(3);
	var viewtype=tbl.attr('viewtype');
	var row=curTR.index();
	var col=me.parent().index();
	var oldtext;
	var bChecked;
	console.log('this.checked ['+this.checked+']');
	if(this.checked) {
		oldtext='0';
		bChecked='1';
	} else {
		oldtext='1';
		bChecked='0';
	}
	
	switch(viewtype){
	case 'TREE':
	case 'HRZN':
		oAttr=$('#tbl'+tblid+' thead tr:eq(0) th:eq('+col+')');
		dtype=oAttr.attr('dtype');
		pos=col;
		break;
	case 'VERT':
		oAttr=$('#tbl'+tblid+' tbody tr:eq('+row+') td:eq(0)');
		dtype=oAttr.attr('dtype');
		pos=row;
	}
	var oldrecord;
	switch(viewtype){
	case 'TREE':
	case 'HRZN':
		oldrecord=getLine(tblid,row);
		break;
	case 'VERT':
		oldrecord=getLine(tblid,col);
	}
	console.log('viewtype ['+viewtype+'] dtype ['+dtype+'] bChecked ['+bChecked+'] pos ['+pos+'] row ['+row+'] col ['+col+'] oldtext ['+oldtext+'] HTML ['+oldrecord+']');
	fBlur($(this),me,pos,tblid,viewtype,oldtext,dtype,oldrecord);
	
	e.stopPropagation();
})
/* ***********************************************************************/
.on('mouseup','table[id^=tbl] tbody td',function(e){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	var col=$(this).index();
	var curTR=$(this).parent();
	var tbl=$(this).closest('table');
//	alert(tbl.attr('id'));
	var tblid=(tbl.attr('id')).substr(3);
	var viewtype=tbl.attr('viewtype');
	var row=curTR.index();

	if(eobj['x']!=e.PageX || eobj['y']!=e.pageY || e.timeStamp-eobj['now']<300) {

		/*----------------------------------------------------------------*/
		/* Check for delete record in HZRN table                    */
		/*----------------------------------------------------------------*/
		if(viewtype!='TREE' && col==0) {
			$(this).toggleClass('crossed');
			var skip=false;
			$('#tbl'+tblid+' tbody tr').each(function(){
				if($(this).find('td:first').hasClass('crossed')){
					skip=true; return false;
				}
			});
			if(skip){
				$('#delete'+tblid).show();
				$('#chk'+tblid).attr('checked',true);
				
			} else {
				$('#delete'+tblid).hide();
				$('#chk'+tblid).attr('checked',false);
			}
		/*----------------------------------------------------------------*/
		/* Click parent record and Show only child records      */
		/*----------------------------------------------------------------*/
		} else {	// for selecting row
			if(viewtype=='TREE'){
				tbl.find('tr').filter(function(){
					var bcolor=$(this).hasClass('selected')
					return bcolor==true;
				}).not(curTR).removeClass('selected');
				curTR.toggleClass('selected');
			} else {
				tbl.find('tr.selected').not(curTR).removeClass('selected');
				curTR.toggleClass('selected');
			}
			if($('#tbl'+tblid+' tbody tr.selected').length<1){
				showAllNodes(tblid);
			} else {
				var buttons=$('#delete'+tblid+',#levellt'+tblid+',#levelrt'+tblid+',#levelup'+tblid+',#leveldn'+tblid);
				if(viewtype=='TREE') {
					buttons.show();
				}
				var arChild=curPage.getChild(tblid);
				if(arChild.length<1) return ;
				
				var row=$('#tbl'+tblid+' tbody tr.selected').index();
				console.log('row index ['+row+']');
				$.each(arChild,function(nChild,valObj){
					var pkey=getNodeText(tblid,row,valObj['parent_col']);
					$('#tbl'+valObj['child_grid']+' tbody tr').hide();
					var nFirst=-1;
					console.log('viewtype ['+$('#tbl'+valObj['child_grid']).attr('viewtype')+'] child_col ['+valObj['child_col']+']');
					switch($('#tbl'+valObj['child_grid']).attr('viewtype')){
					case 'HRZN':
						$('#tbl'+valObj['child_grid']+' tbody tr').each(function(){
							if($(this).find('td:eq('+valObj['child_col']+')').text()==pkey){
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
							}
						});
						$('#tbl'+valObj['child_grid']+' tbody tr:eq('+nFirst+') td:eq(1)').trigger('mouseup');
						break;
					case 'TREE':
						switch(valObj['child_col']){
						case 1:
							$('#tbl'+valObj['child_grid']+' tbody tr[data-tt-id='+pkey+']').each(function(){
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
								showNode(valObj['child_grid'],pkey);
							});
							break;
						case 2:
							$('#tbl'+valObj['child_grid']+' tbody tr[data-tt-parent-id='+pkey+']').each(function(){
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
								showNode(valObj['child_grid'],$(this).attr('data-tt-id'));
							});
							break;
						default:
							var ndx=parseInt(valObj['child_col'])-3;
							$('#tbl'+valObj['child_grid']+' tbody tr').each(function(){
								if($(this).find('td:eq('+ndx+')').text()!=pkey) return true;
								if(nFirst==-1) nFirst=$(this).index();
								$(this).show();
								showNode(valObj['child_grid'],$(this).attr('data-tt-id'));
							});
						}
					}
				});
			}
		}
		return false;
	}
	
	// Routine for Editting cell.

	var dtype, digit,pixel, oAttr,pos;
	switch(viewtype){
	case 'TREE':
	case 'HRZN':
		oAttr=$('#tbl'+tblid+' thead tr:eq(0) th:eq('+col+')');
		dtype=oAttr.attr('dtype');
		digit=parseInt(oAttr.attr('digit'));
		pixel=parseInt(oAttr.attr('pixel'));
		pos=col;
		// console.log('LINE ['+getLine(tblid,row)+']');
		break;
	case 'VERT':
		oAttr=$('#tbl'+tblid+' tbody tr:eq('+row+') td:eq(0)');
		dtype=oAttr.attr('dtype');
		digit=parseInt(oAttr.attr('digit'));
		pixel=parseInt(oAttr.attr('pixel'));
		pos=row;
	}
	console.log('tblid ['+tblid+'] col ['+col+'] viewtype ['+viewtype+'] dtype ['+dtype+'] digit ['+digit+'] pixel ['+pixel+'] text ['+oAttr.text()+']');
	
	var oldtext = $(this).text();
	var oldrecord;
	switch(viewtype){
	case 'TREE':
	case 'HRZN':
		oldrecord=getLine(tblid,row);
		break;
	case 'VERT':
		oldrecord=getLine(tblid,col);
	}
	console.log('pos ['+pos+'] row ['+row+'] col ['+col+'] oldtext ['+oldtext+'] HTML ['+oldrecord+'] dtype ['+dtype+']');
	
	var pstr = '';
	var strCtl='';
	var num_input='';
	x=0;

	var me=$(this);
	
	switch(dtype){
	case 'gender':                                                
//	case 'bool':
	case 'align':
	case 'valign':
		if(dtype=='gender'){
			pstr='<select id=seltext name=seltext><option value=M>남</option><option value=F>여</option></select>';
		} else if(dtype=='bool'){
			pstr='<select id=seltext name=seltext><option value=1>1</option><option value=0>0</option></select>';
		} else if(dtype=='align'){
			pstr='<select id=seltext name=seltext><option value="left">left</option><option value="center">center</option><option value="right">right</option></select>';
		} else if(dtype=='valign'){
			pstr='<select id=seltext name=seltext><option value="top">top</option><option value="middle">middle</option><option value="bottom">bottom</option></select>';
		}
		$(this).html(pstr);
		
		setTimeout(function(){$('#seltext').focus()},100); 
		$('#seltext')
		.on('keydown',function(e){
			switch( e.keyCode||e.which ){
			case KEY_ESCAPE:
				me.text(oldtext);
				return false;
			case KEY_TAB: case KEY_ENTER:
				$(this).trigger('blur');
				break;
			}
		})
		.on('blur',function(e){
			try {[].undef ()} catch (e) {
				wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
			}
			fBlur($(this),me,pos,tblid,viewtype,oldtext,dtype,oldrecord);
		});
		break;
	case 'radio':
	case 'select':
		strCtl="seltext";
//		console.log('dtype tblid ['+tblid+'] ndx ['+col+']');
		var pstr='';
		var oArg={};
		// oArg['optype']=dtype;
		// oArg['tblid']=tblid;
		if(viewtype=='VERT'){
			oArg['ndx']=row;
		} else {
			oArg['ndx']=col;
		}
		oArg['viewtype']=viewtype;
		if(dtype=='select'){
			oArg['oldrecord']=oldrecord;
		}
		console.log(oArg);
		// $.post('_radio.php',oArg,function(json){
		// 	console.log(json);
		// 	if(json['result']!='0'){
		// 		alert(json['msg']);
		// 		return false;
		// 	}
			pstr="<select id=seltext name=seltext><option value=''></option>";
			// $.each(json['element'].split('`'),function(ndx,val){
			// console.log('list ['+$('#tbl'+tblid+' tr:eq(0) th:eq('+oArg['ndx']+')').attr('list')+']');
			$.each($('#tbl'+tblid+' tr:eq(0) th:eq('+oArg['ndx']+')').attr('list').split('`'),function(ndx,val){
				pstr+="<option value='"+val+"'";
				if(oldtext==val) pstr+=" selected";
				pstr+=">"+val+"</option>";
			});
			pstr+="</select>";
		// },'json')
		// .always(function(){
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
					$(this).trigger('blur');
					break;
				}
				return false;
			})
			.on('blur',function(e){
				try {[].undef ()} catch (e) {
					wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
				}
				fBlur($(this),me,pos,tblid,viewtype,oldtext,dtype,oldrecord);
				return false;
			});
		// });
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
			fBlur($(this),me,pos,tblid,viewtype,oldtext,dtype,oldrecord);
			return false;
		});
	}
	return false;		// to stop double click to occur twice.
})
/* ***********************************************************************/
.on('click','#mainmenu li',function(){
//	var pageid=$(this).attr('page_id');
	if(this.id=='') {
		alert('No page defined.');
		return false;
	}
	console.log('./crmctl.php?_p='+this.id);
	document.location='./crmctl.php?_p='+this.id;
})
/* ***********************************************************************/
.on('click','thead tr',function(){
	wlog('thead tr');
})
/* ***********************************************************************/
.on('scroll','div[id^=dv]',function(){
//	console.log('scroll');
})
/* ***********************************************************************/
.on('click','label[id^=leveldn]',function(){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

	/*
	 * based on the cascade delete.
	 */
	var tblid=(this.id).substr(7);
	
	var curTR=$('#tbl'+tblid+' tbody tr.selected');
	var curNode={ rowid:curTR.attr('data-tt-id'), par_rowid:curTR.attr('data-tt-parent-id'),seqno:parseInt(curTR.find('td:eq(1)').text())};
	
	var row=curTR.index()+1;
	console.log('row ['+row+'] length ['+$('#tbl'+tblid+' tbody tr').length+']');
	if(row>$('#tbl'+tblid+' tbody tr').length-1) return false;
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
.on('contextmenu','body',function(){
//	alert('custom')
//	$('#custom_menu').menu();
//	return false;
})
;

$('div')
.scroll(function(){
//	console.log('document scroll');
})
;

/* ***********************************************************************/
function fPage(){
	var me=this;
	this.arForeign=[];	// key = parent_grid
	this.arGrid=[];
	this._p=$('#_p').val();
	this.grid_count=$('.tGrid').length;
//	alert('_p ['+$('#_p').val()+']');
	$.post("_foreign.php",{_p:$('#_p').val()},
		function(json){
			console.log(json);
			if(json['result']!="0"){
//				alert(json['msg']);
				return false;
			}
//			$.each(json['foreign'],function(ndx,val){
//				me.arForeign.push(val);
//			});
			if(json['foreign'].length>0) {
				me.arForeign=json['foreign'].slice();
				console.log(me.arForeign);	
			}
		},'json')
		.always(function(){
			var arJS=['addnew','delete','update'];
			$('table[id^=tbl]').each(function(){
				var tblid=(this.id).substr(3);
				
				tGrid=new fGrid(tblid);
				tGrid.drawGrid(false);
				me.arGrid[tblid]=tGrid;
//				$.each(arJS,function(ndx,val){
//					$.getScript('plugin/'+tblid+'_'+val+'.js');
//				});
			}); 
//			console.log(me);
//			$('.treetable').treetable({expandable:true}).treetable('expandAll');
		});
}
/* ***********************************************************************/
fPage.prototype={
	getParent:function(rowid){
		var ce=this;
		var retObj={};
		$.each(ce.arForeign,function(ndx,val){
			if(val['child_grid']==rowid) {
				retObj['parent_grid']=val['parent_grid']; 
				retObj['parent_col']=val['parent_col'];
				retObj['child_col']=val['child_col'];
				return false;
			}
		});
		return retObj;
	},
	getChild:function(rowid){
		var ce=this;
		var retArr=[];
		$.each(ce.arForeign,function(ndx,val){
			if(val['parent_grid']==rowid){
				retArr.push({'child_grid':val['child_grid'],'child_col':val['child_col'],'parent_col':val['parent_col']});
//					return false;
			}
		});
		return retArr;
	}
}
/* ***********************************************************************/
function fGrid(rowid){
	this.rowid=rowid; 
	this.start=0;
}
/* ***********************************************************************/
fGrid.prototype={
	drawGrid:function(bRefresh){
		var me=this;
		var viewtype=$('#tbl'+me.rowid).attr('viewtype');
		$.post('_drawgrid.php',{rowid:me.rowid,start:me.start},function(json){
			console.log(json);
			if(json['result']!='0'){
				alert(json['msg']); return false;
			}
			if(parseInt(json['pagesize'])==0) me.start=-1;
			else me.start+=parseInt(json['pagesize']);
			switch(viewtype){
			case 'HRZN':
				$('#tbl'+me.rowid+' tbody').append(json['html']);
//				console.log(json['html']);
				break;
			case 'TREE':
				if(!bRefresh){
					$.each(json['tree'],function(ndx,val){
//						console.log(val);
	//					if(val['par_rowid']==''){
	//						$('#tbl'+me.rowid).treetable("loadBranch",null,json['line']);				
	//					} else {
	//						var pnode=$('#tbl'+me.rowid).treetable('node',val['par_rowid']);
	//						$('#tbl'+me.rowid).treetable("loadBranch",pnode,json['line']);				
	//					}
						$('#tbl'+me.rowid+' tbody').append(val['line']);
	//					return false;
					});
				} else {
					$('#tbl'+me.rowid).treetable();
					$.each(json['tree'],function(ndx,val){
						console.log(val);
						if(val['par_rowid']==''){
							$('#tbl'+me.rowid).treetable("loadBranch",null,json['line']);				
						} else {
							var pnode=$('#tbl'+me.rowid).treetable('node',val['par_rowid']);
							$('#tbl'+me.rowid).treetable("loadBranch",pnode,json['line']);				
						}
					});
				}
				$('#tbl'+me.rowid).treetable({expandable:true});
				$('#tbl'+me.rowid).treetable('expandAll');
				break;
			case 'VERT':
				console.log(json['vert'])
				var i=0;
				$.each(json['vert'][0],function(ndx,val){
					console.log(val);
					$('#tbl'+me.rowid+' tbody tr:eq('+i+')').append(val);
					i++;
				});
			}
		},'json');
	}
}

/*
 * textstr : the text string in <select> list
 * valstr : the value in <select> list
 * tabstr : the table name where textstr,valstr will be searched
 * selname : the ID/name of <select> tag.
 */
function fillSelect(textstr,valstr,tabstr,selname){
	$.ajax({
		datatype:'xml',
		data:'optype=fillselect&textstr='+textstr+'&valstr='+valstr+'&tabstr='+tabstr,
		beforeSend:function(){
		},
		success:function(xml){
			console.log(xml)
			$('#'+selname).empty().show().append('<option value="-" selected>All</option>');
			$(xml).find('crlf').each(function(){
				var opt='<option value="'+$(this).find(valstr).text()+'">'+$(this).find(textstr).text()+'</option>';
				$('#'+selname).append(opt);
			});
		},
		complete:function(){
			$('#'+selname).show();
		}
	});
}

/* ***********************************************************************/
function validateData(dtype,value){
	try {[].undef ()} catch (e) {
//		wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	}

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

/* ***********************************************************************/
function showNode(tblid,pkey){
	console.log('showNode tblid ['+tblid+'] pkey ['+pkey+']');
	$('#tbl'+tblid+' tbody tr[data-tt-parent-id='+pkey+']').each(function(){
		$(this).show();
		showNode(tblid,$(this).attr('data-tt-id'));
	});
}

/* ***********************************************************************/
function removeNode(tblid,rowid){
	console.log('tblid ['+tblid+'] rowid ['+rowid+']');
	$('#tbl'+tblid+' tbody tr[data-tt-parent-id='+rowid+']').each(function(){
		removeNode(tblid,$(this).attr('data-tt-id'));
	});
	var curTR=$('#tbl'+tblid+' tbody tr[data-tt-id='+rowid+']');
	var arRecord=[];
	arRecord.push(rowid);
	arRecord.push(curTR.attr('data-tt-parent-id'));
	curTR.find('td:gt(0)').each(function(){
		arRecord.push($.trim($(this).text()));
	});
	console.log(arRecord.join('|'));
	$.post('_delete.php',{rowid:tblid,record:arRecord.join('|')},function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		curTR.remove();
	},'json')
	.always(function(){
	});	
}

/* ***********************************************************************/
function fBlur(curCtl,curTD,pos,tblid,viewtype,oldtext,dtype,oldrecord){
	console.log('ctrl type ['+curCtl.prop('type')+']');
	var new_value = null;
	if(curCtl.prop('type')=='checkbox'){
		if(curCtl.is(':checked')) new_value='1';
		else new_value='0';
	} else {
		new_value=curCtl.val();
	}
	if( new_value == null ) new_value='';
	console.log('fBlur pos ['+pos+'] tblid ['+tblid+'] viewtype ['+viewtype+'] oldtext ['+oldtext+'] new_value ['+new_value+'] dtype ['+dtype+'] oldrecord ['+oldrecord+']');
	curTD.text(new_value);
	if( oldtext == new_value) return;
	if(!validateData(dtype,new_value)){
		curCtl.text(oldtext);
		return;
	}
	$.post('_update.php',{viewtype:viewtype,newval:new_value,tblid:tblid,oldrecord:oldrecord,pos:pos},function(json){
		if(json['result']!='0'){
			alert(json['msg']);
			curTD.text(oldtext);
			return false;
		}
		curTD.text(new_value);
	},'json');
//	curTD.text(new_value);	
}

/* ***********************************************************************/
// getNodeText( tableid, row, col )
/* ***********************************************************************/
function getNodeText(tblid,row,ndx){
	
	var grid=$('#tbl'+tblid+' tbody tr:eq('+row+')');
	if(grid.length<1) return '';
	ndx=parseInt(ndx);
	if($('#tbl'+tblid).attr('viewtype')=='TREE'){
//		console.log('getNodeText TREE tblid ['+tblid+'] row ['+row+'] ndx ['+ndx+']');
		switch(ndx){
		case 1:
			return grid.attr('data-tt-id'); break;
		case 2:
			return grid.attr('data-tt-parent-id'); break;
		default:
			return grid.find('td:eq('+(ndx-TREE_BASE)+')').text();
		}
	} else {
//		console.log('getNodeText HRZN tblid ['+tblid+'] row ['+row+'] ndx ['+ndx+']');
		return grid.find('td:eq('+ndx+')').text();
	}
	return '';
}

/* ***********************************************************************/
function getLine(tblid,pos){
	var pstr='';
	switch($('#tbl'+tblid).attr('viewtype')){
	case 'TREE':
		var grid=$('#tbl'+tblid+' tbody tr:eq('+pos+')');
		if(grid.length<1) return '';

		pstr=grid.attr('data-tt-id')+'|'+grid.attr('data-tt-parent-id');
		grid.find('td').each(function(){
			pstr+='|'+$.trim($(this).text());
		});
		break;
	case 'HRZN':
		var grid=$('#tbl'+tblid+' tbody tr:eq('+pos+')');
		if(grid.length<1) return '';

		grid.find('td:gt(0)').each(function(){
			if(pstr!='') pstr+='|';
			var chk=$(this).find('input[type=checkbox]').length;
			if(chk>0){
				if($(this).find('input[type=checkbox]').prop('checked')) {
					pstr+='1';
				} else {
					pstr+='0';
				}
			} else {
				console.log('['+$.trim($(this).text())+']');
				pstr+=$.trim($(this).text());				
			}
		});
		break;
	case 'VERT':
		var grid=$('#tbl'+tblid+' tbody tr');
		console.log('grid ['+grid.length+']');
		if(grid.length<1)	return '';
		$('#tbl'+tblid+' tbody tr:gt(0)').each(function(){
			if(pstr!='') pstr+='|';
			pstr+=$(this).find('td:eq('+pos+')').text();
		});
		console.log('getLine ('+$('#tbl'+tblid).attr('viewtype')+') ['+pstr+']');
	}
	return pstr;
}

/* ***********************************************************************/
function showAllNodes(rowid){
	console.log('showAllNodes ['+rowid+']')
	var viewtype=$('#tbl'+rowid).attr('viewtype');
	var tbl=$('#tbl'+rowid);
	var arChild=curPage.getChild(rowid);
	console.log(arChild);
	if(arChild.length<1) return ;
	console.log('selected length ['+$('#tbl'+rowid+' tbody tr.selected').length+']');
	
	$.each(arChild,function(nChild,retObj){
		console.log(retObj);
		$('#tbl'+retObj['child_grid']+' tbody tr').show();

		var buttons=$('#delete'+rowid+',#levellt'+rowid+',#levelrt'+rowid+',#levelup'+rowid+',#leveldn'+rowid);
		if(viewtype=='TREE') {
			buttons.hide();
		}
		showAllNodes(retObj['child_grid']);
	});
}

/* ***********************************************************************/
function getRoot(tblid,rowid){
	var par_rowid=$('#tbl'+tblid+' tbody tr[data-tt-id='+rowid+']').attr('data-tt-parent-id');
	while(par_rowid!=''){
		rowid=par_rowid;
		par_rowid=$('#tbl'+tblid+' tbody tr[data-tt-id='+rowid+']').attr('data-tt-parent-id');
	}
	return rowid;
}

function alarms(){
	alert('alarms');
}