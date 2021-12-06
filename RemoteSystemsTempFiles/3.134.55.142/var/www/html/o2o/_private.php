<?
session_start();
$answer=array();
$answer['result']=-1;
$answer['msg']="";
if(!isset($_SESSION['member_id'])){
    $answer['result']=-99;
    $answer['msg']="Login first, please.";
    echo json_encode($answer);
    exit;
}
include_once '../common/_init_.php';
$debug=true;
switch($_POST['optype']){
case "private":
    try{
        $sql="select * from a_member a left outer join private_{$_SESSION['party']} b on a.member_id=b.member_id where a.member_id='{$_SESSION['member_id']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
        if($rs->num_rows<1) throw new Exception("No information");
        $answer['data']=$rs->fetch_assoc();
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
    } finally {
        $rs=null;
    }
    break;
case "country":
    try {
        $sql="select distinct country from ipaddr order by country";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
        if($rs->num_rows<1) throw new Exception("No country.");
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
    } finally {
        $rs=null;
    }
    break;
case "update":
    try {
        $mysqli->autocommit(false);
        if($_POST['tbl']=="private") {
            $tblname="private_{$_SESSION['party']}";
            $sql="select count(*) cnt from {$tblname} where member_id='{$_SESSION['member_id']}'";
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            $rs=$mysqli->query($sql);
            if($mysqli->error) throw new Exception($mysqli->error);
            $row=$rs->fetch_assoc();
            if(intval($row['cnt'])<1){
                $sql="insert into {$tblname} set member_id='{$_SESSION['member_id']}',{$_POST['column']}='{$_POST['value']}'";
            } else {
                $sql="update {$tblname} set {$_POST['column']}='{$_POST['value']}' where member_id='{$_SESSION['member_id']}'";
            }
        } else {
            $tblname="a_member";
            $sql="update {$tblname} set {$_POST['column']}='{$_POST['value']}' where member_id='{$_SESSION['member_id']}'";
        }
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$mysqli->affected_rows;

        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) {
            $answer['msg'].=" [{$sql}]";
            errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
        }
    } finally {
        $mysqli->autocommit(true);
        $rs=null;
    }
}
$mysqli->close();
echo json_encode($answer);
?>