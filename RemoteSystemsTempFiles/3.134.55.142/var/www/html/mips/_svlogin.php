<?
include_once("include.php");

$log="";
foreach($_POST as $key=>$value)	{
    $log.="{$key} [{$value}] ";
}

errorlog("{$log}",__LINE__,__FUNCTION__,__FILE__);


try{
    $rs=sqlrun("select member_id,member_name,passcode from a_member where userid='{$_POST['userid']}'",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception("등록되지 않은 아이디입니다.");
    while($row=$rs->fetch_assoc()){
        if($row['passcode']==$_POST['passcode']){
            $_SESSION['member_id'] = $row["member_id"];
            $_SESSION['member_name'] = $row['member_name'];
            $_SESSION['superuser']=$row['member_id'];
            // startpage(a_member) should be set by default page that shows personal information to be updated.
            break;
        }
    }
    if(!isset($_SESSION['superuser'])) throw new Exception("비밀번호가 일치하지 않습니다.");
    
    errorlog("superuser [{$_SESSION['superuser']}] startpage [{$startpage}]",__LINE__,__FUNCTION__,__FILE__);
    $rs=sqlrun("select a.rowid,a.party,b.name_kor from a_mem_par a, a_party b where a.member_id='{$_SESSION['superuser']}' and a.party=b.party order by b.name_kor",__LINE__,__FUNCTION__,__FILE__);
    $answer['party']="";
    while($row=$rs->fetch_assoc()){
        if($row['name_kor']=="관리화면") {
            //                 array_push($answer['party'],$row['party'].",".$row['name_kor']);
            $answer['party']=$row['party'].",".$row['name_kor'];
            break;
        }
    }
    $rs=sqlrun("update a_member set last_login=now() where member_id='{$_SESSION['superuser']}'",__LINE__,__FUNCTION__,__FILE__);
    if($mysqli->error!="") throw new Exception($mysqli->error);
    $mysqli->commit();
    $answer['result']="0";
} catch(Exception $e){
    $mysqli->rollback();
    $answer['msg']=$e->getMessage();
    errorlog("{$answer['msg']} -> [{$sql}]",__LINE__,__FUNCTION__,__FILE__);
}
errorlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$mysqli->close();
echo json_encode($answer);
?>
