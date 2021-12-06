<?
include_once("header.php");
?>
<table align=center style='border:1px solid yellow;'>
<tr style='height:200px;'>
	<td valign=top style='width:300px;border:1px solid yellow;'><? showBBS("housing",10) ?></td>
	<td valign=top style='width:300px;border:1px solid yellow;'><? showBBS("stay",10) ?></td>
	<td valign=top style='width:400pxborder:1px solid yellow;;' align=right rowspan=3><? showBBS("recent",20)?></td>
</tr>
<tr style='height:200px;'>
	<td valign=top style='border:1px solid yellow;'><? showBBS("freetalk",10)?></td>
	<td valign=top style='border:1px solid yellow;'><? showBBS("qna",10)?></td>
</tr>
<tr style='height:200px;'>
	<td valign=top style='border:1px solid yellow;'><? showBBS("market",10)?></td>
	<td valign=top style='border:1px solid yellow;'><? showBBS("notice",10)?></td>
</tr>
</table>
<?
include_once 'footer.php';

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
    echo "<table valign=top>";
    echo "<tr><th colspan=3><a href='/bbs/list.php?_type={$bbsname}' style='text-decoration:none;'>{$bbs_title}</a></th></tr>";
    echo "<tr style='background-color:black;'><th style='width:200px'><font color=white>제목</font></th><th style='width:50px;'><font color=white>작성일</font></th><th><font color=white>작성자</font><th></tr>";
    
    if($where!="") $where=$where." and ";
    $sql="select a.rowid,a.title,a.created,b.nick from bbs a,bbs_member b where {$where} a.user_rowid=b.rowid order by a.rowid desc limit 0,{$linecount}";
    $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($rs===false) throw new Exception("Failed to execute SQL.");
    if($rs->num_rows<1) {
        echo "<tr style='height:20px'><td colspan=3>&nbsp;</td></tr>";
    } else {
        while($row=$rs->fetch_assoc()){
            echo "<tr style='cursor:pointer' onclick='document.location=\"post.php?rowid={$row['rowid']}\";'><td width=200px>{$row['title']}</td><td align=center>".timegap($row['created'])."</td><td align=center>{$row['nick']}</td></tr>";
        }
    }
    echo "</table>";
}
?>
<script>
//document.location='list.php';
</script>
