<?
@session_start();
require_once 'include.php';

if(!isset($_SESSION['party'])) exit;

$log="";
switch($_POST['optype']){
case "newest": case "check-expiry": case "pertain":
	break;
default:
	foreach($_POST as $key=>$value)	{
		if($key=="optype") {
			$log.="{$key} [<font color=red>{$value}</font>]";
		} else{
			$log.="{$key} [{$value}] ";
		}
	}
	wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);
}

switch($_POST['optype']){
case "resetlog":
	resetlog();
	break;
case "get-customer":
	$t_column=array(
		"takeout"=>"a.arrival_time,b.mobile,b.nickname",
		"delivery"=>"a.arrival_time,b.mobile,b.nickname,b.address",
		"book"=>"a.arrival_time,b.mobile,b.nickname,a.howmany"
	);
	$sql="select {$t_column[$_POST['ordertype']]} from {$ob_master} a left outer join {$ob_member} b ".
			"on a.mobile=b.mobile where ordertype='{$_POST['ordertype']}' and a.order_id='{$_POST['order_id']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false){
		$answer['success']=-1; $answer['message']=$mysqli->error;
	} else if($rs->num_rows<1){
		$answer['success']=0; $answer['message']="Nobody found.";
	} else{
		$row=$rs->fetch_assoc();
		wlog("t_column [{$t_column[$_POST['ordertype']]}]",__LINE__,__FUNCTION__,__FILE__);
		foreach(explode(",",$t_column[$_POST['ordertype']]) as $key=>$value){
			wlog("{$value} [{$row[substr($value,2)]}]",__LINE__,__FUNCTION__,__FILE__);
			$answer[substr($value,2)]=$row[substr($value,2)];
		}
		$answer['success']=0;
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	
	break;
case "new-order-id":
	$answer['success']=0;
	$answer['order_id']=$_now;
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "working-table":
	try{
		$sql="select tableno,order_id from {$ob_master} where party='{$_SESSION['party']}' and ordertype='inhouse' and (status='' or status='done') order by tableno";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		$answer['inhouse']=array();
		while($row=$rs->fetch_array(MYSQLI_ASSOC)) {
			$inhouse=array();
			foreach($row as $k=>$v)	$inhouse[$k]=$v;
			wlog("order_id [{$inhouse['order_id']}] [{$order_id}]",__LINE__,__FUNCTION__,__FILE__);
// 			if($inhouse['order_id']>$order_id) $order_id=$inhouse['order_id'];
			array_push($answer['inhouse'],$inhouse);
		}
		$answer['success']=0;
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "working-inhouse":
	try{
		$sql="select b.rowid,a.order_id,a.tableno,b.name from {$ob_master} a left join {$ob_order} b on a.order_id=b.order_id ".
				"where a.party='{$_SESSION['party']}' and ordertype='inhouse' and b.status='' order by b.rowid desc";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		$answer['inhouse']=array();
		while($row=$rs->fetch_array(MYSQLI_ASSOC)) {
			$inhouse=array();
			foreach($row as $k=>$v)	$inhouse[$k]=$v;
			array_push($answer['inhouse'],$inhouse);
		}
		$answer['success']=0;
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
/*case "working-takeout":
case "working-delivery":
case "working-book":
	$t_alias=explode("-",$_POST['optype']);
	$t_alias=$t_alias[1];
	try{
		$sql="select {$t_field[$t_alias]},a.status from {$ob_master} a left outer join {$ob_member} b on a.mobile=b.mobile ".
			"where ordertype='{$t_alias}' and a.party='{$_SESSION['party']}' and (a.status='' or a.status='done') order by a.order_id";
		// and substr(a.order_id,0,14)>='{$business_start}' 
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);

		$arOrderID=array();
		$answer[$t_alias]=array();
		while($row=$rs->fetch_array(MYSQLI_ASSOC)) {
			$record=array();
			foreach($row as $k=>$v)	$record[$k]=$v;
			array_push($arOrderID,$row['order_id']);
			array_push($answer[$t_alias],$record);
		}
		$sql="select rowid,mobile,name,price from {$ob_order} where order_id in('".implode("','",$arOrderID)."' order by rowid";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		$answer[$t_alias."0"]=array();
		while($row=$rs->fetch_array(MYSQLI_ASSOC)){
			$record=array();
			foreach($row as $k=>$v)	$record[$k]=$v;
			array_push($answer[$t_alias."0"],$record);
		}
		$answer['success']=0;
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;*/
	
case "table":
	$sql="select table_count,table_position from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false){
		$answer['message']=$mysqli->error;
	} else {
		$row=$rs->fetch_array(MYSQLI_ASSOC);
		$answer['table_count']=$row['table_count'];
		$answer['table_position']=$row['table_position'];
		$answer['success']=0;
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "setting":
	$sql="select a.rowid,a.party,a.postcode,a.address,a.table_count,a.phone,b.mobile,a.wifi,a.business_start,a.delivery_fee,".
			"a.order_inhouse,a.order_takeout,a.order_delivery,a.order_book,a.pay_inhouse,a.pay_takeout,a.pay_delivery,a.pay_book ".
			",logo_updated,logo_image ".
			"from {$ob_party} a left join ob_admin b on a.rowid=b.party where a.rowid='{$_SESSION['party']}' and b.admin_level=0";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false){
		$answer['message']=$mysqli->error;
	} else {
		$answer['success']="0";
		$row=$rs->fetch_array(MYSQLI_ASSOC);
		foreach($row as $k=>$v)	{
			wlog("{$k} [{$v}]");
			$answer[$k]=$v;
		}
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "pertain":
	if(isset($_SESSION['party'])) echo "1";
	else echo "0";
	break;
case "admin_check":
	$sql="select count(*) cnt from {$ob_party} where rowid='{$_SESSION['party']}' and passcode_admin='{$_POST['passcode_admin']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$result=$mysqli->query($sql);
	if($result===false){
		echo $mysqli->error;
		mysqli_close(); exit;
	}
	$row=$result->fetch_assoc();
	wlog("intval [{$row['cnt']}]",__LINE__,__FUNCTION__,__FILE__);
	if(intval($row['cnt'])==1) {echo "0"; wlog('=>0',__LINE__,__FUNCTION__,__FILE__);}
	else { echo "-1"; wlog('=>-1',__LINE__,__FUNCTION__,__FILE__);}
	exit;
case "loadmenu":
	try{
		$sql="select * from {$ob_menu} where party='{$_SESSION['party']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		$pstr=""; $n=1;
		while($row=$rs->fetch_assoc()){
			$imgfile=$row['menu_image'];
			if($imgfile=="")	$imgfile="imgnotfound.jpeg";
			$pstr.="<tr id='{$row['rowid']}'><td><table><tr><td><img style='width:120px;height:90px;' name=imgMenu ".
					"src='./menu/{$imgfile}' title='이미지를 변경하려면 클릭하세요.'></td></tr>".
					"<tr><td align=center><input type=file name=filename style='display:none;' accept='.gif,.jpg,.png,.jpeg'>".
					"<button name=btnUpload style='display:none;'>&nbsp;</button>".
					"<button name=btnRemoveImage style='font-size:9px;'>제거</button></td></tr></table></td>".
					"<td>{$row['name']}</td><td>{$row['price']}</td><td>{$row['type']}</td>".
					"<td>{$row['comment']}</td><td align=center><button class=remove-menu>지우기 </button></td></tr>";
			$n++;
		}
		wlog($pstr,__LINE__,__FUNCTION__,__FILE__);
		echo $pstr;
	} catch(Exception $e){
	}
	break;
case "loadmanager":
	try{
		$sql="select * from {$ob_admin} where party='{$_SESSION['party']}' and userid!='{$_SESSION['userid']}' order by userid";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		$pstr="";
		while($row=$rs->fetch_assoc()){
			$pstr.="<tr id='{$row['rowid']}'><td>{$row['name']}</td><td>{$row['mobile']}</td><td>{$row['userid']}</td>".
					"<td align=center><button class=remove-manage>지우기</button></td></tr>";
		}
		wlog($pstr,__LINE__,__FUNCTION__,__FILE__);
		echo $pstr;
	} catch(Exception $e){
	}
	break;
/*case "working":
	$sql="select tableno from {$ob_master} where party='{$_SESSION['party']}' and ordertype='inhouse' and payment_code is null";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$result=$mysqli->query($sql);
	$pstr="";
	while($row=$result->fetch_assoc()){
		if($pstr!="") $pstr.=",";
		$pstr.=$row['tableno'];
	}
	wlog("[{$pstr}]",__LINE__,__FUNCTION__,__FILE__);
	echo $pstr;
	break;*/
case "menu":		
	$sql="select rowid,type,name,price,comment from {$ob_menu} where party='{$_SESSION['party']}' order by type,name";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		$answer['message']=$mysqli->error;
	} else {
		$answer['success']=0;
		$answer['record']=array(); $record=array();
		while($row=$rs->fetch_assoc()){
			foreach($row as $k=>$v)	$record[$k]=$v;
			array_push($answer['record'],$record);
		}
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__,false);
	echo json_encode($answer);
	break;

case "check-booking":
	$sql="select * from {$ob_master} where party='{$_POST['party']} and mobile='{$_POST['mobile']}' and ordertype='book' and tableno=0 and status=''";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	wlog("num_rows [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
	if($rs===false || $rs->num_rows==0){
		echo "";
	} else {
		$row=$rs->fetch_assoc();
		echo "{$row['order_id']}|{$row['nickname']}|{$row['howmany']}|{$row['arrival_time']}";
	}
	break;
	
case "eachmenu":	// 메뉴별 매출 
	$sql="select business_start from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($rs->num_rows<1) $business_start="1700";
	else {
		$row=$rs->fetch_assoc();
		$business_start=str_replace(":","",$row['business_start']);
		if($business_start=="") $business_start="170000";
	}
	$business_start.="000000";
	
	$fromdate=$_POST['fromdate'].$business_start;$todate=$_POST['todate'].$business_start;
	$where="where party='{$_SESSION['party']}' and status='paid' order_id between '{$fromdate}' and '{$todate}'";
	$sql="select name,sum(price) sumprice from {$ob_master} {$where}) group by name order by name";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		wlog($mysqli->error);
	}
	else {
		$total=0;
		$pstr="<thead><tr><th colspan=2 style='background-color:cyan;'>메뉴별 매출</th></tr>".
				"<tr style='background-color:black;'>".getTH("메뉴이름").getTH("매출액")."</tr></thead><tbody>";
		while($row=$rs->fetch_assoc()){
			$pstr.="<tr>".getTD($row['name']).getTD(number_format(floatval($row['sumprice']),2,".",","),"R")."</tr>";
			$total+=floatval($row['sumprice']);
		}
		$pstr.="<tr><td align=right>Total</td><td align=right>".number_format($total,2,".",",")."</td></tr>";
		$pstr.="</tbody>";
		echo $pstr;
	}
	break;
case "ordertype":
	$sql="select business_start from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($rs->num_rows<1) $business_start="1700";
	else {
		$row=$rs->fetch_assoc();
		$business_start=str_replace(":","",$row['business_start']);
	}
	$business_start.="000000";
	
	$fromdate=$_POST['fromdate'].$business_start;$todate=$_POST['todate'].$business_start;
	$where="where party='{$_SESSION['party']}' and status='1' and order_id between '{$fromdate}' and '{$todate}'";
	$sql="select ordertype,ifnull(sum(paid),0.00) sumprice from {$ob_master} {$where} group by ordertype order by sumprice";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		wlog($mysqli->error);
	}
	else {
		$total=0;
		$pstr="<thead><tr><th colspan=2 style='background-color:cyan;'>주문형태별 매출</th></tr>".
				"<tr style='background-color:black;'>".getTH("주문형태").getTH("매출액")."</tr></thead><tbody>";
		while($row=$rs->fetch_assoc()){
			$pstr.="<tr>".getTD($row['ordertype']).getTD(number_format($row['sumprice'],2,".",","),"R")."</tr>";
			$total+=floatval($row['sumprice']);
		}
		$pstr.="<tr><td align=right>Total</td><td align=right>".number_format($total,2,".",",")."</td></tr>";
		$pstr.="</tbody>";
		echo $pstr;
	}
	break;
case "per-table":
	$sql="select business_start from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($rs->num_rows<1) $business_start="1700";
	else {
		$row=$rs->fetch_assoc();
		$business_start=str_replace(":","",$row['business_start']);
	}
	$business_start.="000000";
	
	$fromdate=$_POST['fromdate'].$business_start;$todate=$_POST['todate'].$business_start;
	
	$sql="select tableno,ifnull(sum(paid),0.00) sumprice from {$ob_master} ".
			"where party='{$_SESSION['party']}' and status='paid' and ordertype='inhouse' and order_id between '{$fromdate}' and '{$todate}' ".
			"group by tableno order by sumprice";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		wlog($mysqli->error);
	}
	else {
		$total=0;
		$pstr="<thead><tr><th colspan=2 style='background-color:cyan;'>테이블별 매출</th></tr>".
				"<tr style='background-color:black;'>".getTH("테이블번호").getTH("매출액")."</tr></thead><tbody>";
		while($row=$rs->fetch_assoc()){
			$pstr.="<tr>".getTD($row['tableno']).getTD(number_format($row['sumprice'],2,".",","),"R")."</tr>";
			$total+=floatval($row['sumprice']);
		}
		$pstr.="<tr><td align=right>Total</td><td align=right>".number_format($total,2,".",",")."</td></tr>";
		$pstr.="</tbody>";
		echo $pstr;
	}
	break;	
