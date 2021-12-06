<?
include_once("header.php");

$rownum=0;
if(isset($_GET['_p'])){
	$rownum=$_GET['_p'];
}
$srch="";
if(isset($_GET['author'])){
    $sql="select rowid from bbs_member where nick='{$_GET['nick']}'";
    $wlog->write($sql,__LINE__,_FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($rs===false || $rs->num_rows<1){
?>
<script language='javascript'>
alert('사용자를 찾을 수 없습니다.');
window.history.back();
</script>
<?        
    } else {       
        $rowA=$rs->fetch_assoc();
        $srch=" and a.user_rowid='{$rowA['rowid']}'";
    }
}
$pageline="";
$perpage=30;
$sql="select count(*)/{$perpage} pagecount from bbs where _type='{$_type}'{$srch}";
$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);

$rs=$mysqli->query($sql);
$row=$rs->fetch_assoc();
$pagecount=intval($row['pagecount']);
if($rownum>$perpage){
	$pageline="<a href='list.php?_type={$_type}&_p=".strval(($rownum/$perpage)+1)."</a>&nbsp;";
}
$pageline.="<select name=pageno>";
for($n=0; $n<$pagecount+1; $n++){
	$pageline.="<option value={$n}>".($n+1)."</option>";
}
$pageline.="</select>";
if($rownum<$pagecount){
	$rownum++;
	$pageline.="<a href='list.php?_type={$_type}&_p={$rownum}";
}
?>

<input type=hidden id=_type value=<?=$_GET['_type'] ?>>
<table align=center style='width:780px;'>
<tr>
	<td align=center><h1>
<?
    $rsB->data_seek(0);
    while($rowB=$rsB->fetch_assoc()){
        if($rowB['bbs']==$_GET['_type']) {
            echo $rowB['bbs_title']; break;
        }
    }
?>
	</h1></td>
</tr>
<tr>
	<td align=right><?=$pageline ?>
<? if(isset($_SESSION['userid'])) { ?>
		&nbsp;&nbsp;<a href="post.php?optype=new&_type=<?=$_type ?>">새글쓰기</a></td>
<? } ?>
</tr>
</table>
<?
if($_type!="notice"){
    $sql="select a.rowid,a.title,b.nick author,a.readcount from bbs a,bbs_member b where a._type='notice' and a.user_rowid=b.rowid order by a.rowid";
    $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
    $rsN=$mysqli->query($sql);
    $notice=$rsN->num_rows;
}
?>
<table align=center id=tblList style='width:780px'>
<tr class=trhd>
<?
switch($_type){
case "housing":
    $sql="select a.rowid,title,_type,b.nick,good,bad,readcount,a.created,price,deposit,housetype,roomtype,staytype from bbs a,bbs_member b,bbs_house c ".
            "where _type='{$_type}' and a.user_rowid=b.rowid and a.rowid=c.rowid {$srch} order by a.created desc limit {$rownum},{$perpage}";
?>
	<th class=td1 align=center >&nbsp;</th>
	<th class=td1 align=center style='width:80px' >작성일</th>
	<th class=td1 align=center style='width:60px'>주택형태</th>
	<th class=td1 align=center style='width:100px'>주거형태</th>
	<th class=td1 align=center style='width:80px'>입주가능</th>
	<th class=td1 align=center style='width:60px'>가격</th>
	<th class=td1 align=center style='width:60px'>보증금</th>
	<th class=td1 align=center style='width:200px'  >제목</th>
	<th class=td1 align=center  style='width:150px' >작성자</th>
	<th class=td1 align=center style='width:50px' >조회수</th>
	<th class=td1 align=center  style='width:60px'>좋아요/<br>싫어요</th>
<?
    $colspan=6;
    break;
case "freetalk":
case "market":
case "qna":
case "notice":
case "gallery":
default:
?>
	<th class=td1 align=center >&nbsp;</th>
	<th class=td1 align=center style='width:80px' >작성일</th>
	<th class=td1 align=center style='width:200px'  >제목</th>
	<th class=td1 align=center  style='width:150px' >작성자</th>
	<th class=td1 align=center style='width:50px' >조회수</th>
	<th class=td1 align=center  style='width:60px'>좋아요/<br>싫어요</th>
<?
    $colspan=1;
    $sql="select a.rowid,title,_type,b.nick,good,bad,readcount,a.created from bbs a, bbs_member b where _type='{$_type}' and a.user_rowid=b.rowid {$srch} order by a.created desc limit {$rownum},{$perpage}";
    break;
}
?>
</tr>
<?
if(isset($rsN) && $rsN->num_rows>0){
    $x=1;
    $rsN->data_seek(0);
    while($rowN=$rsN->fetch_assoc()){
        echo "<tr class=trbd id={$rowN['rowid']}><td class=td1>{$x}</td><td align=right class=td1>".timegap($rowN['rowid'])."</td><td colspan={$colspan} class=td2><b>{$rowN['title']}</b></td><td align=center class=td1>{$rowN['author']}</td>".
            "<td align=right colspan=2 class=td2>{$rowN['readcount']}</td></tr>";
        $x++;
    }
}
try{
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) throw new Exception("게시판 내용찾기 오류.");
	while($row=$rs->fetch_assoc()){
		$rownum++;
		switch($_type){
	    case "housing":
?>
		<tr id=<?=$row['rowid']?> class=trbd>
			<td class=td1 align=right ><?=$rownum?></td>
			<td class=td1 align=right ><? echo timegap($row['created']) ?></td>
			<td class=td1><?=$row['housetype']?></td>
			<td class=td1><?=$row['roomtype']?></td>
			<td class=td1><?=$row['staytype']?></td>
			<td class=td1 align=right><?=$row['price']?></td>
			<td class=td1 align=right><?=$row['deposit']?></td>
			<td class=td1><?=$row['title']?></td>
			<td class=tdAuthor align=center><?=$row['nick']?></td>
			<td class=td1 align=center ><?=$row['readcount']?></td>
			<td class=td1 align=center><? echo $row['good']."/".$row['bad'] ?></td>
<?
            break;
        case "freetalk":
	    case "market":
	    case "qna":
	    case "notice":
	    case "gallery":
	    default:
?>
		<tr id=<?=$row['rowid']?> class=trbd>
			<td class=td1 align=right ><?=$rownum?></td>
			<td class=td1 align=right ><? echo timegap($row['created']) ?></td>
			<td class=td1><?=$row['title']?></td>
			<td class=tdAuthor align=center><?=$row['nick']?></td>
			<td class=td1 align=center ><?=$row['readcount']?></td>
			<td class=td1 align=center><? echo $row['good']."/".$row['bad'] ?></td>
<?
            break;
		}
	}
?>
</table>
<table align=center style='width:780px;'>
	<tr>
		<td align=right><?=$pageline ?>
<? if(isset($_SESSION['userid'])) { ?>
		&nbsp;&nbsp;<a href="post.php?optype=new&_type=<?=$_type ?>">새글쓰기</a></td>
<? } ?>
	</tr>
</table>
<?
} catch(Exception $e){
	$wlog->write($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
}
if(isset($_SESSION['user_rowid'])){
    include_once("contextmenu.php");
}
?>
<script type='text/javascript' src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>