function doCopyRecord(oGrid) {
//	console.log(oGrid);
	var parentRow = $(oGrid['gridname']+' tr[bgcolor='+CLICKED_BGCOLOR+']').index();
	if( parentRow < 1 ) {
		alert('Please choose original record to be copied.');
		return;
	}
	var row = AddNewRow(oGrid);
//	wlog('row ['+row+']');
	if( row < 1 )return false;
	
	++parentRow;
//	wlog('parentRow ['+parentRow+']');
	var ndx = 0;
	$.each(oGrid['gridinfo'],function(index,value){
		ndx++;
		if( value['fname'] == 'rowid' )	return true;
		var pstr = $(oGrid['gridname']+' tr:eq('+parentRow+') td:eq('+ndx+')').text();
//		wlog('ndx ['+ndx+'] pstr ['+pstr+']');
		$(oGrid['gridname']+' tr:eq('+row+') td:eq('+ndx+')').text(pstr);
	});
	updateRow(oGrid,row,-1,'blur');
//	move2NextCol(oGrid,row,0);
} 

function bbsWidget(_itemcode,_x,_y,_width,_height,_title,_rownum,_belongto) {
	var pstr='';
	$.ajax({
		data:'optype=bbswidget&_itemcode='+_itemcode+'&_rownum='+_rownum, datatype:'text',async:true,
		beforeSend: function(){
			wlog('optype=bbswidget'+_itemcode+'&_rownum='+_rownum);
		},
		success: function(retval) {
			pstr = '<table border=0 cellpadding=0 cellspacing=0 bgcolor=#dedede><tr><td>'+
				'<table border=0 cellspacing=1 cellpadding=0 id='+_itemcode+' name='+_itemcode+'>';
//			var strTxt = new String(txt);
			retval = retval.replace(/<tr/g,'<tr bgcolor=white');
//			wlog(retval);
			pstr += retval;
			pstr += '</table></td></tr></table>';
			_arBBS.push(_itemcode);
		},
		complete: function(e) {
			pstr = '<div id=div'+_itemcode+' name=div'+_itemcode+' valign=top'+
				' style="border-style:dotted;border-width:1px;border-color:#98bf21;width:'+_width+'px;height:'+_height+'px;position:absolute;overflow:hidden">'+
				'<table border=0 cellpadding=0 cellspacing=0 width='+_width+'px height='+_height+'px valign=top>'+
				'<tr height=24px><td valign=top>&nbsp;<a href="../church/blltn.php?_t='+_itemcode+'">'+_title+'</a></td></tr>'+
				'<tr><td valign=top>'+pstr+'</td></tr></table></div>';
			if( _belongto.substr(0,1) == '.' ) _belongto = $(_belongto);
			else	_belongto = $('#'+_belongto);
			_belongto.append(pstr);
			$('#div'+_itemcode).css({top:_y+'px',left:_x+'px',width:_width+'px',height:_height+'px'});
			$('#div'+_itemcode+' td').addClass('vanilla');
		}
	});
}

function noticeWidget(_itemcode,_x,_y,_width,_height,_title,_rownum,_belongto) {
	var pstr='';
	$.ajax({
		data:'optype=noticewidget&_itemcode='+_itemcode+'&_rownum='+_rownum, datatype:'text',async:true,
		beforeSend: function(){
			console.log('optype=noticewidget&_itemcode='+_itemcode+'&_rownum='+_rownum);
		},
		success: function(retval) {
			pstr = '<table border=0 cellpadding=0 cellspacing=0 bgcolor=#dedede><tr><td>'+
				'<table border=0 cellspacing=1 cellpadding=0 id='+_itemcode+' name='+_itemcode+'>';
//			var strTxt = new String(txt);
			retval = retval.replace(/<tr/g,'<tr bgcolor=white');
//			console.log('retval ['+retval+']');
			pstr += retval;
			pstr += '</table></td></tr></table>';
			_arBBS.push(_itemcode);
		},
		complete: function(e) {
			pstr = '<div id=div'+_itemcode+' name=div'+_itemcode+' valign=top'+
				' style="border-style:dotted;border-width:1px;border-color:#98bf21;width:'+_width+'px;height:'+_height+'px;position:absolute;overflow:hidden">'+
				'<table border=0 cellpadding=0 cellspacing=0 width='+_width+'px height='+_height+'px valign=top>'+
				'<tr height=24px><td valign=top>&nbsp;<a href="../church/blltn.php?_t='+_itemcode+'">'+_title+'</a></td></tr>'+
				'<tr><td valign=top>'+pstr+'</td></tr></table></div>';
			if( _belongto.substr(0,1) == '.' ) _belongto = $(_belongto);
			else	_belongto = $('#'+_belongto);
			_belongto.append(pstr);
			$('#div'+_itemcode).css({top:_y+'px',left:_x+'px',width:_width+'px',height:_height+'px'});
			$('#div'+_itemcode+' td').addClass('vanilla');
		}
	});
}

