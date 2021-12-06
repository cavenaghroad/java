<?
include_once '../common/_init_.php';

$answer=array();
$answer['result']=-1;
$answer['msg']="";
$log="";
foreach($_POST as $key=>$value)	{
    $log.="{$key} [{$value}] ";
}
errorlog($log,__LINE__,__FUNCTION__,__FILE__);
switch($_POST['optype']){
case "init":
    try {
        $today=date('Ymd');
        $sql="select seq,name,birth,tvid,school,address,ipaddr from ncs_student where classcode='{$_POST['class']}' and alive='1' and seq is not null order by seq";
        $rs=$mysqli->query($sql);
        if($rs->num_rows<1) throw new Exception("학생목록이 없습니다.");
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row);
        }
        $answer['result']='0';
        
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        
    }
    break;
case "ipaddr":
    try{
        $sql="update ncs_student set ipaddr='{$_POST['ipaddr']}' where classcode='{$_POST['class']}' and seq={$_POST['seq']} and ipaddr<>'{$_POST['ipaddr']}'";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['result']='0';
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "drill":
    try{
        $sql="select name from ncs_drill where classcode='{$_POST['class']}' order by created desc";
        $rs=$mysqli->query($sql);
        if($rs->num_rows<1) throw new Exception($sql);
        $answer['data']=array();
        while($row=$rs->fetch_assoc()){
            array_push($answer['data'],$row['name']);
        }
        $answer['result']='0';
        
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        
    }
    break;
case "getdrill":
    try{
        $sql="select done,submit from ncs_drill where name='{$_POST['drill']}' and classcode='{$_POST['class']}' order by name";
        $rs=$mysqli->query($sql);
        if($rs->num_rows<1) throw new Exception($sql);
        // $answer['done']=array();
        // $answer['submit']=array();
        // while($row=$rs->fetch_assoc()){
        $row=$rs->fetch_assoc();
            $answer['done']=$row['done'];
            $answer['submit']=$row['submit'];
        // }
        $answer['result']='0';
        
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "attend":
    try {
        $sql="select presented from ncs_student where classcode='{$_POST['class']}' and alive='1' and name='{$_POST['name']}'";
        $rs=$mysqli->query($sql);
        if($rs->num_rows<1) throw new Exception($sql);
        $row=$rs->fetch_assoc();
        $today=date('Ymd');
        if($today!=$row['presented']){
            $sql="update ncs_student set presented='{$today}' where alive='1' and name='{$_POST['name']}'";
            $answer['result']='Y';
        } else {
            $sql="update ncs_student set presented=null where alive='1' and name='{$_POST['name']}'";
            $answer['result']='N';
        }
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);

    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "add":
    try {
        $sql="insert into ncs_drill(name,created,classcode) values('{$_POST['name']}','".date('YmdHis')."','".$_POST['class']."')";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['result']="0";
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "delete":
    try {
        $sql="delete from ncs_drill where classcode='{$_POST['class']}'  and name='{$_POST['name']}'";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception("과제명 삭제 실패");
//         $answer['msg']=$sql;
        $answer['result']="0";
        
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "tv":
    try {
        $sql="update ncs_student set {$_POST['id']}='{$_POST[$_POST['id']]}' where classcode='{$_POST['class']}' and alive='1' and name='{$_POST['name']}'";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['result']='0';
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "setmission":
    $update="update ncs_drill set ";
    $where=" where classcode='{$_POST['class']}' and name='{$_POST['drill']}'";
    try{
        if($_POST['status']=="working"){    // 작업중 -> 확인중
            $sql="select submit from ncs_drill {$where}";
            $rs=$mysqli->query($sql);
            if($rs->num_rows<1) throw new Exception($sql);
            $row=$rs->fetch_assoc();
            $student=$row['submit'];
            if(strpos($student,$_POST['student'])===false){
                if($student!="") $student.=",";
                $student.=$_POST['student'];
                $sql="{$update} submit='{$student}' {$where}";
            //     $rs=$mysqli->query($sql);
            //     if($mysqli->error) throw new Exception($sql); 
            }
            $answer['data']="checking";
        } else {    // 완료(done),확인중(checking) -> 작업중
            $sql="select submit,done from ncs_drill {$where}";
            $rs=$mysqli->query($sql);
            if($rs->num_rows<1) throw new Exception($sql);
            $row=$rs->fetch_assoc();
            $submit=$row['submit']; $done=$row['done'];
            $n=strpos($submit,$_POST['student']);
            if($n!==false){
                if($n>0) {
                    $prev=","; $next="";
                } else if($submit!=$_POST['student']){
                    $prev=""; $next=",";
                } else {
                    $prev=$next="";
                }
                $submit=str_replace($prev.$_POST['student'].$next,"",$submit);
            }
            // $sql=array();
            $n=strpos($done,$_POST['student']);
            if($n!==false){
                if($n>0) {
                    $prev=","; $next="";
                } else if($done!=$_POST['student']){
                    $prev=""; $next=",";
                } else {
                    $prev=$next="";
                }
                $done=str_replace($prev.$_POST['student'].$next,"",$done);
                // array_push($sql,"done='{$done}'");
            }
            $sql=$update."submit='{$submit}',done='{$done}'".$where;
            $answer['data']="working";
        }
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['result']='0';
        $answer['msg']=$sql;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
case "done":
    try {
        $update="update ncs_drill set ";
        $where="where classcode='{$_POST['class']}' and name='{$_POST['drill']}'";
        $sql="select submit,done from ncs_drill {$where}";
        $rs=$mysqli->query($sql);
        if($rs->num_rows<1) throw new Exception($sql);
        $row=$rs->fetch_assoc();
        $submit=$row['submit']; $done=$row['done'];
        $n=strpos($submit,$_POST['student']);
        if($n!==false){
            if($n>0) {
                $prev=","; $next="";
            } else if($submit!=$_POST['student']){
                $prev=""; $next=",";
            } else {
                $prev=""; $next="";
            }
            $submit=str_replace($prev.$_POST['student'].$next,"",$submit);
        }
        $n=strpos($done,$_POST['student']);
        if($n==false){
            if($done!="") $done.=",";
            $done.=$_POST['student'];
        }
        $sql=$update."submit='{$submit}',done='{$done}' ".$where;
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['data']="done";
        $answer['result']="0";
        $answer['msg']=$sql;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
// case "absence":
//     try {
//         $sql="update ncs_student set absence=";
//         if($_POST['status']=="1"){
//             $sql.="'1'";
//         } else {
//             $sql.="null";
//         }
//         $sql.=" where name='{$_POST['student']}'";
//         $rs=$mysqli->query($sql);
//         if($mysqli->error) throw new Exception($sql);
//         $answer['result']="0";
//     } catch (Exception $e){
//         $answer['msg']=$e->getMessage();
//     }
//     break;
// case "one2one":
//     try {
//         $sql="update ncs_student set one2one=";
//         if($_POST['status']=="1"){
//             $sql.="'".date("Ymd")."'";
//         } else {
//             $sql.="null";
//         }
//         $sql.=" where name='{$_POST['student']}'";
//         $rs=$mysqli->query($sql);
//         if($mysqli->error) throw new Exception($sql);
//         $answer['result']="0";
//     } catch (Exception $e){
//         $answer['msg']=$e->getMessage();
//     }
//     break;
// case "get_one2one":
//     try{
//         $sql="select name from ncs_student where one2one='".date("Ymd")."'";
//         $rs=$mysqli->query($sql);
//         if($mysqli->error) throw new Exception($sql);
//         $answer['data']=array();
//         while($row=$rs->fetch_assoc()){
//             array_push($answer['data'],$row['name']);
//         }
//         $answer['result']='0';
//     } catch(Exception $e){
//         $answer['msg']=$e->getMessage();
//     }
//     break;
case "checkabsence":
    try {
        $now=date("ymdHi");
        $sql="select name,if(presented=date_format(now(),'%Y%m%d'),'1','0') presented,".       
                "if(isnull(active),'0',if(cast(date_format(now(),'%y%m%d%H%i') as decimal)-cast(active as decimal)>5,'0','1')) active ".   
                "from ncs_student and alive='1'";
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($sql);
        $answer['presented']=array();$answer['active']=array();
        while($row=$rs->fetch_assoc()){
            if($row['presented']=="1") array_push($answer['presented'],$row['name']);
            if($row['active']=="1") array_push($answer['active'],$row['name']);
        }
        $answer['result']='0';
        $answer['msg']=$sql;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
// case "msg":
//     try {
//         $now=date("YmdHis");
//         $sql="update ncs_student set msg='{$_POST['message']}',msgtime='{$now}' where alive='1' and name='{$_POST['name']}'";
//         $rs=$mysqli->query($sql);
//         if($mysqli->error) throw new Exception($sql);
//         $answer['result']='0';
//     } catch(Exception $e){
//         $answer['msg']=$e->getMessage();
//     }
//     break;
// case "RemoveMsg":
//     try {
//         $sql="update ncs_student set msg=null where alive='1' and name='{$_POST['name']}'";
//         $rs=$mysqli->query($sql);
//         if($mysqli->error) throw new Exception("");
//         $answer['result']='0';
//         $answer['msg']='';
//     } catch(Exception $e){
//         $answer['msg']=$e->getMessage();
//     }
//     break;
// case "sendmsg":
//     try{
//         $sql="update ncs_student set msg2student='{$_POST['msg']}' where alive='1' and name='{$_POST['name']}'";
//         $rs=$mysqli->query($sql);
//         if($mysqli->error) throw new Exception($mysqli->error);
//         $answer['result']='0';
//         $answer['msg']='';
//     } catch(Exception $e){
//         $answer['msg']=$e->getMessage();
//     }
//     break;
case "changeseat":
    try {
        if(intval($_POST['oldseat'])==-1){
            $sql = "select count(*) cnd from ncs_student where classcode='{$_POST['class']}' and name='{$_POST['name']}'";
            $rs=$mysqli->query($sql);
            $row=$rs->fetch_assoc();
            if(intval($row['cnt'])>0){
                $sql="update ncs_student set seq={$_POST['newseat']},alive='1' where classcode='{$_POST['class']}' and name='{$_POST['name']}'";
            } else {
                $sql = "insert into ncs_student set classcode='{$_POST['class']}',name='{$_POST['name']}',seq='{$_POST['newseat']}', alive='1'";
            }
        } else {
            $sql="update ncs_student set seq={$_POST['newseat']} where classcode='{$_POST['class']}' and alive='1' and name='{$_POST['name']}' and seq={$_POST['oldseat']}";
        }
        $rs=$mysqli->query($sql);
        if($mysqli->error) throw new Exception($mysqli->error);
        $answer['result']='0';
        $answer['msg']='';
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    break;
}
echo json_encode($answer);
$mysqli->close();

?>