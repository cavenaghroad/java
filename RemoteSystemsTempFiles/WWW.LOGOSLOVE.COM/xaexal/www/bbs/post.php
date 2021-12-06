<?
include_once("header.php");

$wlog = new Logging();

$_optype="read";
switch($_GET['optype']){
case "modify":
    $_optype="modify";
    break;
case "new":
    $_optype="new";
    break;
}
if($_optype=="modify" || $_optype=="read"){
	$sql="select * from bbs a, bbs_member b where a.rowid='{$_GET['rowid']}' and a.user_rowid=b.rowid";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	try {
		$rs=$mysqli->query($sql);
		if($rs->num_rows<1) throw new Exception("This post does not exist.");
		$row=$rs->fetch_assoc();
		if($row['user_rowid']!=$_SESSION['user_rowid']) {
			$_optype="read";
		}
		$_type=$row['_type'];
	} catch(Exception $e){
		$wlog->write($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
		die($e->getMessage);
	}
}
?>
<style>
table {font-family:tahoma; font-size:12px;}
.td1 {border:1px solid yellow; text-align:right;}
.td2 {border:1px solid yellow;}
</style>
<input type='hidden' id=optype value='<?=$_GET['optype']?>'>
<input type='hidden' id=_type value='<?=$_type?>'>
<input type=hidden id=rowid value='<?=$_GET['rowid'] ?>'>
<table align=center width=540px>
<tr>
	<td align=center><h1>
<?
    try {
        $sql="select * from bbs_config where bbs='{$_type}'";
        $rsB=$mysqli->query($sql); 
        if($mysqli->error) throw new Exception($mysqli->error);
        $rowB=$rsB->fetch_assoc();
        echo $rowB['bbs_title'];
    } catch(Exception $e){
        $wlog->write($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
        die($e->getMessage());
	}
?>
	</h1></td>
</tr>
</table>
<?
switch($_type){
case "housing":
case "stay":
    include_once($_type.".php");
    break;
}
?>
<table align=center width=540px>
<tr>
	<td class='td1' width=64px><b>제목</b></td><td class='td2' style='font-size:20px'>
<?
	if($_optype=="read"){
	    echo "&nbsp;&nbsp;<b>{$row['title']}</b></td>";
	} else {
?>
	<input type=text size=64 maxlength=128 id=title value=<?=$row['title']?>></td>
<?
	}
?>
</tr></table>
<?
	if($_optype=="read"){
?>
	<table width=540px align=center>
	<tr>
	<td class='td1'>작성자 [<?=$row['nick']?>]</td><td class=td1>조회수 [<?=$row['readcount']?>]</td>
	<td class=td1>작성일 [<?=timegap($row['created'])?>]</td>
	</tr></table>
<?
	}
?>
<table align=center width=540px>
<tr>
	<td class='td1' width=64px valign=top>내용</td><td class='td2' valign=top><div style='min-height: 400px;'>
<?
	if($_optype=="read"){
	    $view_img="imagefirst";
	    $sql="select view_img from bbs_member where rowid='{$_SESSION['user_rowid']}'";
	    $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	    $rs1=$mysqli->query($sql);
	    if($rs1!==false && $rs1->num_rows>0){
	        $row1=$rs1->fetch_assoc();
	        $view_img=$row1['view_img'];
	    }
	    if($view_img!="imagefirst") echo $row['content'];
	    
	    $arImage=array();
		$sql="select * from bbs_picture where par_rowid='{$_GET['rowid']}' order by filename";
		$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs1=$mysqli->query($sql);
		if($rs1!==false && $rs1->num_rows>0){
			while($row1=$rs1->fetch_assoc()){
				array_push($arImage,$row1['filename']);
				$finfo=getimagesize("./picture/".$row1['filename']);
				$width=$finfo[0];
				$height=$finfo[1];
				if($width<$height)  $basis="height";
				else $basis="width";
				echo "<img src='./picture/{$row1['filename']}' style='{$basis}:540px;' vspace=5><br>";
			}
		}
		if($view_img=="imagefirst") echo $row['content'];
	} else {
?>
	<textarea <? if($_optype=="read") echo "readonly=readonly onfocus='this.blur();' "; ?> rows=16 cols=72 name=content id=content style='font-family:tahoma;font-size:12;'><?=$row['content']?></textarea>
<?
	}
?>
	</div></td>
</tr>
<tr>
	<td class='td2' colspan=2>
		<table width=100%>
		<tr>
			<td> 
                <input type=button onclick='document.location="list.php?_type=<?=$_type ?>";' value='목록으로 돌아가기'>&nbsp;
                <input type=button onclick='document.location="list.php?_type=<?=$_type ?>";' value='최신목록'>
        	</td>
        	<td align=right>
<? 
    if($_optype=="modify" || $_optype=="new" ) {
        if($_SESSION['privilege']=="admin" && $_type=="freetalk" ) $showNotice="";
        else $showNotice=" style='display:none;'";
?>				
				<select id=selNotice <?=$showNotice?>>
<?
            if($showNotice==""){
                $selected="";
                if($row['notice']=="") $selected=" selected";
                echo "<option value='' {$selected}>-</option>";
                $selected="";
                if($row['notice']=="all")$selected=" selected";
                echo "<option value=all{$selected}>All</option>";
                $rsB->data_seek(0);
                while($rowB=$rsB->fetch_assoc()){
                    if($row['notice']==$rowB['bbs']) $selected=" selected";
                    else $selected="";
                    echo "<option value={$rowB['bbs']}{$selected}>{$rowB['bbs_title']}</option>";
                }
            }
?>				
				</select>
        		<input type=button name=btnWrite id=btnWrite value='등록'>&nbsp;
        		<input type=button onclick='window.history.back();' value='취소'>
<? } else if($_optype=="read"){
            if($_SESSION['user_rowid']==$row['user_rowid']){
?>			
				<input type=button id=btnModify value='수정' >&nbsp;
				<input type=button id=btnDelete value='삭제'>
<?
            } else if(isset($_SESSION['user_rowid']) || $row['notice']==""){
?>
				<input type=button id=btnLike value='좋아요 <?=$row['good']?>' >&nbsp;
				<input type=button id=btnHate value='싫어요 <?=$row['bad']?>'>
<?
            }
		}
?>			
			</td>
		</tr>
		</table>
	</td>
</tr>
<?
if($_optype=="modify"){
	$sql="select * from bbs_picture where par_rowid='{$_GET['rowid']}' order by filename";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs3=$mysqli->query($sql);
	if($rs3===false || $rs3->num_rows<1){
	} else {
?>
<table align=center width=540px>
<tr>
	<td class=td2>지울 화일 선택</td>
</tr>
<tr>
	<td class=td2 height=150px><div id=imglist0 style='overflow-x:auto;white-space: nowrap;scroll:auto;'>
<?
		while($row3=$rs3->fetch_assoc()){
			$finfo=getimagesize("./picture/".$row3['filename']);
			$width=$finfo[0];
			$height=$finfo[1];
			$h1=80;
			$w=h1*width/height;
			echo "<div align=left><input type=checkbox id='{$row3['filename']}'>선택 <br>".
				"<img src='./picture/{$row3['filename']}' id='{$row3['filename']}' style='height:{$h1}px;width:{$w}px;' hspace=5></div>";
		}
	}
?>
	</div></td>
</tr></table>
<?
}
if($_optype!="read"){
?>
<table width=540px align=center>
<tr>
	<td class=td1>화일(이미지) 추가 </td>
	<td class=td2 >
		<form name=myform id=myform enctype="multipart/form-data"><input type=file id=pic1 name=pic1 /></form>
	</td>
</tr>
<tr>
	<td class=td1 height=150px colspan=2><div id=imglist style='overflow-x:auto;white-space: nowrap;scroll:auto;'></div></td>
</tr>
</table>
<?
}
if($_optype=="read" && $row['notice']==""){
?>
<table id=tblReplies align=center width=540px>
<?
	showReplies($_GET['rowid'],0);
?>
</table>
<table align=center width=540px>
<tr>
	<td class=td2>
		<textarea id=txtReply cols=60 rows=5 style='font-family:Tahoma;font-size:12px;'></textarea>
	</td>
	<td class=td2>
		<input type=button id=btnReplyAdd value='댓글등록'>
	</td>
</tr>
</table>
<?
}
function showReplies($rowid,$indent){
	global $mysqli;
	$sql="select content,a.user_rowid,b.nick,readcount,a.rowid,a.created,a.updated from bbs a,bbs_member b where a.par_rowid='{$rowid}' and a.user_rowid=b.rowid order by a.rowid";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs2=$mysqli->query($sql);
	$wlog->write("num_rows [{$rs2->num_rows}]");
	if($rs2===false || $rs2->num_rows<1) return;

	while($row2=$rs2->fetch_assoc()){
		echo "<tr><td width={$indent}px class=td2>&nbsp;</td><td class=td2><table width=100%><tr><td valign=top class=td2 width=120px>{$row2['nick']}<br>{$row2['created']}";
		if($_SESSION['user_rowid']==$row2['user_rowid']) {
			echo "<br><input type=button id={$row2['rowid']} class=delReply value='삭제'>";
		}
		echo "</td><td valign=top class=td2>{$row2['content']}<td></tr></table></td></tr>";
		showReplies($row2['rowid'],$indent+10);
	}
}
function delpost($rowid){
	global $mysqli;

	$sql="delete from bbs where rowid='{$rowid}'";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $mysqli->num_rows<1) {
		$wlog->write("rs is not normal",__LINE__,__FUNCTION__,__FILE__);
		return false;
	}
	$wlog->write("ok nah",__LINE__,__FUNCTION__,__FILE__);

	$sql="select filename from bbs_picture where par_rowid='{$rowid}'";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs!==false || $rs->num_rows>0){
		while($row=$rs->fetch_assoc()){
			ulink("./picture/{$row['filename']}");
		}
	}
	$sql="delete from bbs_picture where par_rowid='{$rowid}'";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);

	$sql="select rowid from bbs where par_rowid='{$rowid}'";
	$wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs!==false && $rs->num_rows>0){
		while($row=$rs->fetch_assoc()){
			if(!delpost($row['rowid'])) return false;
		}
	}
	return true;
}

?>
<script type='text/javascript' src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>
<?
if($_optype=="read" && $_SESSION['user_rowid']!=$row['user_rowid'] && $_type!="notice"){
    $sql="update bbs set readcount=readcount+1 where rowid='{$_GET['rowid']}'";
    $wlog->write($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
}
include_once 'footer.php';
?>