case "daily":
	$sql="select business_start from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($rs->num_rows<1) $business_start="1700";
	else {
		$row=$rs->fetch_assoc();
		$business_start=str_replace(":","",$row['business_start']);
		if($business_start=="") $business_start="170000";
	}
	$business_start.="000000";
	
	$fromdate=$_POST['fromdate'].$business_start;$todate=$_POST['todate'].$business_start;
	$where="where party='{$_SESSION['party']}' and status='1' order_id between '{$fromdate}' and '{$todate}'";
	$sql="select concat(substr(order_id,1,4),'-',substr(order_id,5,2),'-',substr(order_id,7,2)) order_time,sum(paid) sumprice from {$ob_master} ".
			"{$where}' group by 1 order by 1 desc";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		wlog($mysqli->error);
	}
	else {
		$total=0;
		$pstr="<thead><tr><th colspan=5 style='background-color:cyan;'>일별 총매출</th></tr><tr style='background-color:black;'>".
				getTH("일자").getTH("합계")."</tr></thead><tbody>";
		while($row=$rs->fetch_assoc()){
			$pstr.="<tr>".getTD($row['order_time']).getTD(number_format($row['sumprice'],2,".",","),"R")."</tr>";
			$total+=floatval($row['sumprice']);
		}
		$pstr.="<tr><td align=right>Total</td><td align=right>".number_format($total,2,".",",")."</td></tr>";
		$pstr.="</tbody>";
		echo $pstr;
	}
	
	break;
