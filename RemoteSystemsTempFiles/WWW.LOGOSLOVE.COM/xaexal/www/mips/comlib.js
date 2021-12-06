$(document)
.on('click','#btnChat',function(){
	if($('#divChat').length==0){
		$('body').append('<div id=divChat name=divChat style="width:200px;height:300px">'+
				'<table width=100% height=100%>'+
				'<tr><td><select id=lstChat name=lstChat size=18 style="width:270px"></select></td></tr>'+
				'<tr><td><textarea id=txtChat rows=4 cols=42></textarea>'+
				'<input type=button id=btnSend value=Send /></td></tr></div>');
	}
	$('#divChat').dialog();
})
.on('click','#btnSend',function(){
	alert('comlib.php ['+member_id+']');;
	var msg=$.trim($('#txtChat').val());
	if(msg=='') return false;
	$.ajax({
		url: 'msg.php', datatype:'text',
		data: 'optype=send&msg='+msg+'&sender='+member_id,
		beforeSend: function() {
			wlog('#b',this.data);
		},
		success:function(_return){
			if(_return=='') return false;
			$('#lstChat').append('<option value="">'+_return+'\n'+msg+'</option>'); 
		},
		complete:function(){}
	});
})
.ajaxStart(function(){
	console.log('dvLoading')
	$('#dvLoading').dialog({autoOpen:true});
	$('.ui-dialog-titlebar').hide();
	$('#dvLoading').css('border-style','none');
})
.ajaxStop(function(){
	$('#dvLoading').dialog('close');
//	console.log('ajaxStop');
})
.ajaxError(function(){
	$('#dvLoading').dialog('close');
//	console.log('ajaxError');
})
;

$(':input[name^=btnDelete]').click(function(){
	var fname = '#'+$(this).closest('form').attr('name');
	fn_Delete(fname);
});

function init_screen(_e,_p) {
	try { [].undef () } catch (e) {
	     console.log ('f ['+e.stack.split ('\n')[1].split (/\s+/)[2]+']');
	  }
	var xLayout = $('body').layout();
	xLayout.close('east');
//	setLocalMessage();
	$( "input[type=buttn],button").button().css('height','20px');
}
function wlog(pstr,flag){
	$.get('_wlog.php',{optype:'write',logstr:pstr,flag:flag},function(json){},'json');
}

function resetlog(){
	console.log('resetlog')
	$.get('_wlog.php',{optype:'resetlog'},function(json){},'json')
	.done(function(){
//		console.log('Reset Done.');
	})
	.fail(function(){
//		console.log('Reset Fail.');
	});
}

function wlog1() {
	var pstr=''
//	console.assert(dbg==true,'dbg is unabled');
	if( !dbg )	return;
	
	var color='',n=1;
	switch(arguments[0]){
	case '#c': case '#b':
		color = 'background:blue;color:cyan ';
		pstr+= '%c ';
		break;
	case '#r':
		color = 'background:red;color:white ';
		pstr+='%c ';
		break;
	case '#y':
		color='background:yellow;color:black'; pstr+='%c ';
		break;
	default:
		n=0;
		break;
	}
	for( var i in arguments ) {
		if(i<n)continue;
		if(i%2==n){
			pstr += arguments[i];
		} else {
			pstr += ' ['+arguments[i]+'] ';
		}
	}
	if(color!='') console.log(pstr,color);
	else console.log(pstr);
}

function clog(){
	if(!dbg) return;
	var pstr='';
	for(var i in arguments){
		pstr+=arguments[i]+' ['+eval(arguments[i])+'] ';
	}
	console.log(pstr);
}

function filelog(){
	if( !dbg )	return;
	
	var pstr = '';
	for( var i in arguments ) {
		pstr += arguments[i];
	}
	$.get('dbwork.php',{optype:'wlog',logtext:pstr});
}

$.fn.clearForm = function() {
	return this.each(function() {
		var cname = this.id;
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')	return $(':input',this).clearForm();
		if (type == 'text' || type == 'password' || tag == 'textarea')	this.value = '';
		else if (type == 'checkbox' || type == 'radio')		this.checked = false;
		else if (tag == 'select' || tag == 'select-one' ) {
			if( cname.substr(0,3) == 'sel' ) $('#'+cname).html('');
			else this.selectedIndex = -1;
		}
  });
};

$.fn.resetForm = function() {
	return this.each(function() {
		var cname = this.id;
		var type = this.type, tag = this.tagName.toLowerCase();
		if (tag == 'form')	return $(':input',this).resetForm();
		if (type == 'text' || type == 'password' || tag == 'textarea')	this.value = '';
		else if (type == 'checkbox' || type == 'radio')		this.checked = false;
		else if (tag == 'select' || tag == 'select-one' ) {
			if( cname.substr(0,3) != 'sel' )	this.selectedIndex = -1;
		}
  });
};

$.fn.initForm = function() {
	return this.each( function() {
		var cname = this.id;
		var type = this.type, tag = this.tagName.toLowerCase();
		if( tag == 'form' )	return $(':input',this).initForm();
		
		switch( type ) {
		case 'text': case 'password': case 'textarea': case 'hidden':
			this.value = '';
			break;
		case 'checkbox': case 'radio':
			this.checked = false;
			break;
		case 'select': case 'select-one':
			this.selectedIndex = -1;
		}
	});
}



$(':button[name^=btnAdd]').click(function(){
	
	fn_Add('#'+$(this).closest('form').attr('name'));
	return false;  // this prevents the entire screen to be refreshed.
});



/*
 * ------------------ Auto Run for every control ----------------
 */
