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
$debug=true;
include_once '../common/_init_.php';
switch($_POST['optype']){
case "list":
    try{
        $sql="select a.hist_id,a.rowid,a.meet_dt,a.location,a.chapter from  o_history a ".
                "where a.rowid='{$_POST['rowid']}' order by a.meet_dt";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$rs->num_rows;    
        if($rs->num_rows<1) {
            error_log($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("등록된 일정이 없습니다.");
        }
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "addnew":
    try {
        $mysqli->autocommit(false);
        $hist_id=getUnique();
        $sql="insert into o_history set hist_id='{$hist_id}',rowid='{$_POST['rowid']}', meet_dt='{$_POST['meet_dt']}', ".
                "location='{$_POST['location']}', chapter='{$_POST['chapter']}',".
                "created=date_format(now(),'%Y%m%d%H%i%s'),updated=date_format(now(),'%Y%m%d%H%i%s')";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error){
            $answer['result']=-1;
            $answer['errcode']=$mysqli->errno;
            errorlog("[{$mysqli->errno}] {$mysqli->error}",__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("[{$mysqli->errno}] {$mysqli->error}");
        }
        $answer['result']=$mysqli->affected_rows;
        $mysqli->commit();
    } catch(Exception $e) {
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally{
        $rs=null;
        $mysqli->autocommit(true);
    }
    break;
case "remove":
    try {
        $mysqli->autocommit(false);
        $sql="delete from o_history where hist_id='{$_POST['hist_id']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) {
            $answer['result']=-1;
            throw new Exception("[{$mysqli->errno}] {$mysqli->error}");
        }
        $answer['result']=$mysqli->affected_rows;
        $mysqli->commit();
    } catch(Exception $e) {
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
        $mysqli->autocommit(true);
    }
    break;
case "all":
       try {
           $sql="select b.* from o_mentoring a,o_history b ".   
                    "where a.mentor='{$_POST['member_id']}' and a.class='mentee' and a.rowid=b.rowid order by b.meet_dt" ;
           errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
           $rs=$mysqli->query($sql);
           if($mysqli->error) throw new Exception("[{$mysqli->errno}] {$mysqli->error}");
           $answer['result']=$rs->num_rows;
           $answer['data']=array();
           while($row=$rs->fetch_assoc()){
               array_push($answer['data'],$row);
           }
       } catch(Exception $e){
           $answer['msg']=$e->getMessage();
           if($debug) $answer['msg'].=" [ {$sql} ]";
           errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
       } finally {
           $rs=null;
       }
}
$mysqli->close();
echo json_encode($answer);
?>