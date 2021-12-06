<?php 
include_once("include.php");

$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$_now= date('YmdHis'.$micro);

$answer=array();
$answer['result']=-1;
$answer['msg']="";

$log="";
switch($_POST['optype']){
	case "newest": case "check-expiry":
		break;
	default:
		foreach($_POST as $key=>$value)	{
			if($key=="optype") {
				$log.="{$key} [<font color=red>{$value}</font>]";
			} else{
				$log.="{$key} [{$value}] ";
			}
		}
		wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);
}
/*if($_POST['optype']!="newest")*/

$pColumn = "*"; $strLog = $_optype."::";
$log = "{{$_optype}} ";

$sqlCount = "";
// foreach( $_POST as $keys => $values ) {
// 	wlog($keys." [{$values."]");
// }
switch( $_POST['optype'] ) {
case "resetlog":
	resetLog(__LINE__,__FUNCTION__,__FILE__,true);
	break;
case "wlog":
	wlog(">>>{$_POST['logtext']}",__LINE__,__FUNCTION__,__FILE__);
	exit;
case "duplicate":
    try{
        $sql="select userid from a_member where userid='{$_POST['userid']}'";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs->num_rows>0){
            throw new Exception(_label("existing user",$_SESSION['lang']));
        }
        $answer['result']=0;
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
        
    }
    echo json_encode($answer);
    break;
case "buildTree":
	$sql = "select distinct a.party,b.name_kor name_ko from a_navi a left join a_party b on a.party=b.party order by b.name_kor";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows<1){
		$mysqli->close();
		exit;
	}
	break;


case "logon": // to display multiple companies user belongs to.
    //	resetLog(__LIINE__,__FUNCTION__,__FILE__,true);
	$retval = "";
	$_sql = "select member_id,userid,member_name,passcode from a_member where userid='{$_POST['userid']}'";// and passcode='{$_POST['passcode']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false) {
		$mysqli->close();
		exit;
	}
	$self = new xmlCls();
	if( $rs->num_rows < 1 ){
		$child=$self->createFirst("errmsg","There is no user ID [{$_POST['userid']}].");
	} else {
		$bExist=false;
		while($row=$rs->fetch_array(MYSQLI_BOTH)){
			if( $row['passcode'] == $_POST['passcode'] ) {
				$bExist=true;
				break;
			}
		}
		if( !$bExist ) $child=$self->createFirst("errmsg","You have given incorrect password to login");
		else {
			$self->createFirst("errmsg","");
	
			$_SESSION['userid'] = $_POST['userid'];
			$_SESSION['member_id'] = $row["member_id"];
			$_SESSION['member_name'] = $row['member_name'];
	
			$child=$self->createFirst("member_id",$_SESSION['member_id']);
			$child=$self->createFirst("member_name",$_SESSION['member_name']);
			
			$_sql="select b.party,b.name_kor party_name,a.level from a_mem_par a, a_party b where a.member_id='{$_SESSION['member_id']}' and a.party=b.rowid";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false || $rs->num_rows<1){
				$mysqli->close();
				exit;
			}
			while($row = $rs->fetch_array(MYSQLI_BOTH)){
				$crlf=$self->createFirst("crlf");
				for( $i=0; $i<$rs->field_count; $i++){
					$rs->field_seek($i);
					$finfo=$rs->fetch_field();
					$child=$self->createNode($crlf,$finfo->name,$row[$i]);
				}
			}
			$sql = "update a_member set last_login=now() where member_id='{$_SESSION['member_id']}'";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			wlog("member [{$_SESSION['member_id']}] member_name [{$_SESSION['member_name']}]",__LINE__,__FUNCTION__,__FILE__);
		}
	}
	$self->sendXML();
	exit;

    