$(':input').blur( function(){
//	wlog('['+$(this).val()+']');
	var pstr = $(this).val();
	pstr = $.trim(pstr);
	$(this).val(pstr);
//	wlog(this.id+' ['+pstr+']');
});
/*
 * ----------------------------------------------------------------- 
 */
 
function calcFloat(a,b,op)
{
	if( a == '' )	a='0';
	if( b == '' )	b='0';
	
	var ar1 = a.split('.');
	var ar2 = b.split('.');
	
	if( ar1.length < 2 ) ar1.push('0');
	if( ar2.length < 2 )	ar2.push('0');

	if( ar1[0].length == 2 && ar1[0].substr(0,1) == '0' )	ar1[0] = ar1[0].substr(1,1);
	if( ar1[1].length == 2 && ar1[1].substr(0,1) == '0' )	ar1[1] = ar1[1].substr(1,1);
	if( ar2[0].length == 2 && ar2[0].substr(0,1) == '0' )	ar2[0] = ar2[0].substr(1,1);
	if( ar2[1].length == 2 && ar2[1].substr(0,1) == '0' )	ar2[1] = ar2[1].substr(1,1);
	
	ar1[0] = parseInt(ar1[0]);		ar1[1] = parseInt(ar1[1]);
	ar2[0] = parseInt(ar2[0]);		ar2[1] = parseInt(ar2[1]);
	
	switch(op) {
	case '+':
		var n = ar1[1]+ar2[1];
		var x = ar1[0]+ar2[0];
		if( n > 99 ) {
			n -= 100;	x++;
		}
		break;
	case '-':
		var n = ar1[1]-ar2[1];
		var x = ar1[0]-ar2[0];
		if( n < 0 ) {
			n += 100;	x--;
		}
		break;
	default:		return '';
	}
	if( x < 0 )	return a;
	
	var pstr = x.toString();
	if( n < 10 && n > 0 ) 	pstr += '.0'+n.toString();
	else	pstr += '.'+n.toString();

	return pstr;
}
function DateAdd(timeU,byMany,dateObj) {
	var millisecond=1;
	var second=millisecond*1000;
	var minute=second*60;
	var hour=minute*60;
	var day=hour*24;
	var year=day*365;

	var newDate;
	var dVal=dateObj.valueOf();
	switch(timeU) {
	case "ms": newDate=new Date(dVal+millisecond*byMany); break;
	case "s": newDate=new Date(dVal+second*byMany); break;
	case "mi": newDate=new Date(dVal+minute*byMany); break;
	case "h": newDate=new Date(dVal+hour*byMany); break;
	case "d": newDate=new Date(dVal+day*byMany); break;
	case "y": newDate=new Date(dVal+year*byMany); break;
	}
	return newDate;
}
function deleteRecord(fname,fBefore,fAfter){
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;

	bSuccess = false;
 
	$.ajax({
		datatype:"text",
		data:'optype=delete&'+serializeX(fname),
		beforeSend:function(){
			if( sqldbg ) {wlog('#b',this.data);
			}
			if( fBefore != '' )	eval(fBefore);
		},
		success:function(strReturn){
			if(  strReturn == 'ok' ) bSuccess = true;
			else	bSuccess = false;
		},
		complete:function(){
			if( !bSuccess )	return false;
			if( fAfter != '' )	eval(fAfter);
		}
	});
}


function descSort(fname) {
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;
	
	var arItem = new Array();
	var selname = $(fname+' :input[name^=sel]').attr('name');
	selname = fname+' :input[name='+selname+']';
	
	if( $(selname+' option').length < 2 )	return;
	
	for( var i=0; i<$(selname+' option').length; i++ ) {
		$(selname).attr('selectedIndex',i);
		arItem[i] = new Array();
		arItem[i][0] = $(selname+' option:selected').text();
		arItem[i][1] = $(selname+' option:selected').val();
	}
	$(selname).html('');
	arItem.sort();
	for( var i=0,pstr; i<arItem.length; i++ ) {
		pstr = '<option value="'+arItem[i][1]+'">'+arItem[i][0]+'</option>';
		$(selname).prepend(pstr);
	}
}

function addSelect(pValue,pText){
	
}
function disableForm(fname,bStatus) {
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;

	if( bStatus ) 	$(fname+' :input[type!=hidden]').attr('disabled','disabled');
	else	$(fname+' :input[type!=hidden]').removeAttr('disabled');
	$(fname+' :input[name^=sel]').removeAttr('disabled');
	$(fname+' :button').removeAttr('disabled');
}
function fetchForm(fname,fBefore,fAfter) {
	bSuccess = false;
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;
	
	$.ajax({
		dataType:'xml',
		data:'optype=select&'+serializeX(fname),
		beforeSend: function(){
			if( sqldbg ) {wlog('#b',this.data);
			}
			if( fBefore != '' )	eval(fBefore);
		},
		success: function(xml){
			$(xml).find('crlf').children().each(function(){
				var pColumn = fname+' :input[name='+this.nodeName+']';
				var pValue = $(this).text();
				var pNode=$(pColumn), bCheck;
				
				switch( pNode.attr('type') ) {
	            case 'select-multiple':
	            case 'select-one':
	            	setListByValue(pColumn,pValue);
	            	break;
	            case 'text':
	            case 'textarea':
	            case 'hidden':
	            	pNode.val(pValue);
	            	if( pColumn.substr(0,7) == 'picture' )	{
	            		showPicture(pValue);
	            	}
            		break;
	            case 'checkbox':
	            case 'radio':
	            	setCheckbox(pColumn,pValue);
	            	break;
	            default:
				}
			});
			$('#btnDelete').attr('disabled',false);
			bSuccess = true;
		},
		error: function(){
			bSuccess = false;
		},
		complete:function() {
			if( fAfter != '' )	eval(fAfter);
		}
	});
}

