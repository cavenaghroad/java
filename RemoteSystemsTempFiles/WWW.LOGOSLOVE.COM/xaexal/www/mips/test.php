<html>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<head>
	<meta charset='utf-8'>
	<title>AJAX text</title>
</head>
<body>
<table id=tbl border=1 style='border-collapse:collapse;width:500px;'>
<caption>박재형</caption>
</table>
<br><br>
이름:<input type=text id=txtname><br>
행:<input type=text id=row><br>
열:<input type=text id=col><br><br>
<button id=btncall>서버호출</button>
</body>
</html>
<script language='javascript'>
$(document)
.ready(function(){
	$.post('checkdb.php',{},function(txt){
			alert(txt);
	},'text');
})
.on('click','#btncall',function(){
	$.get('test3.php',
		{name:$('#txtname').val(),row:$('#row').val(),
		col:$('#col').val()},
		function(jsn){
			console.log(jsn);	// name,row,col
			var name=jsn['name'];
			var row=jsn['row'];
			var col=jsn['col'];
			var pstr='';
			for(i=0;i<parseInt(row);i++){
				pstr+="<tr>";
				for(n=0;n<parseInt(col);n++){
					pstr+="<td>"+name+"</td>";
				}
				pstr+="</tr>";
			}
			$('#tbl').append(pstr);
		},'json'
	);
});
</script>