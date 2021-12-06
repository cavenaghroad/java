<?
include_once("header.php");

// resetLog(__LINE__,__FUNCTION__,__FILE__,true);

unset($_SESSION['party']);
$log="";
foreach($_SESSION as $key=>$value)	{
    $log.="{$key} [{$value}] ";
}
echo "<font color=magenta>{$log}</font><br>";

$sql = "select b.party,b.name_kor from a_mem_par a,a_party b where a.member_id='{$_SESSION['member_id']}' and a.party=b.party order by a.level";
// echo $sql;
$rs=$mysqli->query($sql);
wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
if($rs===false || $rs->num_rows<0) {
	die('<br><br><br>You should apply to any of the registered company or register your own company.<br><a href=./index.php>Go to Home Page</a>');
} else {
?>
<table align=center border=1  style='border-collapse:collapse;border:1px solid yellow'>
<tr style='color:white;background-color:black;'><th>가입 단체</th><th>등록인원수</th><th>&nbsp;</th></tr>
<?
	while($row=$rs->fetch_assoc()){
	    $cnt=showSummary($row['party']);
	    if($cnt==0) {
	        $pstr="처리중";
	    } else {
	        $pstr="{$cnt} 명";
	    }
?>
<tr >
	<td><h1><?=$row['name_kor']?></h1></td>
	<td>&nbsp;<?=$pstr  ?></td>
	<td align=center>
<?
        if($cnt!=0){
?>
		<button style='cursor:pointer;' id='btnNav<?=$row['party'] ?>'>이동하기</button>
<?
        } else {
?>
		<button style='cursor:pointer;' id='btnStop<?=$row['party'] ?>'>신청취소</button>
<?
        }
?>
	</td>
</tr>
<?		
	}
}
?>
<tr>
<td colspan=2 align=center><button style='cursor:hand;' class=logout>로그아웃</button></td>
<td align=center><button style='cursor:hand;' id=btnAddParty>추가 등록</button></td>
</tr>
</table>
<?
function showSummary($party){
	global $mysqli;
	$pstr = 0;
	
	$sql="select accepted from a_mem_par where member_id='{$_SESSION['member_id']}' and party='{$party}'";
	$rs=$mysqli->query($sql);
	$row=$rs->fetch_assoc();
	if($row['accepted']=="1"){
    	// the total members.
    	$sql = "select count(*) from a_mem_par where accepted='1' and party='{$party}'";
    	$rs=$mysqli->query($sql);
    	wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
    	if($rs!==false && $rs->num_rows> 0 ){
    		$row = $rs->fetch_array();
    		$pstr =intval($row[0]);
    	}
	}
	return $pstr;
}
?>
<script language='javascript'>
$(document)
.ready(function(){
	$('#dvParty').dialog({
		autoOpen:false,
		resizeable:false,
		height:330,width:550,
		'open':function(e,ui){
			$.post('_xdbwork.php',{optype:'loadparty'},function(json){
				if(json['result']!='0'){
					alert(json['msg']); return false;
				}
				$('#selParty').empty().append(json['html']);
			},'json');
		},
		buttons:{
			'Select':function(){
				$(this).dialog('close');
				// find the detail of party with enteprise ID.
				$.post('_xdbwork.php',{optype:'add2party',_e:$('#selParty').val()},function(json){
					if(json['result']!='0') {
						alert(json['msg']); return false;
					}
					location.reload();
				},'json');
			},
			'Cancel':function(){
				$(this).dialog('close');
			}
		}
	});
	$('input[type=button]').button();	
})
.on('click','button[id^=btnNav]',function(){
	var ce=this.id;
	$.post('_xdbwork.php',{optype:'crmctl',_e:ce.substr(6)},function(json){
		if(json['result']!='0'){
			alert(json['msg']);
			return false;
		}
		document.location='crmctl.php?_p='+json['firstpage'];
	},'json');
	return false;
})
.on('click','#btnAddParty',function(){
	$('#dvParty').dialog('open');
})
.on('click','button[id^=btnStop]',function(){
	var ce=this.id;
	if(!confirm('정말로 등록을 취소하시겠습니까?')) return false;
	$.post('_xdbwork.php',{optype:'removeparty',_e:ce.substr(7)},function(json){
		if(json['result']!='0'){
			alert(json['msg']); return false;
		}
		location.reload();
	},'json');
	return false;
});
</script>
<div name=dvParty id=dvParty style='display:none'>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td align=center><?=_label("group",$lang)?></td>
</tr>
<tr>
<td align=center><select name=selParty id=selParty style='width:500px;' size=12>
<option value='na' selected><?=_label("na",$lang)?></option>
</select>
</td>
</tr>
<tr>
	<td><? echo $n; ?>&nbsp;Companies</td>
</tr>
</table>
</div>
<?
$mysqli->close();
?>