case "unpaid":
	$sql="select business_start from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception($mysqli->error);
	if($rs->num_rows<1) $business_start="1700";
	else {
		$row=$rs->fetch_assoc();
		$business_start=str_replace(":","",$row['business_start']);
		if($business_start=="") $business_start="170000";
	}
	$business_start.="000000";
	
	$fromdate=$_POST['fromdate'].$business_start;$todate=$_POST['todate'].$business_start;
	$where="where party='{$_SESSION['party']}' and status='unpaid' and order_id between '{$fromdate}' and '{$todate}'";
	
	$sql="select order_id,a.mobile,nickname,price from {$ob_master} a left outer join {$ob_member} b on a.mobile=b.mobile ".
			" {$where} order by order_id";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		wlog($mysqli->error);
	}
	else {
		$total=0; $paid=0;
		$pstr="<thead><tr><th colspan=6 style='background-color:cyan;'>외상미수금 </th></tr><tr style='background-color:black;'>".
					getTH("주문시각").getTH("주문형태").getTH("전화번호").getTH("대표자이름").getTH("총액").getTH("지불금액")."</tr></thead><tbody>";
		while($row=$rs->fetch_assoc()){
			$pstr.="<tr>".getTD(showTime($row['order_id'])).getTD($row['ordertype']).getTD(_phone($row['mobile'])).getTD($row['nickname']).
				getTD(number_format($row['price'],2,".",","),"R").getTD(number_format($row['paid'],2,".",","),"R")."</td></tr>";
				$total+=floatval($row['price']); $paid+=floatval($row['paid']);
		}
		$pstr.="</tbody>";
		$pstr.="<tr><td align=right colspan=4>Total</td><td align=right>".number_format($total,2,".",",")."</td><td align=right>".number_format($paid,2,".",",")."</tr>";
		echo $pstr;
	}
	break;