case "menu":
	$view_level = chkMemShip($_POST['_e']);
	if( $view_level == -1 ) exit;
	
	$sql = "select b.name_kor party_name,a.title,a.seqno,a.rowid,a.par_rowid,a.page_id _p,a.view_level ".
			"from a_navi a left outer join a_party b on a.party=b.party ".
			"where a.inactivated='0' and a.party='{$_POST['_e']}' and a.view_level>={$view_level} order by party_name,a.par_rowid,a.seqno";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try{
		$rs=$mysqli->query($sql);
		wlog("num_rows [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		$answer['rcount']=$rs->num_rows;
		$answer['crlf']=array();
		while($row=$rs->fetch_array(MYSQLI_ASSOC)){
			wlog("field_count [{$rs->field_count}]",__LINE__,__FUNCTION__,__FILE__);
			array_push($answer['crlf'],$row);
		}
		$answer['result']="0";
	} catch(Exception $e){
		$answer['msg']=$e->getMessage();
		wlog("error [{$e->getMessage()}]",__LINE__,__FUNCTION__,__FILE__);
	}
	echo json_encode($answer);
	break;
	
case "newcomer":
	$sql = "select member,member_name,passcode from {$t_member} where member='{$_POST["member"]}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs!==false && $rs->num_rows > 0 ) {
		$row = $rs->fetch_array(MYSQLI_BOTH);
		if( $row['passcode'] == "" ) {
			echo "empty_passcode";
		} else {
			echo "exist";
		}
	} else {
		$sql = "select member,member_name,passcode from {$t_membertobe} where member='{$_POST["member"]}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if($rs!==false && $rs->num_rows > 0 ) {
			echo "outstanding";
		} else {
			echo "newcomer";
		}
	}
	exit;

case "newneterprise":
	$arMember = array("an administrator","a candidate","a member");
	
	if( $_POST['party'] == "" )	$party = strtoupper(dechex(date("YmdHis")));
	$sql = "select * from a_party where party='{$party}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 1 ) {
		$sql = "insert into a_party(rowid,party,name_kor) values ('".encode_html($party)."','".encode_html($party).
			"','".encode_html($_POST['name_kor'])."')";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
	}
	if( $_POST['member_id'] == "" )	$member = strtoupper(dechex(date("YmdHis")));
	$sql = "select * from a_member where member='{$member}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 1 ) {
		$sql = "insert into a_member(rowid,member,member_name) values ('".encode_html($member)."','".encode_html($member)."','".
			encode_html($_POST['member_name'])."')";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
	}
	$sql = "select * from a_mem_par where party='{$enteprise}' and member='{$member}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 1 ) {
		$level = 2;
		$sql = "insert into a_mem_par (rowid,party,member,level) values ('".encode_html($enteprise)."','".encode_html($party)."','".
				encode_html($member)."',".encode_html($level).")";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
	}
	if( $rs->num_rows < 1 )	die("failed to create record in a_mem_par.");
	$sql = "select * from a_mem_par where party='{$party}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs!==false && $rs->num_rows > 1 ) {
		if( intval($_POST['level']) < 1 ) die('if you are not first member you cannot '.$arMember[intval($_POST['level'])] );
	}
	$sql = "update a_mem_par set level=".encode_html($_POST['level'])." where party='{$party}' and member='{$member}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs!==false && $rs->num_rows > 0 )	{
		$mysqli->close();
		die('you became '.$arMember[intval($_POST['level'])]);
	}
	die('you failed to become '.$arMember[intval($_POST['level'])]);
	break;

