<?
require_once 'header.php';

if(!isset($_SESSION['party'])) {
?>
<script language='javascript'>
document.location='login.php';
</script>
<?
	exit;
}
require_once 'include.php';
require_once 'chgpasscode.php';

 ?> 
<script language='javascript'>
var admin_level=<?=$_SESSION['admin_level'] ?>
</script>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr >
	<td width="35%" valign=top>
		<a href='admin.php' style='cursor:pointer;text-decoration:none;'>주문관리<font size=1>로 가기</font></a>
	</td>
	<td width="30%" align="center" valign=top>
		<b><?=$_SESSION['partyname'] ?></b>
	</td>
	<td width="35%" align="right" valign=top>
		<table border=0>
		<tr height=30px>
		<td><a onclick='javascript:chgpasscode();' style='cursor:pointer;'><?=$_SESSION['name']?></a></td>
		<td>
<!-- 			<input type=password class=passcode <? if(isset($_SESSION['party'])) echo "style='display:none'"; ?>>&nbsp; -->
			<button id=btnLogout <? if(!isset($_SESSION['party'])) echo "style='display:none'";?>>로그아웃 </button>&nbsp;
			<button onclick='javascript:showAbout()'>About...</button>
		</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td colspan=3>
		<div class='dvAdmin' style='background-color:#d5d2c3;'>
			<ul>
				<li><a href=#setting>환경설정</a></li>
				<li><a href=#manager >매장담당자 관리</a></li>
				<li><a href=#menuconfig>메뉴관리</a></li>
				<li><a href=#statistics>통계보기</a></li>
		<!-- 		<li><a href=#chgpassword>비밀번호 변경</a></li> -->
			</ul>
<?
include_once 'statistics.php';
include_once 'setting.php';
include_once 'menuconfig.php';
include_once 'manager.php';
?>
		</div>
	</td>
</tr>
</table>
</body>
</html>
<script language='javascript'>
$(document)
.tooltip()
.ready(function(){
	pertainSession();
	$('.dvAdmin,.dvSales').tabs({
		heightStyle:'fill'
	});
	var party;
	if(gParty!='') {
		party=gParty;
	} else {
		party=getCookie('party');
	}
	if(party=='') return false;

	$("#selparty option").filter(function() {
	    return $(this).text() == party; 
	}).prop('selected', true);
	var pos2=$('.dvAdmin').position();
	console.log($(window).height()+'/'+$(window).width()+'/'+pos2.top);
	var startop=$(window).height()-Math.floor(pos2.top*1.9);
	$('.dvAdmin>div').css('height',Math.floor(startop)+'px');
	startop*=.9;
	$('.dvSales>div').css('height',Math.floor(startop)+'px');	
	if(admin_level!=0) {
		$('input[type=text],textarea').attr('diabled','disabled');
		$('input[type=button]').attr('disabled','disabled');
	}
	timer_Expiry=setInterval(function(){document.location='admin.php';},24*60*1000);
	resize();
	return false;
})
;

function resize(){
	var pos2=$('.dvAdmin').position();
	var startop=$(window).height()-Math.floor(pos2.top*3);
	var tall=startop-10;
	$('#setting,#menuconfig,#manager,#statistics').css('height',tall+'px');
	$('#dvMenu,#dvSales').css('height',tall+'px');
// 	$('#dvDelivery0,#dvTakeout0,#dvBook0').css('height',tall+'px');
	$('.dvAdmin>div').css('height',startop+'px');	
}

$(window).resize(function(){
	resize();
});
</script>