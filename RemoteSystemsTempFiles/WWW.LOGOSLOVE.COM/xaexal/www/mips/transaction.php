<?php 
include_once("comfunc.php");

switch( $_POST["optype"] ) {
case "topup":
	$sql = "update a_member set balance=balance+{$_POST['add']} where member='{$_POST['member']}'";
	@$nCount = runQuery($sql,$result);
	if( $nCount > 0 )	echo "ok";
	else	echo "fail";
	exit;

case "sales":
	// enterprise, table_no, member, member_name, phone_mobile, paidby
	$sql = "select * from a_order where enterprise='{$_POST['enterprise']}' and table_no='{$_POST['table_no']}'";
	@$nCount = runQuery($sql,$result);
	if( $nCount < 1 ) {
		echo 'no order';
		exit;
	}
	$sales_id = date("YmdHis");
	$order_id = ""; $mileage = 0; $price= 0;
	while( $row = mysql_fetch_array($result) ) {
		$sql = "replace into a_sales set sales_id='{$sales_id}',member_name='{$_POST['member_name']}',phone_mobile='{$_POST['phone_mobile']}',paid='{$_POST['paid']}'";
		for( $i = 0; $i < mysql_num_fields($result); $i++) {
			$sql .= ",".mysql_field_name($result,$i)."=";
			switch( mysql_field_name($result,$i)) {
			case "order_id": 
				$order_id .= "{$row[$i]} ";
				$sql .= "'{$row[$i]}'";
				break;
			case "mileage":
				$mileage += floatval($row[$i]);
				$sql .= $row[$i];
				break;
			case "price":
				if( $_POST['member'] == "" )	$price += floatval($row[$i]);
				$sql .= $row[$i];
				break;
			case "member_price":
				if( $_POST['member'] != "" )	$price += floatval($row[$i]);
				$sql .= $row[$i];
				break;
			case "created": case "updated":
				$sql .= date("YmdHis");
				break;
			case "createdby": case "updatedby":
				$sql .= "'{$_SESSION['name_kor']}'";
				break;
			default:
				$sql .= "'{$row[$i]}'";
			}
		}
		@$nCount = runQuery($sql,$result1);
	}
	$sql = "delete from a_order where enterprise='{$_POST['enterprise']}' and table_no='{$_POST['table_no']}'";
	@$nCount = runQuery($sql,$result);

	if( $_POST['paid'] == '4' && $_POST['member'] != "'" ) {	// paid by mileage
		$sql = "update a_member set mileage=mileage-{$mileage} where member='{$_POST['member']}' and mileage >= {$mileage}";
		@$nCount = runQuery($sql,$result);
	} else {
		$sql = "select column_name from information_schema.columns where table_schema='xaexal' and table_name='a_payment'";
		@$nCount = runQuery($sql,$result);
		$sql = "replace into a_payment set payment_id='".date("YmdHis")."'";
		while( $row = mysql_fetch_array($result) ) {
			if( $row['column_name'] == "payment_id" )	continue;
			$sql .= ",{$row['column_name']}=";
			switch( $row['column_name'] ) {
			case "order_id_text" :
				$sql .= "'{$order_id}'"; break;
			case "paidby":
				$sql .= "'{$_POST['paid']}'"; break;
			case "pay_price":
				$sql .= $price; break;
			case "pay_date": case "created": case "updated": case "payment_id":
				$sql .= "'".date("YmdHis")."'"; break;
			case "createdby": case "updatedby":
				$sql .= "'{$_SESSION['name_kor']}'"; break;
			case "member": case "member_name": case "phone_mobile": case "enterprise":
				$sql .= "'{$_POST[$row['column_name']]}'"; break;
			default:
				$sql .= "'{$_SESSION[$row['column_name']]}'";	
			}
		}
		@$nCount = runQuery($sql,$result);
		if( $nCount < 1 ) echo "fail";
		else	echo "ok";
	}
}
?>