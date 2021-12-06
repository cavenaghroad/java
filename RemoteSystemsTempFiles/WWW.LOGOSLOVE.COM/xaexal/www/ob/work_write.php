<?
@session_start();
require_once 'include.php';

if(!isset($_SESSION['party'])) {
	$answer['success']="-1";
	$answer['message']="Session is terminated.";
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	$mysqli->close();
	exit;
}

$log="";
switch($_POST['optype']){
case "newest": //case "check-expiry":
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


case "table":
	$sql="update {$ob_party} set table_position='{$_POST['position']}' where rowid='{$_SESSION['party']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	break;

case "remove-logo":
	try{
		$mysqli->autocommit(false);

		$sql="update ob_party set logo_image=null where rowid='{$_SESSION['party']}'";
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
case "reset-position":
	try{
		$mysqli->autocommit(false);
		
		$sql="update {$ob_party} set table_position='' where rowid='{$_SESSION['party']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("테이블 위치 리셋 실패.");
		$mysqli->commit();
		$answer['success']='0';
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
		wlog($sql."=>".$e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;

case "remove-master":
// 	resetLog();
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try {
		$mysqli->autocommit(false);
		
		$sql="delete from {$ob_master} where order_id='{$_POST['order_id']}'";
		$rs=$mysqli->query($sql);
		if($rs===false)  throw new Exception($mysqli->error);

		$sql="delete from {$ob_order} where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false)  throw new Exception($mysqli->error);
		$answer['success']=0;
		$mysqli->commit();
	}catch (Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "add-takeout":
	try{
		$mysqli->autocommit(false);
		
		$sql="select order_id from {$ob_master} where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false)  throw new Exception($mysqli->error);
		if($rs->num_rows<1){
			$sql="insert into {$ob_master} set ordertype='takeout',order_id='{$_POST['order_id']}',party='{$_SESSION['party']}'";
		
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false)  throw new Exception($mysqli->error);
			if($mysqli->affected_rows<1 && $substr($sql,0,6)=="insert")	throw new Exception("테이크아웃주문이 등록되지 않았습니다.");
		}	
		$mysqli->commit();
		$answer['success']="0";
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	} 
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
	
case "add-delivery":
	try{
		$mysqli->autocommit(false);
		
// 		$sql="select order_id from {$ob_delivery} where order_id='{$_POST['order_id']}'";
		$sql="select order_id from {$ob_master} where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false)  throw new Exception($mysqli->error);
		if($rs->num_rows<1){
			$sql="insert into {$ob_master} set ordertype='delivery',order_id='{$_POST['order_id']}',party='{$_SESSION['party']}'";
			
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);
			if($mysqli->affected_rows<1 && $substr($sql,0,6)=="insert") throw new Exception("배달주문이 등록되지 않았습니다.");
		}
		$mysqli->commit();
		$answer['success']=0;
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "set-contact":
	try{
		$mysqli->autocommit(false);
		
		$sql="select mobile from {$ob_member} where mobile='{$_POST['mobile']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false)  throw new Exception($mysqli->error);
		if($rs->num_rows<1) {
			$mobile="";
		} else $mobile=$_POST['mobile'];
		$ahora=date("YmdHis");
		
		$sql="update {$ob_order} set mobile='{$_POST['mobile']}' where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);

		$sql="update {$ob_master} set mobile='{$_POST['mobile']}' where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);

		switch($_POST['ordertype']){
		case "takeout":
			$strNickname="";
			if(isset($_POST['nickname']) && $_POST['nickname']!="") $strNickname=",nickname='{$_POST['nickname']}'";
			if($mobile==""){
				$sql1="insert into {$ob_member} set mobile='{$_POST['mobile']}',regdate='{$ahora}',updated='{$ahora}'{$strNickname}";
			} else {
				$sql1="update {$ob_member} set updated='{$ahora}'{$strNickname} where mobile='{$_POST['mobile']}'";
			}
			$sql2="update {$ob_master} set mobile='{$_POST['mobile']}',arrival_time='{$_POST['arrival_time']}' where order_id='{$_POST['order_id']}'";
			break;
		case "delivery":
			if($mobile==""){
				$sql1="insert into {$ob_member} set mobile='{$_POST['mobile']}',nickname='{$_POST['nickname']}',address='{$_POST['address']}',regdate='{$ahora}',updated='{$ahora}'";
			} else {
				$sql1="update {$ob_member} set nickname='{$_POST['nickname']}',address='{$_POST['address']}',updated='{$ahora}' where mobile='{$_POST['mobile']}'";
			}
			$sql2="update {$ob_master} set mobile='{$_POST['mobile']}',arrival_time='{$_POST['arrival_time']}' where order_id='{$_POST['order_id']}'";
			break;
		case "book":
			if($mobile==""){
				$sql1="insert into {$ob_member} set mobile='{$_POST['mobile']}',nickname='{$_POST['nickname']}',address='{$_POST['address']}',regdate='{$ahora}',updated='{$ahora}'";
			} else {
				$sql1="update {$ob_member} set nickname='{$_POST['nickname']}',address='{$_POST['address']}',updated='{$ahora}' where mobile='{$_POST['mobile']}'";
			}
			$sql2="update {$ob_master} set mobile='{$_POST['mobile']}',arrival_time='{$_POST['arrival_time']}',howmany={$_POST['howmany']} where order_id='{$_POST['order_id']}'";
		}
		wlog($sql1,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql1);
		if($rs===false)  throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1 && $substr($sql,0,6)=="insert")	throw new Exception("고객연락처를 저장할 수 없습니다.");

		wlog($sql2,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql2);
		if($rs===false)  throw new Exception($mysqli->error);
		
		$answer['success']="0";
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "cancel":
	$sql="select rowid from {$ob_order} where order_id='{$_POST['order_id']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	$nCount=$rs->num_rows;
	try{
		$mysqli->autocommit(false);

		$sql="delete from {$ob_order} where rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($sql." [{$mysqli->error}]");
		--$nCount;
		if($nCount==0){
// 			$sql="delete from {$ob_inhouse} where order_id='{$_POST['order_id']}'";
			$sql="delete from {$ob_master} where order_id='{$_POST['order_id']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false)	throw new Exception($sql." [{$mysqli->error}]");
		}
		$mysqli->commit();
		echo $nCount;
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		echo "-1";
	}
	$mysqli->autocommit(true);
	break;
case "calc-price":
// 	resetLog();
	try {
		$mysqli->autocommit(false);

		$sql="update {$ob_master} set price=(select sum(price) from {$ob_order} where order_id='{$_POST['order_id']}') ".
				"where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows>0) {
			$mysqli->commit();
		}
		$answer['success']=0;
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "new-master":
	try{
		$mysqli->autocommit(false);

		$sql="insert into {$ob_master} set ordertype='{$_POST['ordertype']}',order_id='{$_now}',party='{$_SESSION['party']}',tableno='{$_POST['tblno']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("Nothing added.");

		$mysqli->commit();
		$answer['success']="0";
		$answer['order_id']=$_now;
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "new-order":
	try{
		$mysqli->autocommit(false);
		
		$sql="insert into {$ob_order} set order_id='{$_POST['order_id']}',rowid='{$_now}',name='{$_POST['name']}',price={$_POST['price']}";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("Nothing added.");

		$sql="update {$ob_master} set status='',price=(select sum(price) from {$ob_order} where order_id='{$_POST['order_id']}') where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);

		$mysqli->commit();
		$answer['success']="0";
		$answer['rowid']=$_now;
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;

/*
case "orderfromtablet":
    try{
        $mysqli->autocommit(false);
        if($_POST['order_id']==""){
            $_orderid=$_POST['order_id'];
        } else {
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $_orderid= date('YmdHis'.$micro);
            
            $sql="insert into {$ob_master} set ordertype='tablet',order_id='{$_orderid}',party='{$_POST['party']}',tableno='{$_POST['table']}'";
            wlog($sql,__LINE__,__FUNCTION__,__FILE__);
            $rs=$mysqli->query($sql);
        }
        $price=0.0;
        foreach(explode(";",$_POST['order']) as $k=>$v){
            list($menuname,$cnt)=explode("^",$v);
 
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $_now= date('YmdHis'.$micro);
            
            $sql="select price*{$cnt} from {$ob_menu} where party='{$_POST['party']}' and name='{$menuname}'";
            wlog($sql,__LINE__,__FUNCTION__,__FILE__);
            $rs=$mysqli->query($sql);
            if($rs===false) throw new Exception("failed to execute SQL.");
            if($rs->num_rows<1) throw new Exception("해당 메뉴[{$menuname[$n]}]의 가격을 찾을 수 없습니다.");
            $row=$rs->fetch_array();
            $price+=floatval($row[0]);
            
            $sql="insert into {$ob_order} set order_id='{$_orderid}',rowid='{$_now}',name='{$menuname}',price={$row[0]}";
            wlog($sql,__LINE__,__FUNCTION__,__FILE__);
            $rs=$mysqli->query($sql);
            if($rs===false) throw new Exception($mysqli->error);
            if($mysqli->affected_rows<1) throw new Exception("Failed to insert ORDER.");
        }
        if($price!=0.0){
            $sql="update {$ob_master} set price=price+{$price} where order_id='{$order_id}'";
            wlog($sql,__LINE__,__FUNCTION__,__FILE__);
            $rs=$mysqli->query($sql);
            if($rs===false) throw new Exception($mysqli->error);
        }
        $answer['order_id']=$_orderid;
        $answer['success']="0";
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
        $answer['message']=$e->getMessage();
    }
    $mysqli->autocommit(true);
    break;
*/

case "merge":
	try{
		$mysqli->autocommit(false);
		
		if($_POST['order_id2']==""){	// Move to empty table.
			$sql="update {$ob_master} set tableno='{$_POST['tableno']}',ordertype='inhouse' where order_id='{$_POST['order_id1']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);
			if($mysqli->affected_rows==0) throw new Exception("목표 테이블로 이동실패.");
		} else {		// Merge to working table.
			$sql="update {$ob_order} set order_id='{$_POST['order_id2']}' where order_id='{$_POST['order_id1']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);
			if($mysqli->affected_rows==0) throw new Exception("목표테이블과 병합 실패.");

			$sql="delete from {$ob_master} where order_id='{$_POST['order_id1']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);
			if($mysqli->affected_rows==0) throw new Exception("이전 테이블 삭제 실패.");
			
			$sql="update {$ob_master} set price=(select sum(price) from {$ob_order} where order_id='{$_POST['order_id2']}'),ordertype='inhouse' ".
					"where order_id='{$_POST['order_id2']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);
			if($mysqli->affected_rows==0) throw new Exception("합병후 계산총액 산출실패.");
		}

		$answer['success']=0;
		$mysqli->commit();
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$mysqli->rollback();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "unpaid":
	try {
		$mysqli->autocommit(false);
		
// 		$sql="update {$ob_order} set status='unpaid' where order_id='{$_POST['order_id']}'";
// 		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
// 		$rs=$mysqli->query($sql);
// 		if($rs===false) throw new Exception($mysqli->error);
		
		$sql="update {$ob_master} set status='unpaid',visible='0' where order_id='{$_POST['order_id']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		
		$mysqli->commit();
		$answer['success']="0";
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "payment":
	try{
		$mysqli->autocommit(false);

		$sql="select count(*),count(case status when 'done' then 1 end) from {$ob_order} where order_id='{$_POST['order_id']}'";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception($mysqli->error);
		$row=$rs->fetch_array();
		if()
		if(intval($row[0])>0) throw new Excpetion("처리되지 않은 메뉴가 있으면 결제할 수 없습니다.");
		
		/*
		 * status: '' = ordered, 'done' = processed, 'unpaid' = unpaid(미수금)
		 * 
		 */
// 		$sql="update {$ob_order} set status='2' where order_id='{$_POST['order_id']}'";
// 		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
// 		$rs=$mysqli->query($sql);
// 		if($rs===false)	throw new Exception($sql," [{$mysqli->error}]");
// 		if($mysqli->affected_rows<1) throw new Exception("No updated record.");
			
		$rowid=date("YmdHis");
		$sql="update {$ob_master} set payment_type='{$_POST['payment_type']}',paid={$_POST['actual_price']},".
				"status='paid',pay_time='{$rowid}' where order_id='{$_POST['order_id']}'";
		
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false)	throw new Exception($sql," [{$mysqli->error}]");

		$sql="update {$ob_order} set status='paid' where order_id='{$_POST['order_id']}'";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$mysqli->affected_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception($mysqli->error);
        
        $mysqli->commit();
		$answer['success']="0";
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "remove-menu":
	try{
		$mysqli->autocommit(false);

		$sql="delete from {$ob_menu} where rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("ROWID에 해당하는 메뉴를 찾을 수 없습니다.");
		$answer['success']="0";
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "add-menu":
	try{
		$mysqli->autocommit(false);

		if($_POST['rowid']!=""){
			$sql="update {$ob_menu} set name='{$_POST['name']}',price={$_POST['price']},comment='{$_POST['comment']}',".
					"type='{$_POST['type']}' where rowid='{$_POST['rowid']}'";
		} else {
			$sql="insert into {$ob_menu} set name='{$_POST['name']}',price={$_POST['price']},comment='{$_POST['comment']}',".
					"type='{$_POST['type']}',party='{$_SESSION['party']}',rowid='{$_now}'";
		}
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false ) throw new Exception($mysqli->error);
		if($_POST['rowid']=="" && $mysqli->affected_rows<1) throw new Exception("ROWID에 해당하는 메뉴항목을 찾을 수 없습니다.");
		$mysqli->commit();
		$answer['success']="0";
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "remove-manage":
	try{
		$mysqli->autocommit(false);
		
		$sql="delete from {$ob_admin} where rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("No manager removed.");
		$mysqli->commit();
		$answer['success']=0;
	} catch (Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "add-manage":
    try{
		$mysqli->autocommit(false);
		$sql="select * from {$ob_admin} where userid='{$_POST['userid']}' and (party='{$_SESSION['party']}' or passcode='{$_POST['passcode']}')";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows>0) throw new Exception("같은 아이디가 이미 등록돼있습니다.");
		
		if($_POST['rowid']!=""){
			$sql="update {$ob_admin} set name='{$_POST['username']}',userid='{$_POST['userid']}',mobile='{$_POST['mobile']}',".
					"passcode='{$_POST['passcode']}',admin_level=9 where rowid='{$_POST['rowid']}'";
		} else {
			$sql="insert into {$ob_admin} set name='{$_POST['username']}',userid='{$_POST['userid']}',mobile='{$_POST['mobile']}',".
					"passcode='{$_POST['passcode']}',admin_level=9,party='{$_SESSION['party']}',rowid='{$_now}'";
		}
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($_POST['rowid']=="" && $mysqli->affected_rows<1) throw new Exception("No updated record.");
		$mysqli->commit();
		$answer['success']=0;
	} catch (Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "chgpasscode-user":
	wlog("optype [{$_POST['optype']}]",__LINE__,__FUNCTION__,__FILE__);
	$sql="update {$ob_party} set passcode='{$_POST['passcode']}' where rowid='{$_SESSION['party']}' and passcode='{$_POST['passcode_old']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false){
		echo $mysqli->error;
	} else if($mysqli->affected_rows<1){
		echo "No updated.";
	} else echo "0";
	break;
case "chgpasscode-admin":
	try{
		$mysqli->autocommit(false);

		$sql="update {$ob_admin} set passcode='{$_POST['passcode']}' where party='{$_SESSION['party']}' ".
			"and userid='{$_SESSION['userid']}' and passcode='{$_POST['passcode_old']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($mysqli->affected_rows<1) throw new Exception("No updated.");
		$answer['success']="0";
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "change-mobile":
	try{
		$mysqli->autocommit(false);
	
		$sql="update {$ob_admin} set mobile='{$_POST['mobile']}' where party='{$_SESSION['party']}' ".
				"and userid='{$_SESSION['userid']}' and passcode='{$_POST['passcode']}' and mobile<>'{$_POST['mobile']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
// 		if($mysqli->affected_rows<1) throw new Exception("No updated.");
		$answer['success']="0";
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		$answer['message']=$e->getMessage();
	}
	$mysqli->autocommit(true);
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
case "forgot":
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 8; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	$newpasscode=implode($pass);
	switch($_POST['way']){
		case "send2email":
			$title="OnChaLim : 임시비밀번호 입니다.";
			$sql="select name,mobile from {$ob_admin} where email='{$to}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			try {
				$mysqli->autocommit(false);
				$rs=$mysqli->query($sql);
				if($rs===false) throw new Exception($mysqli->error);
				if($rs->num_rows<1) throw new Exception("등록된 사용자의 이메일주소가 아닙니다.");
				$row=$rs->fetch_assoc();
				
				$msg=$row['name']."님,\n\n임시비밀번호는 [<font color=red>{$newpasscode}</font>] 입니다.\n\n".
						"로그인 후 반드시 새로운 비밀번호로 변경하시기 바랍니다.\n\n".
						"OnChaLim.";
				$retval=mail($to,$title,$msg);
				wlog("{$retval}:{$title}<br>{$msg}",__LINE__,__FUNCTION__,__FILE__);
				if($retval){
					$sql="update {$ob_admin} set passcode='{$newpasscode}' where email='{$to}'";
					if($rs===false) throw new Exception($mysqli->error);
					if($mysqli->affected_rows<1) throw new Exception("임시비밀번호가 등록되지 않았습니다.");
				}
				$mysqli->commit();
				$answer['success']="0";
			} catch(Exception $e){
				$mysqli->rollback();
				wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
				$answer['message']=$e->getMessage();
			}
			$mysqli->autocommit(true);
			break;
		case "send2mobile":
			echo send2mobile($_POST['email']);
	}
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	break;
}
$mysqli->close();
?>