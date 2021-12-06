<?
session_start();
$answer=array();
$answer['result']=-1;
$answer['msg']="";
$debug=true;
include_once '../common/_init_.php';
foreach($_POST as $key=>$post ){
    errorlog("{$key} [{$post}]");
}
switch($_POST['optype']){
case "study":
    try{
        $sql="select a.rowid,a.title,a.readcount,a.created,a.good,b.member_name writer ".
                 "from bbs a left outer join a_member b on a.user_rowid=b.member_id ".
               "where a._type='study' order by a.created limit 0,10";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;    
        if($rs->num_rows<1) throw new Exception("");
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "free":
    try{
        $sql="select a.rowid,a.title,a.readcount,a.created,a.good,b.member_name writer ".
                 "from bbs a left outer join a_member b on a.user_rowid=b.member_id ".
               "where a._type='freetalk' order by a.created limit 0,10";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
        if($rs->num_rows<1) throw new Exception("");
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "notice":
    try{
        $sql="select a.rowid,a.title,a.readcount,a.created,a.good,b.member_name writer ".
                 "from bbs a left outer join a_member b on a.user_rowid=b.member_id ".
               "where a._type='notice' order by a.created limit 0,10";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
        if($rs->num_rows<1) throw new Exception("");
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "qna":
    try{
        $sql="select a.rowid,a.title,a.readcount,a.created,a.good,b.member_name writer ".
                 "from bbs a left outer join a_member b on a.user_rowid=b.member_id ".
               "where a._type='qna' order by a.created limit 0,10";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
        if($rs->num_rows<1) throw new Exception("");
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
}
$mysqli->close();
echo json_encode($answer);
?>
