<?
require_once 'header.php';

if(!isset($_SESSION['mobile']) || !isset($_SESSION['party'])){
?>
<script language='javascript'>
document.location='login.php';
</script>
<?
} else {
?>
<script language='javascript'>
	tActive='<?=$_SESSION['tab']?>';
</script>
<?
}
require_once 'include.php';
require_once 'chgpasscode.php';
?>
<script type='text/javascript' src='admin.js'></script>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr >
	<td width="35%" valign=top>
<?
if($_SESSION['admin_level']=="0"){
?>
		<a  href='config.php' style='cursor:pointer;text-decoration:none;'>매장관리<font size=1>로 가기</font></a>
<?
}
?>
	&nbsp;</td>
	<td width="30%" align="center" valign=top>
		<b><?=$_SESSION['partyname'] ?></b>
		<input type=hidden id=party value='<?=$_SESSION['party']?>'>
	</td>
	<td width="35%" align="right" valign=top>
		<table border=0>
		<tr height=30px>
		<td><a onclick='javascript:chgpasscode();' style='cursor:pointer;'><?=$_SESSION['name']?></a></td>
		<td>
			<button id=btnLogout <? if(!isset($_SESSION['party'])) echo "style='display:none'";?>>로그아웃 </button>&nbsp;
			<button onclick='javascript:showAbout()'>About...</button>
		</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
<td colspan=3>
<?
$sql="select * from {$ob_party} where rowid='{$_SESSION['party']}'";
wlog($sql,__LINE__,__FUNCTION__,__FILE__);
$rs=$mysqli->query($sql);
$order_inhouse="1"; $order_takeout="1"; $order_delivery="1"; $order_book="1";
if($rs!==false && $rs->num_rows>0){
	$row=$rs->fetch_array(MYSQLI_BOTH);
	$order_inhouse=$row['order_inhouse']; $order_takeout=$row['order_takeout']; $order_delivery=$row['order_delivery']; $order_book=$row['order_book'];
}
?>
<div class='dvOrder' style='background-color:#d5d2c3;border: 1px solid black'>
	<ul>
<?
if($order_inhouse=="1"){
?>
		<li><a href=#inhouse>매장주문<label class=lblinhouse></label></a></li>
<?
}
if($order_delivery=="1"){
?>		
		<li><a href=#delivery>배달주문<label class=lbldelivery></label></a></li>
<?
}
if($order_takeout=="1"){
?>		
		<li><a href=#takeout>테이크아웃주문<label class=lbltakeout></label> </a></li>
<?
}
if($order_book=="1"){
?>		
		<li><a href=#book>좌석예약<label class=lblbook></label></a></li>
<?
}
?>
		<li><a href=#sale>매출현황</a></li>
	</ul>
