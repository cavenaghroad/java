var ob_member='ob_member';
var ob_party='obj_party';
var ob_menu='ob_menu';
var ob_order='ob_order';
var ob_book='ob_book';
var ob_delivery="ob_delivery";
var ob_takeout='ob_takeout';
var ob_inhouse='ob_inhouse';

var RAINSONG='#97ab9b';

var winWidth=1280;
//var gLayout=null;
var gInhouse,gDelivery,gTakeout,gBook;
var gTopHeight=70;

var cEMPTY='#a4cff0';
var cWORKING='#ff6140';
var cPAID='#3D7199'
var cFAIL2PAY='#00FFE6';
//var cDONE='#00FFe6';
var cDONE='#cc1914';
var cCLICKED='cyan';
var cHOVER='#f9f4bc';

var gSoundPlay=false;
var dbOB;
var gDebug=false;
var tActive=0;
var last={inhouse:'',inhouse0:'',delivery:'',delivery0:'',takeout:'',takeout0:'',book:'',book0:''};
var timer_Expiry;
if(window.console)	gDebug=true;

//$.ajaxSetup({cache:false,async:false,datatype:'json',type:'post'});

$(document)
.on('click','#btnLogout',function(){
	$.post('_login.php',{
		optype:'logout'
	},function(json){
		if(json['success']!='0'){
			alert(json['message']); return false;
		}
		alert('로그아웃했습니다.');
		document.location='login.php';
	},'json');
})
;

function pertainSession(){
	$.post('work_read.php',{optype:'pertain'},function(txt){
		if(txt!='1') {
			wlog('logout');
			$('.btnLogout').click();
		}
	},'text');
}

//function wlog(pstr){
//	if(gDebug) console.log(pstr);
//}
function wlog(){
	if(gDebug){
		var pstr='';
		for(var i=0; i<arguments.length; i++){
			pstr+=arguments[i];
		}
		console.log(pstr);
	}
}
/*
 * Defined for PAYMENT
 */
var merchantID='logos0002m';
var merchantKEY='/Bfbqw3Vw+4VQ8EgI32ZLbvAK0MXHPjlufLa3VQ4NZiFLBdb7sVcRnoPs/zCTOPkwmWs2BHOxSKTvp/8MWPsUg==﻿';

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function validateEmail(pstr) {
	if( pstr == '' )	return false;
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var retval = reg.test(pstr);
	return retval;
}

function isSpecial(str){
	return !/[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/g.test(str);
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

function sortSelect(selectname){
	var options = $('#'+selectname+' option');
	var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
	arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
	options.each(function(i, o) {
	  o.value = arr[i].v;
	  $(o).text(arr[i].t);
	});	
}

function newedit(){
	document.location='/house/new.php';
}
function non_member(){
	alert('가입한 사용자만 사용할 수 있습니다.');
	var counter=0;
	var blink=function(){
		if(++counter>10) return false;
		
		$('.btnLogin').animate({
	        opacity: '0'
	    }, function(){
	        $(this).animate({
	            opacity: '1'
	        }, blink);
	    });
	}
	blink();
}

var mode;
var alertseq = 0;
function seqAlert()
{
	var pstr = alertseq++;
	pstr = '{'+pstr+'} ';
	for( var i in arguments )	pstr += ' '+arguments[i];
	
	alert(pstr);
}

function money_format(x){
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

//var ggParty;

function showByMinute(pstr){
//	wlog('pstr ['+pstr+']');
	return pstr.substr(0,4)+'-'+pstr.substr(4,2)+'-'+pstr.substr(6,2)+' '+pstr.substr(8,2)+':'+pstr.substr(10,2);
}

function showHM(pstr){
	return pstr.substr(8,2)+':'+pstr.substr(10,2);
}

if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function(elt /*, from*/){
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0) ? Math.ceil(from) : Math.floor(from);
    if (from < 0)  from += len;

    for (; from < len; from++){
      if (from in this && this[from] === elt) return from;
    }
    return -1;
  }
}

function _phone(pstr){
	if(pstr.substr(0,3)=='010'){
		return pstr.substr(0,3)+'-'+pstr.substr(3,4)+'-'+pstr.substr(7,4);
	} else if(pstr.substr(0,2)=='02'){
		return pstr.substr(0,2)+'-'+pstr.substr(2);
	} else return pstr;
}

function checkExpiry(){
	$.post("work_read.php",{'optype':'check-expiry'},function(txt){
		if(txt=='-1'){
			$('#service-expiry').show();
			blink();
			clearInterval(timer_Expiry);
		}
	},'text');
}

function resetlog(){
	$.post("work_read.php",{
		optype:'resetlog'
	},function(json){		
	},'json');
}
var x=1;var y;

function blink()
{
	var col;
 if(x%2) 
 {
  col = "rgb(255,0,0)";
 }else{
  col = "rgb(255,255,255)";
 }

 $('#service-expiry').css('color',col);
 x++;
 if(x>2){x=1};setTimeout("blink()",500);
}

function isEmpty(pstr){
	if(pstr=='' || pstr==null) return true;
	return false;
}
