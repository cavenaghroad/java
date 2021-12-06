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
case "party":
    try{
        $sql="select party,name_kor from a_party where party<>'FFFFFFFFFFFF00000' and length(name_kor)>4 order by name_kor";
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
        errorlog($answer['msg'],__LINE__,__FUNCTION__,__FILE__);
    } finally {
        $rs=null;
    }
    break;
case "enter":
    try{
        $mysqli->autocommit(false);
        $rowid=getUnique();
        $now=getNOW();
        $sql="insert into a_member set member_id='{$rowid}', member_name='{$_POST['name']}',".
                "mobile='{$_POST['mobile']}', passcode='{$_POST['passcode']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $sql="insert into a_mem_par set rowid='{$rowid}',member_id='{$rowid}', party='{$_POST['party']}',".
            "created='{$now}', updated='{$now}'";
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
}
$mysqli->close();
echo json_encode($answer);
?>