<html>
<head>
<title>NCS</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
<body style='background-color:#C8E8F5;font-size:20px' onselectstart='return false;' align=center> 
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="jquery.cookie.js"></script>
<style>
td,th {
    background-color:white;
}
td.checking {
    color:red;
    font-weight:bold;
}
td.done {
    color:yellow;
    background-color:black;
    font-weight:bold;
}
td.working {
    color:blue;
    font-weight:bold;
}
#whether {
    cursor:pointer;
}
@-webkit-keyframes blink {
  50% {
    background: #cc5;
    background-size: 75px 150px;
  }
}
@-moz-keyframes blink {
  50% {
    background: #cc5;
    background-size: 75px 150px;
  }
}

.laser {
  animation: blink 2s infinite;
  -webkit-animation: blink 2s infinite;
  -moz-animation: blink 2s infinite;
}
</style>
<?
// if(!isset($_GET['name'])) {
//     echo "URL에 다음과 같이 훈련생이름을 적어주십시오<br>";
//     die("www.logoslove.com/ncs/ncs.php?name=훈련생이름");
// }
include_once 'common.php';
$name=$_GET['name'];
// if($_GET['audit']!="Y" && get_client_ip()!="58.74.90.6"){
//     die("휴먼교육센터(천안) 외부에서는 접속할 수 없습니다.");
// }
include_once '../common/_init_.php';

$rs=null;
// $sql="select absence,tvid,tvkey from ncs_student where name='{$name}'";
// try {
//     $rs=$mysqli->query($sql);
//     if($rs->num_rows<1) throw new Exception("해당하는 이름을 학생명단에서 찾을 수 없습니다.");
//     $row=$rs->fetch_assoc();
//     if($row['absence']=="1") $pAbsence="checked";
//     else $pAbsence="";
    $sql="select * from ncs_config where classcode='{$_GET['class']}'";
    $rs=$mysqli->query($sql);
//     if($mysqli->error) throw new Exception("failed to execute SQL.");
    $row=$rs->fetch_assoc();
//     $mysqli->close();
// } catch(Exception $e){
//     $mysqli->close();
//     die( $e->getMessage());
// }
?>
<input type=hidden id=student value="<?=$name?>">
<input type=hidden id=classid value='<?=$_GET['class']?>'>
<p align=center><h1><?=$row['title']?></h1></p>
<p><h2 style='color:magenta;'><?=$row['period1'] ."~".$row['period2'] ?></h2></p>
<table align=center valign=top>
<tr><td colspan=2 align=center id=studentname><h1><b><?=$name?></b></h1></td></tr>
<!-- <tr><td>TeamViewer&nbsp;&nbsp;<input type=button id=btnTv value="보기"></td><td align=right>&nbsp;</td></tr> 
<tr><td align=right>ID&nbsp;&nbsp;</td><td><input type=text id=tvid size=32 maxlength=128 value="<?=$row['tvid']?>"></td></tr>
<tr><td align=right>Key&nbsp;&nbsp;</td><td><input type=text id=tvkey size=12 value="<?=$row['tvkey'] ?>"></td></tr>
<tr style='height:20px;'><td colspan=2>&nbsp;</td></tr>
<tr><td colspan=2 valign=center><!-- <input type=checkbox id=absence <?=$pAbsence ?>>자리비움 
선생님께 부탁할 메세지를 쓰세요 :<br><textarea id=txtMsg rows=2 cols=40></textarea><br><button id=btnSend>전달</button> 
 </td> 
     </tr> -->
<tr style='height:40px;'><td colspan=2>&nbsp;</td></tr>
<tr><td style='background-color:#dedede' colspan=2>
	<div style='overflow:auto;height:600px;width:500px;'>
    <table id=tblMission style='border:1px solid white;width:100%;'>
        <thead><tr style='background-color:white;height:40px;'><th align=center>과제</th>
        <th align=center>상태&nbsp;&nbsp;&nbsp;&nbsp;<input type=button id=btnRefresh value='새로고침'></th></tr></thead>
        <tbody>
        </tbody>
    </table>
    </div>
</td></tr>
</table>
<!-- <div id=msg2student title="선생님으로부터 온 메세지">
	<p id=lbl2student></p>
</div>
</body> -->
</html>
<?
$mysqli->close();
?>
<script src='ncs.js'></script>