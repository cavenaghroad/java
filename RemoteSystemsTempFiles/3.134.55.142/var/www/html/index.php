<? include_once "bbs/top.php" ?>
<script src='<?=substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],".")) ?>.js'></script>
<? include_once "bbs/header.php" ?>
		<table class='full'>
        <tr>
        	<td style='width:50%;' valign=top>
        		<table class='borderly'>
        		<thead>
        			<tr style='height:24px;background-color:black;color:white;'><th>작성일</th><th>제목</th><th>작성자</th><th style='width:50px'>좋아요</th><th>조회수</th></tr>
        		</thead>
        		<tbody id=bbs0></tbody>
        		</table>
        	</td>
        	<td style='width:50%;' valign=top>
        		<table class='borderly'>
        		<thead>
        			<tr style='height:24px;background-color:black;color:white;'><th>작성일</th><th>제목</th><th>작성자</th><th>좋아요</th><th>조회수</th></tr>
        		</thead>
        		<tbody id=bbs1></tbody></table>
        	</td>
        </tr>
        <tr>
        	<td style='width:50%;' valign=top>
        		<table class='borderly'>
        		<thead>
        			<tr style='height:24px;background-color:black;color:white;'><th>작성일</th><th>제목</th><th>작성자</th><th>좋아요</th><th>조회수</th></tr>
        		</thead>
        		<tbody id=bbs2 valign=top></tbody></table>		
        	</td>
        	<td style='width:50%;' valign=top>
        		<table class='borderly'>
        		<thead>
        			<tr style='height:24px;background-color:black;color:white;'><th>작성일</th><th>제목</th><th>작성자</th><th>좋아요</th><th>조회수</th></tr> 
        		</thead>
        		<tbody id=bbs3></tbody></table>	
        	</td>
        </tr>
        </table>
<? include_once "bbs/right.php" ?>
<? include_once "bbs/footer.php" ?>