case "newest":
	$sql="select order_inhouse,order_takeout,order_delivery,order_book,last_inhouse,last_takeout,last_delivery,last_book ".
			"from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) break;
	$row=$rs->fetch_assoc();
	if($row['order_inhouse']=='1'){
		$sql="select order_id from {$ob_master} where party='{$_SESSION['party']}' and order_id>'{$row['last_inhouse']}' and ordertype='inhouse'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) break;
		if($rs->num_rows>0)	$answer['inhouse']=1;
		else $answer['inhouse']=0;
	}
	if($row['order_takeout']=='1'){
		$sql="select order_id from {$ob_master} where party='{$_SESSION['party']}' and order_id>'{$row['last_takeout']}' and ordertype='takeout'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) break;
		if($rs->num_rows>0)	$answer['takeout']=1;
		else $answer['takeout']=0;
	}
	if($row['order_delivery']=='1'){
// 		$sql="select order_id from {$ob_delivery} where party='{$_SESSION['party']}' and order_id>'{$row['last_delivery']}'";
		$sql="select order_id from {$ob_master} where party='{$_SESSION['party']}' and order_id>'{$row['last_delivery']}' and ordertype='delivery'";
// 		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) break;
		if($rs->num_rows>0)	$answer['delivery']=1;
		else $answer['delivery']=0;
	}
	if($row['order_book']=='1'){
// 		$sql="select order_id from {$ob_book} where party='{$_SESSION['party']}' and order_id>'{$row['last_book']}'";
		$sql="select order_id from {$ob_master} where party='{$_SESSION['party']}' and order_id>'{$row['last_book']}' and ordertype='book'";
// 		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) break;
		if($rs->num_rows>0)	$answer['book']=1;
		else $answer['book']=0;
	}
	$answer['success']=0;
