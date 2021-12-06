<?
if(isset($_GET['rowid'])){
	$sql="select * from bbs_house where rowid='{$_GET['rowid']}'";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs!==false && $rs->num_rows>0){
		$rowH=$rs->fetch_assoc();
	}
}
?>
<table align=center width=540px>
<tr>
	<td class=td1>위치</td>
	<td class=td2 colspan=3>
<?
if($_optype=="read"){
    echo $rowH['loc_type'].":".$rowH['location'];
} else {
?>
	<select id=loc_type ><option value='address' <? if($rowH['loc_type']=="address") echo "selected"; ?>>주소</option>
	<option value='postcode' <? if($rowH['loc_type']=="postcode") echo "selected"; ?>>우편번호</option></select>&nbsp;
	<input type=text id=location size=40 maxlength=64 value='<?=$rowH['location'] ?>'>
<? } ?>
	</td>
</tr>
<tr>
	<td class=td1>주택종류</td>
	<td class=td2>
<?
if($_optype=="read"){
    echo $rowH['housetype'];
} else {
?>
	<select id=housetype  >
	<option value='HDB' <?if($rowH['housetype']=="HDB") echo "selected";?>>HDB</option>
	<option value='condo' <?if($rowH['housetype']=="condo") echo "selected";?>>콘도</option>
	<option value='Landed' <?if($rowH['housetype']=="Landed") echo "selected";?>>Landed House</option>
	<option value='others' <?if($rowH['housetype']=="others") echo "selected";?>>기타</option></select>
<? } ?>
	</td>
	<td class=td1>거주형태</td><td class=td2>
<?
if($_optype=="read"){
    echo $rowH['roomtype'];
} else {
?>
	<select id=roomtype >
	<option value='룸렌트' <?if($rowH['roomtype']=="룸렌트") echo "selected";?>>룸렌트</option>
	<option value='룸메이트구함' <?if($rowH['roomtype']=="룸메이트구함") echo "selected";?>>룸메이트구함</option>
	<option value='주택렌트' <?if($rowH['roomtype']=="주택렌트") echo "selected";?>>주택렌트</option>
	<option value='주택매매' <?if($rowH['roomtype']=="주택매매") echo "selected";?>>주택매매</option>
	<option value='사무실렌트' <?if($rowH['roomtype']=="사무실렌트") echo "selected";?>>사무실렌트</option></select>
<? } ?>
	</td>
</tr>
<tr>
	<td class=td1>방수</td><td class=td2>
<?
if($_optype=="read"){
    echo $rowH['num_room']."&nbsp;개";
} else {
?>
	<input type=number id=num_room size=2 maxlength=2 style='width:40px;text-align:right;' min=1  value=<?if(isset($rowH['num_room'])) echo $rowH['num_room']; else echo "1";?>>&nbsp;개
<? } ?>
	</td>
	<td class=td1>면적</td><td class=td2>
<?
if($_optype=="read"){
    echo $rowH['housesize']." ".$rowH['sizeunit'];
} else {
?>
	<input type=number id=housesize size=3 maxlength=3 style='width:50px;text-align:right;' min=0  value=<?if(isset($rowH['housesize'])) echo $rowH['housesize']; else echo "0";?>>
	<select id=sizeunit><option value=sqft <?if($rowH['sizeunit']=="sqft") echo "selected";?>>sqft</option><option value=sqm <?if($rowH['sizeunit']=="sqm") echo "selected";?>>sqm</option></select>
<? } ?>
	</td>
</tr>
<tr>
	<td class=td1>입주가능 성별</td><td class=td2 colspan=3>
<?
if($_optype=="read"){
    echo $rowH['staytype'];
} else {
?>
		<input type=radio name=staytype value='여자만' <?if($rowH['staytype']=="여자만") echo "checked"; ?> >여자만&nbsp;
		<input type=radio name=staytype value='남자만' <?if($rowH['staytype']=="남자만") echo "checked"; ?> >남자만&nbsp;
		<input type=radio name=staytype value='커플은 안됨' <?if($rowH['staytype']=="커플은 안됨") echo "checked";?>>커플 안됨&nbsp;
		<input type=radio name=staytype value='커플 가능' <?if($rowH['staytype']=="커플 가능") echo "checked"; ?> >커플 가능
		</td>
<? } ?>	
</td>
</tr>
<tr>
	<td class=td1>최소거주기간</td><td class=td2>
