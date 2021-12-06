<?
if(isset($_GET['_type'])){
    $_type=$_GET['_type'];
} else {
    $sql="select firstpage from bbs_member where userid='{$_SESSION['userid']}'";
    $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($rs->num_rows<1) $_type="freetalk";
    else {
        $row=$rs->fetch_assoc();
        $_type=$row['firstpage'];
    }
}
?>
<div style='width:200px;height:500px;left:200px;top:70px;position:relative;'>
<script type="text/javascript" src="menu.js"></script>
<table style='width:200px;background-color:yellow;'>
<tr height=50px>
	<td valign=top>
		<table valign=top width=100%>
		<tr>
			<td>
<?
$sql="select bbs,bbs_title,fields,col_title from bbs_config order by serial";
$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
$rsB=$mysqli->query($sql);
if($rsB===false||$rsB->num_rows<1){
} else {
    while($rowB=$rsB->fetch_assoc()){
        if($_type==$rowB['bbs']){
            echo $rowB['bbs_title'];
//             $bbs_title=$rowB['bbs_title'];
        } else {
            echo "<a href=list.php?_type={$rowB['bbs']}>{$rowB['bbs_title']}</a>";
        }
		// echo "&nbsp;&nbsp;";
		echo "<br><br>";
    }
}
if($_type=="community") {
	echo "동아리방";
	$bbs_title="동아리방";
} else {
?>
			<a href=list.php?_type=community>동아리방</a>
<? } ?>			
			</td>

		</tr>
		</table>
	</td>
</tr>
</table>
</div>
<div   id=dvGuide title='포인트 적립과 사용 안내'' style='font-family:tahoma; font-size:12px;display:none;'>
- 게시글 작성 : +20<br>
- 작성한 댓글 삭제 : -20<br>
<font color=red>"좋아요"로 획득한 마일리지도 차감됨.</font><br>
<font color=red>"싫어요"로 차감된 마일리지는 복구되지 않음.</font><br>
- 댓글 작성 : +2<br>
- 작성한 댓글 삭제 : -2<br>
- 좋아요 : +1<br>
- 싫어요 : -1<br>
</div>
