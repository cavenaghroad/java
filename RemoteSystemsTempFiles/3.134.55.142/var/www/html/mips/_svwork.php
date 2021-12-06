<?
include_once("include.php");

$log="";
foreach($_POST as $key=>$value)	{
    $log.="{$key} [{$value}] ";
}
$wlog=new Logging();
$wlog->write("{$log}",__LINE__,__FUNCTION__,__FILE__);
switch( $_POST['optype'] ) {
case "svctl":
    try {
        $rs=sqlrun("select firstpage,level from a_mem_par where member_id='{$_SESSION['superuser']}' and party='{$_POST['_e']}'");
        if($rs->num_rows<1) throw new Exception("지정한 단체에 소속돼있지 않습니다.");
        // $_SESSION['party']=$_POST['_e'];
//         if(!isset($_SESSION['party'])) throw new Exception("SESSION is not set.");
        $row=$rs->fetch_assoc();
        $_SESSION['level']=$row['level'];
        $answer['firstpage']=$row['firstpage'];
        if($row['firstpage']==""){
            $answer['firstpage']=getFirstPage($_SESSION['party']);
        }
        $answer['result']=0;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "loadparty":
	try {
	    $rs=sqlrun("select party,name_kor,phone,postcode,address,sector from a_party where party not like 'ffff%' order by name_kor");
        if($rs->num_rows<1) throw new Exception('No loaded party');
		$answer['html']="";
		while($row=$rs->fetch_assoc()){
			$answer['html'].="<option value={$row['party']}>".str_pad($row['name_kor'],33,' ').str_pad($row['sector'],20,' ').
			str_pad($row['phone'],21,' ').str_pad($row['postcode'],9,' ').str_pad($row['address'],40,' ')."</option>";
		}
		$answer['result']="0";
	} catch(Exception $e){
		$answer['msg']=$e->getMessage();
	}
	break;
case "add2party":
    $mysqli->autocommit(false);
    try {
        $rowid=getROWID();

        $rs=sqlrun("insert into a_mem_par set rowid='{$rowid}',member_id='{$_SESSION['member_id']}',party='{$_POST['_e']}',".
            "time_login='".date("YmdHis")."',time_logout='',created='".date("YmdHis")."',updated='".date("YmdHis")."'");
        if($rs->num_rows<1) {
            throw new Exception($rs->getError());
        }
        $answer['result']=0;
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
    }
    $mysqli->autocommit(false);
    break;
case "removeparty":
    $mysqli->commit(false);
    try {
        $rs=sqlrun("delete from a_mem_par where member_id='{$_SESSION['member_id']}' and party='{$_POST['_e']}'");
        if($rs->num_rows<1) {
            throw new Exception($rs->getError());
        }
        $answer['result']=0;
        $mysqli->commit();
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
    }
    $mysqli->commit(false);
    break;
    
case "addnewparty":
    $mysqli->autocommit(false);
    try {
        if(!isset($_SESSION['member_id'])) throw new Exception("Login First.");
        
        $rs=sqlrun("select count(*) cnt from a_party where name_kor like '%{$_POST['partyname']}%'");
        $row=$rs->fetch_assoc();
        if(intval($row['cnt'])>0) throw new Exception(_label("same party",$_SESSION['lang']));
        
        $rs=sqlrun("select menu_id from a_party where sector='{$_POST['sector']}' order by party limit 1");
        $row=$rs->fetch_assoc();
        $menu_id=$row['menu_id'];
        $_now=getROWID();
        
        $rs=sqlrun("insert into a_party set party='{$_now}',name_kor='{$_POST['partyname']}',sector='{$_POST['sector']}',".
            "createdby='{$_SESSION['member_id']}',updatedby='{$_SESSION['member_id']}',".
            "created=now(),updated=now(),menu_id='{$menu_id}'");
        if($rs->num_rows<1) throw new Exception(_label("fail2add",$_SESSION['lang']));
        
        $rs=sqlrun("insert into a_mem_par set rowid='{$_now}',party='{$_now}',member_id='{$_SESSION['member_id']}',".
            "level=10,membership='Administrator',time_login=now(),created=now(),updated=now()");
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
    $mysqli->commit(true);
}
$wlog->write(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$wlog=null;
echo json_encode($answer);
$mysqli->close()
?>