<?
if($_optype=="read"){
    echo $rowH['howlong'];
} else {
?>
	<select name=howlong id=howlong >
		<option value='6개월이상'  <?if($rowH['howlong']=="6개월이상") echo "selected"; ?>>6개월이상 </option>
		<option value='1년이상' <?if($rowH['howlong']=="1년이상") echo "selected"; ?>>1년이상 </option>
		<option value='2년이상' <?if($rowH['howlong']=="2년이상") echo "selected"; ?>>2년이상 </option>
		<option value='기타' <?if($rowH['howlong']=="기타") echo "selected"; ?>>기타 </option>
	</select>
<?}?>
	</td>
	<td class=td1>입주가능일</td><td class=td2>
<?
if($_optype=="read"){
    echo $rowH['expected_moving'];
} else {
?>
	<input type=text id=expected_move value='<?if(isset($rowH['expected_move'])) echo $rowH['expected_move']; else echo '즉시가능';?>' size=8 maxlength=8 >
<?}?>
	</td>
</tr>
<tr>
	<td class=td1 >가격(렌트비)</td><td class=td2>
<?
if($_optype=="read"){
    echo $rowH['price'];
} else {
?>
	<input id=price type=number size=7 maxlength=8 style='width:60px;text-align:right;' min=0 value=<?if(isset($rowH['price'])) echo $rowH['price']; else echo "1000";?> >
<?}?>
	 &nbsp;SGD</td>
	<td class=td1>보증금</td><td class=td2>
<?
if($_optype=="read"){
    echo $rowH['deposit'];
} else {
?>
	<input id=deposit type=number size 7 maxlength=8 min=0 style='width:60px;text-align:right;' value=<?if(isset($rowH['deposit'])) echo $rowH['deposit']; else echo "500";?> >
<?}?>
	&nbsp;SGD</td>
</tr>
<tr>
	<td class=td1>공과금</td><td class=td2 colspan=3>
<?
if($_optype=="read"){
    echo $rowH['pub_include'];
} else {
?>	
	<input type=radio name=pub value='렌트비와 별도'  <?if($rowH['pub_include']=="렌트비와 별도") echo "checked";?> >렌트비와 별도
	&nbsp;<input type=radio name=pub value='렌트비에 포함'  <?if($rowH['pub_include']=="렌트비에 포함" || !isset($rowH['pub_include'])) echo "checked";?> >렌트비에 포함
<?}?>
	&nbsp;&nbsp;<font color=red>(전기/수도/가스/인터넷)</font>
	</td>
</tr>
<tr>
	<td class=td1>요리(취사)</td><td class=td2 colspan=3>
<?
if($_optype=="read"){
    echo $rowH['cookable'];
} else {
?>	
	<input type=radio name=cookable id=cookable1 value='자유롭게 가능' <?if($rowH['cookable']=="fully") echo "checked"; ?> >자유롭게 가능&nbsp;
		<input type=radio name=cookable id=cookable2 value='간단한 요리가능' <?if($rowH['cookable']=="partially") echo "checked";?> >간단한 요리가능&nbsp;
		<input type=radio name=cookable id=cookable3 value='불가' <?if($rowH['cookable']=="none") echo "checked";?> >불가
<?}?>		
	</td>
</tr>
<tr>
	<td class=td1>연락처</td><td class=td2 colspan=3>
<?
$sql="select contact1,contact2,contact3,_contact1,_contact2,_contact3 from bbs_member where rowid='{$_SESSION['user_rowid']}'";
$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
$rsM=$mysqli->query($sql);
if($rsM===false || $rsM->num_rows<1){
} else {
    $rowM=$rsM->fetch_assoc();
    if(isset($rowM['_contact1'])) echo $rowM['contact1'].":&nbsp;".$rowM['_contact1']."<br>";
    if(isset($rowM['_contact2'])) echo $rowM['contact2'].":&nbsp;".$rowM['_contact2']."<br>";
    if(isset($rowM['_contact3'])) echo $rowM['contact3'].":&nbsp;".$rowM['_contact3']."<br>";
}
?>
	</td>
</tr>
</table>
<script type='text/javascript' src='housing.js'></script>