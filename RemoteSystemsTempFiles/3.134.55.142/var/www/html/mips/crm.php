<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?
include_once("include.php");
?>
<html>
<head>
<title>Academy CRM</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="CACHE-CONTROL"  content="NO_CACHE">
<meta name="AUTHOR" CONTENT="PARK JAE HYUNG">
<meta http-equiv="progma" content="no-cache">
<link href="<?=$BaseFolder ?>/base.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.structure.min.css" rel="stylesheet" />
<link type="text/css" href="/jsfile/jquery-ui.theme.min.css" rel="stylesheet" />
<!-- <link type="text/css" href="/jsfile/fullcalendar-1.4.10/fullcalendar.css" rel="stylesheet" /> -->
<!-- <link type="text/css" href="/jsfile/uploadify/uploadify.css" rel="stylesheet" />  -->
<!--<link rel="stylesheet" type="text/css" href="/jsfile/DataTables-1.10.7/media/css/jquery.dataTables.css">-->
<!--<link rel="stylesheet" type="text/css" href="/jsfile/DataTables-1.10.7/extensions/TableTools/css/dataTables.tableTools.css">-->
<!--<link rel="stylesheet" type="text/css" href="/jsfile/Editor/css/editor.dataTables.min.css">-->
<!-- <link rel="stylesheet" href="/jsfile/skin/ui.dynatree.css" /> -->
<link rel="stylesheet" href="/jsfile/jquery.treetable.css" />
<link rel="stylesheet" href="/jsfile/jquery.treetable.theme.default.css" />
<link rel="stylesheet" href="/jsfile/screen.css" />
<!-- <link rel="stylesheet" href="/jsfile/jquery.contextMenu.css" /> -->
</head>
<!-- <script type="text/javascript" src="/jsfile/jquery-1.11.2.min.js"></script> -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="/jsfile/jquery-ui.min.js"></script>
<script type="text/javascript" src="/jsfile/jquery-migrate-1.4.1.min.js"></script>
<!-- <script type="text/javascript" src="/jsfile/fullcalendar-1.4.10/fullcalendar.js"></script> -->
<!-- <script type="text/javascript" src="/jsfile/uploadify/swfobject.js"></script> -->
<!-- <script type="text/javascript" src="/jsfile/uploadify/jquery.uploadify.v2.1.4.min.js"></script> -->
<!-- <script type="text/javascript" src="/jsfile/jquery-barcode-2.0.2.js"></script> -->
<script type="text/javascript" src="/jsfile/jquery.cookie.js"></script>
<!-- <script type="text/javascript" src="/jsfile/jquery.dynatree.js"></script> -->
<!-- <script type="text/javascript" src="/jsfile/jquery.layout.js"></script> -->
<script type="text/javascript" src="/jsfile/jquery.treetable.js"></script>
<!-- <script type="text/javascript" src="/jsfile/jquery.contextMenu.js"></script>-->
<script type="text/javascript" src="<?=$BaseFolder ?>/include.js"></script> 
<script type="text/javascript" src="<?=$BaseFolder ?>/comlib.js"></script>
<script type="text/javascript" src="crmctl.js"></script> 
<!-- <script type="text/javascript" src="appendix.js"></script> -->
<body id=bodyback>
<?
errorlog();

