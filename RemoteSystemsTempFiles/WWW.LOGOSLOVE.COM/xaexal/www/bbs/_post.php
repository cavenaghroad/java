<?
include_once 'include.php';

$answer=array();
$answer['msg']="";

$pstr=" POST ";
foreach($_POST as $key=>$value){
	$pstr.=" {$key} [{$value}]";
}
wlog($pstr,__LINE__,__FUNCTION__,__FILE__);

if(!isset($_SESSION['userid'])) {
	$answer['msg']="Login first !!!";
	wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
	echo json_encode($answer);
	exit;
}
$now=date("YmdHis");
$t = microtime(true);
$rowid=strtoupper(dechex($now));
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$micro=strtoupper(dechex($micro));
wlog($rowid.":".$micro,__LINE__,__FUNCTION__,__FILE__);
$rowid.=$micro;

switch($_POST['optype']){
case "add-reply":
	$sql="insert into bbs set par_rowid='{$_POST['par_rowid']}',rowid='{$rowid}',content='{$_POST['content']}',userid='{$_SESSION['userid']}',created='{$now}',updated='{$now}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try {
		$mysqli->autocommit(false);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("failed to execute SQL.");
		if($mysqli->affected_rows<1) throw new Exception("Failed to add new reply.");
		
		$sql="update bbs_member set mileage=mileage+(select mileage_reply from bbs_config where bbs='{$_POST['_type']}') where userid='{$_SESSION['userid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false || $mysqli->affected_rows<1) throw new Exception("Mileage is not updated.");
		
		$mysqli->commit();
		
	} catch(Exception $e) {
		$mysqli->rollback();
		$answer['msg']=$e->getMessage();
		wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	$answer['rowid']=$rowid;
	$answer['created']=$now;
	break;
case "del-reply":
	$answer['retval']="-1";
	try{
		$mysqli->autocommit(false);
		$sql="select count(*) from bbs where par_rowid='{$_POST['rowid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("Failed to execute SQL.");
		if($rs->num_rows<1) throw new Exception("This reply has no parent content.");
		$row=$rs->fetch_assoc();
		if(intval($row[0])<1){
			$sql="delete from bbs where rowid='{$_POST['rowid']}'";
		} else {
			$sql="update bbs set content='[삭제된 댓글입니다.]' where rowid='{$_POST['rowid']}'";
		}
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception("Failed to execute SQL.");
		if($mysqli->affected_rows<1) throw new Exception("Failed to delete non-existing reply.");
		
		$sql="update bbs_member set mileage=mileage+(select mileage_reply from bbs_config where bbs='{$_POST['_type']}') where userid='{$_SESSION['userid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false || $mysqli->affected_rows<1) throw new Exception("Mileage is not updated.");
		
		$answer['retval']="0";
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['msg']=$e->getMessage();
		wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	break;
case "del-image":
    if($_POST['fileid']=="") break;
    $aFile=explode(",",$_POST['fileid']);
    $filelist=implode("','",$aFile);
    $mysqli->autocommit(false);
    try{
        $sql="delete from bbs_picture where filename in ('{$_POST['fileid']}') or filename='' ";
        wlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($rs===false) throw new Exception("Failed to execute SQL.");

        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
        $answer['msg']=$e->getMessage();
    }
    $mysqli->autocommit(true);
    foreach($aFile as $key=>$filename){
        $result=unlink("./picture/".$_POST['fileid']);
        if(!$result){
            $answer['msg'].="{$filename} is not delete.\n";
        }
    }
    break;
case "like":
    switch($_POST['like']){
    case "yes": //like
        $usertype="like_user";
        $typename="좋아요";
        break;
    case "no":  // hate
        $usertype="hate_user";
        $typename="싫어요";
        break;
    }
    $sql="select {$usertype} from bbs where rowid='{$_POST['rowid']}';
    wlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $mysqli->autocommit(false);
    try{
        $rs=$mysqli->query($sql);
        if($rs===false) throw new Exception("Failed to execute SQL.");
        if($rs->num_rows<1) throw new Exception("Can not find this post.");
        $row=$rs->fetch_array();
        $userlist=$row[0];
        $n=strpos($userlist,$_SESSION['user_rowid']);
        if($n===false)  {
            if($userlist!="") $userlist.=",";
            $userlist.=$_SESSION['user_rowid'];
            $answer['msg']="+1";
        }  else {
            $userlist=str_replace($_SESSION['user_rowid'],"",$userlist);
            $userlist=str_replace(",,",",",$userlist);
            $answer['msg']="-1";
        }
        $sql="update bbs set {$usertype}='{$userlist}' where rowid='{$_POST['rowid']}'";
        wlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($rs===false) throw new Exception("Failed to execute SQL.");
        
        
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
        $answer['msg']=$e->getMessage();
    }
    $mysqli->autocommit(true);    
}
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);


?>