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
case "all":
    try {
        $sql="select b.* from o_mentoring a, a_member b where mentor='{$_SESSION['member_id']}' order by b.member_name";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="")  {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        if($rs->num_rows<1) {
            $answer['result']=0;
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("등록된 양육자가 없습니다.");
        }
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
    break;
case "list":
    try{
        $sql="select a.rowid,b.member_id,b.member_name,case b.gender when 'm' then '남' when 'f' then '여' else '' end gender,".
                    "ifnull(b.birthday,'') birthday,ifnull(b.mobile,'') mobile, ifnull(a.start_dt,'') start_dt,".
                    "ifnull(a.end_dt,'') end_dt,ifnull(a.graduated,'N') graduated ".
                " from o_mentoring a left outer join a_member b on a.mentee=b.member_id ".
                "where a.mentor='{$_POST['mentor']}' and a.class='mentee' order by a.start_dt";
        $rs=$mysqli->query($sql);
        $answer['sql']=$sql;
        if($mysqli->error!="") {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("{$mysqli->error} [{$sql}]");
        }
        $answer['result']=$rs->num_rows;    
        if($rs->num_rows<1) {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("등록된 양육자가 없습니다.");
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
case "find":
    try {
        if($_POST['mentor']=="")    $mentor=$_SESSION['member_id'];
        else $mentor=$_POST['mentor'];
        
        $sql="select b.member_id,b.member_name,case b.gender when 'm' then '남' when 'f' then '여' else '' end gender,".
                    "ifnull(b.birthday,'') birthday,ifnull(b.mobile,'') mobile, ifnull(a.start_dt,'') start_dt,".
                    "ifnull(a.end_dt,'') end_dt ".
                " from o_mentoring a left outer join a_member b on a.mentee=b.member_id ".
                "where a.mentor='{$mentor}' order by b.member_name ";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="")  {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        if($rs->num_rows <1) {
            $answer['result']=0;
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("등록된 동반자가 없습니다.");
        }
        $answer['result']=$rs->num_rows;
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    }
    break;
case "addnew":
    try{
        $mysqli->autocommit(false);
        $sql="select count(*) cnt from o_mentoring where mentee='{$_POST['mentee']}' and class='mentee'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$rs->num_rows;
        $row=$rs->fetch_assoc();
        if(intval($row['cnt'])>0) {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("이미 등록된 동반자입니다.");
        }
        $rowid=getUnique();
        $sql="insert into o_mentoring(rowid,mentee,mentor,class,created,updated) values ('{$rowid}','{$_POST['mentee']}','{$_POST['mentor']}',".
                "'mentee',date_format(now(),'%Y%m%d%H%i%s'),date_format(now(),'%Y%m%d%H%i%s'))";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $mysqli->query($sql);
        $answer['result']=-1;
        if($mysqli->error!="") {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$mysqli->affected_rows;
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
        if(parseInt($answer['result'])>-1) $answer['result']=-1;
    }
    $mysqli->autocommit(true);
    break;
case "remove":
    try {
        $mysqli->autocommit(false);
        $sql="delete from o_mentoring where rowid='{$_POST['rowid']}' ";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$mysqli->affected_rows;
      
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
    } finally {
        $mysqli->autocommit(true);
        $rs=null;
    }
    break;
case "change":
    try{
        $mysqli->autocommit(false);
        $now=getNOW();
        $sql="update o_mentoring set start_dt='{$_POST['start_dt']}', end_dt='{$_POST['end_dt']}',graduated='{$_POST['graduated']}',".
            "mentor='{$_POST['mentor']}',updated=date_format(now(),'%Y%m%d%H%i%s') where mentee='{$_POST['mentee']}'";
        $mysqli->query($sql);
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        if($mysqli->error!="") {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        
        $answer['result']=$mysqli->affected_rows;
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    }
    $mysqli->autocommit(true);
}
$mysqli->close();
echo json_encode($answer);
?>