// 	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "check-expiry":
// 	resetLog();
// 	wlog("service_expiry [{$_SESSION['service_expiry']}] now [".date("Ymd")."]",__LINE__,__FUNCTION__,__FILE__); 
// 	if($_SESSION['service_expiry']<date("Ymd")) echo "-1";		// over the expiry
// 	else echo "0";
	$sql="select last_orderid,payday,fare_rate from {$ob_party} where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows<1) break;
	
	$row=$rs->fetch_assoc();
	if(intval($payday)<intval(date("d") && $row['last_dayday']<date("Ymd"))){
		try {
			$mysqli->autocommit(false);
		
			$start_orderid=$row['last_orderid'];
			$sql="select paid*{$row['fare_rate']} service_charge,max(order_id) last_orderid from ob_command ".
					"where  order_id>'{$start_orderid}' and status='paid' and party='{$_SESSION['party']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			if($rs->num_rows<1) throw new Exception("No record.");
			$row=$rs->fetch_assoc();
			$sql="update ob_party_pay set service_charge={$row['service_charge']},party='{$_SESSION['party']}',".
					"rowid='{$_now}',paytime='{$_now}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			if($mysqli->affected_rows<1) throw new Exception("No record updated.");
			$sql="update {$ob_party} set last_payday='{$row['last_orderid']}' where rowid='{$_SESSION['party']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			if($mysqli->affected_rows<1) throw new Exception("No record updated.");
			$mysqli->commit();	
		} catch(Exception $e){
			$mysqli->rollback();
			wlog($e->getMessage()."=>".$sql,__LINE__,__FUNCTION__,__FILE__);
		}
		$mysqli->autocommit(false);
	}
	break;
}
$mysqli->close();

?>