<?
if($order_inhouse=="1"){
?>		
	<div id=inhouse >
		<div id='dvMesa' style='top:0px;left:0px;' style='font-size:18px;border:1px solid black;' >
			<input type=checkbox class=chkPosition>좌석배치조정</input>
			<button class=reset-position style='display:none;' style='font-size:18px;'>초기화</button>
		</div>
		<div class=dvInhouse style='position:absolute;top:40px;left:801px;width:500px;' class=font18>		
			<table width=100%>
			<tr>
				<td>
					구분:&nbsp;<label id=cWorking>주문처리중</label>&nbsp;<label id=cDone>처리완료</label>&nbsp;<label id=cPaid>결제완료</label>
					&nbsp;<label id=cFail2Pay>결제실패</label>
				</td>
			</tr>
			<tr>
				<td>
					<div style='overflow:auto;width:400px;position:relative;' id=dvInhouse>
						<table id=tblinhouse0 border=1 style='cursor:pointer'>
						<thead><tr class="blackbar">
						<th class=font18><font color=white>테이블번호</font></th><th class=font18><font color=white>이름</font></th>
						<th class=font18><font color=white>주문시각</font></th></tr></thead><tbody></tbody>
						</table>
					</div>
				</td>
			</tr>
			</table>
		</div>
	</div>
<?
}
if($order_delivery=="1"){
?>	
	<div id=delivery>
		<table>
		<tr>
			<td valign=top>
				<table width=100%>
				<tr id='trBase'>
					<td style='font-size:18px;'><button id=add-delivery>주문추가</button></td>
					<td align=right>
						구분:&nbsp;<label id=cWorking>주문처리중</label>&nbsp;<label id=cDone>처리완료</label>&nbsp;<label id=cPaid>결제완료</label>
						&nbsp;<label id=cFail2Pay>결제실패</label>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td valign=top>
				<div style='overflow:auto;position:relative;' id=dvDelivery>
					<table id=tbldelivery  style='cursor:pointer;' border=1>
						<thead><tr class=blackbar><th class=font18><font color=white>주문시각</font></th>
						<th class=font18><font color=white>총액</font></th><th class=font18><font color=white>연락처</font></th>
						<th class=font18 style='display:none;'><font color=white>주소</font></th>
						</tr></thead><tbody></tbody>
					</table>
				</div>
			</td>
			<td width=10px>&nbsp;</td>
			<td valign=top>
				<div style='overflow:auto;position:relative;' id=dvDelivery0>		
					<table id=tbldelivery0 style='cursor:pointer;' border=1>
						<thead><tr class=blackbar><th class=font18><font color=white>모바일</font></th>
						<th class=font18><font color=white>메뉴</font></th><th class=font18><font color=white>주문시각</font></th></tr></thead><tbody></tbody>
					</table>
				</div>
			</td>
		</tr>
		</table>
	</div>
<?
}
if($order_takeout=="1"){
?>		
	<div id=takeout>
		<table border=0>
		<tr><td>
			<table width=100%>
			<tr>
				<td style='font-size:18px;'><button id=add-takeout>주문추가</button></td>
				<td align=right>
					구분:&nbsp;<label id=cWorking>주문처리중</label>&nbsp;<label id=cDone>처리완료</label>&nbsp;<label id=cPaid>결제완료</label>
					&nbsp;<label id=cFail2Pay>결제실패</label>
				</td>
			</tr>
			</table></td></tr>
		<tr><td valign=top>
			<div style='overflow:auto;position:relative;' id=dvTakeout>
				<table id=tbltakeout border=1 style='cursor:pointer'>
					<thead><tr class=blackbar><th class=font18><font color=white>주문시각</font></th><th class=font18><font color=white>총액</font></th>
					<th class=font18><font color=white>연락처</font></th>
					<th class=font18><font color=white>도착예정시각</font></th></tr></thead><tbody></tbody>
				</table>
			</div>
		</td>
		<td width=10px>&nbsp;</td>
		<td valign=top>
			<div style='overflow:auto;position:relative;' id=dvTakeout0>
				<table id=tbltakeout0 border=1 style='cursor:pointer'>
					<thead><tr class=blackbar><th class=font18><font color=white>모바일</font></th><th class=font18><font color=white>메뉴</font></th>
					<th class=font18><font color=white>주문시각</font></th></tr></thead><tbody></tbody>
				</table>
			</div>
		</td></tr>
		</table>
	</div>
<?
}
if($order_book=="1"){
?>		
	<div id=book>
		<table border=0>
		<tr><td>
			<table width=100%>
			<tr>
				<td style='font-size:18px;'><button id=add-book>주문추가</button></td>
				<td align=right>
					구분:&nbsp;<label id=cWorking>주문처리중</label>&nbsp;<label id=cDone>처리완료</label>&nbsp;<label id=cPaid>결제완료</label>
					&nbsp;<label id=cFail2Pay>결제실패</label>
				</td>
			</tr>
			</table></td></tr>
		<tr><td valign=top>
			<div style='overflow:auto;position:relative;' id=dvBook>
				<table id=tblbook border=1 style='cursor:pointer'>
					<thead><tr class=blackbar><th class=font18><font color=white>주문시각</font></th><th class=font18><font color=white>총액</font></th>
					<th class=font18><font color=white>연락처</font></th>
					<th class=font18><font color=white>도착예정시각</font></th>
					<th class=font18><font color=white>예약인원수</font></th></tr></thead><tbody></tbody>
				</table>
			</div>
		</td>
		<td width=10px>&nbsp;</td>
		<td valign=top>
			<div style='overflow:auto;position:relative;' id=dvBook0>
				<table id=tblbook0 border=1 style='cursor:pointer'>
					<thead><tr class=blackbar><th class=font18><font color=white>모바일</font></th><th class=font18><font color=white>메뉴</font></th>
					<th class=font18><font color=white>주문시각</font></th></tr></thead><tbody></tbody>
				</table>
			</div>
		</td></tr>
		</table>
	</div>
<? } ?>
	<div id=sale>
		<table border=0>
		<tr><td style='font-size:18px;' colspan=6><button id=btnRefreshSale>새로고침</button></td></tr>
		<tr><td valign=top>
			<div style='overflow:auto;position:relative;' id=dvSale>
			<table id=tblSale border=1 style='cursor:pointer;'></table>
			</div>
		</td>
		<td width=10px>&nbsp;</td>
		<td valign=top>
			<div style='overflow:auto;position:relative;' id=dvWorkiing>
			<table id=tblWorking border=1 style='cursor:pointer;'></table>
			</div>
		</td>
		<td width=10px>&nbsp;</td>
		<td valign=top>
			<div style='overflow:auto;position:relative;' id=dvUnpaid>
			<table id=tblUnpaid border=1 style='cursor:pointer;'></table>
			</div>
		</td></tr>
		</table>
	</div>