function fetchList(fname,fBefore,fAfter) 
{
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;
	var arColumn = $.trim($(fname+' :input[name=_column]').val());

	$.ajax({
		dataType:'xml',
		data:'optype=select&'+serializeX(fname),
		beforeSend: function(){
			if( sqldbg ) {wlog('#b',this.data);
			}
			if( fBefore != '' )	eval(fBefore);
		},
		success: function(xml){
			var selval=0, pTemp;
			arColumn = arColumn.split(',');
			$(xml).find('crlf').each(function(){
				var selstr = '';
				for( var n=0,typestr=''; n<arColumn.length; n++ ) {
					pTemp = $(this).find(arColumn[n]).text();
					if( n == 0 ) selval = pTemp;
					
					if( selstr != '' )	selstr+=',';
					typestr = $(fname+' :input[name='+arColumn[n]+']').attr('type');
//					tracelog('typestr ['+typestr+'] arColumn ['+arColumn[n]+']');
					switch( typestr ) {
					case 'select-multiple':
					case 'select-one':
						getListValue(fname+' :input[name='+arColumn[n]+']',pTemp);
						break;
					case 'checkbox':
					case 'radio':
						setCheckbox(fname+' :input[name='+arColumn[n]+']',pTemp);
//						break;
//					default:
//						selstr += pTemp;
					}
					selstr += pTemp;
				}
				$(fname+' :input[name^=sel]').append('<option value='+selval+'>'+selstr+'</option>');
				selval++;
			});
			descSort(fname);
		},
		complete:function() {
			if( fAfter != '' )	eval(fAfter);
			if( sqldbg ) {
			}
		}
	});
}

function fillSelect(inputname,tname,column,pname,pvalue,orderby,nullitem,strDelimeter) {
	$.ajax({
		datatype:"xml",
		data:"optype=select&_tname="+tname+"&_column="+column+"&_pname="+pname+"&"+pname+"="+pvalue+"&orderby="+orderby,
		beforeSend:function(){wlog('#b',this.data);},
		success:function(xml){
			var pstr;
			var arDelimeter = '';
			strDelimeter = $.trim(strDelimeter);
			if( strDelimeter != '' ) arDelimeter = strDelimeter.split('');	// equal to or greater than arField length.
			column = $.trim(column);
			var arField = column.split(',');
			var x=-1;
			for( var n=0; n < arField.length; n++ ) {
				x = arField[n].indexOf('.');
				if( x > -1 ) arField[n] = $.trim(arField[n].substr(x+1));
			}
			$('#'+inputname).html('');

			var pOut = '';
			if( nullitem )	pOut = "<option value=''>-</option>";
			$(xml).find('crlf').each(function(){
				var strOut = '', sch='';

				for( var n=1; n < arField.length; n++,sch='' ) {
					strOut += $(this).find(arField[n]).text();
					if( n <= arDelimeter.length )	strOut += arDelimeter[n-1];
				}
				pstr = "<option value='"+$(this).find(arField[0]).text()+"'>"+strOut+"</option>";
//				tracelog(pstr);
				pOut += pstr;
			});
			$('#'+inputname).append(pOut);
		},
		complete:function(){},
		error:function(){}
	});		
}

function fn_Add(fname) {
	var arColumn = $.trim($(fname+' :hidden[name=_column]').val());	
	arColumn = arColumn.split(',');
	var pstr = '', value=null;
	var objstr=null, objtype;
	for( var i=0; i<arColumn.length; i++ ) {
		if( pstr != '' )	pstr += ',';

		objstr = fname+' :input[name='+arColumn[i]+']';
		objtype = $(objstr).attr('type');
		switch( objtype ) {
		case 'select-multiple':
		case 'select-one':
			pstr +=  $(objstr+' option:selected').val();
			value = $(objstr).val();	// select list가 main item이므로 primary key역할을 하는 value을 select list에서 추출.
			$(objstr).attr('selectedIndex',-1);
			break;
		case 'checkbox':
			pstr += getCheckbox(objstr);
			$(objstr).each(function(){
				$(this).removeAttr('checked');
			});
			break;
		case 'radio':
			if( $(objstr).val() == 'on' )	{
				pstr += '1';
			} else	pstr += '0';
			$(objstr).each(function(){
				$(this).removeAttr('checked');
			})
			break;
		default:	// text,textarea
			pstr += $(objstr).val();
			$(objstr).val('');
		}
	}
	var selobj = $(fname+' :input[name^=sel]');
	var curSelect = selobj.attr('selectedIndex');
	if( typeof curSelect == 'undefined' || curSelect == '-1' ) 
		selobj.prepend('<option value'+value+'>'+pstr+'</option>');
	else {
		$(fname+' :input[name^=sel] option:selected').val(value);
		$(fname+' :input[name^=sel] option:selected').text(pstr);
	}
}

function fn_Delete(fname) {
	var arColumn = $.trim($(fname+' :hidden[name=_column]').val());
	arColumn = arColumn.split(',');
	var objstr, objtype;
	for( var i=0; i<arColumn.length; i++ ) { 
		objstr =fname+' :input[name='+arColumn[i]+']';
		objtype = $(objstr).attr('type');
		
		if( objtype == 'select' || objtype == 'select-one' ) $(objstr).attr('selectedIndex',-1);
		else if( objtype == 'checkbox' || objtype == 'radio' ) setCheckbox(objstr,'');
		else $(objstr).val('');
	} 
	var curIndex = parseInt($(fname+' :input[name^=sel]').attr('selectedIndex'));
	$(fname+' :input[name^=sel] option:selected').remove();
	if( curIndex >= $(fname+' :input[name^=sel] option').length )	curIndex = $(fname+' :input[name^=sel] option').length-1;
	$(fname+' :input[name^=sel]').attr('selectedIndex',curIndex).click();	
}

