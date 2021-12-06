function doLogout(){
	if(!confirm('정말로 로그아웃하시겠습니까?')) return false;
	$.post('_admin.php',{optype:'logout'},function(answer){
		if(answer['result']=='0'){
			alert('logout');
			document.location='../index.php';
		}
	},'json');
}

function getToday(){
	let dt=new Date();
	var year = dt.getFullYear();              //yyyy
    var month = (1 + dt.getMonth());          //M
    month = month >= 10 ? month : '0' + month;  //month 두자리로 저장
    var day = dt.getDate();                   //d
    day = day >= 10 ? day : '0' + day;          //day 두자리로 저장
    return  year + '' + month + '' + day; 
}
function isDate(pstr){
	if(isNaN(pstr)) return false;
//	if(pstr.substr(0,1)!='2') return false;
	if(parseInt(pstr.substr(4,2))>12) return false;
	if(parseInt(pstr.substr(-2))>31) return false;
	return true;
}