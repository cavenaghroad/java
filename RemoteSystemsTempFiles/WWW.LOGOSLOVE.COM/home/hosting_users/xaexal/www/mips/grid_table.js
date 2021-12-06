$(document)
.ready(function(){
	setlog(-1); 
	win_resize();
	init_screen();

	//  in case of the web browser does not support debug window such as IE9.
	if (typeof console === 'undefined' || typeof console.log === 'undefined') {
		console = {};
		console.log = function(msg) {/*alert(msg)*/;};
		console.clear = function(){}
	}
	garPage.push(new fPage($('#_e').val(),$('#_p').val(),null));
	$('button').css('height','20px');
})
.on('click','#btnPersonal', function() {
	document.location = 'crm.php?_p=124EF3FA72DD&_e='+$('#_e').val();
})
.tooltip()
;

function fPage() {
	var myPage=this;
	myPage._e = arguments[0];
	myPage._p = arguments[1];
	if(arguments.length>2)	myPage.parent=arguments[2];
	else myPage.parent=null;
	
	myPage.arGrid=[];
	myPage.linkinfo=[];
	
	if(myPage.parent==null){
		menuinit(myPage._e,myPage._p);
	}
	wlog('#r','fPage ['+myPage._e+'] ['+myPage._p+']');
	
	$.post('disu.php',{
		optype:'pageinfo',_p:myPage._p,_e:myPage._e
	},function(json){
//		console.log(json);
		if(json['result']!="0"){
			alert(json['msg']); return false;
		}
		var _html='';
		$.each(json['pageinfo'],function(ndx,val){
			myPage[ndx]=val;
		});
//		console.log(myPage);
//		return false;
		$.each(json['gridinfo'],function(ndx,val){
//			wlog('----- ['+ndx+']');
			var oGrid = new fGrid();
			oGrid._e = myPage._e; oGrid.padre={};
			switch(ndx){
			case 'attrinfo':
				$.each(val,function(idx,value){
					oGrid[idx]=value;
				});
				oGrid['seqno']=seqno++;
				oGrid['gridname']='#tbl'+oGrid.rowid;
				break;
			case 'colinfo':
				if( typeof oGrid.colinfo === 'undefined' ) oGrid.colinfo=[];
				var oColinfo = {};
				$.each(val,function(idx,value){
					oColinfo[idx]=value;
				});
				oGrid.colinfo.push(oColinfo);
				console.log(oColinfo);
				break;
			case 'linkinfo':
				if( typeof myPage.linkinfo === 'undefined') myPage.linkinfo=[];
				var oLinkinfo={};
				$.each(val,function(idx,value){
					oLinkinfo[ndx]=value;
				});
				myPage.linkinfo.push(oLinkinfo);
//				console.log(oLinkinfo);
				break;
			default:
				oGrid[ndx]=val;
				wlog('default '+ndx+' ['+val+']');
			}
//			console.log('oGrid');
//			console.log(oGrid);
			/* 
			 * Two kinds of TABS. Vertial Tab / Horizontal Tab.
			 */
			if( typeof myPage.arGrid === 'undefined' ) wlog('arGrid is not defined.');
			else {
				myPage.arGrid.push(oGrid);
				garGrid.push(oGrid);
			}
		});
	},'json')
	.always(function(){
		console.log(myPage);
		if(myPage.parent==null){
			$('#'+myPage._p).html(myPage.layout);
			for(var i=0; i<myPage.arGrid.length; i++ ){
				oGrid=myPage.arGrid[i];
				$('#'+myPage._p).find('.tGrid').eq(i).html(oGrid.layout);
				$('#'+myPage._p).find('.tGrid').eq(i).css('vertical-align','top');
				oGrid.showTitle();
				oGrid.loadTable();
				if(oGrid.show_toolbar=='1') oGrid.buildToolbar();
			}
		} else {
			if($('#'+myPage._p).length==0){
				$('body').append('<div id='+myPage._p+' name='+myPage._p+'><table>'+myPage.layout+'</table></div>');
				for(var i=0,oGrid=null; i<myPage.arGrid.length; i++){
					// the data in garGrid and arGrid are different. they should be same.
					$('#'+myPage._p).find('.tGrid').eq(i).html(myPage.arGrid[i].layout);
				}
			}

			$('#'+myPage._p).dialog({
				autoOpen:false, closeOnEscape:true,modal:true,resizable:true,
				width:800,height:500,
				open:function(e,ui){
					$.each(myPage.arGrid,function(ndx,oGrid){
						oGrid.showTitle();
						oGrid.loadTable();
						wlog('show_toolbar ['+oGrid.show_toolbar+']');
						if(oGrid.show_toolbar=='1') oGrid.buildToolbar();
					});
				},
				buttons:[
				{
					text:'Close',
					click:function(){
						$(this).dialog('close');
					}
				},
				{
					text:'Add',
					click:function(){
						setlog(-1);
						wlog('>>>>>>>>>> Dialog Started by pressing Add button <<<<<<<<<<<<');
						var myDlg=$(this);
						// the selected record on the last Grid in dialog window is unconditionally pushed.
//							var myGrid=myPage.arGrid[myPage.arGrid.length-1];
						var thisId=this.id, myGrid=findGrid('rowid',thisId),arLink=[];
						
						wlog('this.id',this.id);
						// Collecting the parent columns and its values.
						// find the ID of originating grid.
						var oLinkinfo={};
						var oOrigin=findGrid('dialog',thisId);
						
						if($.isEmptyObject(oOrigin)) {
							wlog("Can't find origin Grid");
							return false;
						}

						wlog('#c','oOrigin')
						console.log(oOrigin)
						// find the ID of the parent Grid of originating grid.
						// get the link info of parent grid of this originating grid.
						oLinkinfo=oOrigin.findLinkinfoAs('child');
						$.each(oLinkinfo.arLinkinfo,function(ndx,oLink){
							var oParent=findGrid('rowid',oLink.parent_grid);
							if($.isEmptyObject(oParent)) {
								wlog('Parent is empty');
								return true;
							}
							var nSelected=$(oParent.gridname+' tbody tr.selected').index();
							if(nSelected<0){
								alert('At lest one record on the parent grid should be selected first.');
								return false;
							}
							wlog('child_col',oLink.child_col);
							var oRow=oParent.getRowText(nSelected);
							console.log(oRow.aText);
							arLink.push({
								nChild:parseInt(oLink.child_col),
								par_rowid:oRow.aText[oLink.parent_col]
							});
							wlog('#c','arLink');
							console.log(arLink[arLink.length-1]);
						});
							// Edit to collect the parent columns and its values.

							// process to the multiple selected dialog rows.
						$(myGrid.gridname+' tbody tr.selected').each(function(){
							wlog('row index',$(this).index());
							var aText=[];
							var oRow=myGrid.getRowText($(this).index());
							for(var i=0;i<oOrigin.colinfo.length;i++){
								var assign_value='';
								for(var ndx=0;ndx<oRow.aText.length-1;ndx++){
									if(myGrid.colinfo[ndx].nick==oOrigin.colinfo[i].nick){
										assign_value=oRow.aText[ndx];
										break;
									}
								}
								wlog('Assign_value ('+myGrid.colinfo[i].a4update+'.'+myGrid.colinfo[i].nick+')',assign_value);
								aText.push(assign_value);
							}
							$.each(arLink,function(n,oLink){
								wlog('before aText['+oLink.nChild+']',aText[oLink.nChild]);
								aText[oLink.nChild]=oLink.par_rowid;
								wlog('after  aText['+oLink.nChild+']',aText[oLink.nChild]);
							});
							$.post('disu.php',{
								optype:'new_var',rowid:myGrid.rowid,_e:myGrid._e,_column:aText.join('^')
							},function(json){
								console.log(json);
//								var _return=$(xml).find('errno').text();
								var _return=json['errno'];
								if(_return!=''){
									alert(_return);
									return false;
								}
								var trtext='<tr><td aling=center>-</td>';
								if(myGrid.viewtype!='VERT'){
									var i=0;
									wlog('replyText >>>>>>>>>>>>>['+json['replyText'].join()+']');
//									$.each((json['replyText'].split('^'),function(ndx,val){
//										var colnow=myGrid.colinfo[i++];
//										wlog('i after split ('+colnow.a4update+'.'+colnow.nick+')',val);
//										trtext+='<td'+(colnow.pixel=='0'?' style="display:none;"':(colnow.align==''?' width='+colnow.pixel+
//												'px':' align='+colnow.align+' width='+colnow.pixel+'px'))+'>'+val+'</td>';
//									});
									trtext+='</tr>';
									wlog(trtext);
									$(myGrid.gridname+' tbody').append(trtext);
								}
							},'json')
							.always(function(){
								if($.isFunction(myGrid.aft_new)) if(!myGrid.aft_new()) return false;
							});
						});
						$(this).dialog('close');							
					}
				}]
			});
			if(myPage.child!=''){
				garPage.push(new fPage(myPage._e,myPage.child,myPage.rowid));
			}
		}
	});
}
 
