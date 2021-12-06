<?
require_once 'include.php';

$log="";
foreach($_POST as $key=>$value)	{
		$log.="{$key} [{$value}] ";
}
wlog($log,__LINE__,__FUNCTION__,__FILE__);

switch($_POST['optype']){
case "orderfromtablet":
    $mysqli->autocommit(false);
    try{
        if($_POST['order_id']!=""){
            $_orderid=$_POST['order_id'];
        } else {
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $_orderid= date('YmdHis'.$micro);
            
            $sql="insert into {$ob_master} set ordertype='inhouse',order_id='{$_orderid}',party='{$_POST['party']}',tableno='{$_POST['table']}'";
            $rs=$mysqli->query($sql);
            wlog($sql." [".$mysqli->affected_rows."]",__LINE__,__FUNCTION__,__FILE__);
            if($rs===false) throw new Exception("Failed to execute SQL [{$sql}]");
            if($mysqli->affected_rows<1) throw new Exception("No record added .[{$sql}]");
        }
        foreach(explode("^",$_POST['order']) as $k=>$v){
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $_now= date('YmdHis'.$micro);
            
            $menuname=$v;            
            $sql="insert into {$ob_order} set order_id='{$_orderid}',rowid='{$_now}',name='{$menuname}'";
            $rs=$mysqli->query($sql);
            wlog($sql." [".$mysqli->affected_rows."]",__LINE__,__FUNCTION__,__FILE__);
            if($rs===false) throw new Exception($mysqli->error." [{$sql}]");
            if($mysqli->affected_rows<1) throw new Exception("Failed to insert ORDER. [{$sql}]");
        }
        $sql="select count(*), count(case status when 'done' then 1 end) from {$ob_order} where order_id='{$_orderid}'";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception("Failed to execute SQL.");
        if($rs->num_rows<1) throw new Exception("No order found.");
        $row=$rs->fetch_array();
        if(intval($row[0])!=intval($row[1])){
            $status="";
        } else {
            $status="done";
        }
        $sql="update {$ob_master} set status='{$status}',".
                "price=(select sum(a.price) from {$ob_menu} a,{$ob_order} b where a.party='{$_POST['party']}' and b.order_id='{$_orderid}' and a.name=b.name) ".
                "where order_id='{$_orderid}'";
        $rs=$mysqli->query($sql);
        wlog($sql." [".$mysqli->affected_rows."]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception($mysqli->error." [{$sql}]");

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
case "getOrder":
    try {
        $sql="select name,status,count(*) qty from {$ob_order} ".
                "where order_id='{$_POST['order_id']}' group by name,status order by status desc,name";
        wlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($rs===false) throw new Exception("Failed to execute SQL");
        if($rs->num_rows<1) throw new Exception("아직 주문한 내역이 없습니다.");
        $pstr="";
        $answer['order']=array(); 
        while($row=$rs->fetch_assoc()){
            $order=array();
            foreach($row as $k=>$v) $order[$k]=$v;
            array_push($answer['order'],$order);
//             if($pstr!="") $pstr.="</tr>";
//             else $pstr.="<tr>";
//             $pstr.="<td>{$row['name']}</td><td align=center>{$row['qty']}</td><td align=center>";
//             if($row['status']=="") $pstr.="<button id=btnOrderCancel>주문취소</button>";
//             else $pstr.=$row['status'];
//             $pstr.="</td>";
        }
//         if($pstr!="") $pstr.="</tr>";
//         $answer['result']=$pstr;
        
        $sql="select price from {$ob_master} where order_id='{$_POST['order_id']}'";
        $rs=$mysqli->query($sql);
        wlog($sql,__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception("Failed to execute SQL");
        if($rs->num_rows<1) throw new Exception("No total price");
        $row=$rs->fetch_assoc();
        $answer['total']=$row['price'];
        $answer['success']="0";
    } catch(Exception $e) {
        $answer['message']=$e->getMessage();
        wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    }
    break;
case "RemoveOrder":
    $mysqli->autocommit(false);
    try{
        $sql="select * from {$ob_order} where order_id='{$_POST['order_id']}' and name='{$_POST['menu']}'";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception("Failed to execute SQL.");
        if($rs->num_rows<1) throw new Exception("No order record.");
        $row=$rs->fetch_assoc();
        if($row['status']=="done") throw new Exception("이미 처리된 주문은 취소할 수 없습니다.");

        $sql="delete from {$ob_order} where order_id='{$_POST['order_id']}' and name='{$_POST['menu']}' and status='' limit 1";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$mysqli->affected_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception("Failed to Execute SQL");
        if($mysqli->affected_rows<1) throw new Exception("No order deleted.");
        
        $sql="select count(*),count(case status when 'done' then 1 end) from {$ob_order} where order_id='{$_POST['order_id']}'";
        $rs=$mysqli->query($sql);
        wlog($sql,__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception("Failed to execute SQL");
        if($rs->num_rows<1) throw new Exception("No order found.");
        $row=$rs->fetch_array();
        if(intval($row[0])!=intval($row[1])){
            $status="";
        } else {
            $status="done";
        }
        $sql="update {$ob_master} set status='{$status}',".
                "price=(select sum(a.price) from {$ob_menu} a,{$ob_order} b where a.party='{$_POST['party']}' and b.order_id='{$_POST['order_id']}' and a.name=b.name) ".
                "where order_id='{$_POST['order_id']}'";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$mysqli->affected_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception("Failed to execute SQL.");
        
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
        $answer['message']=$e->getMessage();
    }
    $mysqli->autocommit(true);
}
$mysqli->close();
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
?>