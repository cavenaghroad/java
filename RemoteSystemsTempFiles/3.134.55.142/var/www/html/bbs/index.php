<?
include_once("header.php");
?>
<table align=center style='border:1px solid #aed6f1;width:960px;' >
<tr style='height:200px;'>
	<td valign=top style='border:1px solid yellow;background-color:white;'><? showBBS("notice",10)?></td>
	<td valign=top style='border:1px solid yellow;background-color:white;'><? showBBS("recent",10)?></td>
</tr>
<tr style='height:200px;'>
	<td valign=top style='border:1px solid yellow;;background-color:white;'><? showBBS("freetalk",10)?></td>
	<td valign=top style='border:1px solid yellow;background-color:white;'><? showBBS("qna",10)?></td>
</trstyle=>
<tr style='height:200px;'>
	<td valign=top style='border:1px solid yellow;background-color:white;'><? showBBS("housing",10) ?></td>
	<td valign=top style='border:1px solid yellow;background-color:white;'><? showBBS("stay",10) ?></td>
</tr>
<tr style='height:200px;'>
	<td valign=top style='border:1px solid yellow;background-color:white;'><? showBBS("market",10)?></td>
    <td valign=top style='border:1px solid yellow;background-color:white;'>&nbsp;</td>
</tr>
</table>
<script>
//document.location='list.php';
</script>
<?
include_once('footer.php');

function showBBS($bbsname,$linecount){
    global $mysqli,$wlog;
    
    switch($bbsname){
    case "recent":
        $bbs_title="새 글 ";
        $where ="";
        break;
    default:
        $where=" a._type='{$bbsname}' ";
        $sql="select * from bbs_config where bbs='{$bbsname}'";
        $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
        $rs=$mysqli->query($sql);
        $bbs_title="&nbsp;";
        if($rs===false || $rs->num_rows<1){
        } else {
            $row=$rs->fetch_assoc();
            $bbs_title=$row['bbs_title'];
        }
    }
 ?>
    <table valign=top align=center'>
    <tr><th colspan=5><a href='/bbs/list.php?_type=<? echo $bbsname?>' style='text-decoration:none;'><? echo $bbs_title ?></a></th></tr>
    <tr style='background-color:black;'><th style='width:50px;'><font color=white>작성일</font></th><th style='width:200px'><font color=white>제목</font></th>
    <th><font color=white>작성자</font></th><th><font color=white>조회수</font></th><th><font color=white>좋아요</font></th></tr>
<?    
    if($where!="") $where=$where." and ";
    $sql="select a.rowid,a.title,a.created,b.nick,a.readcount,a.good from bbs a,bbs_member b where {$where} a.user_rowid=b.rowid order by a.rowid desc limit 0,{$linecount}";
    $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($rs===false) throw new Exception("Failed to execute SQL.");
    if($rs->num_rows<1) {
        echo "<tr style='height:20px'><td colspan=5>&nbsp;</td></tr>";
    } else {
        while($row=$rs->fetch_assoc()){
?>            
    <tr style='cursor:pointer' onclick='document.location="post.php?rowid=<? echo $row['rowid'] ?>";'><td align=center><? echo timegap($row['created']) ?></td>
    <td width=200px><b><? echo $row['title'] ?></b></td><td align=center><? echo $row['nick']?></td><td align=right><?echo $row['readcount']?></td>
    <td align=right><? echo $row['good']?></td></tr>
<?    
        }
    }
    echo "</table>";
}
?>