function fn_Get(fname)
{
	if( fname.substr(0,1) != '#')	fname = '#'+fname;

	var selname = $(fname+' :input[name^=sel]').attr('name');
	var curSelect = $(fname+' :input[name='+selname+']').attr('selectedIndex');
	if( curSelect == 'undefined' || curSelect == '-1' ) return false;

	var pstr = $(fname+' :input[name='+selname+'] option:selected').text();
	
	var arValue = pstr.split(',');
	var arColumn = $(fname+' :hidden[name=_column]').val();
//	tracelog('arColumn ['+arColumn+']');
	arColumn = arColumn.split(',');

	for( var i=0; i<arValue.length; i++ ) {
		ctlstr = fname+' :input[name='+arColumn[i]+']';
		switch( $(ctlstr).attr('type') )	{
		case 'select-one':
		case 'select-multiple' :
//			setListByText(ctlstr,arValue[i]);
			setListByValue(ctlstr,arValue[i]);
			break;
		case 'checkbox':
		case 'radio':
			setCheckbox(ctlstr,arValue[i]);
			break;
		default:
			$(ctlstr).val(arValue[i]);
		}
	}
}

function fn_New(fname)
{
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;

	$(fname).resetForm();
}

function fn_Reset(fname){
	var arColumn = $.trim($(fname+' :hidden[name=_column]').val());
	if( arColumn != '' )	arColumn = arColumn.split(',');
	else	arColumn = '';
	var objstr, objtype,objname;
	for( var i=0; i<arColumn.length; i++ ) {
		objstr =fname+' :input[name='+arColumn[i]+']';
		objtype = $(objstr).attr('type');
		objname = $(objstr).attr('name');
		
		if( objtype == 'select' || objtype == 'select-one' ) {
			$(objstr).attr('selectedIndex',-1);
		} else if( objtype == 'checkbox' || objtype == 'radio' ) {
			$(objstr).each(function() {
				$(this).removeAttr('checked');
			});
		} else {
			$(objstr).val('');
		}
	} 
	$(fname+' :input[name^=sel]').attr('selectedIndex',-1);
}

function get4YMMDD(pDate)	// YYYY-MM-DD with Date variable
{
	if( pDate == null || pDate == '' )		return '';
	var n = pDate.getFullYear();
	var pReturn = n.toString()+'-';
	
	n = parseInt(pDate.getMonth());
	if( ++n < 10 )	pReturn += '0';
	pReturn += n.toString()+'-';
	
	n = pDate.getDate();
	if( n < 10 )	pReturn += '0';
	pReturn += n.toString();
	return pReturn;
}
function getCheckbox(ctl)
{ 
	if( $(ctl).length == 1 ) {
		if( $(ctl).attr('checked') )	return '1';
		return '0';
	}
	var pstr='';
	$(ctl).each(function(){
		if( $(this).attr('checked') ) {
			if( pstr != '' )	pstr += '.';
			pstr += $(this).val();
		}
	});
	return pstr;
}
function getDateW10(pstr)
{
	var nYear = parseInt(pstr.substr(0,4));
	var nMonth, nDate;
	if( pstr.substr(5,1) == '0' )	nMonth = parseInt(pstr.substr(6,1)-1);
	else	nMonth = parseInt(pstr.substr(5,2)-1);
	if( pstr.substr(8,1) == '0' )	nDate= parseInt(pstr.substr(9.1));
	else nDate = parseInt(pstr.substr(8,2));
	return new Date(nYear,nMonth,nDate);
}

/*
 * get Datetime as format (YYYYMMDDHHMMSS).
 */
function getDatetime(){
	var strDatetime = new Date();
	var pDatetime = strDatetime.getFullYear();
	pDatetime = pDatetime.toString();
	var pstr = strDatetime.getMonth()+1;
	if( pstr < 10 ) pDatetime += '0';
	pDatetime += pstr.toString();
	var pstr = strDatetime.getDate();
	if( pstr < 10 ) pDatetime += '0';
	pDatetime += pstr.toString();
	pstr = strDatetime.getHours();
	if( pstr < 10 ) pDatetime += '0';
	pDatetime += pstr.toString();
	pstr = strDatetime.getMinutes();
	if( pstr < 10 ) pDatetime += '0';
	pDatetime += pstr.toString();
	pstr = strDatetime.getSeconds();
	if( pstr < 10 ) pDatetime += '0';
	pDatetime += pstr.toString();
	
	return pDatetime;
}

function getListValue(selstr,seltext) {
	var selvalue='';
	
	var curpos = $(selstr).attr('selectedIndex');
	
	for( var i=0; i<$(selstr+' option').length; i++ ) {
		$(selstr).attr('selectedIndex',i);
		if( seltext == $(selstr+' option:selected').text() ) {
			selvalue = $(selstr).val();
			break;
		}
	}
	$(selstr).attr('selectedIndex',curpos);
	return selvalue;
}