case "newparty1":
	if( $_POST['party'] == "" )	{
		echo "no party code";
		exit;
	}

	$arSector = array("Academy"=>"124CA104441D","Restaurant"=>"124CC9F6D9EA","Church"=>"124EE920F598");

	$sql = "update a_member set firstpage=(select screenurl from a_navi where party='".encode_html($arSector[$_POST['sector']]).
		"' and firstpage='1') where member='{$_POST['member_id']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 0 ) {
		$rs=$mysqli->query("rollback");
		$mysqli->close();
		echo "fail2setpersonal";
		exit;
	}
	$_table = array("a_navi","a_lov");

	// menu and LOV for exsiting same party will be removed as initialization.
	for( $n=0; $n<count($_table); $n++ ) {
		$sql = "select * from {$_table[$n]} where party='{$arSector[$_POST['sector']]}'";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if(($rs===false || $rs->num_rows < 1) && $_table[$n] != "a_lov" ) {		// "a_lov" is allowd to have no standard list.
			$rs=$mysqli->query("delete from a_member where member='{$_POST['member_id']}'");
			$rs=$mysqli->query("delete from a_party where party='{$_POST['party']}'");
			echo "fail2get4base";
			$mysqli->close();
			exit;
		}
		while( $row = $rs->fetch_array(MYSQLI_BOTH) ) {
			$sql = "insert into {$t_navi} values (";
			for( $i = 0; $i < $rs->field_count; $i++) {
				$rs->field_seek($i);
				$finfo=$rs->fetch_field();
				$fieldname = $finfo->name;
				if( $i > 0 )	$sql .= ",";
				switch( $fieldname ) {
					case "party":
						$sql .= "'{$_POST[$fieldname]}'"; break;
					case "createdby" : case "updatedby":
						$sql .= "'{$_POST['member_name']}'"; break;
					case "created": case "updated":
						$sql .= strtoupper(dechex(date('YmdHis'))); break;
					default:
						$sql .= "'{$row[$i]}'";
				}
			}
			$sql .= ")";
			wlog($sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($sql);
			if($rs===false || $rs->num_rows<1) {
				$rs=$mysqli->query("delete from a_member where member='{$_POST['member_id']}'");
				$rs=$mysqli->query("delete from a_party where party='{$_POST['party']}'");
				$mysqli->close();
				echo"fail2menu";
				exit;
			}
		}
	}
	$log .= $_POST['name_kor']." was built";
	echo "ok";
	exit;
	
		//case "updateattendance":
		//
		//case "updatefee":
		//	$today = date("Y-m-d");
		//	// calculate the outsatnding date.
		//	$sql = "select a.member,a.pay_day,a.next_pay_date,b.last_pay_date  from {$t_m2e} a left outer join (".
		//				"select member,min(pay_date) last_pay_date from a_member4fee where party='{$_SESSION['party']}' and actual_pay_date is not null group by member) b ".
		//				"on a.member=b.member order by a.member";
		//	$rs->num_rows = runQuery($sql,$result);
		//	while( $row = mysql_fetch_array($result) ) {
		//		if( $row['next_pay_date'] == "" ) {		// ë“±ë¡�ì§�í›„ì�¸ ìƒ�íƒœ.
		//
		//		}
		//		if( $row['last_pay_date'] != "" || $row['last_pay_date'] >= $today ) continue;
		//
		//
		//	}
		//	break;

case "fillselect":
	$texstr=$_POST['textstr'];
	$valstr=$_POST['valstr'];
	$sql = "select {$texstr},{$valstr} from {$_POST['tabstr']} order by {$texstr}";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 1 ) {
		$mysqli->close();exit;
	}
	$self=new xmlCls();
	while($row=$rs->fetch_array(MYSQLI_BOTH)){
		$child=$self->createFirst('crlf');
		$gchild=$self->createNode($child,$texstr,$row[$texstr]);
		$gchild=$self->createNode($child,$valstr,$row[$valstr]);
	}
	$self->sendXML();
	exit;
	
case "sendpasscode":
	$sql = "select passcode from {$table_member} where userid='{$_POST['userid']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 1 )	echo "fail";
	else {
		$row = $rs->fetch_array(MYSQLI_BOTH);
		if( $row['passcode'] != "" )	 $passcode = $row['passcode'];
		else {
		}
	}
	break;
	
case "setlog":
	if( $_POST["logwrite"] == "-1" )	 {
	    resetLog(__LINE__,__FUNCTION__,__FILE__,true);
	}
	exit;

case "showtable":
	$sql = "show columns from {$_post['']}";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	break;

case "newmember":
    $mysqli->autocommit(false);
    try{
        $rowid=getROWID();
        $sql="insert into a_member set member_id='{$rowid}',userid='{$_POST['userid']}',passcode='{$_POST['passcode']}'";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($mysqli->affected_rows==0){
            throw new Exception($mysqli->error);
        }
        $answer['msg']=_label("need2login",$_SESSION['lang']);
        $mysqli->commit();
        $answer['result']=0;
    } catch(Exception $e){
        $mysqli->rollback();
        $answer['msg']=$e->getMessage();
    }
    $mysqli->autocommit(true);    
    echo json_encode($answer);
    
	break;

