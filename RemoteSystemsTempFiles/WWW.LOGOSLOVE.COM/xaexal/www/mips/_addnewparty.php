<?
include_once("include.php");

$wlog=new Logging();

$mysqli->autocommit(false);
try {
    if(!isset($_SESSION['member_id'])) throw new Exception("Login First.");
    
    $rs=sqlrun("select count(*) cnt from a_party where name_kor like '%{$_POST['partyname']}%'",__LINE__,__FUNCTION__,__FILE__);
    $row=$rs->fetch_assoc();
    if(intval($row['cnt'])>0) throw new Exception(_label("same party",$_SESSION['lang']));
    
    $rs=sqlrun("select menu_id from a_party where sector='{$_POST['sector']}' order by party limit 1",__LINE__,__FUNCTION__,__FILE__);
    $row=$rs->fetch_assoc();
    $menu_id=$row['menu_id'];
    $_now=getROWID();
    
    $rs=sqlrun("insert into a_party set party='{$_now}',name_kor='{$_POST['partyname']}',sector='{$_POST['sector']}',".
        "createdby='{$_SESSION['member_id']}',updatedby='{$_SESSION['member_id']}',".
        "created=now(),updated=now(),menu_id='{$menu_id}'",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception(_label("fail2add",$_SESSION['lang']));
    
    $rs=sqlrun("insert into a_mem_par set rowid='{$_now}',party='{$_now}',member_id='{$_SESSION['member_id']}',".
        "level=10,membership='Administrator',time_login=now(),created=now(),updated=now()",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception(_label("fail2add",$_SESSION['lang']));
    
    $mysqli->commit();
    $answer['result']=0;
    $answer['msg']=_label("success2add",$_SESSION['lang']);
    $answer['party']=$_now;
} catch(Exception $e) {
    $mysqli->rollback();
    $answer['msg']=$e->getMessage();
    $wlog->write($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
}
$mysqli->autocommit(true);
}
$wlog->write(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
$mysqli->close();
?>