</div>
</td>
</tr></table>
<!-- 결제화면 -->
<div id=dlgPayment style='display:none;'>
<input type=hidden id=dlgpayment-order-id>
<input type=hidden id=dlgpayment-ordertype>
<table>
<tr>
	<td>번호&nbsp;<input type=text readonly id='cert_num' size=20 maxlength=20></td>
	<td rowspan=2>
		<table border=1 width=100% height=100%>
		<tr>
			<td>		
				<input type=radio name=paymethod value=creditcard checked>신용카드<br>
				<input type=radio name=paymethod value='cash'>현금<br>
			</td>
		</tr>
		<tr>
			<td valign=top>총액:&nbsp;<font color=blue><label id=lblPrice style='text-align: right;width:60px;'></label></font>천원</td>
		</tr>
		<tr>
			<td valign=top class=payment>결제액:&nbsp;<input type=text id=payprice style='text-align: right;width:60px;'>천원</td>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td><select id=selList style='width:150px' size=5></select>	
</tr>
</table>
</div>

<!-- 주문 추가 (배달/예약/takeout) -->
<div id=dlgUser style="display:none;background-color:#c2c7ed;">
<input type=hidden id=dlguser-order-id><input type=hidden id=dlguser-ordertype>
<table border=1>
	<tr id=trMobile><td style='font-size:18px;'>전화번호</td><td><input type=tel id=dlguser-mobile></td></tr>
	<tr id=trNickname><td style='font-size:18px;'>주문자 이름</td><td><input type=text id=dlguser-nickname></td></tr>
	<tr id=trAddress><td style='font-size:18px;'>배달주소</td><td><textarea id=dlguser-address cols=32 rows=2></textarea></td></tr>
	<tr id=trHowmany><td style='font-size:18px;'>예약인원</td><td><input type=number id=dlguser-howmany value='1' min=1 max=50>&nbsp;명</td></tr>
	<tr id=trArrivaltime><td style='font-size:18px;'>도착예정시각</td><td><input type=text id=dlguser-arrival-time size=10></td></tr>
</table>
</div>

<!-- 
//--- 추가메뉴 주문 화면.
 -->
<div id=dlgMenu style="display:none;background-color:#c2c7ed;">
<div id=tabOrder>
	<ul>
		<li id=liSelected><a href='#dvSelected'>선택한 메뉴</a></li>
		<li id=liOrdered><a href='#dvOrdered'>주문한 메뉴</a></li>				
	</ul>
	<div id=dvSelected style='overflow:auto;'>
    	<table>
        <tr>
        	<td valign=top>
        		<table style='width:240px;'>
        		<tr>
        			<td><label id=lblTableno style='font-size:18px;'></label>
        				<input type=hidden id=dlgmenu-order-id><input type=hidden id=dlgmenu-ordertype>
        			</td>
        		</tr>
        		<tr>
        			<td valign=top>
        				<select id=selSelected style='width:240px;' size=20></select>
        			</td>
        		</tr>
        		<tr style='height:100px;'>
        			<td valign=bottom align=center >
        				<button id=btnErase style="width:120px;height:80px;font-size:150%;">주문전체 취소</button>
        			</td>
        		</tr>
        		</table>
        	</td>
        	<td valign=top width=600px>