case "transfer":
	$retval = "fail";
	if( $_POST['userid'] != "" )	$_where = " where userid = '{$_POST['userid']}'";
	else if( $_POST['regdate'] != "" )	$_where = " where regdate = '{$_POST['regdate']}'";
	else	exit;

	$sql = "replace into {$table_member}  select * from {$table_newcomer}{$_where}";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if( $rs->num_rows > 0 )		$retval = "ok";
	else	{
		$retval = "fail";
		echo $retval;
		exit;
	}
	if( strtoupper($_POST['remove']) == "Y" ) {
		$sql = "delete from {$table_newcomer}{$_where}";
		wlog($sql,__LINE__,__FUNCTION__,__FILE__);
		$rs=$mysqli->query($sql);
		if( $rs->num_rows > 0 ) $retval = "ok";
		else	$retval = "fail";
	}
	echo $retval;
	exit;

case "wlog":
	wlog($_POST['log'],__LINE__,__FUNCTION__,__FILE__);
	break;
case "update1":
	$sql = "update {$_POST['_tname']} set ";
	$log .= $_POST['_tname'];
	$where = "";

	$_pname = explode(",",$_POST['_pname']);
	for( $i=0; $i<count($_pname); $i++ ) {
		if( $where == "" )		$where .= " where ";
		else	$where .= " and ";
		$fieldvalue = $_pname[$i];
		if( $fieldvalue == "_table" )	$where .= "_tname=";
		else $where .= $fieldvalue."=";
		if( $_POST[$fieldvalue] == '' )    $_POST[$fieldvalue] = "null";
		else if( $_POST[$fieldvalue] == "PRIMARY_YMDHIS" ) $_POST[$fieldvalue] = strtoupper(dechex(date('YmdHis')));
		else if( $_POST[$fieldvalue] == "PRIMARY_YMD" )	$_POST[$fieldvalue] = PRIMARY_YMD;
		else if( !is_numeric($_POST[$fieldvalue]) )	$_POST[$fieldvalue] = "'".encode_html($_POST[$fieldvalue])."' ";
		$where .= encode_html($_POST[$fieldvalue]);
	}
	if( $where == "" )	{
		echo "none";
		exit;
	}
	foreach( $_POST as $column => $fieldvalue ) {
		$fieldvalue = strval($fieldvalue);
		if( $column == "optype" || $column == "_tname" || $column == "_pname" /*|| substr($column,0,1) == "_" || $column == "" || $column == "passcode1" || $column == "password1" */) continue;
		if( strpos($_POST['_pname'],$column) === false ) {
			if( $column == "_table" )	$sql .= "_tname=";
			else $sql .= $column."=";
			if( $fieldvalue == "" )    $fieldvalue = "null";
			else if( $fieldvalue == "PRIMARY_YMDHIS" ) $fieldvalue = strtoupper(dechex(date('YmdHis')));
			else if( $fieldvalue == "PRIMARY_YMD" )	$fieldvalue = PRIMARY_YMD;
			else if( !is_numeric($fieldvalue) )	$fieldvalue = "'".encode_html($fieldvalue)."' ";
			$sql .= encode_html($fieldvalue);
		}
		// 		else if( substr($column,-2) != "_p" ) $fieldvalue = "'{$fieldvalue}'";
	}
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql.$where);
	if( $rs!==false && $rs->num_rows > 0 )   $strEcho = "ok" ;
	else $strEcho = "fail";
	echo $strEcho;
	$mysqli->close();
	exit;

case "update1":
	/*
	 * optype=update&_tname=&_column=col1,col2,..,colN&col1=&col2=&...&colN=&_pname=p1,p2,...,pN&p1=&p2=&...&pN=
	 */
	$arKey = explode(",",$_POST['_pname']);
	$_column = explode("^",$_POST['_column']);
	$_where=""; $pColumn="";
	for( $i=0; $i<count($arKey); $i++ ) {
		if( $_where == "" )	$_where .= " where ";
		else	$_where .= " and ";
		$_where .= $arKey[$i]."=";
		if( !is_numeric($_POST[$arKey[$i]]) ) $_where .= "'{$_POST[$arKey[$i]]}'";
		else	if( strlen($_POST[$arKey[$i]]) == 14 )	$_where .= "'{$_POST[$arKey[$i]]}'";
		else	$_where .= $_POST[$arKey[$i]];
	}
	for( $i=0; $i<count($_column); $i++ ) {
		if( $pColumn != "" ) $pColumn .= ",";
		$pColumn .= $_column[$i]."=";
		if( !is_numeric($_POST[$_column[$i]]) ) $pColumn .= "'".encode_html($_POST[$_column[$i]])."'";
		else if( strlen($_POST[$_column[$i]]) == 14 ) $pColumn .= "'".encode_html($_POST[$_column[$i]])."'";
		else	$pColumn .= encode_html($_POST[$_column[$i]]);
	}
	if( $_where == "" )	exit;
	$sql = "update {$_POST['_tname']} set {$pColumn}{$_where}";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	break;
	
