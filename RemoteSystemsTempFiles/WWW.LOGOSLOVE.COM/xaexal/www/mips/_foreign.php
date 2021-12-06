<?
include_once("include.php");

$wlog=new Logging(); 
$wlog->write("member_id [{$_SESSION['member_id']}]",__LINE__,__FUNCTION__,__FILE__);
if(!isset($_SESSION['member_id'])) {
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	$mysqli->close();
	$wlog=null;
	exit;
}

$log="";
foreach($_POST as $key=>$value)	{
	$log.="{$key} [{$value}] ";
}
foreach($_SESSION as $key=>$value)	{
    $log.="SESSION {$key} [{$value}]\n";
}
$wlog->write("{$log}",__LINE__,__FUNCTION__,__FILE__);

$rs=sqlrun("select parent_grid,parent_col,child_grid,child_col,child_sort_col from i_foreign where page_id='{$_POST['_p']}'",__LINE__,__FUNCTION__,__FILE__);

$answer['foreign']=array();
while($row=$rs->fetch_assoc()){
    foreach($row as $k=>$v){
        switch($k){
        case "parent_grid":
            $rs1=sqlrun("select fname from i_col where par_rowid='{$v}' and inactivated='0' order by seqno",__LINE__,__FUNCTION__,__FILE__);
            $n=1;
            while($row1=$rs1->fetch_assoc()){
                if($row1['fname']==$row['parent_col']) break;
                $n++;
            }
            if($n>$rs1->num_rows) $n=-1;
            $row['parent_col']=$n;
            break;
        case "child_grid":
            $rs1=sqlrun("select fname from i_col where par_rowid='{$v}' and inactivated='0' order by seqno",__LINE__,__FUNCTION__,__FILE__);
            $n=1;
            while($row1=$rs1->fetch_assoc()){
                if($row1['fname']==$row['child_col']) break;
                $n++;
            }
            if($n>$rs1->num_rows) $n=-1;
            $row['child_col']=$n;
            break;
        }
    }
    array_push($answer['foreign'],$row);
}
$answer['result']="0";
$mysqli->close();
$wlog->write(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
$wlog=null;
echo json_encode($answer);
?>