<?
$colnum=4;
$sql="select * from {$ob_menu} where party='{$_SESSION['party']}' order by type,name";
$rs=$mysqli->query($sql);
wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
if($rs===false) echo "Failed to execute SQL.";
if($rs->num_rows<1) echo "No menu found";
else {
    $arHead=array(); $arBody=array(); 
    echo "<div id=dvMenu style='overflow:auto;'><ul>";
    $_type=""; $_header=""; $_body=""; $n=0; $i=0;
    while($row=$rs->fetch_assoc()){
        if($row['type']!=$_type){
            array_push($arHead,"<li><a href='#type{$n}'>{$row['type']}</a></li>");
            if($_type!=""){
                while($i++%$colnum!=0) $pstr.="<td>&nbsp;</td>";
                $pstr.="</tr></table></div>";
                array_push($arBody,$pstr);
            }
            $pstr="<div id='type{$n}'><table>";
            $_type=$row['type'];
            $n++;
            $i=0;
        }
        if($i%$colnum==0) {
            if($i!=0)   $pstr.="</tr>";
            $pstr.="<tr>";
        }
        $pstr.="<td align=center><button id='mnu{$row['rowid']}' style='width:125px;height:75px;' class='ui-button ui-widget ui-corner-all'>{$row['name']}<br>[{$row['price']}]</button></td>";
        $i++;
    }
    while($i++%$colnum!=0)  $pstr.="<td>&nbsp;</td>";
    $pstr.="</tr></table></div>";
    array_push($arBody,$pstr);
    foreach($arHead as $k=>$val)    echo $val;
    echo "</ul>";
    foreach($arBody as $k=>$val)    echo $val;
}
?>
        	</td>
        	<td valign=top>
        		<table>
        		<tr>
        			<td><button id=btnsubmitMenu style='height:80px;width:120px;font-size:18px'>확인</button></td>
        		</tr><tr id=pnMobile>
        			<td>모바일번호 : <input type=text id=mobile size=14></td>
        		</tr><tr id=pnArrival_time>
        			<td>도착예정시각 : <input type=text id=arrival_time size=7></td>
        		</tr><tr id=pnHowmany>
        			<td>예약인원 : <input type=text id=howmany size=3 style='text-align:right;'>명</td>
        		</tr><tr id=pnAddress>
        			<td>배달지 주소 : <textarea id=address cols=30 rows=2></textarea></td>
        		</tr>
        		</table>
        	</td>
        </tr>
        </table>
	</div>
	<div id=dvOrdered style='overflow:auto;'>
		<table boder=1 width=150px height=500px>
		<tr height=100px id='merge-panel'>
			<td valign=top>
				<table>
				<tr>
					<td><input type=button id='merge' style='height:80px;width:120px;font-size:18px;' title='다른 테이블과 합치거나 빈 테이블로 이동합니다.' value='이동'></td>
				</tr>
				<tr>
					<td align=center><select id='working-table' style='width:120px;display:none;' size=7></select></td>
				</tr>
				</table>
			</td>
			<td valign=top>
				<select id=selOrdered style='width:360px;' size=20></select>
			</td>
			<td valign=top>
				<table border=0>
        		<tr>
        			<td valign=bottom align=center style='font-size:18px;'>총액 [<label id='lblTotal'></label>]천원</td>
        		</tr>
        		<tr id='payment-panel'>
        			<td>
        				<table border=1 width=100%>
        				<tr><td style='font-size:18px;'><input type=radio name=payment_method value=credit checked>신용카드</td></tr>
        				<tr><td style='font-size:18px;'><input type=radio name=payment_method value=cash>현금</td></tr>
        				<tr height=40px><td colspan=2 align=center valign=bottom style='font-size:18px;'>
        					결제&nbsp;&nbsp;<input type=text id=actual_price size=4 maxlength=4>천원
        				</td>
        				<tr>
        					<td valign=top align=center>
        						<button id=btnPayment style='height:80px;width:120px;font-size:18px;'>결제하기</button></td>
        				</tr>
        				</table>
        			</td>
        		</tr>
        		<tr>
        			<td valign=bottom align=center>
        				<button name=btnUnpaid id=btnUnpaid style="width:120px;height:60px;background-color:yellow;font-size:150%;">미수처리</button></td>
        		</tr>
        		</table>
    		</td>
    	</tr>
    	</table>
	</div>
</div>

</div>
<div id=dlgPaid style='display: none'></div>
</body>
</html>
<?
$mysqli->close();
?>