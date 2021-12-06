<?
@session_start();
require_once 'include.php';

// $method=$_SERVER['REQUEST_METHOD'];
if(!isset($_SESSION['party'])) exit;

$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$_now= date('YmdHis'.$micro);

$answer=array();
$answer['success']=-1;
$answer['message']="";

$log="";
foreach($_POST as $key=>$value)	{
		$log.="{$key} [{$value}] ";
}
wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);

$t_field=array(
	"takeout"=>"a.order_id,a.price,a.mobile,b.nickname,a.arrival_time",
	"delivery"=>"a.order_id,a.price,a.mobile,b.nickname,b.address",
	"book"=>"a.order_id,a.price,a.mobile,b.nickname,a.arrival_time,a.howmany"
);

switch($_POST['optype']){

case "sale":
	try{
		$sql="select business_start from {$ob_party} where party='{$_SESSION['party']}'";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows<1) $business_start="1700";
		else {
			$row=$rs->fetch_assoc();
			$business_start=str_replace(":","",$row['business_start']);
		}
		wlog("business start [{$business_start}] now [".date("His")."]");
		if($business_start."00">date("Hi")){
			$starttime=date("Ymd",strtotime("-1 days")).$business_start;
		} else {
			$starttime=date("Ymd").$business_start;
		}
		wlog("starttime [{$starttime}]");
		$starttime.="000000";
		$column=",price,paid,payment_type";
		$where=" where party='{$_SESSION['party']}' and order_id>='{$starttime}' and status='paid' ";
		// Paid
		$sql="select order_id,case ordertype when 'inhouse' then '매장' when 'delivery' then '배달' when 'takeout' then '테이크아웃' when 'book' then '예약' end,".
				"case ordertype when 'inhouse' then tableno else mobile end{$column} from {$ob_master} {$where} order by order_id";
		
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false)	throw new Exception($mysqli->error);
		
		$pstr="<thead><tr><th colspan=5 style='background-color:cyan;'>매출</th></tr><tr style='background-color:black;'>".
				getTH("주문형태").getTH("테이블/모바일").getTH("총액").getTH("지불").getTH("지불방식")."</tr></thead><tbody>";
		$fieldcount=$rs->field_count;
		while($row=$rs->fetch_array(MYSQLI_BOTH)){
			$pstr.="<tr id='{$row['order_id']}'>";
			for($n=1;$n<$fieldcount;$n++){
				switch($n){
				case 1: case 2: case 5:	// 주문형태,테이블/모바일,지불방식 
					$pstr.=getTD($row[$n],"C"); break;
				case 3: case 4: 				// 총액,지불(액)
					$pstr.=getTD($row[$n],"R"); break;
				default:
					$pstr.=getTD($row[$n]); break;
				}
			}
			$pstr.="</tr>";
		}
		$pstr.="</tbody>";