function getLOV(ctlname,flag) {
//	if( arguments.length < 2 )	return '';
//	var ctlname = arguments[0];
	if( ctlname.substr(0,1) != '#' )	ctlname = '#'+ctlname;
//	var flag = arguments[1];
	
	var pText = 'name_'+$.cookie('lingua'), pValue='flag', i=0;
	var pstr ='';
	
	var paramEnterprise = '';
//	if( arguments.length > 2 ) {
//		if( arguments[2] != '' )	paramEnterprise = '&enterprise='+arguments[2];	
//	}

	$.ajax({
		data:'optype=select&_tname=a_lov&_pname=parent_flag&parent_flag='+flag+'&_column='+pValue+','+pText+'&orderby='+pText+paramEnterprise,
		beforeSend:function(){
			wlog('#b',this.data);
			$(ctlname+' option').remove();
		},
		success:function(xml){
			var ndx=0;
			$(xml).find('crlf').each(function() {
				pstr = '<option value="'+$(this).find(pValue).text()+'">'+$(this).find(pText).text()+'</option>';
//				wlog(ctlname+' ['+pstr+']');
				$(ctlname).append(pstr);
//				$(ctlname).append($('<option></option>').attr('value',$(this).find(pValue).text()).text($(this).find(pText).text()));
				ndx++;
			});
//			$(ctlname).append(pstr);
//			wlog('LOV count ['+ndx+']');
		}
	});		
}
/*
function getMessage(code) {
	for( var i=0; i<arMessage.length; i++ ) {
		if( arMessage[i][0] == code ) {
			return arMessage[i][1] ;
		}
	}
	return 'No Message matched';
}
*/
function getMSG(pstr) {
	$.ajax({
		dataType: 'text',
		data: 'optype=msg&code='+pstr,
		beforeSend: function(){
			wlog('#b',this.data);
		},
		success: function(retval) {
			return retval;
		} 
	});
}
function getNewYMDHIS()
{	
var retval='';
	$.ajax({
		dataType: 'text', 
//		async:false,
		data: 'optype=new',
		success: function(pReturn){
			retval = pReturn;	
		}
	});
	return retval;
}

function hideSpinner()
{
	$('#dvLoading').fadeOut();
	$('body').css('cursor','default');
}
/*
function how_old(pstr1,pstr2) {
	
	if( pstr1.length < 8 ) 	return -1;
	
	var pNow;
	if( pstr2 == '' || pstr2 == null )	pNow = new Date();
	else	if( pstr2.length < 8 )	return -1;
	else 	pNow = new Date(pstr2.substr(0,4),pstr2,substr(4,2),pstr2.substr(6,2));
	
	var pNowYear =pNow.getFullYear();
	var pNowDay = '';
	
	if( pNow.getMonth()+1 < 10 ) pNowDay = "0"+String(pNow.getMonth());
	else	pNowDay = String(pNow.getMonth());
	if( pNow.getDate() < 10 ) pNowDay += '0';
	pNowDay += String(pNow.getDate());
	pHowOld = parseInt(pNowYear)-parseInt(pstr1.substr(0,4));
	if( pNowDay < parseInt(pstr1.substr(4,4)) ) pHowOld -= 1;
//	tracelog('['+pstr1+'] ['+pNowYear+','+pNowDay+'] ['+pHowOld+']');
	return pHowOld;
}
*/

function isAlphaNumeric(val)
{
	if (val.match(/^[a-zA-Z0-9]+$/))	return true;
	return false;
}

function isSymbol(val) {
	var ascii = val.charCodeAt(0);
	if( (ascii>32 && ascii<48) || (ascii>90 && ascii<97) || (ascii>123 && ascii<127) )	return true;
	return false;
}

function isDate(dateStr){	// yyyy-mm-dd yyyy/mm/dd yyyy/m/d yyyy-m-d
	if( dateStr == '' )	return true;
	var datePat = /^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/;
	var matchArray = dateStr.match(datePat); // is the format ok?
//	wlog('dateStr ['+dateStr+']');
	if (matchArray == null) {
	alert("Please enter date format as either yyyy/mm/dd or yyyy-mm-dd.");
	return false;
	}

	month = matchArray[3]; // p@rse date into variables
	day = matchArray[5];
	year = matchArray[1];

	if (month < 1 || month > 12) { // check month range
	alert("Month must be between 1 and 12.");
	return false;
	}

	if (day < 1 || day > 31) {
	alert("Day must be between 1 and 31.");
	return false;
	}

	if ((month==4 || month==6 || month==9 || month==11) && day==31) {
	alert("Month "+month+" doesn`t have 31 days!")
	return false;
	}

	if (month == 2) { // check for february 29th
	var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
	if (day > 29 || (day==29 && !isleap)) {
	alert("February " + year + " doesn`t have " + day + " days!");
	return false;
	}
	}
	return true; // date is valid
	}

function is_date(val){
	var d = new Date(val);
    return !isNaN(d.valueOf());
}

function is_time(val){
	var t=val.split(':');
	if(t.length<2) return false;
	if(parseInt(t[0])<0 || parseInt(t[0]>23)) return false;
	if(parseInt(t[1])<0 || parseInt(t[1]>59)) return false;
	return true;
}

function isImageFile(pstr) {
	if( pstr == '' )	return false;
	if( pstr.indexOf('.png') == -1 && pstr.indexOf('.jpg') == -1 && pstr.indexOf('.gif') == -1 )	return false;
	return true;
}

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}
function loadErrorMessage() {
	var i=0;
	$.ajax({
		data:'optype=errmsg&dl='+$.cookie('lingua')+'&code=',
		beforeSend: function(){
			wlog('#b',this.data);
			oMessage = {};
		},
		success: function(xml){
			$(xml).find('crlf').each( function(){
				i++;
				oMessage[$(this).find('code').text()] = $(this).find('msg').text();
			});
		},
		complete: function(){
//			wlog('message count ['+i+']'); 
			console.log(oMessage);
		}
	});
}

