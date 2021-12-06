<?
/*
 *     교역자 | mentor | 양육자 후보    <= 양육자반 layout
 *     양육자 | mentee | 동반자           <= 동반자반 layout
 */
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
case "getMentor":
    try {
        $sql="select b.member_name,ifnull(a.graduated,'') graduated,ifnull(a.start_dt,'') start_dt,ifnull(a.end_dt,'') end_dt ".
                "from o_mentoring a left outer join a_member b on a.mentor=b.member_id ".
                "where a.mentee='{$_POST['member_id']}' and a.class='mentee' ";
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
    } finally {
        $rs=null;
    }
    break;
case "getMentee":
    try {
        $sql="select a.rowid,a.mentee,b.member_name,case b.gender when 'f' then '여' when 'm' then '남' else '-' end gender,".
            "ifnull(c.birthday,'') birthday,ifnull(b.mobile,'') mobile,ifnull(a.graduated,'N') graduated,".
            "ifnull(a.start_dt,'') start_dt,ifnull(a.end_dt,'') end_dt ".
            "from o_mentoring a left outer join a_member b on a.mentee=b.member_id ".
            "left outer join private_{$_SESSION['party']} c on a.mentee=c.member_id ".
            "where a.mentor='{$_POST['member_id']}' and a.class='mentee' ";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) {
            $answer['errcode']=$mysqli->errno;
            throw new Exception($mysqli->error);
        }
        $answer['result']=$rs->num_rows;
        if($rs->num_rows<1) throw new Exception("");
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
case "getPastor":
    try {
        $sql="select b.member_name,ifnull(a.graduated,'N') graduated,ifnull(a.start_dt,'') start_dt,ifnull(a.end_dt,'') end_dt ".
            "from o_mentoring a left outer join a_member b on a.mentor=b.member_id ".
            "where a.mentee='{$_POST['member_id']}' and a.class='mentor' ";
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
    } finally {
        $rs=null;
    }
    break;
case "list":    // admin.js,index.js 두군데서만 사용함.
    try {
        $mentor="";
        if(isset($_POST['mentor'])) $mentor="a.mentee='{$_POST['member_id']}' and ";
        $sql="select a.mentee mentor,c.member_name,case c.gender when 'm' then '남' when 'f' then '여' else '' end gender,".
                "ifnull(c.birthday,'') birthday,ifnull(c.mobile,'') mobile,ifnull(a.start_dt,'') start_dt,ifnull(a.end_dt,'') end_dt,".
                "ifnull(a.graduated,'N') graduated,ifnull(d.member_name,'') pastor, ifnull(d.member_id,'') pastor_id".
                " from o_mentoring a left outer join a_member d on a.mentor=d.member_id,  a_mem_par b, a_member c ".
                "where {$mentor} a.class='mentor' and a.mentee=b.member_id and b.party='{$_SESSION['party']}' ".
                "and a.mentee=c.member_id";
        $rs=$mysqli->query($sql);
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        if($mysqli->error) {
            errorlog($mysqli->error."/".$sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("{$mysqli->error}");
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
        errorlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
    } finally {
        $rs=null;
    }
    break;
case "remove":
    try {
        $mysqli->autocommit(false);
        $sql="select count(*) cnt from o_mentoring where mentor='{$_POST['mentor']}' ";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        
        $answer['result']=$rs->num_rows;
        $row=$rs->fetch_assoc();
        if(intval($row['cnt'])>0) {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception("동반자가 있는 양육자는 동반자 먼저 삭제해야 합니다.");
        }
        $sql="delete from o_mentoring where mentee='{$_POST['mentor']}' and class='mentor'";
        $mysqli->query($sql);
        if($mysqli->error!="") {
            errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$mysqli->affected_rows;
        if($mysqli->affected_rows>0){
            $answer['msg']="삭제됐습니다.";
        } else {
            $answer['msg']="삭제된 양육자가 없습니다.";
        }
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [ {$sql} ]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally{
            $rs=null;
            $mysqli->autocommit(true);
    }
    break;
case "addnew":
    try{
        $mysqli->autocommit(false);
        $rowid=getUnique();
        $sql="insert into o_mentoring(rowid,mentee,mentor,class,created,updated) values ('{$rowid}','{$_POST['mentor']}','','mentor',date_format(now(),'%Y%m%d%H%i%s'),date_format(now(),'%Y%m%d%H%i%s'))"; 
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
    }
    $mysqli->autocommit(true);
    break;
case "change":    
    try{
        $mysqli->autocommit(false);
        $now=getNOW();
        $sql="update o_mentoring set start_dt='{$_POST['start_dt']}', end_dt='{$_POST['end_dt']}',graduated='{$_POST['graduated']}',".
            "mentor='{$_POST['pastor']}',updated=date_format(now(),'%Y%m%d%H%i%s') where mentee='{$_POST['mentor']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $mysqli->query($sql);
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
    } finally {
        $mysqli->autocommit(true);
    }
    break;
case "filter":
    errorlog("filter [{$token}]",__LINE__,__FUNCTION__,__FILE__);
    switch($_POST['filter']){
    case "feed":
        $sql="select distinct mentor member_id from o_mentoring a where class='mentee' and graduated='N'";
        break;
    case "wait":
        $sql="select distinct a.mentor member_id from o_mentoring a, private_{$_SESSION['party']} b where a.mentor=b.member_id and b.status='wait' ";
        break;
    case "stop":
        $sql="select distinct a.mentor member_id from o_mentoring a, private_{$_SESSION['party']} b where a.mentor=b.member_id and b.status='stop' ";
        break;
    case "inclass":
        $sql="select distinct mentee member_id from o_mentoring a where class='mentor' and graduated='N' ";
        break;
    case "graduated":
        $sql="select distinct mentee member_id from o_mentoring a where class='mentor' and graduated='Y' ";
        break;
    }
    try {
        $rs=$mysqli->query($sql);
        if($mysqli->error) {
            throw new Exception($mysqli->error." [".$mysqli->errno."]");
        }
        $answer['result']=$rs->num_rows;
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row['member_id']);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
}
$mysqli->close();
echo json_encode($answer);
?>