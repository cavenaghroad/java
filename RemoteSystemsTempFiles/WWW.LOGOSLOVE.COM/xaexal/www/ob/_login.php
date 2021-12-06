<?
@session_start();
require_once 'include.php';

$log="";
switch($_POST['optype']){
case "login":
	resetlog();
	try{
		$sql="select b.rowid,b.party,a.admin_level,a.mobile from {$ob_admin} a left outer join {$ob_party} b on a.party=b.rowid ".
				"where a.mobile='{$_POST['mobile']}' and a.passcode='{$_POST['passcode']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows<1) throw new Exception("사용자 아이디가 존재하지 않거나<br>비밀번호가 일치하지 않습니다.");
		
		$_SESSION['mobile']=$_POST['mobile'];
		
		// throw exception to display dialog box to let user choose one of parties on the screen when the belonged parties are more than 1.
        if($rs->num_rows>1) {
            $user=array(); $answer['user']=array();
            while($row=$rs->fetch_assoc()){
                foreach($row as $k=>$v) $user[$k]=$v;
                array_push($answer['user'],$user);
            }
            $answer['success']="2";
            throw new Exception("2개 이상의 단체/회사에 소속돼있습니다. 하나를 선택하여 로긴하십시오.");
        }
        
		$row=$rs->fetch_assoc();

		$_SESSION['party']=$row['rowid'];
		$_SESSION['partyname']=$row['party'];
		$_SESSION['admin_level']=$row['admin_level'];
		$answer['success']="0";
	} catch(Exception $e){
	    $answer['message']=$e->getMessage();
	    wlog($sql."=>".$e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	    wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);
	}
	break;
case "choose-party":
	$answer['party']=$_SESSION['party'];
	if(!isset($_SESSION['party'])) $_SESSION['party']=$_POST['rowid'];
	if(!isset($_SESSION['admin_level'])) $_SESSION['admin_level']=$_POST['admin_level'];
	if(!isset($_SESSION['partyname'])) $_SESSION['partyname']=$_POST['partyname'];
	
	try{
	    $mysqli->autocommit(false);
	    
		$sql="select business_start from {$ob_party} where rowid='{$_SESSION['party']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);

		$row=$rs->fetch_assoc();
		$_SESSION['business_start']=str_replace(":","",$row['business_start']);

		$sql="select b.mobile email,b.name,a.mid,a.mkey,b.mobile,a.mid,a.mkey,a.service_expiry from {$ob_party} a, {$ob_admin} b ".
				"where a.rowid='{$_SESSION['party']}' and b.mobile='{$_SESSION['mobile']}' and a.rowid=b.party";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows<1) throw new Exception($mysqli->error);

		$row=$rs->fetch_assoc();
		$_SESSION['service_expiry']=$row['service_expiry'];
		$_SESSION['name']=$row['name'];
		wlog("SESSION service_expiry [{$_SESSION['service_expiry']}]",__LINE__,__FUNCTION__,__FILE__);

		$answer['name']=$row['name']."-".$_SESSION['party'];

		$sql="insert into {$ob_login} set actiontime='{$_now}',party='{$_SESSION['party']}',mobile='{$_POST['mobile']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);

		$answer['success']="0";
		$mysqli->commit();
	} catch(Exception $e){
		$mysqli->rollback();
		$answer['message']=$e->getMessage();
		wlog($sql."=>".$e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	$mysqli->autocommit(true);
	$log="";
	wlog(is_array($_SESSION),__LINE__,__FUNCTION__,__FILE__);
	if(is_array($_SESSION)){
		foreach($_SESSION as $key=>$value)	$log.="SESSION {$key} [{$value}]<br>";
	}
	wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);
	break;
case "logout":
	try{
// 		$sql.="update {$ob_login} set logout_time='{$_now}' where party='{$_SESSION['party']}'".
// 				" and login_time =(select max(login_time) from {$ob_login} where party='{$_SESSION['party']}')";
		$sql="insert into {$ob_login} set action='logout',actiontime='{$_now}',party='{$_SESSION['party']}',mobile='{$_SESSION['uesrid']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);

		session_unset();
		$answer['success']="0";
	} catch(Exception $e){
		$answer['message']=$e->getMessage();
		wlog($sql."=>".$e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
	}
	break;
case "register":
	try{
		$mysqli->autocommit(false);
		
		$sql="select mobile from {$ob_admin} where mobile='{$_POST['mobile']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows>0) $sameuser=true;
		else $sameuser=false;
		
		$sql="select regnum from {$ob_party} where regnum='{$_POST['regnum']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows>0) {
			if($sameuser) throw new Exception("이미 등록된 업체입니다. 바로 로긴하고 사용하면 됩니다.");
			else throw new Exception("같은 사업자등록번호의 업체가 이미 등록돼있습니다. 다른 이름을 선택하십시오.");
		}
		
		$sql="insert into {$ob_party} set rowid='{$_now}',party='{$_POST['party']}',regnum='{$_POST['regnum']}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs===false) throw new Exception($mysqli->error);
		
		if(!$sameuser){
			$sql="insert into {$ob_admin} set rowid='{$_now}',party='{$_now}',name='{$_POST['name']}',mobile='{$_POST['mobile']}',".
					"passcode='{$_POST['passcode']}',mobile='{$_POST['mobile']}',admin_level=0,email='{$_POST['email']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false) throw new Exception($mysqli->error);
		}
		wlog("affected_rows [{$mysqli->affected_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($mysqli->affected_rows!=1) throw new Exception("affected_rows [{$mysqli->affected_rows}]");
		$mysqli->commit();
		echo "0";
	} catch(Exception $e){
		$mysqli->rollback();
		wlog("[{$sql}] <= {$e->getMessage}",__LINE__,__FUNCTION__,__FILE__);
		echo $e->getMessage();
	}
	$mysqli->autocommit(true);
	break;
case "dupcheck":
	$sql="select mobile from {$ob_admin} where mobile='{$_POST['mobile']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) { $answer['message']=$mysqli->error; }
	else if($rs->num_rows>0){ $answer['message']="이미 사용중인 아이디입니다."; }
	else { 
		$answer['message']="사용가능한 아이디입니다."; 
		$answer['success']="0";
	}
	break;
case "tableset":
    $sql="select * from {$ob_party} where branch='{$_POST['branch']}'";
    wlog($sql,__LINE__,__FUNCTION__,__FILE__);
    try{
        $rs=$mysqli->query($sql);
        if($rs===false) throw new Exception("Failed to execute SQL.");
        if($rs->num_rows<1) throw new Exception("Invalid branch name.");
        $row=$rs->fetch_assoc();
        $exist_table=false;
        foreach(explode(";",$row['table_position']) as $key=>$tblinfo){
            $tbl=explode(",",$tblinfo);
            if($tbl[0]==$_POST['table']) {
                $exist_table=true;
                break;
            }
        }
        if(!$exist_table) throw new Exception("Non-existing table name.");
        if($row['passcode']!=$_GET['passcode']) throw new Exception("Incorrect password.");
        $answer['success']="0";
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
}
$mysqli->close();
wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
echo json_encode($answer);
?>