function logging(pstr) {
	pstr = pstr.replace(/&/g,'^');
	$.ajax({
		url: 'dbwork.php',
		data: 'optype=log&url='+pstr,
		beforeSend: function() {
			wlog('#b',this.data);
		}
	});
}

function menuinit(_e,_p) {
	$('#c_menu').dynatree({
		title:'Menu'
		,autoCollapse:true
		,onDblClick: function(node,event){
			return false;
		}
		,onActivate: function(node){
			if( node.data.url == '' ) return false;
			wlog('_p ['+node.data.url+'] _e ['+_e+']');
			document.location='crm.php?_p='+node.data.url+'&_e='+_e;
		}
		,dnd:{
			onDragStart: function(node){
				return true;
			},
			onDragEnter: function(node,sourceNode) {
				if( node.parent !== sourceNode.parent ) return false;
				return ['before','after'];
			},
			onDrop: function(node,sourceNode,hitMode,ui,draggable) {
				sourceNode.move(node,hitMode);
			}
		}
	});
	var rootNode = $('#c_menu').dynatree('getRoot');
//	resetlog();
	var tree = $('#c_menu').dynatree('getTree');
	$.post('dbwork.php',{
		optype:'menu',lingua:$.cookie('lingua'),_e:_e
	},function(json){
//		console.log(json);
		var rootname = '';
		var arNode=[];
		$.each(json['crlf'],function(ndx,value){
			var oNode=new Object;
//			console.log(value);
			$.each(value,function(idx,val){
//				wlog(idx+'/'+val);
				if(val==null) oNode[idx]='';
				else oNode[idx]=val;
			});
			arNode.push(oNode);
		});
		function drawTree(parent){
			var node,_isFolder,pTitle;
			for(var i=0;i<arNode.length;i++){
				node=arNode[i];
				if(node.par_rowid!=parent)continue;
					
//				wlog('parent ['+parent+'/'+node.par_rowid+'] ['+node.rowid+']');
				if(parent=='') par_node=rootNode;
				else par_node=tree.getNodeByKey(parent);
				_isFolder=false;
				pTitle=node.description;
				if(node._p=='') _isFolder=true;
				else if( node._p == _p) pTitle = '<font style="background-color:blue;" color=white>'+pTitle+'</font>';
				par_node.addChild({
					title:pTitle,//node.description,
					key:node.rowid,
					url:node._p,
					isFolder:_isFolder,
					expand:true
				});
				drawTree(node.rowid);
			}
		}
		drawTree('');
	},'json').always(function(){
		rootNode.visit(function(node){
			if( node.data.url == _p ){
				$('#screen_name').text(rmTag(node.data.title));
				node.visitParents(function(pnode){
					if( pnode.data.title != null ) $('#screen_name').text(rmTag(pnode.data.title)+' > '+$('#screen_name').text());
				});
//					wlog($('#screen_name').text());
				return false;
			}
		});
	});
}

function rmTag(pstr){
	return pstr.replace(/(<([^>]+)>)/ig,'');
}
function newRecord(fname,fBefore,fAfter) {
	bSuccess = true;
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;
	var pname = $(fname+' :input[name=_pname]').val();

	$.ajax({
		dataType:"text", 
//		async:false,
		data : "optype=new",
		beforeSend: function(){
			$(fname).clearForm();
			if( fBefore != '' )	eval(fBefore);
			if( !bSuccess )	return false;	
		}, 
		success: function(strReturn) {
			alert(strReturn);
			$(fname+' :input[name='+pname+']').val(strReturn);
		},
		complete: function() {
			if( fAfter != '' )	eval(fAfter);
		}
	});	
}
function saveList(fname,fBefore,fAfter)
{
//	wlog('fname ['+fname+']');
	var selstr = $(fname+' :input[name^=sel]').attr('name');
	selstr = fname+' :input[name='+selstr+']';

	var pstr = $(fname+' :input[name=_pname]').val();
	var arColumn = [];
	if( pstr.indexOf(',') == -1 )	arColumn.push(pstr);
	else	arColumn = pstr.split(',');
	
	var purl = 'optype=replace&_formtype=list&_tname='+$(fname+' :input[name=_tname]').val()+'&_pname='+$(fname+' :input[name=_pname]').val();
	for( var i=0; i< arColumn.length; i++ ) {
		purl += '&'+arColumn[i]+'='+$(fname+' :input[name='+arColumn[i]+']').val();
	}
//	tracelog('purl ['+purl+']');
	arColumn = $.trim($(fname+' :input[name=_column]').val());
	arColumn = arColumn.split(',');
	var arValue = null;

	for( var i=0; i<$(selstr+' option').length; i++ ) {
		$(selstr).attr('selectedIndex',i);
		arValue = $.trim($(selstr+' option:selected').text());
		arValue = arValue.split(',');
		pstr = purl;

		for( var n=0, ctlstr; n<arColumn.length; n++) {
//			ctlstr = fname+' :input[name='+arColumn[n]+']';
			pstr += '&'+arColumn[n]+'='+arValue[n];
		}
		pstr += '&rownum='+i;
		$.ajax({
			datatype:'text',
			data:pstr,
			beforeSend:function(){
				wlog('#b',this.data);
			},
			success:function(strReturn){
				if( strReturn == "ok" ) bSuccess = true;
				else	bSuccess = false;
			},
			complete: function() {
				if( !bSuccess )	return false;
				if( fAfter != '' )	eval(fAfter);
			}
		});
	}
}