case 'noticewidget':
	$sql = "select * from ".bbs_config." where school='bulletin' and item_code='{$_POST['_itemcode']}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 1 )	exit;
	$info = $rs->fetch_array(MYSQLI_BOTH);

	$arTitle=array(); $arWidth=array(); $arField=array(); $arAlign=array();
	if( $info['item_value_widget'] == "" )	exit;

	$_column = explode(',',$info['item_value_widget']);
	$_pstr = "<tr height=20px>";
	for( $i=0; $i<count($_column); $i++) {
		$_itemvalue = explode('|',$_column[$i]);
		array_push($arField,$_itemvalue[0]);
		array_push($arTitle,$_itemvalue[1]);
		array_push($arWidth,$_itemvalue[2]);
		array_push($arAlign,$_itemvalue[3]);
		if( strpos($_itemvalue[0],"created") != false )	continue;
		else if( strpos($_itemvalue[0],"picture") !== false ) {
			if( $info['show_picture_widget'] != "1" ) continue;
		} else if( $_itemvalue[1] == "" ) continue;
		$_pstr .= "<td style='padding-left:10px;padding-right:10px' width={$_itemvalue[2]}px align=center>{$_itemvalue[1]}</td>";
	}
	$_pstr .= "</tr>";
	$_temp = implode(",",$arField);
	if( $_temp == "" ) $_temp = "*";
	$sql = "select {$_temp} from ".bbs_table." where school='bulletin' and par_rowid='' and title <> '' and notice in ('1','2') order by created_at desc limit 0,{$_POST['_rownum']}";

	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	$rs=$mysqli->query($sql);
	if($rs===false || $rs->num_rows < 1 ) exit;
	while( $row = $rs->fetch_array(MYSQLI_BOTH)) {
		$_record = "";
		// 		$_url = "../church/blltn.php?_t={$_POST['_itemcode']}&_created={$row['created'];
		for( $i=0; $i<$rs->field_count; $i++ ) {
			if( $arField[$i] == 'created') {
				$_record = "<td style='display:none'>".decode_html($row[$i])."</td>{$_record}";
			} else if( strpos($arField[$i],"picture") !== false ) {
				if( $info['show_picture_widget'] == "1" ) {
					$_record = "<td><img src='../picture/".decode_html($row[$i])."' width=50px ></td>";
				}
			} else {
				$_record .= "<td style='padding-left:10px;padding-right:10px' align={$arAlign[$i]}>".decode_html($row[$i])."</td>";
			}
		}
		$_pstr .= "<tr height=20px style=\"cursor:hand\" onMouseOver=\"this.style.background='yellow';\" onMouseOut=\"this.style.background='white';\"'>{$_record}</tr>";
	}
	echo $_pstr;
	wlog($_pstr,__LINE__,__FUNCTION__,__FILE__);
	break;
}
$mysqli->close();

function chkMemShip($_e){
	global $mysqli;
	if($_SESSION['member_id']=="12480F802448") return 0; // xaexal@gmail.com
	
	$sql = "select level from a_mem_par where member_id='{$_SESSION['member_id']}' and party='{$_e}'";
	wlog($sql,__LINE__,__FUNCTION__,__FILE__);
	try{
		$rs=$mysqli->query($sql);
// 		wlog("RS is common",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false || $rs->num_rows<1) $retval=-1;
		else {
			$row=$rs->fetch_array(MYSQLI_BOTH);
			$retval=  intval($row['level']);
		}
// 		wlog("retval [{$retval}]",__LINE__,__FUNCTION__,__FILE__);
	} catch(Exception $e){
		wlog("error [{$e->getMessage()}]",__LINE__,__FUNCTION__,__FILE__);
	}
	return $retval;
}

//------------------------------ End of File -------------------------------------------
?>