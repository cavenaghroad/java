<?
session_start();
$answer=array();
$answer['result']=-1;
$answer['msg']="";
if(!isset($_SESSION['member_id'])){
    $answer['result']=-99;
    $answer['msg']="Login first, please.";
    echo json_encode($answer);
    exit;
}
include_once '../common/_init_.php';
$debug=true;
switch($_POST['optype']){
case "checkLevel":
    $answer['data']="";
    if(isset($_SESSION['level']))    $answer['data']= "<a href='admin.php?level={$_SESSION['level']}'>관리화면으로</a>";
    break;
case "logout":
    session_unset();
    $answer['result']=0;
    break;
case "picklist":
    if(isset($_POST['orderby'])) $orderby = "order by {$_POST['orderby']}";
    else $orderby="order by name";
    try{
        $sql="select name from o2o_{$_POST['tblname']}_{$_SESSION['party']} {$orderby}";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']=$rs->num_rows;    
        if($rs->num_rows<1) throw new Exception("no record");
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
}
$mysqli->close();
echo json_encode($answer);
?>