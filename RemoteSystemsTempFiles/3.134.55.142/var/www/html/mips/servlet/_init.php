<? 
if(!isset($_SESSION['member_id'])) {
    $answer['result']=-99;
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	exit;
}
include_once("/common/_init_.php");

switch($_POST['optype']){
case "partylist":
    try {
        errorlog(">>> Building up Party dropdown list.",__LINE__,__FUNCTION__,__FILE__);
        $sql="select b.party,b.name_kor from a_mem_par a,a_party b where a.member_id='{$_SESSION['member_id']}' and a.party=b.party and b.party!='{$_POST['_p']}' order by a.level";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['data']="";
        if($rs->num_rows==1) {
            $row=$rs->fetch_assoc();
            $answer['data']="<a href='crmctl.php?_p=".$row['party'].'>'.$row['name_kor'].'</a>';
        } else {
            $answer['data']= "<select id=selParty>";
            while($row=$rs->fetch_assoc()){
                $answer['data'].="<option value='{$row['party']}'>{$row['name_kor']}</option>";
            }
            $answer['data'].="</select>";
        }
    } catch(Exception $e){
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $answer['msg']=$e->getMessage();
    }
    break;
}
$rs=null;
$mysqli->close();
echo json_encode($answer);
?>