function imgWidget(_itemcode,_x,_y,_width,_height,_title,_rownum,_belongto,_interval){
	var pstr='';
	$.ajax({
		data:'optype=imgwidget&_itemcode='+_itemcode+'&_count='+_rownum, dataType:'text', async:false, cashe: false,
		beforeSend:function(){
			wlog('optype=imgwidget&_itemcode='+_itemcode+'&_count='+_rownum);
		},
		success:function(retval){
			if( retval == '' )	{
				retval = '<tr><td>&nbsp;</td></tr>';
			}
			pstr = '<table border=0 cellpadding=0 cellspacing=0 id="'+_itemcode+'" name="'+_itemcode+'">';

			pstr += retval;
			pstr += '</table>';
//			wlog(pstr);
		},
		complete:function(e){
			pstr = '<div id=div'+_itemcode+' name=div'+_itemcode+' valign=top'+
				' style="width:'+_width+'px;height:'+_height+'px;border-style:dotted;border-color:#dedede;border-width:1px;position:absolute;overflow:hidden;">'+
				_title+'<br>'+pstr+'</div>';

			if( _belongto.substr(0,1) == '.' ) _belongto = $(_belongto);
			else	_belongto = $('#'+_belongto);
			_belongto.append(pstr);
//			console.log(pstr);
//			var newpos = new Object();
//			newpos.top = _y; newpos.left = _x;
//			$('#'+_itemcode).offset(newpos);
			$('#div'+_itemcode+' td').addClass('vanilla');
			$('#div'+_itemcode).css({top:_y+'px',left:_x+'px',width:_width+'px',height:_height+'px'});

			setTimeout(function(){DeferStart(_itemcode);},4000);
		}
	});
}

function DeferStart(_itemcode){
	bob = setInterval(function(){imgMove(_itemcode);},50);
	
}
/*
 * 1. Remove the first picture 
 * 2. Append the first picture after last picture
 * 3. Repeat 1&2 every given time interval 
 */
var _distance = 0, bob;
var _step = '+=50';
function imgMove(_itemcode){

//	wlog('['+_distance+'] ['+$('#div'+_itemcode).scrollLeft()+'] ['+_step+']');
	if( $('#div'+_itemcode).scrollLeft() == _distance ) {
		if( _distance == 0 )	_step = '+=5';
		else _step = '-=5';
	} else {
		_distance= $('#div'+_itemcode).scrollLeft();
	}
	$('#div'+_itemcode).animate({scrollLeft:_step},100);
}

function continueSlide(_itemcode){
//	wlog('ending');
	bob = setInterval(function(){imgMove(_itemcode);},1000);
}
var _arBBS = [];

$(document)
.on('click','td',function(e){
	_table = $(this).closest('table').prop('id');
//	wlog('_table ['+_table+']');
	var i;
	for( i=0; i<_arBBS.length; i++ ){
		if( _arBBS[i] == _table ) break;
	}
	if( i > _arBBS.length-1 ) return true;

	var _row = $(this).parent().parent().children().index($(this).parent());
//	console.log('../blltn.php?_t='+_table+'&_created='+$('#'+_table+' tr:eq('+_row+') td:eq(0)').text());
	document.location = '../church/blltn.php?_t='+_table+'&_created='+$('#'+_table+' tr:eq('+_row+') td:eq(0)').text();
});