function fGrid(rowid,ndx,_e,_p) {
	this.row = -1; this.col = -1; 
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
			wlog('#b',this.data);
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

fGrid.prototype={
	showTitle:function(){
		var my=this;
		switch(my.viewtype){
		case 'TREE':
//			var pstr='<th>&nbsp;</th>';	// for numbering
			var pstr='';
			var nWidth=0;
			for(var i=0, thisCol='';i<my.colinfo.length;i++){
				thisCol=my.colinfo[i];
				pstr+='<th align=center'+(thisCol.pixel=='0'?' style="display:none;"':' width='+thisCol.pixel+'px')+'>'+thisCol.title+'</th>';
				nWidth+=parseInt(thisCol.pixel);
			}
			wlog('#c','pstr',pstr);
			$(my.gridname).html('<thead>'+pstr+'<thead><tbody></tbody').width(nWidth);
			break;
		case 'VERT':
			var pstr='<thead><tr><th>&nbsp;</th></tr></thead><tbody>';
			var nWidth=0;
			for(var i=0,thisCol='';i<my.colinfo.length;i++){
				thisCol=my.colinfo[i];
				pstr+='<tr'+(thisCol.pixel=='0'?' style="display:none;"':' height=24px')+'><td align=right>'+thisCol.title+'</td></tr>';
			}
			pstr+='</tbody>';
			$(my.gridname).html(pstr);
			break;
		default:	// HRZN, TREE
			var pstr='<th'+(my.numbering!='1'?' style="display:none"':'')+'>&nbsp;</th>';	// for numbering
			var nWidth=0;
			console.log(my);
			for(var i=0, thisCol='';i<my.colinfo.length;i++){
				thisCol=my.colinfo[i];
				pstr+='<th align=center'+(thisCol.pixel=='0'?' style="display:none;"':' width='+thisCol.pixel+'px')+'>'+thisCol.title+'</th>';
				nWidth+=parseInt(thisCol.pixel);
			}
			$(my.gridname).html('<thead>'+pstr+'<thead><tbody></tbody').width(nWidth);
			break;
		}
		my.loadLOV();
	},
	loadTable:function(){
		var my=this;
		
		switch(my.viewtype){
		case 'TREE':
			pstr = 'optype=select_var&rowid='+my.rowid+'&_e='+my._e;
			break;
		default:
			var total=0;
			if($.type(my._y)==='undefined')	my._y=1;
			pstr = 'optype=select_var&rowid='+my.rowid+'&_e='+my._e+'&_y='+my._y;
		}
		$.ajax({
			data:pstr,url:'disu.php',
			beforeSend: function(){
				wlog('#b',this.data);
				showSpinner();
				switch(my.viewtype){
				case 'TREE':
					$(my.gridname).treetable({ expandable: true});
					$(my.gridname).css('font-size','12px');
					break;
				default:
					$('#toolbar'+my.rowid).find('.modify').hide();
				}
			},
			success:function(xml){
				switch(my.viewtype){
				case 'TREE':
//					console.dirxml(xml);
					var pstr,node,trtext;
					var _e='',strParty='';
					var thisGrid=$(my.gridname);
					$(xml).find('crlf').each(function(){
						/*
						 * <tr data-tt-id=xxxx data-tt-parent-id=zzzz><td><span class=file|folder>DESCRIPTION</span></td><td>...</td></tr>
						 * party,party_name,rowid,par_rowid,description,seqno : default columns for tree table. only seqno is invisible.
						 */
						var oNode={};
						arCol=[];
						$(this).children().each(function(){
							oNode[this.nodeName]=$(this).text();
						});
						if(_e!=oNode.party){
							_e=oNode.party;
							strParty=' party='+oNode.eneterprise+' party_name="'+oNode.party_name+'"';
							trtext = '<tr data-tt-id='+_e+strParty+'><td><span class=folder><font color=white style="background-color:red">'+oNode.party_name+'</font></span></td>';
							for(var i=TREE_INDEX_START+1;i<my.colinfo.length;++i){
								trtext+='<td'+(my.colinfo[i].pixel=='0'?' style="display:none;" ':'')+'>&nbsp;</td>';
							}
							trtext+='</tr>';
							thisGrid.treetable('loadBranch',thisGrid.treetable('node',null),trtext);
						}
//						wlog('par_rowid',oNode.par_rowid);
						trtext = '<tr data-tt-id='+oNode.rowid+strParty;
						if(oNode.par_rowid==''){
							oNode.par_rowid=_e;
							trtext+='data-tt-parent-id='+oNode.par_rowid+'><td><span class=file>'+oNode.description+'</span></td>';
						} else {
							trtext+='data-tt-parent-id='+oNode.par_rowid+'><td><span class=file>'+oNode.description+'</span></td>';
						}
						for(var i=TREE_INDEX_START+1;i<my.colinfo.length;++i){
							trtext+='<td'+(my.colinfo[i].pixel=='0'?' style="display:none;" ':'')+'>'+oNode[my.colinfo[i].nick]+'</td>';
						}
						trtext+='</tr>';
						thisGrid.treetable('loadBranch',thisGrid.treetable('node',oNode.par_rowid),trtext);
//						thisGrid.find('tr[rowid='+oNode.par_rowid+'] > td > span').removeClass('file').addClass('folder');
					});
					break;
				case 'VERT':
					console.log(xml);
					total=parseInt($(xml).find('tcount').text());
					var nFetched=parseInt($(xml).find('rcount').text());
					var datatext='';
					var nRow=my._y;
					$(xml).find('crlf').each(function(){
						var n=0;
						$(my.gridname+' thead tr').append('<th align=center>'+(my.numbering=='1'?nRow:'&nbsp;')+'</th>');
						$(this).children().each(function(){
							var thisCol=my.colinfo[n];
							var colval=$(this).text()
							if(thisCol.dtype=='html'){
								colval=colval.escapeHTML();
							}
							var pstr='<td align=center>'+colval+'&nbsp;</td>';
							wlog(pstr);
							$(my.gridname+' tbody tr:eq('+n+')').append(pstr);
							n++;
						});
						nRow++;
					});
					break;
				default :	// HRZN
					console.log(xml);
					total=parseInt($(xml).find('tcount').text());
					var nFetched=parseInt($(xml).find('rcount').text());
					var datatext='';
					var nRow=my._y;
					$(my.gridname+' tbody').empty();
					$(xml).find('crlf').each(function(){
						datatext+='<tr><td align=center'+(my.numbering=='1'?'':' style="display:none;"')+'>'+
							(my.numbering=='1'?nRow:'&nbsp;')+'</td>';
						var nCol=0;
						$(this).children().each(function(){
							var thisCol=my.colinfo[nCol];
							var colval=$(this).text();
							if(thisCol.dtype=='html'){
								colval=colval.escapeHTML();
							}
							datatext+='<td'+(thisCol.align==''?'':' align='+thisCol.align)+
								(thisCol.pixel=='0'?' style="display:none;"':' width='+thisCol.pixel+'px')+'>'+colval+'</td>';
							nCol++;
						});
						datatext+='</tr>';
						nRow++;
						
					});
					$(my.gridname+' tbody').html(datatext);
				}
			},
			complete:function(){
				switch(my.viewtype){
				case 'TREE':
					
//					wlog('with_jsfile ['+my.with_jsfile+'] ['+my.rowid+'')
					if( my.with_jsfile !='' ){
						$.getScript('plugin/'+my.with_jsfile+'.js')
						.done(function(){
							loadJS();
						})
						.fail(function(){alert('failed to load '+my.with_jsfile+'.js.');});
					}
					/*if(my.collapse=='1')*/	$(my.gridname).treetable('collapseAll');
					if( $.isFunction(my.afterLoad) )	my.afterLoad();
					$('.doubleTap').resizable({
						minHeight:24,maxHeight:24,handles:'w,e'
					});
					$('#toolbar'+my.seqno).append('&nbsp;<input type=button name=btnRemove id=btnRemove value=Delete>'+
							'&nbsp;<input type=button name=btnUp id=btnUp value="Up" style="display:none" />'+
							'&nbsp;<input type=button name=btnDn id=btnDn value="Down" style="Display:none" />'+
							'&nbsp;<input type=button name=btnHigher id=btnHigher value="Make Higher" style="display:none;" />');
					$('input[type=button]').button().css('height','20px');
					$(my.gridname+' .file,'+my.gridname+' .folder').draggable({
						helper: 'clone',
						opacity: .75,
						refreshPositions: true,
						revert: 'invalid',
						revertDuration: 300,
						scroll: true
					});
					$(my.gridname+' .folder,'+my.gridname+' .file').each(function() {
						$(this).parents(my.gridname+' tr').droppable({
							accept: '.file, .folder',
							drop: function(e, ui) {
								var parent=$(this);
								var _ret=false;
								var droppedEl = ui.draggable.parents('tr');
								wlog('tr html',droppedEl.html());
								wlog('this html',$(this).html());
								var curid=droppedEl.data('ttId'), 
									par_rowid=$(this).data('ttId');
								wlog('curid',curid,'par_rowid',par_rowid,'_e',par_rowid,'party',parent.attr('party'),'party_name',parent.attr('party_name'));
								var urltext='optype=upd_tree&rowid='+my.rowid+'&curid='+curid+'&par_rowid='+par_rowid;
								$.ajax({
									dataType:'text',url:'disu.php',
									data:urltext,
									beforeSend:function(){wlog('#b',this.data);},
									success:function(_return){
										switch(_return){
										case '0':
											$(my.gridname)
											.treetable('move', curid, par_rowid)
											.treetable('reveal',par_rowid);
											droppedEl.attr('party',parent.attr('party'));
											droppedEl.attr('party_name',parent.attr('party_name'));
											_ret=true;
											break;
										case '-1':
											break;
										default:
											alert(_return);
										}
									},
									error:function(e){},//
									complete:function(){}
								});
								wlog('droppedEl ',droppedEl.attr('party'),droppedEl.attr('party_name'));
								return _ret;
							},
							hoverClass: 'accept'
							,over: function(e, ui) {
								var droppedEl = ui.draggable.parents('tr');
								if(this != droppedEl[0] && !$(this).is('.expanded')) {
									$(my.gridname).treetable('expandNode', $(this).data('ttId'));
								}
							}
						});
					});
					$(my.gridname).treetable({expandable:true});
					$.getScript('grid_tree.js');

					$(my.gridname).show();
					break;
				
				default:	// HRZN, VERT.
					$(my.gridname).addClass('datatable');
					if(my.pagesize!='0'){
						var pstr='', i=1;
						do {
							pstr+='<option';
							if(i==my._y) pstr+=' selected';
							pstr+=' value='+i+'>&nbsp;'+i+'~ </option>';
							i+=parseInt(my.pagesize);
						} while(i<total);
						setTimeout(function(){$('.selpage').eq(parseInt(my.seqno)).html(pstr);},100);
					}
					if(my.viewtype=='HRZN'){
						$(my.gridname+' tbody tr:eq(0) td:gt(0)').each(function(){
							var n=$(this).index();
							
						});
					} else {
						
					}
				}
				hideSpinner();
//				wlog('hideSpinner');
				switch(my.viewtype){
				case 'VERT':
					$(my.gridname+' tbody td').mouseenter(function(){
						$(this).css('background-color','yellow');
					}).mouseleave(function(){
						$(this).css('background-color','');
					});
					break;
				default:
					$(my.gridname+' tbody tr').mouseenter(function(){
						$(this).css('background-color','yellow');
					}).mouseleave(function(){
						$(this).css('background-color','');
					});
				}
				if($(my.gridname).closest('td.tGrid').index()==0){
					$(my.gridname).closest('td.tGrid').prop('width',$(my.gridname).width());
				}
				$(my.gridname+' tbody tr').draggable({
					helper:'clone',
					opacity:.75,
					refreshPositions: true,
					revert: 'invalid',
					revertDuration: 300,
					scroll: true,
					'start':function(){
						wlog('drag started.');
					}
				});
			}
		});
	},
	buildToolbar:function(){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}
		
		var my=this;
		var pstr='';
		$.ajax({
			data:'optype=build_toolbar&rowid='+my.rowid, url:'disu.php',
			beforeSend:function(){
				wlog('#b',this.data);
			},
			success:function(xml){
//				console.log(xml);
				pstr=$(xml).find('toolbar').text();
			},
			complete:function(){
				var myToolbar=$('#toolbar'+my.rowid);
				myToolbar.html(pstr);
				myToolbar.css({'border':'1px solid cyan','background-color':'#CCFFCC'});
				$(':input[type=button]').button();
				myToolbar.find('.modify').hide();
				if(my.pagesize=='0') myToolbar.find('.prev,.next,.selpage').hide();
				if(my.searchable!='1') myToolbar.find('.search,.find').hide();
				myToolbar.attr('title',my.rowid);
			}
		});
	},
	loadLOV:function(){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var my=this;
		var pstr;
		for(var n=0;n<my.colinfo.length;n++){
			switch(my.colinfo[n].dtype){
			case 'radio':	// title0:value0,title1:value1,...,titleN:valueN
			case 'select':	// tablename;columname;conditionColumn;conditionValue
				if($.isArray(my.colinfo[n].element)) return;
				if(my.colinfo[n].dtype=='radio'){
					pstr=my.colinfo[n].element;
					wlog('pstr radio ['+pstr+']');
				} else {
					pstr=my.getImported(n);
					wlog('pstr getImported ['+pstr+']');
				}
				my.colinfo[n].element=[];
				if(my.colinfo[n].nullable=='1')	my.colinfo[n].element.push('');
				wlog('pstr.split ['+pstr+']');
				$.each(pstr.split('`'),function(ndx,val){
					my.colinfo[n].element.push(val);
				});
				break;
			default:
			}
		}
	},
	getImported: function(n){
		var _pstr = '', my=this;
		var col = n;
		$.ajax({
			dataType:'xml',url:'disu.php',async:false,
			data:'optype=imported&rowid='+my.rowid+'&_e='+my._e+'&_ndx='+col,
			beforeSend:function(){
//				wlog('#b',this.data);
			},
			success:function(xml){
//				console.log(xml);
				var arImport=[];
				$(xml).find('crlf').each( function(){
					arImport.push($(this).text());
				});
				_pstr = arImport.join('`');
			},
			error:function(e){},
			complete:function(){}
		});
		return _pstr;
	},
	inline_update:function(row,col){
		var my=this;
		
//		wlog('inline_update',row+'/'+col,'viewtype',my.viewtype);
		switch(my.viewtype){
		case 'TREE':
			var rowtext='';
			var mytr=$(my.gridname+' tbody tr:eq('+row+')');
			wlog('row ['+row+']');
			rowtext=mytr.attr('party')+'^'+mytr.attr('party_name')+'^'+mytr.attr('data-tt-id')+'^'+mytr.attr('data-tt-parent-id');
			mytr.find('td').each(function(){
				rowtext+='^'+$.trim($(this).text());
			});
			wlog('rowtext ['+rowtext+']');
			rowtext='&_column='+rowtext+'&_ndx='+(col+TREE_INDEX_START);
			break;
		case 'HRZN':
			var rowtext=[];
			$(my.gridname+' tbody tr:eq('+row+')').find('td:gt(0)').each(function(){
				rowtext.push($.trim($(this).text()));
			});
			rowtext='&_column='+rowtext.join('^')+'&_ndx='+(col-1);
			break;
		case 'VERT':
			var rowtext=[];
			$(my.gridname+' tbody tr').each(function(){
				rowtext.push($.trim($(this).find('td:eq('+col+')').text()));
			});
			rowtext='&_column='+rowtext.join('^')+'&_ndx='+row;
			break;
		default:
			return false;
		}
		wlog(rowtext);
//		return false;
		$.ajax({
			data:'optype=upd_var&rowid='+my.rowid+'&_e='+my._e+rowtext,
			url:'disu.php',datatype:'text',
			beforeSend:function(){wlog('#b',this.data);},
			success:function(_return){
				if(_return!=''){
					alert(_return);
				}
			},
			complete:function(){
			}
		});
	},
	getParents:function(){
		var my=this;
		var oPRowid={};
		var nChild=-1;
		oPRowid.arLink=[];
		/*
		 * Find the par_rowid from parent grid to add new child record.
		 */
		var oParent=my.findLinkinfoAs('child');	// Find the parent grid rowid.
		$.each(oParent.arLinkinfo,function(ndx,oLink){
			var oGrid=findGrid('rowid',oLink.parent_grid);
			wlog('parent_grid',oLink.parent_grid);
			if(!$.isEmptyObject(oGrid)){
				var nSelected=$(oGrid.gridname+' tbody tr.selected').index();
				if(nSelected<0){
					alert('At lest one record on the parent grid should be selected first.');
					return false;
				}
				// Save the link info for each child column.
				// Each column of child can have their own parents, so array is mandatory.
				// Parent grid = [child column number:parent rowid value]
				oPRowid.arLink.push({
					nChild:parseInt(oLink.child_col),
					par_rowid:$(oGrid.gridname+' tbody tr.selected td:eq('+(oLink.parent_col+1)+')').text()
				});
				wlog('arLink nChild',oPRowid.arLink[oPRowid.arLink.length-1].nChild,'par_rowid',oPRowid.arLink[oPRowid.arLink.length-1].par_rowid);
			}			
		});
		return oPRowid;
	},
	input4new:function(){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var my=this;
		var pstr='';
		if($('#input4new').length<1){
			$('body').append('<div id=input4new name=input4new><form id=frmNew name=frmNew><table id=dlgnew name=dlgnew></table></form></div>');
		} else{
			$('#dlgnew').empty();
		}
		var oParent=my.getParents();
		
		var nLine=0;
		$.each(my.colinfo,function(ndx,val){
			var par_rowid='';
			$.each(oParent.arLink,function(ndx1,val1){
				if(val1.nChild==ndx){	// Check if each column is mapped to the Link info column.
					par_rowid=val1.par_rowid;
					return false;
				}
			});
			var def_val='', num_input='';
			if(par_rowid!='') def_val=par_rowid;
			
			pstr+='<tr';
			if(val.pixel=='0' || val.pixel=='') pstr+= ' style="display:none;"';
			else nLine++;
			pstr+='><td align=right>'+val.title+'&nbsp;</td><td>&nbsp;';
			switch(val.dtype){
			case 'radio':
			case 'select':
				pstr+='<select id='+val.nick+' name='+val.nick+' title="'+val.tooltip_msg+'">';
				$.each(val.element,function(col,elval){
					pstr+='<option ';
					if(val.defval==elval || def_val==elval) pstr+='selected ';
					pstr+='value="'+elval+'">'+elval+'</option>';
				});
				pstr+='</select>';
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
				if(parseInt(val.digit)>32) {
					nSize=32;
					var nRow = parseInt(val.digit)/nSize;
					if(parseInt(val.digit) % nSize>0) nRow++;
					pstr+='<textarea rows='+nRow+' cols='+nSize+' id='+val.nick+' name='+val.nick+'>'+def_val+'</textarea>';
					nLine+=nRow-1;
				} else {
					nSize=parseInt(val.digit);
					pstr+='<input type='+val.dtype+' id='+val.nick+' name='+val.nick+' size='+nSize+' maxlength='+val.digit+
						num_input+' value="'+def_val+'" title="'+val.tooltip_msg+'"></td></tr>';
				}
				break;
			}
		});
		$('#dlgnew').html(pstr);
//		wlog('#r','dlgnew',$('#dlgnew').html());
		$('#input4new').dialog({
			autoOpen:true, closeOnEscape:true,modal:false,resizable:true,
			width:400,height:nLine*30+100,
			open:function(e,ui){
				$('input[name$=_date]').datepicker();
				
			},
			buttons:[
			{
				text:'Add',
				click:function(){
					console.log($('#frmNew').serializeArray());
					var pstr=[];
					$.each($('#frmNew').serializeArray(),function(ndx,val){
						pstr.push(val.value);
					});
					$.each(arLink,function(ndx,oLink){
						pstr[oLink.child_col]=oLink.par_rowid;
					});
					pstr='optype=new_var&rowid='+my.rowid+'&_e='+my._e+'&_column='+pstr.join('^');
					$.ajax({
						data:pstr,url:'disu.php',async:true,datatype:'xml',
						beforeSend:function(){wlog('#b',this.data);},
						success:function(xml){
							console.log(xml);
							if($(xml).find('errno').text()!=''){
								alert($(xml).find('replyText').text());
								return false;
							}
							var rowText=$(xml).find('replyText').text(),trtext='';
							$.each(rowText.split('^'),function(nCol,colval){
								if(my.colinfo[nCol].dtype=='html'){
									colval=colval.escapeHTML();
								}
								var thisCol=my.colinfo[nCol];
								trtext+='<td'+(thisCol.align==''?'':' align='+thisCol.align)+
									(thisCol.pixel=='0'?' style="display:none;"':' width='+thisCol.pixel+'px')+'>'+colval+'</td>';
							});
							trtext='<tr><td align=center>new</td>'+trtext+'</tr>';
							wlog('new_var ['+trtext+']');
							$(my.gridname+' tbody').prepend(trtext);
						},
						complete:function(){
						}
					});
					$(this).dialog('close');
				}
			},
			{
				text:'Close',
				click:function(){
					$(this).dialog('close');
				}
			}
			]
		});
	},
	list4new:function(){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var my=this;
//		wlog('my.dialog ['+my.dialog+']');
		$.each(garPage,function(n,oPage){
			$.each(oPage.arGrid,function(m,oGrid){
				if(oGrid.rowid==my.dialog) {
					$('#'+oPage.rowid).dialog('open');
					return false;
				}
			});
		});
	},
	deleteRow:function(){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var my=this;
		var aText=[],row=-1,mytr;
//		if(!confirm($(my.gridname+' tbody tr.selected').index(),'row')) return false;
		var oRow=my.getRowText($(my.gridname+' tbody tr.selected').index());
		console.log(oRow.aText);

		// To process the delete once and the cascading delete.
		var pstr='optype=del_var&rowid='+my.rowid+'&_e='+my._e+'&p='+my._p+'&_column='+oRow.aText.join('^');

		$.ajax({
			data:pstr,url:'disu.php',datatype:'text',
			beforeSend:function(){wlog('#b',this.data);		return false;},
			success:function(_return){
//				alert('['+_return+']');
				if(_return!='') {
					alert(_return);
					return false;
				} else if( my.viewtype!='VERT') $(my.gridname+' tbody tr.selected').remove();
				else {}
			},
			complete:function(){
//				alert('complete started');
				// for Cascade delete
				var oChild=my.findLinkinfoAs('parent');
				if($.isEmptyObject(oChild)) return false;
				
				$.each(oChild.arLinkinfo,function(ndx,oLink){
					var oGrid=findGrid('rowid',oLink.child_grid);
					if($.isEmptyObject(oGrid)) return false;
						
					$(oGrid.gridname+' tbody tr.selected').removeClass('selected');
					$(oGrid.gridname+' tbody tr').each(function(){
						if($(this).find('td:eq('+parseInt(oLink.child_col)+')').text()!=oText[parseInt(oLink.parent_col)]) {
							return true;
						}
						$(this).addClass('selected','selected');
						oGrid.deleteRow();
					});					
				});
			}
		});
		return false;
	},
	duplicateRow:function(){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var my=this;
		var nSelected=$(my.gridname+' tbody tr.selected').index();
		if(!confirm(nSelected)) return false;
//		var aRowText=[];
		var objRow=my.getRowText(nSelected);
		console.log(objRow.aRowText);

		var pstr='optype=new_var&rowid='+my.rowid+'&_e='+my._e+'&_column='+objRow.aText.join('^');
		$.ajax({
			data:pstr,url:'disu.php',datatype:'xml',
			beforeSend:function(){wlog('#b',this.data);},
			success:function(xml){
				console.log(xml);
				if($(xml).find('errno').text()!=''){
					alert('['+$(xml).find('errno').text()+']');
					return false;
				}

				var arText=[];
				if( my.viewtype!='VERT') {
					aText=($(xml).find('replyText').text()).split('^');
//					$(this).find('column').each(function(){
//						arText.push($(this).text());
//					});
					var trtext='<tr><td align=center>-'+aText.join('</td><td>')+'</td></tr>';
					wlog('new_var ['+trtext+']');
//					$(my.gridname+' tbody tr.selected').append(trtext);
					(trtext).insertAfter($(my.gridname+' tbody tr.selected'));
				}
				else {}
			},
			complete:function(){
				var oChild=my.findLinkinfoAs('parent');
				if($.isEmptyObject(oChild)) return false;
				
				$.each(oChild.arLinkinfo,function(ndx,oLink){
					var parent_value=objRow.aRowText[parseInt(oLink.parent_col)];
					var child_grid=oLink.child_grid;
					var child_col=parseInt(oLink.child_col);
					wlog('parent_value',parent_value,'child_grid',child_grid,'child_col',child_col);
					
					var oGrid=findGrid('rowid',child_grid);
					if($.isEmptyObject(oGrid)) return true;
						
					$(oGrid.gridname+' tbody tr.selected').removeClass('selected');
					$(oGrid.gridname+' tbody tr').each(function(){
						if($(this).find('td:eq('+child_col+')').text()!=parent_value) return true;
						wlog('row index',$(this).index());
						
						$(this).addClass('selected','selected');
						oGird.duplicateRow();
					});					
				});
			}
		});
		return false;		
	},
	getRowText:function(row){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var my=this;
		var aText=[];
		var mytr;

		switch(my.viewtype){
		case 'TREE':
			mytr=$(my.gridname+' tbody tr:eq('+row+')');
			aText.push(mytr.attr('party'));
			aText.push(mytr.attr('party_name'));
			aText.push(mytr.attr('data-tt-id'));
			aText.push(mytr.attr('data-tt-parent-id'));
			mytr.find('td').each(function(){
				aText.push($(this).text());
			});
			break;
		case 'VERT':
			var col = $(my.gridname+' tbody td:eq('+row+')').index();
			$(my.gridname+' tbody tr td:eq('+col+')').each(function(){
				aText.push($(this).text());
			});
			break;
		default: // HRZN
			mytr=$(my.gridname+' tbody tr:eq('+row+')');
			mytr.find('td:gt(0)').each(function(){
				aText.push($(this).text());
			});
		}
//		console.log(aText);
		return {aText};
	},
	findLinkinfoAs:function(ndx){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var my=this;
		if(ndx=='parent' || ndx=='child') ndx+='_grid';

		var _retObj={}
		_retObj.arLinkinfo=[];
		$.each(garPage,function(n1,oPage){
			$.each(oPage.linkinfo,function(n2,oLink){
				if(oLink[ndx]==my.rowid){
					_retObj.arLinkinfo.push(oLink);
				}
			});
		});
		return _retObj;
	}
}

