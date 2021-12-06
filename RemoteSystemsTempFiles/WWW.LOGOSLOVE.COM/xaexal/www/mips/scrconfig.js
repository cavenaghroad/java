/*
$(document).keypress(function(event){
	alert(event.which+','+event.ctrlKey+','+event.shiftKey+','+event.altKey);
	event.preventDefault();
	if( event.which == 19 && event.ctrlKey && event.shiftKey && !event.altKey )	$('#btnSave').click(); 
});
 */

$(document).ready(function(){
	init_screen();
	win_resize();
	setLog(-1);
//	var oGrid = new fGrid('scrconfig');
//	objArr.push(oGrid);
//	oGrid.initTable();
//	oGrid.load2table();
	var newPage = new fPage(gPHPSELF);
});