// 		wlog(html_entity_decode($pstr),__LINE__,__FUNCTION__,__FILE__);
		$answer['sale']=$pstr;
		
		// Working
		$where=" where party='{$_SESSION['party']}' and order_id>='{$starttime}' and (status!='paid' and status!='unpaid') ";
		$sql="select order_id,case ordertype when 'inhouse' then '매장' when 'delivery' then '배달' when 'takeout' then '테이크아웃' when 'book' then '예약' end,".
				"case ordertype when 'inhouse' then tableno else mobile end{$column},status from {$ob_master} {$where} order by order_id";
		
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception($mysqli->error);
		
		$pstr="<thead><tr><th colspan=6 style='background-color:cyan;'>활동</th></tr><tr style='background-color:black;'>".
				getTH("주문형태").getTH("테이블/모바일").getTH("금액").getTH("지불").getTH("지불방식").getTH("상태")."</tr></thead><tbody>";
		$fieldcount=$rs->field_count;
		while($row=$rs->fetch_array(MYSQLI_BOTH)){
			$pstr.="<tr id='{$row['order_id']}'>";
			for($n=1;$n<$fieldcount;$n++){
				switch($n){
					case 1: case 5:
						$pstr.=getTD($row[$n],"C"); break;
					case 3: case 4:
						$pstr.=getTD($row[$n],"R"); break;
					default:
						$pstr.=getTD($row[$n]); break;
				}
			}
			$pstr.="</tr>";
		}
		$pstr.="</tbody>";
		wlog(html_entity_decode($pstr),__LINE__,__FUNCTION__,__FILE__);
		$answer['working']=$pstr;

		// Unpaid
		$starttime=date("Ymd",strtotime("-7 days")).$business_start;
		$starttime.="000000";
		
		$column=",price,'orderhistory'";
		
		$where=" where party='{$_SESSION['party']}' and order_id>='{$starttime}' and status='unpaid' ";
		$sql="select order_id,case ordertype when 'inhouse' then '매장' when 'delivery' then '배달' when 'takeout' then '테이크아웃' when 'book' then '예약' end,".
				"case ordertype when 'inhouse' then tableno else mobile end{$column} from {$ob_master} {$where} order by order_id";
		
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception($mysqli->error);

		$pstr="<thead><tr><th colspan=6 style='background-color:cyan;'>미수</th></tr><tr style='background-color:black;'>".
				getTH("주문형태").getTH("테이블/모바일").getTH("금액").getTH("주문내역")."<th>&nbsp;</th></tr></thead><tbody>";
		$fieldcount=$rs->field_count;
		while($row=$rs->fetch_array(MYSQLI_BOTH)){
			$pstr.="<tr id='{$row['order_id']}'>";
			for($n=1;$n<$fieldcount;$n++){
				switch($n){
					case 1: 	// ordertype
						$pstr.=getTD($row[$n],"C"); break;
					case 3: 	// price
						$pstr.=getTD($row[$n],"R"); break;
					case 4:	// order history
						$sql="select name from {$ob_order} where order_id='{$row['order_id']}' order by rowid";
						$rsOrder=$mysqli->query($sql);
						wlog("{$sql} [{$rsOrder->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
						if($rsOrder===false) throw new Exception($mysqli->error);
						$pstr.="<td>";
						while($rOrder=$rsOrder->fetch_assoc()){
							$pstr.=$rOrder['name']."<br>";
						}
						$pstr.="</td>";
						break;
					default:	// tableno/mobile
						$pstr.=getTD($row[$n]); break;
				}
			}
			$pstr.="<td align=center><button id=btnSettle>결제</button></td></tr>";
		}
		$pstr.="</tbody>";
		$answer['unpaid']=$pstr;
		$answer['success']="0";
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
	}
	echo json_encode($answer);
	break;
