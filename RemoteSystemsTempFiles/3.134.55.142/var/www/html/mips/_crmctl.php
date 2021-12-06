<?
include_once("include.php");
try {
    $sql="select firstpage,level from a_mem_par where member_id='{$_SESSION['member_id']}' and party='{$_POST['_e']}'";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($mysqli->error)  throw new Exception($mysqli->error);
    if($rs->num_rows<1) throw new Exception("지정한 단체에 소속돼있지 않습니다.");
    $row=$rs->fetch_assoc();
    $_SESSION['party']=$_POST['_e'];
    $_SESSION['level']=$row['level'];
    $answer['firstpage']=$row['firstpage'];
    
    if($row['firstpage']==""){
        $answer['firstpage']=getFirstPage($_SESSION['party']);
    }
    errorlog("소속단체 [{$_SESSION['party']}] 권한레벨 [{$_SESSION['level']}] firstPage [{$answer['firstpage']}]",__LINE__,__FUNCTION__,__FILE__);
    $answer['result']=0;
} catch(Exception $e){
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $answer['msg']=$e->getMessage();
}
$mysqli->close();
//errorlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);

function getFirstPage($rowid){
    $rs=sqlrun("select rowid,page_id,firstpage from i_navi where par_rowid='{$rowid}' order by seqno",__LINE__,__FUNCTION__,__FILE__);
    while($row=$rs->fetch_assoc()){
        if($row['page_id']!="" /*&& $row['firstpage']!="0"*/) return $row['page_id'];
    }
    $rs->data_seek(0);
    while($row=$rs->fetch_assoc()){
        $page_id=getFirstPage($row['rowid']);
        if($page_id!="") return $page_id;
    }
}
?>