<?php 
include_once("comfunc.php");

switch( $_GET['optype'] ) {
case 'newpasscode':
	$nPasscode = str_pad(strval(mt_rand(0,999999)),6,"0",STR_PAD_LEFT);
	$bMail = mail($_GET['param'],"Academy CRM - 임시비밀번호 (로긴용) ","임시로 사용하실 로그인 비밀번호는 [".
					$nPasscode."]입니다.\r\n\r\n로긴하신 후 임시비밀번호는 본인의 새로운 비밀번호로 변경하시기 바랍니다.\r\n\r\n감사합니다.\r\n\r\nAcademy CRM");
	if( $bMail ) {
		$psql = "update a_member set passcode='".$nPasscode."' where userid='".$_GET['param']."'";
		@$nCount = runQuery($psql,$result);
		if( $nCount > 0 )	 {
			echo "ok";
			exit;
		}	
	}
	echo "fail";
	break;
default:
	
}
	