function saveRecord(fname,fBefore,fAfter)
{
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;
	bSuccess = false;

	if( $(fname+' :input[name=_formtype]').val() == 'form' ) {
		$.ajax({
			dataType:"text", 
			//async:false,
			data : "optype=replace&"+serializeX(fname),
			beforeSend: function(){  
				if( sqldbg ) {wlog('#b',this.data);}
				if( fBefore != '' )	eval(fBefore);
			},
			success: function(strReturn) {
				if( strReturn == "ok" ) bSuccess = true;
				else	bSuccess = false;
			},
			complete: function() {
				if( !bSuccess )	return false;
				if( fAfter != '' )	eval(fAfter);
			}
		});
	} else {	// list
		var fBefore = fAfter = 'saveList("'+fname+'","","")';
		deleteRecord(fname,fBefore,fAfter);		// 저장하기전에 매번 저장된 내용을 지워버림. integrity 문제발생.

	}
}

function scrollTo() {
	var nPosition = 0;
	var nSpeed = 'fast';

	if( arguments.length == 1 ) {
		nPosition = arguments[0];
	} else if( arguments.length == 2 ) {
		nPosition = arguments[0];
		nSpeed = arguments[1];
	}
	$('html,body').animate({scrollTop: nPosition}, nSpeed);
		
}

//02 SEP 2011
function selectRecord(fname,fBefore,fAfter) {
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;

	if( $(fname+' :hidden[name=_formtype]').val() == 'form' ) fetchForm(fname,fBefore,fAfter);
	else fetchList(fname,fBefore,fAfter);
}

function SendMail(str,param) {
	$.ajax({
		dataType: 'text',
		url: 'exeSendMail.php',
		data: 'optype='+str+'&param='+param,
		beforeSend: function(){wlog('#b',this.data);		},
		success: function(strReturn){
			if( strReturn == 'ok' ) {
				alert('비밀번호가 새로 발급됐습니다. 이메일로 전송된 새 비밀번호로 로긴하십시오.');
			} else if( strReturn == 'fail' ) {
				alert('비밀번호 발급이 실패했습니다. 관리자에게 연락하십시오.');
			}
		},
		complete: function(){}
	});
}

function seqAlert()
{
	var pstr = alertseq++;
	pstr = '{'+pstr+'} ';
	for( var i in arguments )	pstr += ' '+arguments[i];
	
	alert(pstr);
}

function serializeX(fname)
{
	if( fname.substr(0,1) != '#' )	fname = '#'+fname;
	
	var strValue = $(fname).serialize();
//	wlog(strValue);
	strValue = strValue.replace(/=on/g,'=1');
	strValue = strValue.replace(/=off/g,'=0');
	$(fname+' :input[type=checkbox]').each(function(){
		var pstr = $(this).attr('name');
		strValue += '&'+pstr+'='+getCheckbox(fname+' :input[name='+pstr+']');
	});
//	wlog('serializeX ['+strValue+']');
	return strValue;
}

function setAllText(ctlname,value) {
	$(':input[name='+ctlname+']').each(function() {
		$(this).val(value);
	});
} 

function setCheckbox(ctl,valuestr)
{
	if( $(ctl).length == 1 ) {
		if( valuestr == '1' )	$(ctl).attr('checked',true);
		else	$(ctl).removeAttr('checked');
		return;
	}
	if( valuestr == '' ) {
		$(ctl).each(function(){	$(this).removeAttr('checked'); });
		return;
	}
	var arValue = valuestr.split('.');
	for( var i=0; i<arValue.length; i++ ) {
		$(ctl).filter('[value='+arValue[i]+']').attr('checked','checked');
	}
}

function setList(inputname,tname,column,where,orderby,nullitem,strDelimeter) {

	$.ajax({
		datatype:"xml",
		data:"optype=select&church=each&_tname="+tname+"&column="+column+"&where="+where+"&orderby="+orderby,
		beforeSend:function(){
			wlog('#b',this.data);
		},
		success:function(xml){
			var arDelimeter = strDelimeter.split('');	// equal to or greater than arField length.
			column = $.trim(column);
			var arField = column.split(',');
			var x=-1;
			for( var n=0; n < arField.length; n++ ) {
				x = arField[n].indexOf('.');
				if( x > -1 ) arField[n] = $.trim(arField[n].substr(x+1));
			}
			$('#'+inputname).html('');

			var pOut = '';
			if( nullitem )	pOut = "<option value=''>-</option>";
			$(xml).find('crlf').each(function(){
				var strOut = '', sch='';

				for( var n=1; n < arField.length; n++,sch='' ) {
					if( n < arDelimeter.length-1 )	strOut += arDelimeter[n-1];
					strOut += $(this).find(arField[n]).text();
				}
				if( n < arDelimeter.length )	strOut += arDelimeter[n];
				pOut += "<option value="+$(this).find(arField[0]).text()+">"+strOut+"</option>";

			});
			$('#'+inputname).append(pOut);
		},
		complete:function(){},
		error:function(){}
	});		
}

function setListByText(selstr,seltext) {
	for( var i=0; i<$(selstr+' option').length; i++ ) {
		$(selstr).attr('selectedIndex',i);
		if( seltext == $(selstr+' option:selected').text() ) break;
	}
	if( i >= $(selstr+' option').length )	$(selstr).attr('selectedIndex',-1);
}

function setListByValue(selstr,selvalue)
{
//	for( var i=0; i < $(selstr+' option').length; i++ ) {
//		$(selstr).attr('selectedIndex',i);
//		if( selvalue == $(selstr+' option:selected').val() )	break;
//	}
//	if( i >= $(selstr+' option').length )	$(selstr).attr('selectedIndex',-1);
	
	$(selstr+' option').filter(function(){
		return $(this).val() == selvalue;
	}).prop('selected',true);
}

