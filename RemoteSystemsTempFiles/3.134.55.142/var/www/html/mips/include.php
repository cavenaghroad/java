<?
@session_start();

$fullpath = "/var/www/html";
$orgpath="/var/www/html/";
$admin_log=$fullpath."/ob1/mips_adm.html";
$comm_log=$fullpath."/ob1/mips.html";

include_once '../common/_init_.php';

$answer=array();
$answer['result']=-1;
$answer['msg']="";

define(SUPERVISOR,"12480F80244800000");
define("PRIMARY_YMD","date_format(NOW(),'%Y%m%d')");

$basename = "party";
$BaseFolder="/mips";
$primary = "member";

$t_lesson = "a_lesson";
$t_party = "a_party";
$t_member = "a_member";						//saints";
$t_m2class = "a_member2class";
$t_m2lesson = "a_member2lesson";
$t_m2e = "a_member2party";
$t_attend = "a_attend";
$t_fee = "a_fee";
$t_membertobe = "a_membertobe";		//saints_newcomer";
$t_lov = "a_lov";					//listofvalue";
$t_navi = "a_navi";			//saint_navi";
$t_config = "a_config";

$sqldbg = true;

$imgdir = "/picture/";

$dberror = 0;
$dberror_msg = ""; 

// XML global 
$doc; $root;

define("SUPER",1);
define("COMMON",2);
define("SUPER_PARTY","FFFFFFFFFFFF00000");
define('CLICKED_BGCOLOR','#33ff99');
define('BASE_BGCOLOR','white');
define('MOUSEOVER_BGCOLOR','yellow');

define("LONGER",150);
define("IMGDIR_MINI","/picture_mini/");

define("bbs_table","ksw_bbs");
define("bbs_config","ksw_config");
define("bbs_reply","ksw_reply");
define("F_12","FFFFFFFFFFFF");
define("APPEND",false);
define("NEWADD",true);
define("NOLOG",false);
define("VERT","0");
define("HRZN","1");
define("TREE","2");
define("DLOG","3");
define("FORM","4");
define("SUPERUSER","'12480F80244800000'");

define("SELF_INFO_PAGE","'124EF3FA72DD00000'");

$gCountry="";
$gMoney = "";
$gCountryCode = "";

$thispage;
$gLogType = null;

function getCountry($strIP) {
	global $gCountry,$gMoney,$gCountryCode;
	$arIP = explode(".",$strIP);
	for( $n=0; $n < count($arIP); $n++ ) {
		$arIP[$n] = trim(sprintf("%2x",intval($arIP[$n])));
		if( strlen($arIP[$n]) == 1 ) $arIP[$n] = "0{$arIP[$n]}";
	}
	$nIP = hexdec(implode(".",$arIP));
	$rs=sqlrun("select country,abbr3 from ipaddr where '{$nIP}' between start_num and end_num");
	$gCountry="";
	if($rs===false) $gCountryCode="";
	else if($rs->num_rows<1) $gCountryCode="";
	else {
		$row=$rs->fetch_assoc();
		$gCountry=$row['country'];
		$gCountryCode=$row['abbr3'];
	}
	return $gCountry;
}

function ip_country($str){
    $rs=sqlrun("update ip_country set cnt=cnt+1 where country='{$str}'");
}
    
function setCountry($strIP) {
	global $gCountry, $gCountryCode,$gMoney;

	$arIP = explode(".",$strIP);
	for( $n=0; $n < count($arIP); $n++ ) {
		$arIP[$n] = trim(sprintf("%2x",intval($arIP[$n])));
		if( strlen($arIP[$n]) == 1 ) $arIP[$n] = "0{$arIP[$n]}";
	}
	$nIP = hexdec(implode(".",$arIP));
	$rs=sqlrun("select country,money from ipaddr2city where {$nIP} between start_num and end_num");
	if($rs->num_rows > 0 ) {
		$row = $rs->fetch_assoc();
		$gCountry = $row['country'];
		$gMoney = $row['money'];
	}
}

function _label($pcode,$lang){
//     echo "select msg from a_label where code='{$pcode}' and lang='{$lang}'";
	$rs=sqlrun("select msg from a_label where code='{$pcode}' and lang='{$lang}'",__LINE__,__FUNCTION__,__FILE__);
	
	if($rs->num_rows<1){
		$pstr="";
	} else {
		$row=$rs->fetch_assoc();
		$pstr=$row['msg'];
	}
	return $pstr;
}

function getROWID(){
    $t = microtime(true);
    $micro = sprintf("%06d",($t - floor($t)) * 1000000);
    $_now= dechex(date('YmdHis')).dechex($micro);
    return strtoupper($_now);
}

function sqlrun($sql,$ln="",$fn="",$sfile=""){
    global $mysqli;
    global $wlog;
    
    $msc = microtime(true);
    $rs=$mysqli->query($sql);
    $msc = microtime(true)-$msc;
    $msc*=1000;
    if(strtolower(substr($sql,0,6)) == "select"){
        $cnt=$rs->num_rows;
    } else {
        $cnt=$mysqli->affected_rows;
    }
    //errorlog("[{$sql}] [{$cnt}] [{$msc}sec]",$ln,$fn,$sfile);
    return $rs;
}

function showFieldFromTable($colname,$tname,$colstr,$colval){
    $rs=sqlrun("select {$colname} from {$tname} where {$colstr}={$colval}",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows>0) {
        $row=$rs->fetch_assoc();
        return $row[$colname];
    } else {
        return "";
    }
}

function getDtype($pstr){
    switch($pstr){
    case "text": case "gender": case "char": case "varchar":
    case "date": case "select": case "gender": case "radio":
        return  "string";
    case "decimal": case "number": case "money":
        return "number";
    case "html":
        return "html";
    case "bool":
        return "bool";
    default:
        return $pstr;
    }
}