case "restore":
	try{
		$mysqli->autocommit(false);
		
		$sql="update {$ob_master} set status='' where order_id='{$_POST['order_id']}'";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$mysqli->affected_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception($mysqli->error);
		$mysqli->commit();
		$answer['success']="0";
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	echo json_encode($answer);
	break;

case "unlist":
	try{
		$mysqli->autocommit(false);
		
		$sql="update ob_command set visible='0' where order_id='{$_POST['order_id']}'";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$mysqli->affected_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception("{$sql} <- {$mysqli->error}");
		
		$answer['success']="0";
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
		wlog($sql."=>".$e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
	

case "done":
// 	resetlog();
	$answer['order_id']="";
	try{
		$mysqli->autocommit(false);

		$sql="update {$ob_order} set status='done' where rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("No order to be updated.");

		$sql="select order_id from {$ob_order} where rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("No order ID of chlid order.");
		$row=$rs->fetch_assoc();
		$order_id=$row['order_id'];
		
		$sql="select rowid from {$ob_order} where order_id='{$order_id}' and status=''";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows<1){
			$sql="update {$ob_master} set status='done' where order_id='{$order_id}' ";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);
			$answer['order_id']=$order_id;
		}
// 		$answer['order_id']=$order_id;
		$answer['success']=0;
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "get-paid":
	$sql="select currency from {$ob_party} where party='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	$answer['currency']="";
	if($rs!==false && $rs->num_rows>0){
		$row=$rs->fetch_assoc(); $answer['currency']=$row['currency'];
	}

	$sql="select ordertype,tableno,howmany,arrival_time,mobile,price,paid,pay_time,payment_type,payment_code,payment_msg,payment_id,payer,company,allotment,".
			"receipt,payment_number,expect from {$ob_master} where order_id='{$_POST['order_id']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		$answer['message']=$mysqli->error;
	} else if($rs->num_rows<1){
		$answer['message']="No record.";
	} else {
		$row=$rs->fetch_assoc();
		foreach($row as $k=>$v)	$answer[$k]=$v;
		$sql="select name,price,status from ob_order where order_id='{$_POST['order_id']}' order by rowid,name";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) {
			$answer['message']=$mysqli->error;
		} else if($rs->num_rows<1){
			$answer['message']="No record.";
		} else {
			$answer['order']=array();
			while($row=$rs->fetch_assoc()){
				$order=array();
				foreach($row as $k=>$v)	$order[$k]=$v;
				array_push($answer['order'],$order);
			}
			$answer['success']="0";
		}
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "remove-image":
	$folder="menu/";
	try {
		$mysqli->autocommit(false);
		$sql="select menu_image from {$ob_menu} where rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows<1) throw new Exception("No menu record.");
		$row=$rs->fetch_assoc();
		$imgfilename=$row['menu_image'];
		
		$sql="update {$ob_menu} set menu_image=null where rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		
		if(!unlink($folder.$imgfilename)) throw new Exception("메뉴이미지 화일이 삭제되지 않았습니다.");
		$answer['success']="0";		
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;



case "setting":
    $_POST['inhouse']=($_POST['inhouse']=='true'?'1':'0');
    $_POST['takeout']=($_POST['takeout']=='true'?'1':'0');
    $_POST['delivery']=($_POST['delivery']=='true'?'1':'0');
    $_POST['book']=($_POST['book']=='true'?'1':'0');
    $_POST['pay_inhouse']=($_POST['pay_inhouse']=='true'?'1':'0');
    $_POST['pay_takeout']=($_POST['pay_takeout']=='true'?'1':'0');
    $_POST['pay_delivery']=($_POST['pay_delivery']=='true'?'1':'0');
    $_POST['pay_book']=($_POST['pay_book']=='true'?'1':'0');
    foreach($_POST as $k=>$v) wlog("{$k} [{$v}]",__LINE__,__FUNCTION__,__FILE__);
    $mysqli->autocommit(false);
    try {
        
        $sql="update {$ob_party} set party='{$_POST['party']}',phone='{$_POST['phone']}',".
            "postcode='{$_POST['postcode']}',address='{$_POST['addr']}',table_count={$_POST['tablecount']},".
            "pay_inhouse='{$_POST['pay_inhouse']}',pay_takeout='{$_POST['pay_takeout']}',pay_delivery='{$_POST['pay_delivery']}',".
            "pay_book='{$_POST['pay_book']}',delivery_fee={$_POST['delivery_fee']},".
            "order_inhouse='{$_POST['inhouse']}',order_takeout='{$_POST['takeout']}',business_start='{$_POST['business_start']}',".
            "order_delivery='{$_POST['delivery']}',order_book='{$_POST['book']}' where rowid='{$_SESSION['party']}'";
        wlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($rs===false) throw new Exception($mysqli->error);
        
        $sql="update {$ob_admin} set mobile='{$_POST['mobile']}' where party='{$_SESSION['party']}' and admin_level=0";
        wlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($rs===false) throw new Exception($mysqli->error);
        
        $mysqli->commit();
        $answer['success']="0";
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['message']=$e->getMessage();
        wlog($e->getMessage()."=>".$sql,__LINE__,__FUNCTION__,__FILE__);
    }
    $mysqli->autocommit(true);
    wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
    echo json_encode($answer);
    break;
}
$mysqli->close();
?>