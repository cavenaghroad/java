<?
$answer=array();
$answer['result']=-1;
$answer['msg']="";
$debug=true;
include_once '../common/_init_.php';
foreach($_POST as $key=>$post ){
    errorlog("{$key} [{$post}]");
}
switch($_POST['optype']){
case "view":
    try {
        $rs=viewPost($_POST['rowid']);
        if($rs->num_rows<1) throw new Exception("찾을 수 없는 게시물입니다.");
        $row=$rs->fetch_assoc();
        $answer['result']=$rs->num_rows;
        $answer['data']=$row;
        $answer['updatable']='0';
        errorlog($_SESSION['userid']."/".$row['user_rowid'],__LINE__,__FUNCTION__,__FILE__);
        if($_SESSION['user_rowid']==$row['user_rowid']){
            $answer['updatable']='1';
        } 
        $sql="update bbs set readcount=readcount+1 where rowid='{$_POST['rowid']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        errorlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "viewlist":
    try {
        $mysqli->autocommit(false);

        $sql="select count(*) cnt_all from bbs where _type='{$row['_type']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        $row=$rs->fetch_assoc();
        
        $sql="select count(*) cnt from bbs where _type='{$row['_type']}' and rowid>='{$rowid}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        $row=$rs->fetch_assoc();
        errorlog("cnt [{$row['cnt']}]",__LINE__,__FUNCTION__,__FILE__);
        $cnt=$row['cnt'];
        $answer['turn']=$cnt;
        $sql="select a.rowid,a.created,a.title,a.readcount,a.good,ifnull(b.member_name,'') writer ".
                "from bbs a left outer join bbs_member b on a.user_rowid=b.rowid ".
                "order by a.rowid desc limit {$cnt},30";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        $answer['list']=array();
        
        $region="Asia/Seoul";
        $dt = new DateTime("now", new DateTimeZone($region));
        $today= $dt->format('Ymd');
        $thisYear=substr($today,0,4);
        
        while($row=$rs->fetch_assoc())  {
            if(substr($row['created'],0,8)==$today) {
                $dt=substr($row['created'],8);
                $row['created']=substr($dt,0,2).":".substr($dt,3,2);
            } else if(substr($row['created'],0,4)==$thisYear){
                $dt=substr($row['created'],4);
                $row['created']=substr($dt,4,2)."-".substr($dt,5,2);
            } else {
                $dt=substr($row['created'],0,8);
                $row['created']=substr($dt,0,4)."-".substr($dt,4,2)."-".substr($dt,6);
            }
            array_push($answer['list'],$row);
        }
        $mysqli->commit();
    } catch(Exception $e) {
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
        errorlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
    }
}
$mysqli->close();
echo json_encode($answer);

function viewPost($rowid){
    global $mysqli;
    
    $sql="select a.title,a.content,a.readcount,a.good,bad,a.created,a.user_rowid,ifnull(b.member_name,'-') writer,".
        "ifnull(a.like_user,'') like_user,ifnull(a.hate_user,'') hate_user,ifnull(a.legend,'') legend,a._type ".
        "from bbs a left outer join bbs_member b on a.user_rowid=b.rowid ".
        "where a.rowid='{$rowid}'";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    return $mysqli->query($sql);
}
?>