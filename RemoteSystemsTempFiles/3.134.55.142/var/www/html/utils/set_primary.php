<?
include_once "../common/_init_.php";
$total=0;
$rowid="0000";
do {
	try {
		$sql="update a_member set user_rowid='{$rowid}' where  user_rowid is null limit 1";
		$result=$mysqli->query($sql);
		$nCount=$mysqli->affected_rows;
		if($nCount<1) throw new Exception($sql);
		$total++;
		echo $rowid."<br>";
		$rowid=generateROWID($rowid);
		
	} catch(Exception $e){
		echo $e->getMessage();
	}
} while($nCount>0);
echo "<br>".$total." are finished...<br>";
?>