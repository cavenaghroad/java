<?
require_once 'include.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>xaexal</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
<link type="text/css" href="/jsfile/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.structure.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.theme.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/fullcalendar-1.4.10/fullcalendar.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/uploadify3.2.1/uploadify.css" rel="stylesheet" /> 
<link type='text/css' href="/ob/base.css" rel=stylesheet />
<link type='text/css' href="/jsfile/timepicker.css" rel=stylesheet />
</head>
<script type="text/javascript" src="/jsfile/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/jsfile/jquery-ui.min.js"></script>
<script type='text/javascript' src='/jsfile/jquery.layout.js'></script>
<script type="text/javascript" src="/jsfile/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="/jsfile/fullcalendar-1.4.10/fullcalendar.js"></script>
<script type="text/javascript" src="/jsfile/uploadify3.2.1/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="/jsfile/timepicker.js"></script>
<script type="text/javascript" src="/jsfile/jquery.redirect.js"></script>
<script type="text/javascript" src="/ob/include.js"></script>
<script language='javascript'>
</script>
<body>
<?
// table.php?party=오비맥주 등촌점&table=주방3&passcode=xxxxx
if(!isset($_GET['party']) || !isset($_GET['table']) || !isset($_GET['passcode'])){
    $mysqli->close();
?>
<script language='javascript'>
alert('Not enough parameters');
</script>
<?
    exit;
}
$sql="select rowid party from {$ob_party} where party='{$_GET['party']}'";
$rs=$mysqli->query($sql);
wlog($sql." [".$rs->num_rows."]",__LINE__,__FUNCTION__,__FILE__);
if($rs===false || $rs->num_rows<1) {
    $mysqli->close();
?>
<script language='javascript'>
alert('[<?=$_GET['party']?>]를(을) 찾을 수 없습니다.');
</script>
<?
}
$row=$rs->fetch_assoc();
$party=$row['party'];
$sql="select order_id from {$ob_master} where party='{$party}' and tableno='{$_GET['table']}' and status<>'paid'";
wlog($sql,__LINE__,__FUNCTION__,__FILE__);
$rs=$mysqli->query($sql);
if($rs===false) {
    echo "Failed to execute SQL";
    $mysqli->close();
}
if($rs->num_rows<1) {
    $orderID="";
} else {
    $row=$rs->fetch_assoc();
    $orderID=$row['order_id'];
}
?>
<style>
#tblOrdered {
    border-collapse: collapse;
}
#tblOrdered td {
    border: 1px solid #ddd;
}
</style>
<input type=hidden id=order_id value='<?=$orderID ?>'>
<input type=hidden id=party value='<?=$party ?>'>
<input type=hidden id=table value='<?=$_GET['table'] ?>'>
<table width=100%>
<tr>
	<td valign=top>
<?
$sql="select * from ob_menu where party='{$party}' order by type,name";
wlog($sql,__LINE__,_FUNCTION__,__FILE__);
$rs=$mysqli->query($sql);
if($rs===false) echo "Failed to execute SQL.";
if($rs->num_rows<1) echo "No menu found";
else {
    echo "<div id=dvMenu style='overflow:auto;'><ul>";
    $_type=""; $_header=""; $_body=""; $n=0;
    while($row=$rs->fetch_assoc()){
        if($row['type']!=$_type){
            if($_type!=""){
                $_body.="</table></div>";
            }
            $_type=$row['type'];
            $_header.= "<li><a href='#type{$n}'>{$_type}</a></li>";
            $_body.="<div id='type{$n}'><table>";
            $n++;
        }
        if($row['menu_image']=="") $row['menu_image']="imgnotfound.jpeg";
        $_body.="<tr><td valign=middle style='overflow:hidden'><img src='./menu/{$row['menu_image']}' style='width:150px;height:90px'></td><td valign=middle>".
                    "<button id='mnu{$row['rowid']}' class='ui-button ui-widget ui-corner-all' style='width:300px;'>{$row['name']} [{$row['price']}천원]</button></td></tr>";
    }
    $_header.="</ul>"; $_body.="</table></div>";
    echo $_header.$_body."</div>";
}
?>
	</td>
	<td valign=top width=50% height=100%>
		<div id=dvOrder style='position:relative;width:100%;height:100%;'>
			<ul>
				<li id=liSelected><a href='#dvSelected'>선택한 메뉴</a></li>
				<li id=liOrdered><a href='#dvOrdered'>주문한 메뉴</a></li>				
			</ul>
			<div id=dvSelected style='overflow:auto;'>
				<table width=100% ><tr><td align=right><button id=submitOrder style='display:none;'>주문보내기</button></td></tr></table>
				<table id=tblSelected width=100% style='border:1px solid red'>
				</table>
			</div>
			<div id=dvOrdered style='overflow:auto;'>
				<table width=100%><tr><td>총액:&nbsp;<input type=text style='width:100px;text-align:right;' id=txtTotal></input>&nbsp;천원</td></tr></table>
				<table id=tblOrdered style='border:1px solid gray;'>
				</table>
			</div>
		</div>
	</td>
