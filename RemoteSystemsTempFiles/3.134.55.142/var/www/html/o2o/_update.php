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
include_once '../common/_init_.php';
switch($_POST['optype']){
case "tblMentor": case "tblMentee":
    try {
        $mysqli->autocommit(false);
        switch($_POST['column']){
        case "5":
            $colname="start_dt"; break;
        case "6":
            $colname="end_dt"; break;
        case "7":
            $colname="mentor"; break;
        default:
            break;
        }
        if($_POST['optype']=="tblMentor")   $class="mentor";
        else $class="mentee";
        
        $sql="update o_mentoring set {$colname}='{$_POST['newval']}' where mentee='{$_POST['mentor_id']}' and class='{$class}'";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") {
            error_log($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$mysqli->affected_rows;
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        error_log($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $mysqli->autocommit(true);
        $rs=null;
    }
    break;
case "tblHistory":
    try {
        $mysqli->autocommit(false);
        $sql="update o_history set location='{$_POST['newval']}' where rowid='{$_POST['rowid']}' and meet_dt='{$_POST['meed_dt']}'";
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") {
            error_log($sql,__LINE__,__FUNCTION__,__FILE__);
            throw new Exception($mysqli->error);
        }
        $answer['result']=$mysqli->affected_rows;
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        error_log($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $mysqli->autocommit(true);
        $rs=null;
    }
}
$mysqli->close();
echo json_encode($answer);
?>