function drawLayout($_p){
    global $mysqli,$wlog;
    
    errorlog(">>> Building Page.",__LINE__,__FUNCTION__,__FILE__);
    $rs=sqlrun("select * from i_page where par_rowid='{$_p}'",__LINE__,__FUNCTION__,__FILE__);
    if($rs->num_rows<1) {
        echo $mysqli->error;
        $mysqli->close();
        return ;
    }
    
    $row=$rs->fetch_assoc();
    
    $pstr=$row['layout'];
    
    errorlog(">>> Building Grid.",__LINE__,__FUNCTION__,__FILE__);
    $rs=sqlrun("select * from i_grid where par_rowid='{$row['rowid']}' order by seqno",__LINE__,__FUNCTION__,__FILE__);
    
    if($rs->num_rows<1) {
        echo "<h1>No Grid data</h1>";
        $mysqli->close();
        return ;
    }
    $n=1;
    while($row=$rs->fetch_assoc()){
        errorlog(">>> Building Column",__LINE__,__FUNCTION__,__FILE__);
        $rsCol=sqlrun("select * from i_col where par_rowid='{$row['rowid']}' and inactivated='0' order by seqno",__LINE__,__FUNCTION__,__FILE__);
        
        $gLayout=$gSide="";
        
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
                            // if($rowCol['dtype']=="select"){
                            //     $strList=makeList($rowCol);
                            //     errorlog("list [{$strList}]",__LINE__,__FUNCTION__,__FILE__);
        
                            /*} else*/ if($rowCol['dtype']=="radio"){
                                $strList=$rowCol['element'];
                            }
                            $gLayout.="<th {$thStyle} dtype={$rowCol['dtype']} digit={$rowCol['digit']} pixel={$rowCol['pixel']} list='{$strList}'>{$rowCol['title']}</th>";
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
                    if($rowCol['dtype']=="radio"){
                        $strList=$rowCol['element'];
                    }
                    $gLayout.="<th {$thStyle} dtype={$rowCol['dtype']} digit={$rowCol['digit']} pixel={$rowCol['pixel']} list='{$strList}'>{$rowCol['title']}</th>";
                }
                break;
            case "VERT":
                while($rowCol=$rsCol->fetch_assoc()){
                    if($rowCol['pixel']=="0"){
                        $trStyle="style='display:none;'";
                        $thStyle="";
                    } else {
                        $trStyle="height=24px";
                        $thStyle="style='background-color:black;color:white;text-align:right;' align=center";
                    }
                    if($rowCol['dtype']=="radio"){
                        $strList=$rowCol['element'];
                    }
                    $gSide.="<tr {$trStyle}><td {$thStyle} dtype={$rowCol['dtype']} digit={$rowCol['digit']} pixel={$rowCol['pixel']} list='{$strList}'>{$rowCol['title']}&nbsp;&nbsp;</td></tr>";
                }
        }
        $btndelete="";
        
        if(intval($row['can_remove'])>0) $btndelete.="&nbsp;<label style='display:none;' id=delete{$row['rowid']}>삭제</label";
//         if($btndelete!="") $btndelete.="&nbsp;<button class='ui-icon ui-icon-refresh' style='width:20px;height:20px' id=reload{$row['rowid']} />";  link breaking issue happens.
        if($btndelete!="") $btndelete.="&nbsp;&nbsp;<label id=reload{$row['rowid']}>새로고침</label>";
        if($row['searchable']=="1") $btndelete.="&nbsp;<input type size=20 id=srch{$row['rowid']}>&nbsp;<label id='find{$row['rowid']}'>찾기</label>";
        $addnew="";
        if($row['can_insert']=="1") {
            $addnew="&nbsp;&nbsp;<label id=addnew{$row['rowid']}>새로 추가</label>";
            if($row['viewtype']=="TREE"){
                $addnew.="&nbsp;&nbsp;<label id=levellt{$row['rowid']} style='display:none;'>&larr;</label>".
                    "&nbsp;&nbsp;<label id=levelup{$row['rowid']} style='display:none;'>&uarr;</label>".
                    "&nbsp;&nbsp;<label id=leveldn{$row['rowid']} style='display:none;'>&rarr;</label>".
                    "&nbsp;&nbsp;<label id=levelrt{$row['rowid']} style='display:none;'>&darr;</label>";
            }
        }
       
        // page layout
        $tWidth=$row['tWidth'];
        if(is_numeric($tWidth)){
            $tWidth.="px";
        } else if(substr($tWidth,-1)!="%" && substr($tWidth,-2)!="px"){
            $tWidth="100%";
        }
        $tHeight=$row['tHeight'];
        if(is_numeric($tHeight)){
            $tHeight.="px";
        } else if(substr($tHeight,-1)!="%" && substr($tHeight,-2)!="px"){
            $tHeight="100%";
        }
        $gLayout="<div id=hd{$row['rowid']} style='width:{$tWidth};cursor:pointer;border:1px solid green;'>".
            "<table width=100%><caption title='".($_SESSION['member_id']==SUPERVISOR?" [{$row['rowid']}]":"")."'>{$row['classname']}</caption>".
            "<tr><td width=100%>{$btndelete}{$addnew}</td></tr></table></div>".
            "<div id=bd{$row['rowid']} style='width:{$tWidth};height:{$tHeight};overflow:auto;'>".
            "<table class=hovering id=tbl{$row['rowid']} viewtype={$row['viewtype']} border=1 style='border:1px solid gray;border-collapse:collapse;cursor:pointer;'>".
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
}
?>