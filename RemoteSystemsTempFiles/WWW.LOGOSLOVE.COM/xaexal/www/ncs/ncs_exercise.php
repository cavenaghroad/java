<html>
<head>
<title>NCS Class</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
<body style='background-color:#C8E8F5;' onselectstart='return false;'> 

<?
include_once '../common/_init_.php';

if(!isset($_GET['name'])) {
    echo "URL에 다음과 같이 이름을 적어주십시오";
    die "URL?name=이름";
}
$sql="select * from ncs_student where name='{$_GET['name']}'";
try {
    $rs=$mysqli->query($sql);
    wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) throw new Exception("해당하는 이름을 학생명단에서 찾을 수 없습니다. 확인바랍니다.");
    
    
} catch(Exception $e){
    $mysqli->close();
    echo $e->getMessage();
}
?>
</body>
</html>