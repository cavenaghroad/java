<?php 
include_once("comfunc.php");

$limit = "";
$strLog = "";
$orderby = " order by a.name_kr";
$log = $_GET['optype']."::";
$maintable = $t_member;
$nMax = 0;
$t_config = "a_config";

switch( $_GET['optype'] ) {
case 'query_newcomer':
case 'download_newcomer':
	$maintable = $t_membertobe;
case "query":	
case 'query1':
	$limit = " limit ".$_GET['curpage'].",".$_GET['pagesize'];
	if( $_GET['optype'] == "query" )	$limit = "";
	
case 'download':
	$sql = "select item_value from ".$t_config." where school='".$_SESSION['school']."' and item_code='".$_GET['_screen']."'";
	@$nCount = runQuery($sql,$result);
	$fieldlist = "";
	if( $nCount > 0 ) {
		$row = mysql_fetch_array($result);
		if( $row )	$fieldlist = $row['item_value'];
	}
	if( $fieldlist == "" )	{
		$fieldlist = "a.member,a.saint_kind,a.name_kr,a.birth_date,a.gender,a.phone_mobile,a.addr_home";
	}
	if( $_GET['optype'] == "query" && $_GET['picture'] == "1" ) {
		$fieldlist = str_replace("member","member,a.picture",$fieldlist);
	}
	$log .= $table;

	$n = 1;
	$sql = "select  ".$fieldlist." "; 		// chainfield 대상이면 table alias는 빼고 기술한다.
	$sqlcount = "select count(*) ";
	$sqlregdate = "select a.member "; 
	$from = "from a_member a ,a_member2school b"; 
	$where = " where b.school='".$_SESSION['school']."' and a.member=b.member ";
	$table = $from;
	$pWhere = $where;

	$sqlLOV = "select listing,chainfield,join_table,link,code from ".$t_lov." where flag='root' and school='".$_SESSION['school']."' and listing>0 order by listing";
	@$nCount = runQuery($sqlLOV,$result);

	while( $row = mysql_fetch_array($result) ) {
		filelog("chainfield [".$row['chainfield']."] code [".$row['code']."] [".$_GET[$row['code']]."]");
		if( $row['chainfield'] == "" )	{
			if( $_GET[$row['code']] == "none" ) {
				$pWhere .= " and a.".$row['code']." is null ";
			} else if( $_GET[$row['code']] != "all" ) {
				$pWhere .= "and a.".$row['code']."='".$_GET[$row['code']]."' ";
			}
		} else {
			//$chainfield = str_replace("x.","a".$n.".",$row['chainfield']);
			$chainfield = $row['chainfield'];
			
			$sql = str_replace($row['code'],$chainfield." ".$row['code'],$sql);
			if( $row['join_table'] != "" )	$table .=" left outer join ".$row['join_table']." on ".$row['link'];
			$table = str_replace("x","a".$n,$table);

			if( $_GET[$row['code']] == "none" ) {
				$pWhere .= " and ".$chainfield." is null";
			} else if( $_GET[$row['code']] != "all" ) {
				$pWhere .= " and ".$chainfield."='".$_GET[$row['code']]."' ";
			}
			$n++;
		}
		$strLog .= "(".$row['code']."=".$_GET[$row['code']]."] ";
	} 
	$log .= "[".$pWhere."]";
	filelog("sqlLOV [".$sql."]");
	$sqlcount .= $table.$pWhere;
	$nCount = runQuery($sqlcount,$result);
	$nTotal = 0;
	if( $nCount > 0 )	 {
		$row = mysql_fetch_array($result);
		$nTotal = intval($row[0]);
	}
	filelog("nTotal [".$nTotal."]");

	$sqlregdate .= $table.$pWhere.$orderby.$limit;
	$nCount = runQuery($sqlregdate,$result);
	
	$members = "";
	while( $row = mysql_fetch_array($result) ) {
		if( $members != "" )	$members .= ",";
		$members .= "'".$row['member']."'";
	}
	$nCount = 0;
	if( $members != "" ) {
		$sql .= $table.$pWhere." and a.member in (".$members.") ".$orderby;
		filelog("{sql} ".$sql); 
		$nCount = runQuery($sql,$result);
	}
	break;
case "attendance":		// attend, fee
	$sql = "select ".$_GET['_column']." from ".$_GET['_tname']." where school='".$_SESSION['school']."' and member in ('".str_replace(",","','",$_GET['members'])."') order by ".$_GET['orderby'];
	echo $sql."<br>"; 
	$nCount = runQuery($sql,$result);
	$nTotal = $nCount;
	break;
case "querylog":
	$sql = "select ".$_GET['_column']." from ".$_GET['_tname']." where school='".$_GET['school']."' and substring(logdate,0,8) ".
				"between '".$_GET['startdate']."' and '".$_GET['enddate']."' order by logdate desc limit ".$_GET['curpage'].",".$_GET['pagesize'];
	$nCount = runQuery($sql,$result);
	break;
case 'Attend':
case 'Fee':
	$latest_date = "last_pay_date"; $actual_latest_date = "last_paid_date"; $empty_date = "empty_pay_date";
	$sql = "select a.member,b.last_pay_date,b.last_paid_date,c.empty_pay_date,x.name_kr from a_member2school a ".
			"left outer join (select member,max(pay_date) last_pay_date,max(actual_pay_date) last_paid_date from a_fee ".
								  "where pay_date is not null and actual_pay_date is not null group by member) b on a.member=b.member ".
			"left outer join (select member,pay_date empty_pay_date from a_fee where actual_pay_date is null) c on a.member=c.member, a_member x ".
				"where (b.last_pay_date is not null or c.empty_pay_date is not null) and ".
						  "a.school='".$_GET['school']."' and a.saint_kind='".$_GET['saint_kind']."' and a.member=x.member order by a.member,c.empty_pay_date";
	if( $_GET['optype'] == "Attend" ) {
		$sql = str_replace("pay","attend",str_replace("paid","attended",str_replace("a_fee","a_attend",$sql)));
		$latest_date = str_replace("pay","attend",$latest_date);
		$actual_latest_date = str_replace("paid","attended",$actual_latest_date);
		$empty_date = str_replace("pay","attend",$empty_date);
	} 
	$nCount = runQuery($sql,$result);
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('root');
	$root = $doc->appendChild($root);
	
	$child = createNode($doc, $root, "reccount",$nCount);
	
	if( $nCount > 0 ) {
		$member = ""; $cnt = 0;
		while( $row = mysql_fetch_array($result) ) {
			if( $member != $row['member'] ) {
				if( $member != "" ) {
					$child = createNode($doc, $occ, "count", $cnt);
					$cnt = 0; 
				}
				$member = $row['member'];

				$occ = createNode($doc, $root,"crlf");
				$child = createNode($doc, $occ, "member", $member);
				$child = createNode($doc, $occ, "name_kr", $row['name_kr']);
				$child = createNode($doc, $occ, $latest_date, $row[$latest_date]);
				$child = createNode($doc, $occ, $actual_latest_date, $row[$actual_latest_date]);
			}
			$child = createNode($doc, $occ, $empty_date, $row[$empty_date]);
	        $cnt++;
		}
	}
	header("Content-type: text/xml");
	$xml_string = $doc->saveXML();
	echo $xml_string;
	exit;

default:
	switch( $_GET['optype'] ) {
	case "title":
		$sql = "select item_value from ".$t_config." where school='".$_SESSION['school']."' and item_code='".$_GET['screen']."title'";
		@$nCount = runQuery($sql,$result);
		$nTotal = $nCount;
		break;
	case "board":
		$sql = "select item_value from ".$t_config." where school='".$_SESSION['school']."' and item_code='".$_GET['screen']."'";
		@$nCount = runQuery($sql,$result);
		if( $row = mysql_fetch_array($result) )		$field = $row[0];
		else $field = "a.regdate,a.legend,a.title,a.name_kr,a.updated,a.created";
		
		$strLegend = "";
		if( $_GET['legend'] != "all" ) $strLegend = " and a.legend='".$_GET['legend']."' ";
		$sql = "select count(*) from a_inform where school='".$_GET['school']."'".$strLegend;
		@$nCount = runQuery($sql,$result);
		if( $nCount > 0 ) {
			$row = mysql_fetch_array($result);
			$nTotal =  $row[0];
		} else	$nTotal = "0";
		$sql = "select ".$field." from a_inform a where a.school='".$_GET['school']."'".$strLegend." order by a.regdate desc limit ".$_GET['curpage'].",".$_GET['pagesize'];
		$nCount = runQuery($sql,$result);
		break;
	case "search":
		$arField = array("item_value"=>"","tables"=>"","join_expr"=>"","search_fields"=>"","sort_order"=>"");
	
		$sql = "select * from ".$t_config." where school='".$_SESSION['school']."' and item_code='".$_GET['screen']."'";
		@$nCount = runQuery($sql,$result);
		$row = mysql_fetch_array($result);	//$pstr = $row['join_expr'];
		foreach( $arField as $key =>$value ) {
			$arField[$key] = $row[$key];
			foreach( $_GET as $column => $fieldvalue ) {
				$arField[$key] = str_replace("$".$column."$",$fieldvalue,$arField[$key]);
			}
		}
		$sql = "select ".$arField['item_value']." from ".$arField['tables']." where ".$arField['join_expr'];
		if( $arField['sort_order'] != "" ) $sql .= " order by ".$arField['sort_order'];
		@$nCount = runQuery($sql,$result);
		$nTotal = $nCount;
		break;
	case "exequery":
		
		break;
	default:
	}
	
	$doc = new DOMDocument('1.0');
	$root = $doc->createElement('root');
	$root = $doc->appendChild($root);
	
	$child = createNode($doc, $root, 'totalcount', $nTotal);
	$child = createNode( $doc, $root, 'reccount', $nCount);
	 for( $pstr ="", $i = 0; $i < mysql_num_fields($result); $i++) {
	    if( $i != 0 )	$pstr .= ",";
	    $pstr .= mysql_field_name($result,$i);
	}
	$child = createNode( $doc, $root, "fieldlist", $pstr);
	
	if( $nCount > 0 ) {
		while($row = mysql_fetch_array($result)) {
		    $occ = createNode( $doc, $root, 'crlf', '' );
		
		    for( $i = 0; $i < mysql_num_fields($result); $i++) {
		    	$child = createNode( $doc, $occ, mysql_field_name($result,$i), $row[$i]);
		    } // foreach
		} // while
	}
	
	header("Content-type: text/xml");
	$xml_string = $doc->saveXML();
	echo $xml_string;
	$strLog = $_GET['optype']."::".$strLog;
	makelog($sql);
	exit;
}

$doc = new DOMDocument('1.0');
$root = $doc->createElement('root');
$root = $doc->appendChild($root);

$child = createNode($doc, $root, 'totalcount', $nTotal);
$child = createNode( $doc, $root, 'reccount', $nCount);

if( $nCount > 0 ) {
	while($row = mysql_fetch_array($result)) {
	    $occ = createNode( $doc, $root, 'crlf', '' );
	
	    for( $i = 0; $i < mysql_num_fields($result); $i++) {
	    	$child = createNode( $doc, $occ, mysql_field_name($result,$i), $row[$i]);
	    } // foreach
	} // while
}

header("Content-type: text/xml");
$xml_string = $doc->saveXML();
echo $xml_string;
$strLog = $_GET['optype']."::".$strLog;
makelog($sql);
?>