// $_p="124F1828F9C200000";
$_p=$_GET['_p'];
if($_p==""){
?>
<script language='javascript'>
	alert('No page ID given');
	window.history.back();
</script>
<?    
}
if( !isset($_SESSION['member_id']) ) {
?>
<script language='javascript'>
	document.location = './index.php';
</script>
<?
}
// echo "{$_SESSION['member_id']}<br>";
if($_SESSION['member_id']!="12480F80244800000"){    // if not xaexal.
    $sql="select rowid,par_rowid from i_navi where page_id='{$_p}'";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($mysqli->error!="") errorlog($mysqli->error." [".$sql."]",__LINE__,__FUNCTION__,__FILE__);
    $row=$rs->fetch_assoc();
    $par_rowid=$row['par_rowid'];
    while($par_rowid!=""){
        $sql="select rowid,par_rowid from i_navi where rowid='{$row['par_rowid']}'";
        errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
        
        $rs=$mysqli->query($sql);
        if($mysqli->error!="") errorlog($mysqli->error." [".$sql."]",__LINE__,__FUNCTION__,__FILE__);
        $row=$rs->fetch_assoc();
        $rowid=$row['rowid'];
        $par_rowid=$row['par_rowid'];
    }
    $sql="select count(*) from a_mem_par where party='{$rowid}' and member_id='{$_SESSION['member_id']}'";
    errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
    $rs=$mysqli->query($sql);
    if($mysqli->error!="") errorlog($mysqli->error." [".$sql."]",__LINE__,__FUNCTION__,__FILE__);
    $row=$rs->fetch_assoc();
    if(intval($row[0])<1){
//         exit;
    ?>
    <script language='javascript'>
    	document.location = './index.php';
    </script>
    <?
    }
}
?>
<div id=c_header name=c_header class=ui-layout-north>
	<table align=center width=100% height=100%>
	<tr>
	   	<td valign=bottom width=300px><label id=txtNameHeader name=txtNameHeader></label></td>
	    <td align=center valign=top><font color=blue size=5></font></td>
		<td align=right  valign=bottom width="300px">
			<img src='img/chat.png' width="30px" height="30px" alt="" title="Chat with Support team" style="cursor:pointer" id=btnChat name=btnChat/>
			<input type=hidden name=_p id=_p value='<?=$_p?>' />
			<button name=btnPersonal id=btnPersonal class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all"><?php echo $_SESSION['member_name'] ?></button>&nbsp;&nbsp;
			<button name="btnLogout" id="btnLogout" class="ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all logout" >LogOut</button>
		</td>
	</tr>
	</table> 
</div>
<div id=c_menu name=c_menu class=ui-layout-west>
<table class='treetable' id=mainmenu>
<?
$sql="select * from i_page where rowid='{$_p}'";
// errorlog($sql,__LINE__,__FUNCTION__,__FILE__);
$rs=$mysqli->query($sql);
errorlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
if($rs->num_rows<1) {
    echo $mysqli->error;
    $mysqli->close();
    exit;
}

$row=$rs->fetch_assoc();

echo showMenu($row['menu_rowid']);

function showMenu($rowid){
    global $mysqli;
    
    $sql="select * from i_navi where par_rowid='{$rowid}' order by seqno";
    $rs=$mysqli->query($sql);
    errorlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
    $pstr="";
    while($row=$rs->fetch_assoc()){
        $pstr.="<tr data-tt-id='{$row['rowid']}' data-tt-parent-id='{$rowid}' page_id='{$row['page_id']}'><td>{$row['title']}</td></tr>";
        $pstr.=showMenu($row['rowid']);
    }
    return $pstr;
}
?>
</table>
</div>
<div id=c_bottom name=c_bottom class=ui-layout-south> </div>
<div id=c_right name=c_right class=ui-layout-east> </div>
<div id=c_body name=c_body class=ui-layout-center style='overflow:auto;' valign=top>
<table border=0 cellpadding=0 cellspacing=0 width=100%>
<tr height=20px>
	<td align=left><label id=screen_name name=screen_name style='font-family:Tahoma;font-size:11px'></label></td>
