<?
define('__ZBXE__', true);
define('__XE__', true);
?>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<table align=center>
<tr>
  <td align=center>
  	<h1>Human Relationship Management</h1>
  </td>
</tr>
<tr>
  <td>
   <p>개인/단체에서 구성원 혹은 회원을 관리하기 위한, CRM(Customer Relationshp Management) 소프트웨어의 일종입니다.</p>
   <p>member의 개인정보가 아닌, 활동(Activity)에 대한 데이타를 만들고 유지/관리하여, 보다 효과적인 member관리를 위하여 사용됩니다. 
   <p>다음의 단체/업체에서 이 HRM을 활용할 수 있습니다.</p>
   <p> HRM바로가기</p>
  </td>
</tr>
<tr>
	<td>
		<div id='accordian'>
		<h3>요식업(레스토랑/식당/푸드코트)</h3>
		<div style="background-color: #47c6ee">
		<table width=100% height=100%><tr><td style="background-color: #47c6ee">
		- 고객관리<br><br>
		&nbsp;&nbsp;- 신규고객 관리(가입현황통계)<br><br>
		&nbsp;&nbsp;- 기존고객의 식당이용통계분석<br><br>
		- 예약관리<br><br>
		- 매출관리<br><br>
		- 원재료 재고관리<br><br>
		&nbsp;&nbsp;- 현재 재고상태 확인<br><br>
		&nbsp;&nbsp;- 원재료 주문/입출고 관리<br><br>
		- Promotion관리<br><br>
		&nbsp;&nbsp;- 대상고객 선정<br><br>
		&nbsp;&nbsp;- 대상고객에게 안내메일 발송<br><br>
		&nbsp;&nbsp;- 행삭결과 반영 및 분석<br><br>
		</td></tr></table>
		</div>
		<h3>학원(보습학원/예체능학원)</h3>
		<div style="background-color: #47c6ee">
		- 수강생 일반정보관리(연락처)<br><br>
		- 수강과목 관리<br><br>
		&nbsp;&nbsp;- 과목별 강사/일정관리<br><br>
		&nbsp;&nbsp;- 수강신청 현황관리<br><br>
		&nbsp;&nbsp;- 수강생 현황관리<br><br>
		- 수강생 관리<br><br>
		&nbsp;&nbsp;- 출결상황관리<br><br>
		&nbsp;&nbsp;- 회바납부현황 및 차기납부일 관리 및 알리미기능<br><br>
		- 정기적으로 수강생 증감통계분석<br><br>
		</div>
		<h3>종교단체(교회/불교)</h3>
		<div style="background-color: #47c6ee">
		- 구성원 일반정보관리(연락처/직분)<br><br>
		- 가족관계관리 <br><br>
		- 정기적으로 신입구성원의 증감통계분석<br><br>
		- 모임일정관리<br><br>
		&nbsp;&nbsp;- 참석자 출결관리<br><br>
		- 헌금관리<br><br>
		- 지출관리<br><br>
		</div>
		<h3>커뮤니티(동호회)</h3>
		<div style="background-color: #47c6ee">
		- 회원관리(연락처,가입일자)<br><br>
		- 모임출결관리<br><br>
		- 공지사항 이메일 발송관리<br><br>
		&nbsp;&nbsp;- 등급별 발송대상 선정 기능<br><br>
		</div>
		</div>
	</td>
</tr>
<script>
$('document').ready(function() {
	var icons = {
      header: "ui-icon-circle-arrow-e",
      activeHeader: "ui-icon-circle-arrow-s"
    };
	$('#accordian').accordion({
		heightStyle: 'content',
		icons: icons
	});    	
});
</script>
<tr>
  <td align=center>
<?
require_once('../xe/config/config.inc.php');
require_once('../enterprise/comfunc.php');

$oContext = &Context::getInstance();
$oContext->init();

$logged_info = Context::get('logged_info'); 
$id = $logged_info->email_address;
if(!$logged_info)
{
	echo("로그인 해주셔야죠.^^ ");
} else {
	$pstr = "";
	$sql = "select name_kor,enterprise from a_enterprise order by name_kor";
	
	$result0 = mysql_query($sql,$link);
// 	@$nCount = mysql_num_rows($result0);
	while($row0=mysql_fetch_array($result0)) {
		if( $pstr == "" ) $pstr = "<table border=1>";
		$enterprise = $row0['enterprise'];
		$pstr .= "<tr><td><a href='./index.php?enterprise=".$enterprise."&userid=".$id."'>".$row0['name_kor']."</a></td></tr>";
		
// 		$sql = "select admin_flag from a_mem_ent a,a_member b where a.enterprise='".$enterprise."' and b.userid='".$id."' and a.private_id=b.private_id";
// 		$result1 = mysql_query($sql,$link);
// 		@$nCount = mysql_num_rows($result1);
// 		if( $nCount > 0 ) {
// 			$row1=mysql_fetch_array($result1);
// 			if( $row1['admin_flag'] == "0" ) {
// 				$pstr .= "<tr><td><a href='./index.php?userid=".$id."'>".$row0['name_kor']."</a></td><tr>";	
// 			} else {
// 				$pstr .= "<td>회원</td>";
// 			}
// 		} else {
// 			$pstr .= "<td>가입하기</td>";
// 		}
// 		$pstr .= "</tr>";
	}
	$pstr .= "</table>";
	echo $pstr;
}
$oContext->close();
?>
	</td>
</tr>
</table>