<?php 
function form_builder($screen_id,$formname) {
$sql = "select count(*) from screeninfo where screen_code='$screen_id' and formname='$formname' and field_tag_type != 'hidden'";
@$nCount = runQuery($sql,$result);
$nColumn = 2;
if( $nCount > 0 )	 {
	$row = mysql_fetch_array($result);
	$nColumn = intval($row[0])/10;
}

$sql = "select * from screeninfo where screen_code='scr9999' and formname='frmSaint' order by position_num";
@$nCount = runQuery($sql,$result);

// initialization
$formname = "";
$formtype = "";
$group_num = "";
$bLanguge = "kor";
$nCol = -1;
$pOut = "";
//
while( $row = mysql_fetch_array($result) ) {
	if( $row['formname'] != $formname ) {
		while( $nCol > 0 && $nCol < $nColumn-1 ) {
			$pOut .= "<td>&nbsp;</td>";
		}
		if( $formname != "" )	{
			$pOut .= "</tr>";
			if( $formtype == "list") {
				$fname = substr($formname,3);
				$pOut .= "<tr>".
        			"<td>&nbsp;<button  class='ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all' id=btnBudNew name=btn".$fname."New>New</button>&nbsp;".
        			"<button  class='ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all' id=btnBudAdd name=btn".$fname."Add>등록</button>&nbsp;".
        			"<button  class='ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all' id=btnBudDelete name=btn".$fname."Delete>삭제</button></td>".
        		"</tr>";
			}
			$pOut .= "</table></form><br>";
		}
		$formname = $row['formname'];
		$pOut .= "<form id='".$formname."' name='".$formname."'><table class=tonepx width=100% id='".str_replace("frm","tbl",$formname)."' name='".str_replace("frm","tbl",$formname)."'>";
	}
	
	$pTitle = $row['title_'.$bLanguage];
	
	$default_value = $row['field_default_value']; 
	if( substr($default_value,0,3) == "GET" ) $value_str = $_GET[substr($default_value,4)];
	else if( substr($default_value,0,4) == "POST" ) $value_str = $_POST[substr($default_value,5)];
	else	$value_str = $default_value;
	
	switch( $row['field_tag_type'] ) {
	case "hidden":
	case "text":
//	case "checkbox":
	case "dateYMD":
		$pCtrl = "<input type=".$row['field_tag_type']." name='".$row['field_name']."' id='".$row['field_name']."'";
		if( $row['field_tag_type'] != "hidden" ) 	$pCtrl .= " size='".$row['field_size']."' maxlength='".$row['field_maxlength']."' onfocus='this.select()' class='txt";
		if( $row['field_tag_type'] == "dateYMD" )	$pCtrl .= " rt";
		$pCtrl .= "' value='".$value_str."'>";
		if( $row['field_name'] == "_formtype" )	$formtype = $row['field_default_value'];
		break;
	case "select-one":
		$pCtrl = "<select name='".$row['field_name']."' id='".$row['field_name']."' ";
		if( $row['field_maxlength'] != "" )	$pCtrl .="style='width:".$row['field_maxlength']."px'";
		$pCtrl .= ">";
		$sql = "select name_kr from ".$row['foreign_table']." where church='".$_SESSION['church']."' and flag='".$row['foreign_field']."' order by name_kr";
		$nfCount = runQuery($sql,$f_result);
		while( $f_row = mysql_fetch_array($f_result) ) {
			$pCtrl .= "<option value='".$f_row['name_kr']."'";
			if( $f_row['name_kr'] == $value_str )	$pCtrl .= " selected";
			$pCtrl .= ">";
		}
		$pCtrl .= "</select>";
		break;
	case "select":
		$pCtrl = "<select name='".$row['field_name']."' id='".$row['field_name']."' size='".$row['field_size']."' ";
		if( $row['field_maxlength'] != "" )	$pCtrl .="style='width:".$row['field_maxlength']."px'";
		$pCtrl .= "></select>";
	}
	if( $row['field_tag_type'] == "hidden" ) {
		$pOut .= $pCtrl;
	} else {
		if( $nCol == 0 ) $pOut .= "</tr>";
		else $pOut .= "<tr>";	// if $nCol==-1 || $nCol !=0
		if( $nCol > $nColumn-1 ) $nCol = 0;
		if( $group_num == $row['group_num'] ) {
			$pOut .= "&nbsp;&nbsp;".$pTitle."&nbsp;".$pCtrl;
		} else {
			if( $group_num != "" )		$pOut .= "</td>";
			$pOut .= "<td align=right>".$pTitle."&nbsp;</td><td>&nbsp;".pCtrl;
		}
		$nCol++;
	}
}
while( $nCol > 0 && $nCol < $nColumn-1 ) {
	$pOut .= "<td>&nbsp;</td>";
}
$pOut .= "</tr>";
if( $formType == "list" ) {
	$fname = substr($formname,3);
	$pOut .= "<tr>".
        "<td>&nbsp;<button  class='ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all' id=btnBudNew name=btn".$fname."New>New</button>&nbsp;".
        "<button  class='ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all' id=btnBudAdd name=btn".$fname."Add>등록</button>&nbsp;".
        "<button  class='ui-button ui-button-text-only ui-widget ui-state-default ui-corner-all' id=btnBudDelete name=btn".$fname."Delete>삭제</button></td>".
        "</tr>";
}
$pOut .= "</table></form><br>";
?>