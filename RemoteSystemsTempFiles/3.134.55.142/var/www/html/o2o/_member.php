<?
session_start();
$answer=array();
$answer['result']=-1;
$answer['msg']="";
if(!isset($_SESSION['member_id'])){
    $answer['result']=-99;
    $answer['msg']="Login first, please.";
    echo json_encode($answer);
    return;
}
$debug=true;
include_once '../common/_init_.php';
foreach($_POST as $key=>$post ){
    errorlog("{$key} [{$post}]");
}
switch($_POST['optype']){
case "search":
    try {
        $search="";
        if(!isset($_POST['search']) || $_POST['search']==""){
            $sql="select count(*) cnt from a_member a,a_mem_par b ".
                    "where b.party='{$_SESSION['party']}' and a.member_id=b.member_id ".
                    " and a.member_id not in (select mentee from o_mentoring where class='mentor') ".
                    " and a.member_id not in (select mentor from o_mentoring where class='mentee')";
            $rs=$mysqli->query($sql);
            if($mysqli->error!="") {
                errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
                throw new Exception($mysqli->error);
            }
            $answer['result']=$rs->num_rows;
            $row=$rs->fetch_assoc();
            if(intval($row['cnt'])>=100) {
                error_log($sql,__LINE__,__FUNCTION__,__FILE__);
                throw new Exception("검색대상자가 100명이 넘을 경우, 이름의 일부를 입력해야 검색이 됩니다.");
            } 
        } else {
            $search="a.member_name like '%{$_POST['search']}%' and ";
        }
        // Finding who is neither a mentor nor a mentee.
        $sql="select a.member_id,a.member_name,case a.gender when 'm' then '남' when 'f' then '여' else '' end gender,".
            "ifnull(a.birthday,'') birthday,ifnull(a.mobile,'') mobile ".
            "from a_member a,a_mem_par b ".
            "where {$search} a.member_id=b.member_id ".
            " and b.party='{$_SESSION['party']}' ".
            " and a.member_id not in (select mentee from o_mentoring where class='mentor') ".
            " and a.member_id not in (select mentor from o_mentoring where class='mentee')";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") {
            error_log($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$rs->num_rows;
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        error_log($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    }
    break;
case "pastor":
    try{
        $sql="select a.member_id pastor_id,a.member_name pastor_name,".
                "case a.gender when 'm' then '남' when 'f' then '여' else '' end gender,".
                "ifnull(a.birthday,'') birthday,ifnull(a.mobile,'') mobile ".
                "from a_member a,a_mem_par b ".
                "where a.member_id=b.member_id ".
                "   and a.member_name like '%{$_POST['pastor']}%' and b.party='{$_SESSION['party']}' order by a.member_name";
        $rs=$mysqli->query($sql);
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        if($mysqli->error!="") throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;    
        if($rs->num_rows<1) throw new Exception("No pastor");
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    } finally {
        $rs->close();
    }
    break;
case "searchMentee":
    try {
        $search="";
        if($_POST['search']==""){
            $sql="select count(*) cnt ".
                    "from a_member a,a_mem_par b ".
                    "where a.member_id=b.member_id and b.party='{$_SESSION['party']}'".
                    " and a.member_id not in (select mentor from o_mentor)";
            $rs=$mysqli->query($sql);
            if($mysqli->error!="") {
                error_log($sql,__LINE__,__FUNCTION__,__FILE__);
                throw new Exception($mysqli->error);
            }
            $answer['result']=$rs->num_rows;
            $row=$rs->fetch_assoc();
            if(intval($row['cnt'])>=100) {
                error_log($sql,__LINE__,__FUNCTION__,__FILE__);
                throw new Exception("대상자가 100명 이상이면 검색해서 찾아야 합니다.");
            }
        } else {
            $search="a.member_name like '%{$_POST['search']}%' and ";
        }
        $sql="select a.member_id,a.member_name,case a.gender when 'm' then '남' when 'f' then '여' else '' end gender,".
            "ifnull(a.birthday,'') birthday,ifnull(a.mobile,'') mobile ".
                " from a_member a, a_mem_par b ".
                "where {$search} a.member_id=b.member_id and b.party='{$_SESSION['party']}'".
                " and a.member_id not in (select mentor from o_mentor)";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") {
            error_log($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$rs->num_rows;
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
    } catch(Exception $e){
        error_log($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
        $answer['msg']=$e->getMessage();
    } finally {
        $rs=null;
    }
    break;
case "member":
    try {
        $sql="select a.member_id,a.member_name,case a.gender when 'm' then '남' when 'f' then '여' end gender,".
                "ifnull(a.birthday,'') birthday,ifnull(a.mobile,'') mobile ".
                "from a_member a, a_mem_par b ".
                "where a.member_id=b.member_id and b.party='{$_SESSION['party']}' and b.o2o_apply is null ".
                "order by a.member_name";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
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
case "saint":
    try{
        $sql="select a.member_id,a.member_name,case a.gender when 'm' then '남' when 'f' then '여' end gender,".
                "ifnull(a.birthday,'') birthday,ifnull(a.mobile,'') mobile,ifnull(o2o_apply,'') o2o_apply,".
                "b.regdate,b.retire_dt ".
                "from a_member a, a_mem_par b ".
                "where a.member_id=b.member_id and b.party='{$_SESSION['party']}' and b.regdate<>'' ".
                "order by a.member_name";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;    
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
case "remove":    
    try{
        $mysqli->autocommit(false);
        $sql="delete from a_mem_par where member_id='{$_POST['member_id']}'";
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
case "addnew":
    try{
        $mysqli->autocommit(false);
        $rowid=getUnique(); 
        errorlog($rowid,__LINE__,__FUNCTION__,__FILE__);
        $sql="insert into a_member set member_id='{$rowid}',member_name='{$_POST['name']}',".
                "gender='{$_POST['gender']}',birthday='{$_POST['birthday']}',mobile='{$_POST['mobile']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $sql="insert into a_mem_par set rowid='{$rowid}', member_id='{$rowid}',regdate='{$_POST['regdate']}',".
                "retire_dt='{$_POST['retire_dt']}',party='{$_SESSION['party']}',o2o_apply='{$_POST['o2o_apply']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['member_id']=$rowid;
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
case "change":
    try{
        $mysqli->autocommit(false);
        $sql="update a_member set member_name='{$_POST['name']}',gender='{$_POST['gender']}',".
                "birthday='{$_POST['birthday']}',mobile='{$_POST['mobile']}' ".
                "where member_id='{$_POST['member_id']}' ";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $sql="update a_mem_par set regdate='{$_POST['regdate']}',retire_dt='{$_POST['retire_dt']}',".
                "o2o_apply='{$_POST['o2o_apply']}' ".
            "where member_id='{$_POST['member_id']}' ";
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
case "mentee":
    try {
        $sql="select a.member_id,a.member_name,case a.gender when 'm' then '남' when 'f' then '여' end gender,".
            "ifnull(a.birthday,'') birthday,ifnull(a.mobile,'') mobile,ifnull(b.o2o_apply,'') o2o_apply ".
            "from a_member a, a_mem_par b ".
            "where a.member_id=b.member_id and b.party='{$_SESSION['party']}' and b.o2o_apply is not null ".
            "order by a.member_name";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;
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
case "addMentee":
    try {
        $mysqli->autocommit(false);
        $sql="update a_mem_par set o2o_apply=date_format(now(),'%Y-%m-%d') where member_id='{$_POST['member_id']}' ";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) {
            $answer['errcode']=$mysqli->errno;
            throw new Exception($mysqli->error);
        }
        $mysqli->commit();
        $answer['result']=0;
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
    } finally {
        $mysqli->autocommit(true);
        $rs=null;
    }
    break;
case "removeMentee":
    try {
        $mysqli->autocommit(false);
        $sql="update a_mem_par set o2o_apply=null where member_id='{$_POST['member_id']}' ";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) {
            $answer['errcode']=$mysqli->errno;
            throw new Exception($mysqli->error);
        }
        $mysqli->commit();
        $answer['result']=0;
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        if($debug) $answer['msg'].=" [{$sql}]";
    } finally {
        $mysqli->autocommit(true);
        $rs=null;
    }
    break;
}
$mysqli->close();
echo json_encode($answer);
?>