</tr>
</table>
<? 

    $pstr=$row['layout'];
    
    $sql="select * from i_grid where par_rowid='{$_p}' order by seqno";
    $rs=$mysqli->query($sql);
    errorlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);

    if($rs->num_rows<1) {
        echo "<h1>No Grid data</h1>";
        $mysqli->close();
        exit;
    }
    $n=1;
    while($row=$rs->fetch_assoc()){
        $sql="select * from i_col where par_rowid='{$row['rowid']}' and inactivated='0' order by seqno";
        $rsCol=$mysqli->query($sql);
        errorlog("{$sql} [{$rsCol->num_rows}]",__LINE__,__FUNCTION__,__FILE__);

        $gLayout="";
//         errorlog("viewtype [{$row['viewtype']}]",__LINE__,__FUNCTION__,__FILE__);

        switch($row['viewtype']){
        case "TREE":
            while($rowCol=$rsCol->fetch_assoc()){
                switch($rowCol['fname']){
                case "rowid": case "par_rowid": case "party": break;
                default:
                    if($rowCol['pixel']=="0"){
                        $thStyle="style='display:none;'";
                    } else {
                        $thStyle="style='width:{$rowCol['pixel']}px;' align=center ";
                    }
                    $gLayout.="<th {$thStyle} dtype={$rowCol['dtype']} digit={$rowCol['digit']} pixel={$rowCol['pixel']}>{$rowCol['title']}</th>";
                }
            }
            break;
        case "HRZN":
            $gLayout.="<th width=20px><input type=checkbox id=chk{$row['rowid']}></th>";
            while($rowCol=$rsCol->fetch_assoc()){
                if($rowCol['pixel']=="0"){
                    $thStyle="style='display:none;'";
                } else {
                    $thStyle="style='width:{$rowCol['pixel']}px;' align=center ";
                }
                $gLayout.="<th {$thStyle} dtype={$rowCol['dtype']} digit={$rowCol['digit']} pixel={$rowCol['pixel']}>{$rowCol['title']}</th>";
            }
            break;
        case "VERT":
            $gSide="";
            while($rowCol=$rsCol->fetch_assoc()){
                if($rowCol['pixel']=="0"){
                    $trStyle="style='display:none;'";
                    $thStyle="";
                } else {
                    $trStyle="height=24px";
                    $thStyle="style='background-color:black;color:white;text-align:right;' align=center";
                }
                $gSide.="<tr {$trStyle}><td {$thStyle}>{$rowCol['title']}</td></tr>";
            }
        }
        $btndelete="&nbsp;";

        if(intval($row['can_remove'])>0) $btndelete.="<input type=button style='display:none;' id=delete{$row['rowid']} class=removal value='삭제'>";
        if($btndelete!="") $btndelete.="</td><td align=center><input type=button class='ui-icon ui-icon-refresh' style='width:20px;height:20px' id=reload{$row['rowid']} />&nbsp;";
        if($row['searchable']=="1") $btndelete.="<input type=text size=10 id=srch{$row['rowid']}>&nbsp;<button id='find{$row['rowid']}'>찾기</button>&nbsp;";
        $addnew="";
        if($row['can_insert']=="1") {
            $addnew="<button id=addnew{$row['rowid']}>새로 추가</button>";
            if($row['viewtype']=="TREE"){
                $addnew.="<input type=button id=levellt{$row['rowid']} class='ui-icon ui-icon-arrowthick-1-w removal' style='width:20px;height:20px;display:none;' />".
                    "<input type=button id=levelup{$row['rowid']} class='ui-icon ui-icon-arrowthick-1-n removal' style='width:20px;height:20px;display:none;' />".
                        "<input type=button id=leveldn{$row['rowid']} class='ui-icon ui-icon-arrowthick-1-s removal' style='width:20px;height:20px;display:none;' />".
                    "&nbsp;<input type=button id=levelrt{$row['rowid']} class='ui-icon ui-icon-arrowthick-1-e removal' style='width:20px;height:20px;display:none;' />";
            }
        }
        
        // page layout
        $gLayout="<div id=hd{$row['rowid']} style='width:{$row['tWidth']}px;overflow:auto;cursor:pointer;border:1px solid green;'>".
            "<table width=100%'><caption style='background-color:yellow'>{$row['classname']}[{$row['rowid']}]</caption>".
            "<tr><td width=30%>".
            "{$btndelete}</td><td align=right valign=middle>{$addnew}&nbsp;</td></tr></table>".
            "</div>".
            "<div id=bd{$row['rowid']} style='width:{$row['tWidth']}px;height:{$row['tHeight']}px;overflow:auto;'>".
            "<table id=tbl{$row['rowid']} viewtype={$row['viewtype']} border=1 style='border-collapse:collapse;cursor:pointer;'>".
            "<thead><tr style='background-color:black;color:white;' >{$gLayout}</tr></thead><tbody>{$gSide}</tbody></table></div>";
        if(strpos($pstr,"{{$n}}")===false) {
            errorlog("{{$n}} does not exist.",__LINE__,__FUNCTION__,__FILE__);
        } else {
            $pstr=str_replace("{{$n}}",$gLayout,$pstr);
        }
        $n++;
    }
    $pstr="<table valign=top id='{$_p}'>{$pstr}</table>";
    echo $pstr;

?>
</div>
</body>

<div id=dvLoading name=dvLoading style='display:none;filter:alpha(opacity=50); opacity:0.5;'>
<img src='<?=$BaseFolder ?>/img/spinner.gif' width:60px height:60px><b>Loading...</b></img>
</div>
<div id=dvTable name=dvTable style='overflow:auto'></div>
<ul id="crmMenu" class="contextMenu">
	<li class="edit"><a href="#edit">Edit</a></li>
</ul>
</html>