$(document)
.on('click','.new',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var my=garGrid[$(this).index('.new')];
	wlog('dialog',my.dialog);
	
	if($.isFunction(my.bef_new)) if(!my.bef_new()) return false;

	if(my.dialog==''){
		var arglist=[];
		var oParent=my.getParents();
		var nLine=0;
		$.each(my.colinfo,function(ndx,val){
			var par_rowid='';
			$.each(oParent.arLink,function(ndx1,val1){
				if(val1.nChild==ndx){	// Check if each column is mapped to the Link info column.
					par_rowid=val1.par_rowid;
					return false;
				}
			});
			arglist.push(par_rowid);
		});
		var pstr='optype=new_var&rowid='+my.rowid+'&_e='+my._e+'&_column='+arglist.join('^');
		$.ajax({
			data:pstr,url:'disu.php',async:true,
			beforeSend:function(){wlog('#b',this.data);},
			success:function(xml){
				console.log(xml);
				var _return=$(xml).find('errno').text();
				if(_return!=''){
					alert(_return);
					return false;
				}
				switch(my.viewtype){
				case 'HRZN':
					var trtext='<tr><td aling=center>new</td>';
					var i=0;
					$.each(($(xml).find('replyText').text()).split('^'),function(ndx,val){
						var colnow=my.colinfo[i++];
						wlog('i after split ('+colnow.a4update+'.'+colnow.nick+')',val);
						trtext+='<td'+(colnow.pixel=='0'?' style="display:none;"':' width='+colnow.pixel+'px align='+(colnow.align!=''?'left':colnow.align))+
						'>'+val+'</td>';
					});
					trtext+='</tr>';
					wlog(trtext);
					$(my.gridname+' tbody').append(trtext);
					break;
				case 'VERT':
					var trtext='<th>new</th>';
					$(my.gridname+' thead tr').append(trtext);
					var i=0;
					$.each(($(xml).find('replyText').text()).split('^'),function(ndx,val){
						var colnow=my.colinfo[i++];
						trtext='<td'+(colnow.pixel=='0'?' style="display:none;"':' width='+colnow.pixel+'px align='+(colnow.align!=''?'left':colnow.align))+
							'>'+val+'</td>';
						$(my.gridname+' tbody tr:eq('+i+')').apped(trtext);
					});
					break;
				case 'TREE':
					break;
				}
			},
			complete:function(){
				if($.isFunction(my.aft_new)) if(!my.aft_new()) return false;
			}
		});
	} else if(my.dialog=='dialog'){
		my.input4new();
	} else {
		my.list4new();
	}
	return false;
})
.on('click','.duplicate',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var my=garGrid[$(this).index('.duplicate')];
	var pstr=[];
	
	if($.isFunction(my.bef_dup)) if(!my.bef_dup()) return false;
	
	my.duplicateRow();
	return false;
})
.on('click','.delete',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	/*
	 * based on the cascade delete.
	 */
	if(!confirm('Delete ?')) return false;	// confirm() does not work only on the Chrome.
	var my=garGrid[$(this).index('.delete')];

	
	if($.isFunction(my.bef_del)) if(!my.bef_del()) return false;

	my.deleteRow();
	
	if($.isFunction(my.aft_del)) if(!my.aft_del()) return false;
	return false;
})
.on('click','.find',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var n=$(this).index('.find');
	var pstr=$('.search').eq(n).val();
	wlog('pstr',pstr);
	if(pstr==''){
		$(garGrid[n].gridname+' tbody tr.blind').removeClass('blind');
		return false;
	} else {
		$(garGrid[n].gridname+' tbody tr').addClass('blind');
		$(garGrid[n].gridname+' tbody td').filter(function(){return ($(this).text()).indexOf(pstr)>-1;}).closest('tr').removeClass('blind');
	}
	return false;
	
})
.on('dblclick','tbody td',function(){
	wlog('dblclick tbody td');
	var ndx = $(this).closest('table[name^=tbl]').index('table[name^=tbl]');
	if(ndx<0) return false;
	var my=garGrid[ndx];
	
	var row =$(this).parent().parent().children().index($(this).parent());
	var col = $(this).index();
	var oldtext = $(this).text();
	var pstr = '', strCtl='', num_input='';
	
	var colinfo=my.colinfo[col-1];
	if(my.viewtype=='VERT'){
		colinfo=my.colinfo[row];
	} else if(my.viewtype=='TREE'){
		colinfo=my.colinfo[col+TREE_INDEX_START];
	} else {
		colinfo=my.colinfo[col-1];
	}
	
	switch(colinfo.dtype){
	case 'radio':
	case 'select':
		strCtl='seltext';
		pstr='<select name='+strCtl+' id='+strCtl+'>';
		console.log(colinfo);
		$.each(colinfo.element,function(col,val){
			pstr+='<option ';
			if(oldtext==val) pstr+='selected ';
			pstr+='value="'+val+'">'+val+'</option>';
		});
		pstr+='</select>';
		wlog('pstr',pstr);
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
		if(parseInt(colinfo.digit)>32) {
			nSize=32;
			var nRow = parseInt(colinfo.digit)/nSize;
			if(parseInt(colinfo.digit) % nSize>0) nRow++;
			pstr+='<textarea rows='+nRow+' cols='+nSize+' id='+colinfo.nick+' name='+colinfo.nick+'>'+oldtext+'</textarea>';
		} else {
			nSize=parseInt(colinfo.digit);
			pstr+='<input type='+colinfo.dtype+' id='+colinfo.nick+' name='+colinfo.nick+' size='+nSize+' maxlength='+colinfo.digit+
				num_input+' value="'+oldtext+'" title="'+colinfo.tooltip_msg+'"></td></tr>';
		}
		break;
	}
	wlog('dtype',colinfo.dtype,'oldtext',oldtext,'pstr',pstr,'row',row,'col',col);
	
	$(this).html(pstr);
	setTimeout(function(){$('#'+strCtl).focus().select();},100);
	
	$(this).children().first()
	.keydown(function(e){
		switch( e.keyCode||e.which ){
		case KEY_ESCAPE:
			wlog('key_escape');
			$(this).parent().text(oldtext);
			return false;
			break;
		case KEY_TAB: case KEY_ENTER:
//			var new_value = $(this).val();
			wlog('keypress newvalue ['+new_value+'] oldtext ['+oldtext+'] triggering');
			$(this).trigger('blur');
			break;
		}
//		return false;  Can't return false here.
	})
	.blur(function(e){
		try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

		var new_value = $(this).val();
		wlog('blur new',new_value,'oldtext',oldtext,'viewtype',my.viewtype);
		if( new_value == null ) new_value='';
		$(this).parent().text(new_value);
		if( oldtext != new_value) {
			if(!validateData(colinfo.dtype,new_value)){
				$(this).parent().text(oldtext);
				return false;
			}
			if(!my.inline_update(row,col)) {
				$(this).parent().text(oldtext);
				return false;
			}
		}
		$(this).parent().text(new_value);
		return false;
	});
//	return false;
})
.on('change','.selpage',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var ndx=$(this).index('.selpage');
	var my=garGrid[ndx];
	var nPage=$('.selpage').eq(ndx).val();
	wlog('nPage',nPage);
	
	my._y=nPage;
	my.showTitle();
	my.loadTable();
	return false;
})
.on('click','.prev',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var cursel=$('.selpage').eq($(this).index('.prev'));
	var opt=cursel.find(':selected').prev('option');
	wlog('prev opt',opt.length,'val',opt.val());
	if(opt.length>0) {
		cursel.find(':selected').removeAttr('selected');
		opt.attr('selected','selected');
		cursel.change();
	}
	return false;
})
.on('click','.next',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var cursel=$('.selpage').eq($(this).index('.next'));
	var opt=cursel.find(':selected').next('option');
	wlog('next opt',opt.length,'val',opt.val());
	if(opt.length>0) {
		cursel.find(':selected').removeAttr('selected');
		opt.attr('selected','selected');
		cursel.change();
	}
	return false;	
})
.on('click','tbody tr',function(){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var ndx=$(this).closest('table[name^=tbl]').index('table[name^=tbl]');
	wlog('click tbody',ndx,'<<<<<<<<<<<<<<<<<<<<<<<<<<<');
	if(ndx<0) return false;	//no responding grid table.
	var my=garGrid[ndx];
	
//	var row =$(this).parent().parent().children().index($(this).parent());
	var col = $(this).index();
	var oldtext = $(this).text();
	var pstr = '', strCtl='';

	var row=$(this).index();
	var oldrow=$(my.gridname+' tr.selected').index();
	wlog('row',row,'oldrow',oldrow);

	if(oldrow==row){
		$(this).removeClass('selected');
		$('#toolbar'+my.rowid).find('.modify').hide();
		row=-1;
	} else {
		$(this).addClass('selected');
		$(my.gridname+' tr.selected').not(this).removeClass('selected');
		$('#toolbar'+my.rowid).find('.modify').show();
	}
	
	var oChild=my.findLinkinfoAs('parent');
	console.log(oChild);
	// the Linkinof of Children can be mutiple.
	$.each(oChild.arLinkinfo,function(ndx,oLink){
		if(row==-1){
			$('#tbl'+oLink.child_grid+' tbody tr.unchosen').removeClass('unchosen');
			return true;
		}
		var parent_col=parseInt(oLink.parent_col)+1;
		var pvalue=$(my.gridname+' tbody tr:eq('+row+') td:eq('+parent_col+')').text();
//			wlog('linkinfo my.rowid',my.rowid,'pvalue',pvalue);
		
		var child_col=parseInt(oLink.child_col)+1;
		var child_sort_col=parseInt(oLink.child_sort_col)+1;
		wlog('#c','parent_col',parent_col,'child_grid',oLink.child_grid,'child_col',child_col);
		$('#tbl'+oLink.child_grid+' tbody tr').each(function(){
			var tdtext=$(this).find('td:eq('+child_col+')').text();
			if(pvalue==tdtext) $(this).removeClass('unchosen');
			else $(this).addClass('unchosen');
		});
	});
	return false;
})
;

function findGrid(ndx,fkey){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	var _retGrid={};
	$.each(garGrid,function(n,oGrid){
		if(oGrid[ndx]==fkey){
			_retGrid=oGrid;
			return false;
		}
	});
	return _retGrid;
}

function validateData(dtype,value){
	try {[].undef ()} catch (e) {wlog('#y','f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');}

	wlog('validateData dtype',dtype,'value',value);
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