function setlog(pstr)
{
//	console.clear();
	$.post('dbwork.php',{optype:'setlog',logwrite:pstr});

	if( pstr == '-1' ) 	dbg = true;
	else	dbg = false;
}

function setOnlyField(inputname,tablename,fieldname,searchspec,orderby) {		// return only one field.
	var retval = null;
	
	$.ajax({
		datatype:"text",
		data:"tablename="+tablename+"&fieldname="+fieldname+"&searchspec="+searchspec+"&orderby="+orderby+"&optype=onlyone",
		beforeSend:function(){
			wlog('#b',this.data);
		},
		success:function(strReturn){
			retval = strReturn;
			$('#'+inputname).val(strReturn);
		},
		complete:function(){},
		error:function(){}
	});
}
function showAlert(pstr) {
//	alert(oMessage[pstr]);
//	alert(getMSG(pstr));
//	alert(goMsg[pstr]);
}
function showPicture(pstr) {
	var pHTML = "<img src='./img/none.jpg' border=1 width=150px height=180px>";
	if( pstr != "" )	pHTML = "<img src='./img/"+pstr+"' border=1 width=150px height=180px>";
	$("#divSaint").innerHTML = pHTML;
	$('#txtPicture').val(pstr);
}

function showSpinner()
{
	$('#dvLoading').fadeIn();
	$('body').css('cursor','none');
	
	setTimeout('hideSpinner()',10000);
}
function tracelog(str){
	if( dbg )	wlog(str);
}
function validateEmail(pstr) {
	if( pstr == '' )	return false;
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var retval = reg.test(pstr);
	wlog('validateEmail ['+retval+']');
	return retval;
}
//$(window).resize(function()
function win_resize()
{ 
//	$('#c_header').width($(window).width()-20);
//	$('#c_menu').height($(window).height()-$('#c_header').height()-20)
//	$('#c_body').width($(window).width()-40-$('#c_menu').width())
//					.height($('#c_menu').height());
	
}

function getTime4HM() {
	var now = new Date();
	return now.getHours()+':'+now.getMinutes();
}

(function($) {
    $.fn.doubleTap = function(doubleTapCallback) {
        return this.each(function(){
			var elm = this;
			var lastTap = 0;
			$(elm).bind('vmousedown', function (e) {
                               var now = (new Date()).valueOf();
				var diff = (now - lastTap);
                               lastTap = now ;
                               if (diff < 250) {
		                    if($.isFunction( doubleTapCallback ))
		                    {
		                       doubleTapCallback.call(elm);
		                    }
                               }      
			});
        });
   }
})(jQuery);

$(".doubleTap").doubleTap(function(){
	// 'this' is the element that was double tap
});

//Buileting API 
function writeNewPost() {
	var fTarget = '#divPost';
	
	$(fTarget).resetForm();
	
	$('#divPost input[name=created]').val('');
	$('#divPost :input').removeAttr('readonly');
	$('#btnDelete,#btnUpdate').hide();
	$('#divView,#divReply,#divReplyList').hide();
	$('#divUpdate,#btnSave').show();
	$('#divUpload,#btnRemoveFile').show().trigger('show');
	$('#divPost').show().trigger('open');
	$('#div input[name=write_date]').val(get4YMMDD(gdToday));
	scrollTo(140);
	return false;
}

function cancelPost() {
	if( !confirm('ìž‘ì„±ì�„ ì·¨ì†Œí•˜ì‹œê² ìŠµë‹ˆê¹Œ?')) return false;

	var created = $('#divPost input[name=created]').val();
	$('#divPost').resetForm();
	$('#divPost').hide().trigger('close');
	$('#btnSave,#btnCancel').hide();
}

function savePost() {
	var fname = '#divPost';
	
	if( $.trim($(fname+' input[name=title]').val()) == '' ) {
		alert('ì œëª©ì�„ ìž…ë ¥í•˜ì‹­ì‹œì˜¤.');
		$(fname+' input[name=title]').focus();
		return false;
	}
	if( $.trim($(fname+' textarea[name=content]').val()) == '' )  {
		alert('ë‚´ìš©ì�„ ìž…ë ¥í•˜ì‹­ì‹œì˜¤.');
		$(fname+' textarea[name=content]').focus();
		return false;
	}
	if( $.trim($(fname+' input[name=write_date]').val()) == '' ) {
		alert('[ìž‘ì„±ì�¼ìž�]ë¥¼ ë°˜ë“œì‹œ ìž…ë ¥í•˜ì‹­ì‹œì˜¤.');
		return false;
	}
	var newid = getNewYMDHIS();
	if( $(fname+' input[name=created]').val() == '' )	{
		$(fname+' input[name=created],'+fname+' input[name=updated]').val(newid);
	} else	$(fname+' input[name=updated]').val(newid);
	if( $(fname+' input[name=recommend]').val() == '' )	$(fname+' input[name=recommend]').val(0);
	if( $(fname+' input[name=viewcount]').val() == '' )	$(fname+' input[name=viewcount]').val(0);
	
	renameFile(fname);
	saveRecord('divPost','','afterSave()');
}

function updatePost() {	
}
function filelog(pstr){
	$.get('dbwork.php',{optype:'filelog', log:pstr});
}

String.prototype.escapeHTML=function(){
	return this.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function YMDHIS(pstr){
	return pstr.substr(0,4)+'-'+pstr.substr(4,2)+'-'+pstr.substr(6,2)+' '+pstr.substr(8,2)+':'+pstr.substr(10,2)+':'+pstr.substr(12,2);
}