</tr>
</table>
<script language='javascript'>
var selectedTab='';
$(document)
.ready(function(e,u){
	$('#dvMenu,#dvOrder').height($(window).height()-20);
	$('#dvMenu,#dvOrder').tabs({
		heightStyle:'fill',
		beforeActivate:function(e,u){
			switch(u.newTab[0].id){
			case 'liSelected':
				selectedTab='liSelected';
				break;
			case 'liOrdered':
				selectedTab='liOrdered';
				$('#tblOrdered').empty();
				$.post('_table.php',{
					optype:'getOrder',order_id:$('#order_id').val()
				},function(json){
					if(json['message']!='') {
						alert(json['message']); return false;
					}
					$.each(json['order'],function(ndx,val){
						$('#tblOrdered').append('<tr><td>['+val['qty']+']</td><td> '+val['name']+'</td><td align=center>'+(val['status']!='done'?'<button id=btnOrderCancel>주문취소</button>':'처리됨')+'</td></tr>');
					});
// 					$('#tblOrdered').html(json['result']);
					$('#txtTotal').val(json['total']);
				},'json');
				break;
			}
		}
	});
	$('.widget button').button();
})
.on('click','#removeOrder',function(e,u){
	$('#tblSelected tr').remove();	
})
.on('click',':button[id^=mnu]',function(e,u){
	var pstr=$(this).parent().parent().find('td:nth(1)').text();
	pstr=pstr.split('[');
	pstr=$.trim(pstr[0]);
	var n=1;
	$('#tblSelected tr').each(function(){
		token=$(this).find('td:first').text();
		if(token==pstr){
			n=parseInt($(this).find('td:nth(1)').text());
			$(this).find('td:nth(1)').text(++n);
			return false;
		}
	});
	if(n<2) {
		$('#tblSelected').append('<tr><td>'+pstr+'</td><td align=center>'+n+'</td><td align=center><button id=btnMinus>-</button></td></tr>');
	}
	if($('#tblSelected tr').length>0) $('#submitOrder').show();
	else $('#submitOrder').hide();

	$('#dvOrder').tabs('enable',0);
	$('#dvOrder').tabs('refresh');
})
.on('click','#btnMinus',function(e,u){
	var parentTR=$(this).parent().parent();
	n=parseInt(parentTR.find('td:nth(1)').text());
	if(n>1){
		parentTR.find('td:nth(1)').text(--n);
	} else {
		parentTR.remove();
	} 
	wlog('tr length ['+$('#tblSelected').length+']');
	if($('#tblSelected tr').length>0) $('#submitOrder').show();
	else $('#submitOrder').hide();
})
.on('click','#submitOrder',function(e,u){
	var orderlist='';
	$('#tblSelected tr').each(function(){
		var qty=$(this).find('td:nth(1)').text();
		var menu=$(this).find('td:first').text();
		for(var n=0; n<qty; n++){
			if(orderlist!="") orderlist+='^';
			orderlist+=menu;
		}
	});
	wlog('orderlist ['+orderlist+']');
	$.post('_table.php',{
		optype:'orderfromtablet',order_id:$('#order_id').val(),party:$('#party').val(),table:$('#table').val(),order:orderlist
	},function(json){
		if(json['message']!='') {
			alert(json['message']);
			return false;
		}
		$('#order_id').val(json['order_id']);
		alert('주문전달됐습니다.');
		$('#tblSelected tr').each(function(){
			$(this).remove();
		});
		$('#dvOrder').tabs({active:1});
	},'json');
})
.on('click','#btnOrderCancel',function(e,u){
	var trline=$(this).parent().parent();
	var menu=trline.find('td:first').text();
	wlog('menu ['+menu+']');
	$.post('_table.php',{
		optype:'RemoveOrder',menu:menu,party:$('#party').val(),order_id:$('#order_id').val()
	},function(json){
		if(json['message']!='') {
			alert(json['message']); return false;
		}
		trline.remove();
	},'json');
})
;
</script>
<?
require_once "footer.php";
?>