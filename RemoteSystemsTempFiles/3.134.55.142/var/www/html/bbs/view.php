<? 
include_once "top.php" ;
include_once "header.php"; 
?>
<style>
#tblPost,#tblList {
    border-collapse:collapse;
}
#tblPost td,#tblList td {
    border:1px solid green;
}
#tblList thead {
    background-color:black; color:white;
}
#tblList thead th {
    border:1px solid yellow;
}
</style>
	<table>
	<tr>
    	<td valign=top>
            <table id=tblPost style='width:100%;'>
            <tr style='height:24px;'>
            	<td style='width:40px;'>제목</td><td id=td_title colspan=5></td><td style='width:60px;'>조회수</td><td id=td_readcount style='width:40px;'></td>
            </tr>
            <tr style='height:24px;'>
            	<td>작성자</td><td id=td_writer style='width:100px;'></td>
            	<td style='width:100px;'>작성시각</td><td id=td_created style='width:150px;'></td>
            	<td>좋아요</td><td id=td_good></td>
            	<td>싫어요</td><td id=td_bad></td>
            </tr>
            <tr>
            	<td colspan=8 id=td_content style='height:800px;vertical-align:top;'></td>
            </tr>
            <tr>
            	<td colspan=8 align=right>
                	<button id=btnUpdate style='display:none'>수정</button>&nbsp;
                	<button id=btnRemove style='display:none'>삭제</button>&nbsp;
<? $style=""; if(!isset($_SESSION['user_rowid'])) $style="style='display:none;'"; ?>    
                	<button id=btnNewPost <?=$style ?>>새글쓰기</button>
            	</td>
            </tr>
            </table>
    	</td>
	</tr>
	<tr>
    	<td style='width:50%' valign=top>
<? include "list.php" ?>    	
    	</td>
	</tr>
	</table>
<script src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>	
<? include_once "right.php" ?>
<? include_once "footer.php" ?>