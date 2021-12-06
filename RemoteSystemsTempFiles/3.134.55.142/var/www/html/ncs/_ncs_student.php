<?
include_once '../common/_init_.php';

$answer=array();
$answer['result']=-1;
$answer['msg']="";
$log="";
foreach($_POST as $key=>$value)	{
    $log.="{$key} [{$value}] ";
}
errorlog($log,__LINE__,__FUNCTION__,__FILE__);
switch($_POST['optype']){
case "init":
    try{
        $sql="select * from ncs_config order by period1 desc";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;    
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
        $answer['result']=0;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;

case "class_add":
    try {
        $sql="select count(*) cnt from ncs_config where classcode='{$_POST['class']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        $row=$rs->fetch_assoc();
        if(intval($row['cnt'])>0){
            $sql = "update ncs_config set title='{$_POST['title']}',period1='{$_POST['startdate']}',period2='{$_POST['enddate']}',".    
                    "seat_cnt={$_POST['seatcount']},col_cnt={$_POST['colcount']},alive='{$_POST['alive']}' ".
                    "where classcode='{$_POST['class']}'";
        } else {
            $sql="insert into ncs_config set title='{$_POST['title']}',period1='{$_POST['startdate']}',period2='{$_POST['enddate']}',".
                    "seat_cnt={$_POST['seatcount']},col_cnt={$_POST['colcount']},alive='{$_POST['alive']}',classcode='{$_POST['class']}'";
        }
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$mysqli->affected_rows;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "class_get":
    try {
        $sql = "select * from ncs_config where classcode='{$_POST['class']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $row=$rs->fetch_assoc();
        $answer['data']=$row;
        $answer['result']=0;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "class_remove":
    try{
        $mysqli->autocommit(false);
        $sql="delete from ncs_config where classcode='{$_POST['class']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$mysqli->affected_rows;
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $mysqli->autocommit(true);
        $rs=null;
    }
    break;
case "load_student":
    try {
        $sql="select * from ncs_student where classcode='{$_POST['class']}' order by name";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
        $answer['result']=0;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "student_get":
    try {
        $sql="select * from ncs_student where name='{$_POST['name']}' and classcode='{$_POST['class']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $row=$rs->fetch_assoc();
        $answer['data']=$row;
        $answer['result']=$rs->num_rows;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
      $rs=null;
    }
    break;  
case "student_add":
    try {
        $sql="select count(*) cnt from ncs_student where classcode='{$_POST['class']}' and name='{$_POST['name']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $row=$rs->fetch_assoc();
        if(intval($row['cnt'])>0){
            $sql="update ncs_student set seq={$_POST['seq']}, birth='{$_POST['birth']}',mobile='{$_POST['mobile']}',school='{$_POST['school']}',".
                    "tvid='{$_POST['tvid']}' where name='{$_POST['name']}' and classcode='{$_POST['class']}'";
        } else {
            $sql="insert into ncs_student set seq={$_POST['seq']},birth='{$_POST['birth']}',mobile='{$_POST['mobile']}',school='{$_POST['school']}',".
                    "tvid='{$_POST['tvid']}',name='{$_POST['name']}',classcode='{$_POST['class']}'";
        }
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$mysqli->affected_rows;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
      $rs=null;
    }
    break;  
}
echo json_encode($answer);
$mysqli->close();
?>