<?
include_once '../common/_init_.php';

$answer=array();
$answer['result']=-1;
$answer['msg']="";

switch($_POST['optype']){
case "refresh":    
    try {
        $student=$_POST['student'];
        $sql="select msg2student from ncs_student where classcode='{$_POST['class']}' and alive='1' and name='{$student}' and msg2student is not null";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        $answer['msg2student']="";
        if($rs->num_rows>0){
            $row=$rs->fetch_assoc();
            $answer['msg2student']=$row['msg2student'];
            $sql="update ncs_student set msg2student=null where classcode='{$_POST['class']}' and alive='1' and name='{$student}'";
            $rs=$mysqli->query($sql);
        }
        
        $sql="select name,if(instr(done,'{$student}')>0,'done',if(instr(submit,'{$student}')>0,'checking','working')) status from ncs_drill where classcode='{$_POST['class']}' order by created desc";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        if($rs->num_rows<1) throw New Exception("<tr><td align=center colspan=2>{$sql}</td></tr>");
        $answer['data']="";
        while($row=$rs->fetch_assoc()){
            $answer['data'].="<tr height='40px'><td style='cursor:default'>{$row['name']}</td><td align=center id=whether classcode={$row['status']}>";
            switch($row['status']){
            case "done":
                $answer['data'].="완료";break;
            case "checking":
                $answer['data'].="확인중";break;
            default:
                $answer['data'].="작업중";break;
            }
            $answer['data'].="</td></tr>";
        }
        $sql="update ncs_student set active='".date("ymdHi")."' where classcode='{$_POST['class']}' and alive='1' and name='{$student}'";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);

        $answer['result']="0";
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "info":
    try {
        $now=date("ymdHi");
        $sql="select name,one2one,absence,if({$now}-cast(active as unsigned)>5,'1','0') active,msg,msgtime from ncs_student where classcode='{$_POST['class']}' and alive='1' and seq is not null";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['one2one']=array();
        $answer['absence']=array();
        $answer['active']=array();
        while($row=$rs->fetch_assoc()){
            if($row['one2one']==date("Ymd")) array_push($answer['one2one'],$row['name']);
            if($row['absence']=="1") array_push($answer['absence'],$row['name']);
            if($row['active']=="1") array_push($answer['active'],$row['name']);
        }
        $answer['result']="0";
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "getmsg":
    try {
        $sql="select name,msg,msgtime from ncs_student where classcode='{$_POST['class']}' and alive='1' and msg <> '' and msgtime>'{$_POST['lasttime']}' order by msgtime";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['news']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['news'],$row['name'].'^'.$row['msg'].'^'.$row['msgtime']);
        }
        $answer['result']="0";
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
    
case "endtime":
    try{
        $sql="select endtime from ncs_config where classcode='{$_POST['class']}' and period1='20200420' and active='1'";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $row=$rs->fetch_assoc();
        $answer['endtime']=$row['endtime'];
        $answer['result']='0';
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
}
$mysqli->close();
echo json_encode($answer);
?>
