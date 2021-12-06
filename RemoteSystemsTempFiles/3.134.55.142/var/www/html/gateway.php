<?
session_start();
include_once './common/_init_.php';

try {
    $sql="select member_id,member_name from a_member a where a.mobile='{$_POST['mobile']}' and a.passcode='{$_POST['passcode']}'";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($rs->num_rows<1)     throw new Exception("<h1>No information of your membership</h1><br><br><a href='/'>back to the home page</a>");        
    
    $row=$rs->fetch_assoc();
    $_SESSION['member_id']=$row['member_id'];
    $_SESSION['member_name']=$row['member_name'];
    
    $sql="select b.name_kor party_name,a.party,d.url,d.name url_title,c.admin_flag ".
            " from a_mem_par a, a_party b, a_mem_url c, a_url d ".
            "where a.member_id='{$_SESSION['member_id']}' and a.party=b.party and b.party!='FFFFFFFFFFFF00000' ".    
                "and a.member_id=c.member_id and c.url_id=d.rowid";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($mysqli->error) throw new Exception($mysqli->error);
    if($rs->num_rows<1) throw new Exception("개인정보 페이지로 이동하여, 소속을 등록하십시오.");

    $pstr="";
    $n=0;
    while($row=$rs->fetch_assoc()){
        $pstr.="<p><a href='{$row['url']}?party={$row['party']}&level={$row['admin_flag']}'>{$row['party_name']}.{$row['url_title']}</a></p>";
    }
//     $sql="select * from o_mentoring where mentee='{$_SESSION['member_id']}' and class='mentor' and graduated='Y'";
} catch(Exception $e){
    errorlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
} finally {
    $rs=null;
    $mysqli->close();
}
?>
<html>
<head>
	<title>작업종류 선택</title>
</head>
<body>
<?=$pstr ?>
</body>
</html>