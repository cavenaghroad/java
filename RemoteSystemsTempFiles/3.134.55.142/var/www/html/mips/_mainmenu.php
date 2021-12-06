<?
@session_start();

$answer['result']=-1;
$answer['msg']="";

if(!isset($_SESSION['member_id'])){
    $answer['result']=-99;
    $answer['msg']="Login First.";
    echo json_encode($answer);
    exit;
}
include_once '../common/_init_.php';

switch($_POST['optype']){
case "build":
    try{
        $pstr= "<ul id=mainmenu style='cursor:pointer'>";
        errorlog(">>> Building Left menu.",__LINE__,__FUNCTION__,__FILE__);
        $sql="select menu_id from a_party where party='{$_SESSION['party']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") throw new Exception($mysqli->error);
        $row=$rs->fetch_assoc();
        $pstr.= showMenu($row['menu_id']);
        //     echo $pstr;
        errorlog(htmlentities($pstr),__LINE__,__FUNCTION__,__FILE__);
        $answer['result']=0;
        $answer['data']=$pstr."</ul>";
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    } finally {
        $rs=null;
    }
    break;
}
$mysqli->close();
echo json_encode($answer);

function showMenu($rowid,$par_rowid=""){
    global $mysqli;
    
    $sql="select * from i_navi where par_rowid='{$rowid}' order by rowid,par_rowid,seqno";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    $pstr="";
    while($row=$rs->fetch_assoc()){
        $pstr.="<li id='{$row['page_id']}'><div>{$row['title']}</div>";
        $pstr1=showMenu($row['rowid'],$rowid);
        if($pstr1!="") $pstr.="<ul>{$pstr1}</ul>";
        $pstr.="</li>";
    }
    return $pstr;
}

?>