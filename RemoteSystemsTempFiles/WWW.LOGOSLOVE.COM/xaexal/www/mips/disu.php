<?
include_once("include.php");

$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$_now= date('YmdHis'.$micro);

$answer=array();
$answer['result']=-1;
$answer['msg']="";

wlog("party [{$_SESSION['party']}]",__LINE__,__FUNCTION__,__FILE__);
if(!isset($_SESSION['party'])) {
	$answer['msg']="세션이 해지됐습니다. 다시 로그인하십시오.";
	echo json_encode($answer);
	$mysqli->close();
}
if( isset($_POST['optype']) ) 	$_optype = $_POST['optype'];
else if( isset($_GET['optype']) ) $_optype = $_GET['optype'];
else exit;

$log="";
// switch($_POST['optype']){
// case "newest": case "check-expiry":
// 	break;
// default:
	foreach($_POST as $key=>$value)	{
// 		if($key=="optype") {
// 			$log.="{$key} [<font color=red>{$value}</font>]";
// 		} else{
			$log.="{$key} [{$value}] ";
// 		}
	}
	wlog("{$log}",__LINE__,__FUNCTION__,__FILE__);
// }

switch( $_POST['optype'] ) {
case "pageinfo":
    try {
        resetLog(__LINE__,__FUNCTION__,__FILE__,true);
        wlog("optype pageinfo",__LINE__,__FUNCTION__,__FILE__);
        $_p=$_POST['_p'];	// first page
        $_e=$_POST['_e'];	// party ID.
        /*
         * pageinfo
         * gridinfo1
         *     +colinfo1
         *        +linkinfo1
         *        +linkinfoN
         *     +colinfoN
         *        +linkinfo1
         *        +linkinfoN
         * gridinfoN
         *     +colinfo1
         *        +linkinfo1
         *        +linkinfoN
         *     +colinfoN
         *        +linkinfo1
         *        +linkinfoN
         */
        $bMember = chkMemShip($_e);
        
        wlog("pageinfo [{$_p}/{$_e}] bMember [{$bMember}] substr [".substr($p,0,1)."/{$_p}]",__LINE__,__FUNCTION__,__FILE__);
        if($bMember==-1) exit;
        
        $sql = "select * from i_page where rowid='{$_p}'";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception($mysqli->error);
        if($rs->num_rows<1) throw new Exception("No record on I_PAGE.");
        
        $answer['pageinfo']=array();
        $answer['pageinfo']=$rs->fetch_assoc();
        
        $sql="select * from i_grid where par_rowid='{$_p}' order by seqno";
        $rs=$mysqli->query($sql);
        wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
        if($rs===false) throw new Exception($mysqli->error);
        if($rs->num_rows<1) throw new Exception("No record on I_GRID.");
        
        $answer['gridinfo']=array();
        while($row=$rs->fetch_assoc()){   // I_GRID
            $gridinfo=array();
            foreach($row as $k=>$v) $gridinfo[$k]=$v;
            
            switch($row['viewtype']){
                case "TREE":	$_template="TREE"; break;
                case "DIAL":	$_template="DIAL"; break;
                case "VERT":	$_template="VERT"; break;
                case "HRZN":
                default:
                    $_template="HRZN"; break;
            }
            $_template=getOnlyField("a_template","htmltext","code",$_template,__LINE__,__FUNCTION__,__FILE__);
//             wlog("template [".htmlspecialchars($_template)."]",__LINE__,__FUNCTION__,__FILE__);
            $_template = str_replace("{{toollist}}","toolbar{$row['rowid']}",$_template);
//             wlog("template [".htmlspecialchars($_template)."]",__LINE__,__FUNCTION__,__FILE__);
            $_template = str_replace("{{tbl_list}}","tbl{$row['rowid']}",$_template);
//             wlog("template [".htmlspecialchars($_template)."]",__LINE__,__FUNCTION__,__FILE__);
            $_layout = $_template;
            
            $gridinfo['layout']=$_layout;
            
//             wlog("_layout [".htmlspecialchars($_layout)."]",__LINE__,__FUNCTION__,__FILE__);
            
            $token="";
            if(is_numeric($row['viewtype'])) {
                $token=$row['formhtml'];
            }
            
            $rowid=$row['rowid'];
            $arCol=array('title','title_enu','nick','dtype','digit','pixel','nullable','editable','align','element','tooltip_msg','a4update');
            
            $sqlCol="select ".implode(",",$arCol)." from i_col where par_rowid='{$rowid}' and inactivated!='1' order by seqno";
            $rs1=$mysqli->query($sqlCol);
            wlog("{$sqlCol} [{$rs1->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
            if($rs1===false) throw new Exception($mysqli->error);
            if($rs1->num_rows<1) throw new Exception("No record on I_COL.");
            
            $gridinfo['colinfo']=array();
            while($row1=$rs1->fetch_assoc()){
                $colinfo=array();
                foreach($row1 as $k=>$v) $colinfo[$k]=$v;
                array_push($gridinfo['colinfo'],$colinfo);
            }
            $sql="select * from i_foreign where parent_grid='{$rowid}' order by seqno";
            $rs2=$mysqli->query($sql);
            wlog("{$sql} [{$rs2->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
            if($rs2===false) throw new Exception($mysqli->error);
            
            // 			$child=$xmlcls->createNode($gridinfo,"linkinfo");
            
            $gridinfo['linkinfo']=array();
            while($row2=$rs2->fetch_assoc()){
                $linkinfo=array();
                foreach($row2 as $k=>$v) $linkinfo[$k]=$v;
                array_push($gridinfo['linkinfo'],$linkinfo);
            }
            
            array_push($answer['gridinfo'],$gridinfo);
        }
        $answer['result']="0";
    } catch(Exception $e){
        $answer['msg']=$e->getMessage();
    }
    echo json_encode($answer);
	break;

case "imported":
	wlog("<<<<---- imported -------------- rowid [{$_POST['rowid']}] ndx [{$_POST['_ndx']}]----",__LINE__,__FUNCTION__,__FILE__);
	try{
		$oneGrid = new fGrid($_optype,$_POST['rowid']);
		$oneGrid->setColumns($_POST['_column'],__LINE__,__FUNCTION__,__FILE__);
		$self = new xmlCls();
// 		$answer['crlf']=array();
		
		$ndx = intval($_POST['_ndx']);
		$colvalue=$oneGrid->colinfo[$ndx];
		
		$_imported = explode(";",$colvalue['element']);
		wlog("_imported [{$_imported}] fname [{$colvalue['fname']}]",__LINE__,__FUNCTION__,__FILE__);
		switch($_imported[3]){
		case "#g_party":
			$_imported[3]=my._e;
			break;
		default:			
		}
		$sql = "select {$_imported[1]} fname from {$_imported[0]}";
		if(count($_imported)>2) $sql.=" where {$_imported[2]}='{$_imported[3]}'";
		$sql.=" order by fname";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception($mysqli->error);
		while($row=$rs->fetch_array(MYSQLI_BOTH)){
			$occ=$self->createFirst("crlf",$row[0]);
// 			array_push($answer['crlf'],$row[0]);
			$v=$row[0];
		}
		wlog("end?",__LINE__,__FUNCTION__,__FILE__);
// 		$answer['result']="0";
		$occ=$self->createFirst("result","0");
	} catch(Exception $e){
		$occ=$self->createFirst("msg",$e->getMessage());
// 		$answer['msg']=$e->getMessage();
	}
// 	echo json_encode($answer);
		$self->sendXML();
	break;
	
case "get_title":
	wlog("<<<<---- get title --------------",__LINE__,__FUNCTION__,__FILE__);
	$self=new xmlCls();
	try{
		$sql="select title from i_col where par_rowid='{$this->rowid}' and activated='1' order by seqno";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		$child=$self->createFirst('rcount',$rs->num_rows);
		$child=$self->createFirst('crlf');
// 		$answer['rcount']=$rs->num_rows;
// 		$answer['crlf']=array();
		while($row=$rs->fetch_array(MYSQLI_BOTH)){
			for( $i = 0; $i < $rs->field_count; $i++) {
				$rs->field_seek($i);
				$finfo=$rs->fetch_field();
					$occ = $self->createNode($child, $finfo->name, $row[$i]);
// 				array_push($answer['crlf'],$row[$i]);
			}
		}
// 		$answer['result']="0";
		$occ=$self->createFirst("result","0");
	} catch(Exception $e){
// 		$answer['msg']=$e->getMessage();
		$occ=$self->createFirst("msg",$e->getMessage());
	}
		$self->sendXML();
	// 	exit;
	break;

case "select_var":
	wlog("<<<<---- select_var --------------",__LINE__,__FUNCTION__,__FILE__);
// 	$self=new xmlCls();
	$_update = new fGrid($_optype,$_POST['rowid']);
	wlog("before Build_select_sql ",__LINE__,__FUNCTION__,__FILE__);
	$_update->build_select_sql($_POST['_y'],$_POST['_e']);
	wlog("after Build_select_sql ",__LINE__,__FUNCTION__,__FILE__);
// 	$_update->build_toolbar($self);
// 	$self->sendXML();
	exit;

case "build_toolbar":
	try{
		$my=new fGrid($_optype,$_POST['rowid']);
		switch($my->gridinfo['viewtype']){
		case "TREE":
			$tb_code = "toolbar_tree";
		case "HRZN":
		case "VERT":
		case "DIAL":
			$tb_code = "toolbar_hrzn";
			break;
		default:
			$tb_code="";
		}
		$htmltext = getOnlyField("a_template","htmltext","code",$tb_code,__LINE__,__FUNCTION__,__FILE__);
		wlog("my level [{$_SESSION['level']}] grid level [{$my->gridinfo['can_insert']}]",__LINE__,__FUNCTION__,__FILE__);
		if(intval($_SESSION['level']) > $my->gridinfo['can_insert']){
			$htmltext=removeButtonWith($htmltext,"class=new");
			$htmltext=removeButtonWith($htmltext,"class=duplicate");
		}
		if(intval($_SESSION['level'])>$my->gridinfo['can_remove']){
			$htmltext=removeButtonWith($htmltext,"class=delete");
		}
		$self=new xmlCls();
		$child = $self->createFirst('toolbar',$htmltext);
// 		$answer['toolbar']=$htmltext;
// 		$answer['result']="0";
		$child=$self->createFirst("result","0");
	} catch(Exception $e){
// 		$answer['msg']=$e->getMessage();
		$child=$self->createFirst("msg",$e->getMessage());
	}
// 	echo json_encode($answer);
		$self->sendXML();
	break;
// 	exit;

case "dup_var":	// duplicate record
	$self-=new xmlCls();
	$dup=new fGrid($_optype,$_POST['rowid']);
	$dup->setColumns($_POST['_column'],__LINE__,__FUNCTION__,__FILE__);
	
	$dup->build_insert_sql($_POST['_e']);
// 	$dup->execute_sql();
	
	$sql="select * from i_foreign where page_id='{$_POST['_p']}'";
	$rs=$mysqli->query($sql);
	wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
	while($row=$rs->fetch_assoc()){
		if($row['parent_grid']!=$dup->rowid) continue;

		$n=-1;
		for($i=0;$i<count($dup->colinfo);$i++){
			if($dup->colinfo[$i]['nick']==$row['parent_col']) {
				$n=$i;
				break;
			}
		}
		if($n<0) continue;
		
		$childGrid=new fGrid($_optype,$row['child_grid']);
		for($n=-1,$i=0;$i<count($childGrid->colinfo);$i++){
			if($childGrid->colinfo[$i]['nick']==$row['child_col']){
				$n=$i;
				break;
			}
		}
		if($n<0) continue;
		
		$primary=strtoupper(dechex(date("YmdHis")));
		$tblname="tbl{$primary}";
		$sql="create temporary table tbl{$primary} select * from ";
	}
	break;
	
case "new_var":
	$_update = new fGrid($_optype,$_POST['rowid']);
	$_update->setColumns($_POST['_column'],__LINE__,__FUNCTION__,__FILE__);
	$_update->build_insert_sql($_POST['_e']);
// 	$_update->execute_sql();
	break;
	
case "del_var":
	wlog("<<<<---- del_var --------------",__LINE__,__FUNCTION__,__FILE__);
	$_update = new fGrid($_optype,$_POST['rowid']);
	$_update->setColumns($_POST['_column'],__LINE__,__FUNCTION__,__FILE__);
	$_update->build_delete_sql($_POST['_e']);
//  	$_update->execute_sql();
	break;

case "upd_var":
	wlog("<<<<---- upd_var --------------",__LINE__,__FUNCTION__,__FILE__);
	$_update = new fGrid($_optype,$_POST['rowid']);
	$_update->setColumns($_POST['_column'],__LINE__,__FUNCTION__,__FILE__);
	$_update->build_update_sql($_POST['_ndx']);
// 	$_update->execute_sql();
	break;


case "upd_tree":
	wlog("<<<<---- upd_tree --------------",__LINE__,__FUNCTION__,__FILE__);
	$_update = new fGrid($_optype,$_POST['rowid']);
	$_update->build_update_tree($_POST['curid'],$_POST['par_rowid']);
	break;

case "flirt":	// get field list of tables.
	wlog("<<<<---- Flirt ------------",__LINE__,__FUNCTION__,__FILE__);
	$self=new xmlCls();
	$flirt=new fGrid($_optype,$_POST['rowid']);
	$flirt->setColumns($_POST['_column'],__LINE__,__FUNCTION__,__FILE__);
	$flirt->buildList($self);
	$self->sendXML();
	break;
case "showpickup":
	wlog("<<<<---- showpicktable -----------",__LINE__,__FUNCTION__,__FILE__);
	$_pick=new fGrid($_optype,$_POST['rowid']);
	$_pick=setColumns($_POST['_column'],__LINE__,__FUNCTION__,__FILE__);
	$_pick->getTable($_POST['index'],$_POST['orderby'],$_POST['startrow']);
	break;

case "inactivate":
	try{
		$sql="select * from i_grid where rowid='{$_POST['rowid']}'";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		if($rs===false) throw new Exception($mysqli->error);
		if($rs->num_rows<1) throw new Exception("No record in I_GRID for inactivation.");

		$row=$rs->fetch_assoc();
		$arTable=explode(",",$row['_tname']);
		if(count($arTable)<1) throw new Exception("No table.");
		for($i=0;$i<count($arTable);$i++){
			$arTable[$i]=trim($arTable[$i]);
		}
		$arTable=explode(" ",$arTable[0]);
		$_tname=$arTable[0];
		$sql="update {$_tname} set inactivated='1' where rowid='{$_POST['rowid']}' and inactivated<>'1'";
		$rs=$mysqli->query($sql);
		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
// 		exit;
		if($rs===false) throw new Exception($mysqli->error);
		$answer['result']="0";
	} catch(Exception $e){
		$answer['msg']=$e->getMessage();
	}
	break;
	
case "del_cas_admin":	//
	$_p=$_POST['_p'];
	$rowid=$_POST['rowid'];
	$arColumn=explode("^",$_POST['_column']);
	
	$sql="select _tname from i_grid where par_rowid='{$_p}' and rowid='{$rowid}'";
	

default:
	exit;
}
$mysqli->close();

class fTable {
	public $_tname,$_alias,$sql="",$_new;
	public $_unique=array();
	
	function __construct() {
		$this->_tname = $this->_alias = "";
		$this->_new = 0;
		$this->_sql= $this->_unique=array();
		$this->_field=array();
	}
}


class fGrid {
	private $_index=-1;
	private $_column=array();	// the columns from self grid to be searched. used in INSERT/UPDATE/SELECT
	public $gridinfo=null, $colinfo=array(), $joininfo=array(),$rowid="",$optype=null;
	
	function __construct($optype,$rowid){
		global $mysqli;
		try{
			$this->optype=$optype;
			wlog("fGrid optype [{$this->optype}] [{$_optype}] [{$optype}]",__LINE__,__FUNCTION__,__FILE__);
			$this->rowid=$rowid;
	// 		$this->_index=$_ndx;	// column index to be updated.
		
			wlog(">>>>>>>>>>>>>>>> __construct ::Grid [{$this->_p}]",__LINE__,__FUNCTION__,__FILE__);
			wlog(" ... _column [{$_column}]",__LINE__,__FUNCTION__,__FILE__);
		
			$sql = "select * from i_grid where rowid='{$this->rowid}'";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			if($rs->num_rows < 1 ) throw new Exception("No record from I_GRID");
			$this->gridinfo = $rs->fetch_assoc();
		
			$i=0;
			wlog("_tname [{$this->gridinfo['_tname']}]",__LINE__,__FUNCTION__,__FILE__);
			foreach(explode(",",trim($this->gridinfo['_tname'])) as $key=>$value){
				$this->oTable[$value]=new fTable();
				$this->oTable[$value]->_tname=$value;
				$this->oTable[$value]->_new=0;
				wlog("---------------------------alias+tname [{$sch}/{$this->oTable[$value]->_tname}]",__LINE__,__FUNCTION__,__FILE__);
			
				$sql="select column_name from information_schema.columns where table_name='{$value}'";
				$rs=$mysqli->query($sql);
				wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
				if($rs===false) throw new Exception($mysqli->error);
				while($row=$rs->fetch_assoc()){
					array_push($this->oTable[$value]->_field,$row['column_name']);
				}
			}
		
	 		$sql = "select * from i_col where par_rowid='{$this->rowid}' and inactivated!='1' order by seqno";
	 		$rs=$mysqli->query($sql);
	 		wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
	 		if($rs===false) throw new Exception($mysqli->error);

	 		while($row=$rs->fetch_assoc()){
				$this->colinfo[]=$row;
				wlog("::row [{$this->colinfo[count($this->colinfo)-1]['fname']}/{$this->colinfo[count($this->colinfo)-1]['nick']}/{$this->colinfo[count($this->colinfo)-1]['title']}]",__LINE__,__FUNCTION__,__FILE__);
			}
		
			$sql="select * from i_join where par_rowid='{$this->rowid}' order by join_type desc,seqno asc";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			while($row=$rs->fetch_assoc()){
				$this->joininfo[]=$row;
				wlog("::join [".implode("/",$row)."]",__LINE__,__FUNCTION__,__FILE__);
			}
			wlog('END _Construct',__LINE__,__FUNCTION__,__FILE__);
		} catch(Exception $e){
			wlog("fGrid constructor [{$e->getMessage()}]");
		}
	}
	/*
	 * 1. Assign the value from parametr $_POST to column.
	 * 2. Append '_x' to the field that does not exist in physical table, but defined in I_COL table. 
	 *     (orphan/deplicated field)
	 */
	function setColumns($_column,$ln="",$fn="",$filename=""){
		$this->_column=explode("^",$_column);	// get the values of columns from parameter on $_POST.
		
		foreach($this->oTable as $key=>$value){
			for($i=0;$i<count($this->colinfo);$i++){
				if($this->colinfo[$i]['a4update']!=$key) continue;
		
				$_exist=false;
				foreach($value->_field as $ndx=>$val){
					if($val==$this->colinfo[$i]['fname']) {
						$_exist=true;
						break;
					}
				}
				if(!$_exist){
					$this->colinfo[$i]['nick'].="_x";
				}
				$fvalue="";
				if(count($this->_column)>$i)	$fvalue=$this->_column[$i];
				$_col=$this->colinfo[$i];
		
				$this->colinfo[$i]['value']=$fvalue;
				wlog('fname/value ['.$_col['fname'].'/'.$fvalue.']',$ln,$fn,$filename);
			}
		}
	}
	function build_update_sql($_ndx){
		wlog(">>>>>>>>>>>>>>>> build_update_sql",__LINE__,__FUNCTION__,__FILE__);
		global $mysqli;
		try{
			$mysqli->autocommit(false);
			
// 			$gError="";
			$this->_index=intval($_ndx);
			if($this->_index<0 || !is_numeric($this->_index)) throw new Exception("no field index to be updated.");
			
			$_tname=$this->colinfo[$this->_index]['a4update'];
	
			wlog("_index [{$this->_index}] _tname [{$_tname}]",__LINE__,__FUNCTION__,__FILE__);
			
			$sql="select column_name, column_comment, data_type,character_maximum_length size,is_nullable,column_default,column_key ".
					"from information_schema.columns where table_name='{$_tname}'";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			if($rs->num_rows<1) throw new Exception("no column in table [{$_tname}]");
			
			while($row=$rs->fetch_assoc()){
				if($row['column_key']=="PRI") {
					$pColumn=$row['column_name'];
					$pType=$row['data_type'];
				}
				if($row['column_name']==$this->colinfo[$this->_index]['fname']) {
					$datatype=$row['data_type'];
				}
			}			
			$sql="update {$_tname} set {$this->colinfo[$this->_index]['fname']}=";
			if(isString($datatype)) $sql.="'{$this->colinfo[$this->_index]['value']}'";
			else $sql.=$this->colinfo[$this->_index]['value'];
			
			wlog("_sql [{$sql}] pColumn [{$pColumn}]",__LINE__,__FUNCTION__,__FILE__);
			
			$_where="";
			for($i=0;$i<count($this->colinfo);$i++){
				if($this->colinfo[$i]['fname']==$pColumn && $this->colinfo[$i]['a4update']==$_tname){
					$_where=" where {$pColumn}=";
					if(isString($pType)) $_where.="'{$this->colinfo[$i]['value']}'";
					else $_where.=$this->coliinfo[$i]['value'];
					break;
				}
			}
			wlog("_where [{$_where}]",__LINE__,__FUNCTION__,__FILE__);
			
			if($_where!="") $this->oTable[$_tname]->_sql = $sql.$_where;
			else $this->oTable[$_tname]->_sql="";
			wlog("UPDATE [{$this->oTable[$_tname]->_sql}]",__LINE__,__FUNCTION__,__FILE__);
	
			wlog("execute sql [{$this->oTable[$_tname]->_sql}] [{$_tname}]",__LINE__,__FUNCTION__,__FILE__);
			wlog($this->oTable[$_tname]->_sql,__LINE__,__FUNCTION__,__FILE__);
			$rs=$mysqli->query($this->oTable[$_tname]->_sql);
			wlog("errno [{$mysqli->errno}] {$mysqli->error}",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			$answer['result']="0";
			$mysqli->commit();
		} catch(Exception $e){
			$mysqli->rollback();
			$answer['msg']=$e->getMessage();
			wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
		}
		$mysqli->autocommit(true);
	}
	/*
	 * INSERT statement
	 */
	function build_insert_sql($_e,$xaction=true) {
		global $mysqli;
		wlog(">>>>>>>>>>>>>>>> build_insert_sql [{$this->optype}]",__LINE__,__FUNCTION__,__FILE__);
		try{
			$gError="";
			$mysqli->autocommit(false);
		
			$primary_value=strtoupper(dechex(date("YmdHis")));
			foreach($this->oTable as $_tname=>$tbl){
				$_tname=trim($_tname);
				wlog("_tname [{$tbl->_tname}] _new [{$tbl->_new}] _index [{$this->colinfo[$this->_index]['a4update']}/{$_tname}] [".stripos($this->gridinfo['_tname2stop'],$tbl->_tname)."]",__LINE__,__FUNCTION__,__FILE__);
				if( stripos($this->gridinfo['_tname2stop'],$tbl->_tname) !== false ) {
					wlog("No insert SQL is allowed to [{$tbl->_tname}/".stripos($this->gridinfo['_tname2stop'],$tbl->_tname)."{$this->gridinfo['_tname2stop']}]",__LINE__,__FUNCTION__,__FILE__);
					continue;
				}
				// Collect the actual table information from system.
				$sql = "select column_name, column_comment, data_type,character_maximum_length size,is_nullable,".
						"column_default,column_key from information_schema.columns where table_name='{$tbl->_tname}'";
				$rs=$mysqli->query($sql);
				wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
				if($rs===false)	throw new Exception($mysqli->error);
				if($rs->num_rows<1)	throw new Exception("No column in the table [{$tbl->_tname}].");
				
				$this->oTable[$_tname]->_sql=array();
				for($i=0;$i<count($this->colinfo);$i++){
					if($_tname!=trim($this->colinfo[$i]['a4update'])) continue;
				
					$rs->data_seek(0);
					while($row=$rs->fetch_assoc()){
						if($row['column_name']!=$this->colinfo[$i]['fname']) continue;
						// Assign new primary key value to the column with "PRI" value or unique key value "1".
						if($row['column_key']=="PRI"){
							$this->colinfo[$i]['value']=$primary_value;
						} else if($this->optype=="new_var" && $row['is_nullable']=="NO" && $this->colinfo[$i]['value']=="") {
							/* assign the default value to the column without nullable when new record is tried to be created, 
							 * regardless of the existence of column value.
							 * If optype=UPD_VAR, the existing column value should be kept so that it is not updated.
							 */
							switch($this->colinfo[$i]['defvalue']){
							case "#_e":
								$this->colinfo[$i]['value']=$_e;
								break;
							case "#YMDHIS":
								$this->colinfo[$i]['value']=strtoupper(dechex(date("YmdHis")));
								break;
							default:
								if($this->colinfo[$i]['defvalue']!=""){
									$this->colinfo[$i]['value']=$this->colinfo[$i]['defvalue'];
								} else if($row['column_default']!=""){
									$this->colinfo[$i]['value']=$row['column_default'];
								} else $this->colinfo[$i]['value']="";
							}
						}
						$thiscol=$this->colinfo[$i];
						wlog("UPD COL [{$thiscol['a4update']}.{$thiscol['fname']}] value [{$thiscol['value']}] {$row['column_key']}] uniqueness [{$thiscol['unique_key']} nullable [{$row['is_nullable']}]",__LINE__,__FUNCTION__,__FILE__);
						$pstr="{$thiscol['fname']}=";
						if(isString($row['data_type']))	$pstr.="'{$thiscol['value']}'";
						else $pstr.=$thiscol['value'];
						array_push($this->oTable[$_tname]->_sql,$pstr);
					}
				}
				$this->oTable[$_tname]->_sql="insert into {$_tname} set ".implode(",",$this->oTable[$_tname]->_sql);
				wlog("INSERT [{$this->optype}] [{$this->oTable[$_tname]->_sql}]",__LINE__,__FUNCTION__,__FILE__);
			
				wlog("execute sql [{$this->oTable[$_tname]->_sql}] [{$_tname}]",__LINE__,__FUNCTION__,__FILE__);
				$rs=$mysqli->query($this->oTable[$_tname]->_sql);
				if($rs===false) throw new Exception($mysqli->error);
				
// 				wlog("errno [{$mysqli->errno}] {$mysqli->error}",__LINE__,__FUNCTION__,__FILE__);
// 				if( $mysqli->errno != 0 ) {
// 					if($gError!="") $gError.="\n";
// 					$gError.=$mysqli->error;
// 					break;
// 				}
			}
		
			$arReturn=array();
			foreach($this->colinfo as $key=>$value){
				array_push($arReturn,$value['value']);
			}
			$child=$self->createFirst("errno",$gError);
			$child=$self->createFirst("replyText",implode("^",$arReturn));
// 			$answer['resplyText']=implode("^",$arReturn);
// 			$answer['result']="0";
			$mysqli->commit();
		} catch(Exception $e){
			$mysqli->rollback();
// 			$answer['msg']=$e->getMessage();
			$child->$self->createFirst("msg",$e->getMessage());
		}
		$mysqli->autocommit(true);
// 		echo json_encode($answer);
			$self->sendXML();
	}
	// leftmost table is for the tree, and others are for the join to leftmost table.
	function build_update_tree($curid,$par_rowid){
		if($par_rowid==$curid){
			echo "-1";
			return;
		} 
		try{
			$sql="select party from {$this->oTable['a']->_tname} where rowid='{$par_rowid}'";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			if($rs->num_rows!=1) throw new Exception("Too many parent records.");
			$row=$rs->fetch_array(MYSQLI_BOTH);
			$party=$row[0];
			
			$sql="update {$this->oTable['a']->_tname} set party='{$party}',par_rowid='{$par_rowid}' where rowid='{$curid}'";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
// 			echo $mysqli->errno;
		} catch(Exception $e){
			wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);
		}
	}
	
	/*
	 * DELETE statement
	 */
	function build_delete_sql($_e){
		global $mysqli;
		wlog(">>>>>>>>>>>>>>>> build_delete_sql ",__LINE__,__FUNCTION__,__FILE__);
// 		$gError="";
// 		if( $xaction ){
// 			$rs=$mysqli->query("set autocommit=0");
// 			$rs=$mysqli->query("start transaction");
// 		}
		try {		
			foreach($this->oTable as $key=>$value){
				// check if subscribed table for delete exists in the table list not to be deleted.
				if(stripos($this->gridinfo['_tname2stop'],$key)!==false){
					$value->_sql="";
					continue;
				}
				
				// Check if subscribed table has a primary key.
				$sql = "select column_name, column_comment, data_type,character_maximum_length size ".
					"from information_schema.columns where table_name='{$key}' and column_key='PRI'";
				$rs=$mysqli->query($sql);
				wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
				if($rs===false || $rs->num_rows<1) throw new Exception("no primary key for DELETE");	// when this table has no primary key.
				
				$row=$rs->fetch_assoc();
				$this->oTable[$key]->_sql=$_where="";
				for($n=0; $n<count($this->colinfo);$n++) {
					if($this->colinfo[$n]['a4update']!=$key) continue;
	
					if($this->colinfo[$n]['fname']==$row['column_name']){	// found primary key.
						$_where=" where {$this->colinfo[$n]['fname']}=";
						if(isString($row['data_type'])) $_where.="'{$this->colinfo[$n]['value']}'";
						else $_where.=$this->colinfo[$n]['value'];
						$this->oTable[$key]->_sql="delete from {$value->_tname}{$_where}";
						break;
					}
				}
				wlog("delete SQL [{$this->oTable[$key]->_sql}]",__LINE__,__FUNCTION__,__FILE__);
			
				wlog("execute sql [{$this->oTable[$key]->_sql}] [{$key}]",__LINE__,__FUNCTION__,__FILE__);
				$rs=$mysqli->query($this->oTable[$key]->_sql);
				if($rs===false) throw new Exception($mysqli->error);
			}
		} catch(Exception $e){
			wlog($e->getMessage(),__LINE__,__FUNCTION__,__FILE__);			
		}
	}
	/*
	 * SELECT SQL statement
	 */
	function build_select_sql($startrow,$_e){
	    wlog("startrow [{$startrow}] _e [{$_e}]",__LINE__,__FUNCTION__,__FILE__);
		global $mysqli;
// 		$xmlcls=new xmlCls();
		wlog(">>>>>>>>>>>>>>>> build_select_sql ",__LINE__,__FUNCTION__,__FILE__);
		try {
			$column=array(); $_where=""; 
			
			// make the column list from I_COL array.
			for($n=0;$n<count($this->colinfo);$n++){
				$colvalue=$this->colinfo[$n];
				if($colvalue['inactivated']=="1") continue;
	
				// Ignore the field that does not exist in physical table but defined in I_COL table.
				if(substr($colvalue['nick'],-2)!="_x") {
					array_push($column,"{$colvalue['a4update']}.{$colvalue['fname']} {$colvalue['nick']}");
				}
				wlog("ar_sql [{$colvalue['a4update']}.{$colvalue['fname']} {$colvalue['nick']}]",__LINE__,__FUNCTION__,__FILE__);
			}
		
			$_from = array(); 
			
			foreach($this->joininfo as $key=>$val){
				if($val['join_type']=="outer"){
					array_push($_from,"({$val['l_table']} left outer join {$val['r_table']} on {$val['l_table']}.{$val['l_column']} {$val['operator']}{$val['r_table']}{$val['r_column']})");
				} else {
					foreach(array($val['l_table'],$val['r_table']) as $k1=>$v1){
						$v1=trim($v1);
						if($v1=="") continue;
						for($i=0;$i<count($_from);$i++){
							if(strpos($_from[$i],$v1)!==false) break;
						}
						if($i>=count($_from) ) array_push($_from,$v1);
					}
				
					if($_where=="") $_where=" where ";
					else $_where.=" and ";
					$_where.="{$val['l_table']}.{$val['l_column']} {$val['operator']}";
					if($val['r_table']!="") $_where.="{$val['r_table']}.{$val['r_column']}";
					else {
						switch($val['r_column']){
						case "#_e":
							wlog("party [{$_e}]",__LINE__,__FUNCTION__,__FILE__);
							$_where.="'{$_e}'";
							break;
						default:
							break;
						}
					}
				}
			}
			if(count($_from)<1){
				$sql="select distinct trim(a4update) from i_col where par_rowid='{$this->rowid}'";
				$rs=$mysqli->query($sql);
				wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
				while($row=$rs->fetch_array(MYSQLI_BOTH)){
					array_push($_from,$row[0]);
				}
			}
			$_from = " from ".implode(",",$_from);
			wlog("from [{$_from}]",__LINE__,__FUNCTION__,__FILE__);
		
			$_orderby=array();
			$sql="select nick,order_type from i_col where par_rowid='{$this->rowid}' and orderby > 0 order by orderby";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			while($row=$rs->fetch_assoc()){
				array_push($_orderby,"{$row['nick']} {$row['order_type']}");
				wlog("orderby [{$row['nick']} {$row['order_type']}]",__LINE__,__FUNCTION__,__FILE__);
			}
			if(count($_orderby)>0) $_orderby=" order by ".implode(",",$_orderby);
			else $_orderby="";
		
			wlog("order by [{$_orderby}]",__LINE__,__FUNCTION__,__FILE__);
	
			$sql="select count(*) {$_from}{$_where}";
			$sql=str_replace("#_e","'{$_e}'",$sql);
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false || $rs->num_rows>0){
				$row=$rs->fetch_array(MYSQLI_BOTH);
				$answer['tcount']=$row[0];
			} else {
				$answer['tcount']="0";
			}
			$sql = "select ".implode(",",$column)." {$_from}{$_where}{$_orderby}";
			if($this->gridinfo['pagesize']!="0" && is_numeric($startrow)) $sql.=" limit ".intval($startrow-1).",{$this->gridinfo['pagesize']}";
			
			$sql=str_replace("#_e","'{$_e}'",$sql);
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
		
			$answer['rcount']=$rs->num_rows;
			/*
			 * Title and its nick at first line, then from next line nick and data are located.
			 */
			// from second line, nick and data are inserted in XML.
			$answer['crlf']=array();
			while($row=$rs->fetch_assoc()){
			    wlog($row,__LINE__,__FUNCTION__,__FILE__);
				array_push($answer['crlf'],$row);
			} // while
			$answer['result']="0";
		}catch(Exception $e){
			$answer['msg']=$e->getMessage();
		}
		wlog(json_encode($answer),__LINE__,__FUNCTION__,__FILE__);
		echo json_encode($answer);
	}
	
	function form_builder(){
		global $mysqli;
		wlog(">>>>>>>>>>>>>>>> form_builder ::viewtype [{$this->gridinfo['viewtype']}]",__LINE__,__FUNCTION__,__FILE__);
		$this->gridinfo['formhtml']='';
		
		$column_num=intval($this->gridinfo['viewtype']);
		$i=0; $formHTML="";
		foreach($this->colinfo as $k=>$v){
			if($v['visible']!="1") $formHTML.="<input type=hidden name={$v['fname']} id={$v['fname']} />";
			else {
				$formHTML.=$v['title']."&nbsp";
				switch($v['dtype']){
				case "text": case "email": case "phone": case "number": case "YMD":
					$formHTML.="<input type={$v['dtype']} name={$v['fname']} id={$v['email']} size={$v['digit']} style='width:{$v['pixel']}px;' ";
					if($v['dtype']=="number")	$formHTML."align:right";
					$formHTML.=">";
					break;
				case "textarea":
					$formHTML.="<textarea name={$v['fname']} id={$v['fname']} style='width:{$v['pixel']}px'></textarea>";
					break;
				case "select":
					$formHTML.="<select name={$v['fname']} id={$v['fname']}>";
					foreach(explode(",",$v['element']) as $skey=>$sval){
						$formHTML.="<option value={$sval}>{$sval}</option>";
					}
					$formHTML.="</select>";
					break;
				case "imported":
					$element=explode(";",$v['element']);
// 					wlog("element [{$v['element']}]",__LINE__,__FUNCTION__,__FILE__);
					$sql="select {$element[1]} from {$element[0]} where {$element[2]}='{$element[3]}'";
					$rs=$mysqli->query($sql);
					wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
					if($rs===false || $rs->num_rows<1)	break;
					
					$formHTML.="<select name={$v['fname']} id={$v['fname']}>";
					while($row=$rs->fetch_array(MYSQLI_BOTH)){
						$formHTML.="<option name={$row[0]}>{$row[0]}<option>";
					}
					$formHTML.="</select>";
					break;
				}
				if($i%$column_num==0) $this->gridinfo['formhtml'].="<tr>";
				$this->gridinfo['formhtml'].="<td>{$formHTML}</td>";
				if(++$i%$column_num==0) $this->gridinfo['formhtml'].="</tr>";
				$formHTML = "";
			}
		}
		while($i++%$column_num<$column_num-1){
			$this->gridinfo['formhtml'].="<td>&nbsp;</td>";
		}
		$this->gridinfo['formhtml'].="</tr>";
		wlog("formhtml [<xmp>({$this->gridinfo['formhtml']})</xmp>]",__LINE__,__FUNCTION__,__FILE__);
// 		return $this->formhtml;
	}
	function getTable($ndx,$orderby,$startrow){
		global $mysqli;
		try{
			$_xtract = array(); $_hidden = array(); $_align = array();
// 			$self=new xmlCls();
		
			$sql = "select column_name, column_comment, data_type,character_maximum_length size ".
				"from information_schema.columns where table_name='{$this->oTable[$ndx]->_tname}'";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
// 		if($rs===false || $rs->num_rows< 1 ) {
// 			$mysqli->close();
// 			exit;
// 		}
			if($rs===false) throw new Exception($mysqli->error);
			if($rs->num_rows<1) throw new Exception("No record from information_schema.");
			
			$_columns=array();$_echo="<tr>";
			$n=0;
			while($row=$rs->fetch_assoc()){
				$_fieldname = $row['column_name'];
				array_push($_columns,$_fieldname);
	// 			wlog("oTable count [".count($this->oTable[$ndx]->_fname)}]");
				foreach( $this->oTable[$ndx]->_fname as $key=>$value){
					wlog("value [{$value}] fieldname [{$_fieldname}]",__LINE__,__FUNCTION__,__FILE__);
					if( $value != $_fieldname ) continue;
					array_push($_xtract,$n);
					break;
				}
				$visible = "";
				switch($row['data_type']){
				case "char":
					if(intval($row['size'])==12) { // char(12) is mainly system field not to be displayed to user.
						$visible = " style='display:none'";
						array_push($_hidden,$n);
					}
				case "varchar":case"longtext":case"text":case"element":case"mediumtext":
					array_push($_align,"left");
					break;
				default:
					array_push($_align,"right");
				}
				$_echo .= "<td {$visible} align=center>{$_fieldname}</td>";
				$n++;
			}
		$occ=$self->createFirst('xtract',implode(",",$_xtract));
// 			$answer['xtract']=implode(",",$_xtract);
			wlog("_xtract [".implode(",",$_xtract)."]",__LINE__,__FUNCTION__,__FILE__);
			$_echo .= "</tr>";
			if( $orderby != "" ) $orderby = " order by {$orderby}";

			$sql = "select ".implode(",",$_columns)." from {$this->oTable[$ndx]->_tname}{$orderby} limit {$startrow},20";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			
			$_bTitle = true;
			while($row=$rs->fetch_array(MYSQLI_BOTH)){
				$_echo .= "<tr>";
				for( $i = 0; $i < $rs->field_count; $i++) {
					$visible = "";
					foreach( $_hidden as $key=>$value ){
						if( $value != $i ) continue;
						$visible = " style='display:none' ";
						break;
					}
					$_echo .= "<td align={$_align[$i]}{$visible}>{$row[$i]}</td>";
				} 
				$_echo .= "</tr>";
			} // while
		$occ=$self->createFirst('crlf',$_echo);
// 			$answer['crlf']=$_echo;
			wlog("_echo [{$_echo}]",__LINE__,__FUNCTION__,__FILE__);
// 			$answer['result']="0";
		
		} catch(Exception $e){
// 			$answer['msg']=$e->getMessage();
			$occ=$self->createFirst("msg",$e->getMessage());
		}
// 		echo json_encode($answer);
		$self->sendXML();
	}
	
	function buildList($self){
		global $mysqli;
		// 		resetLog(__LIINE__,__FUNCTION__,__FILE__,true);
		try {
			$sql="select max(seqno) from i_col where par_rowid='{$this->rowid}'";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false) throw new Exception($mysqli->error);
			if($rs->num_rows<1)	$seqno=-1;
			else {
				$row=$rs->fetch_array(MYSQLI_BOTH);
				$seqno=$row[0];
				if(is_numeric($seqno)) $seqno=intval($seqno);
				else $seqno=-1;
			}
		
			$sql="select * from information_schema.columns where table_name='i_col'";
			$rs=$mysqli->query($sql);
			wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
			if($rs===false || $rs->num_rows<1) throw new Exception("Not found the field list from i_col.");
		
			$cur=floatval(date("YmdHis"));
		
			foreach($this->oTable as $key=>$value){
				$sql="select * from information_schema.columns where table_name='{$value->_tname}' and column_name not in ".
						"(select fname from xaexal.i_col where par_rowid='{$this->rowid}' and a4update='{$key}')";
				$rs=$mysqli->query($sql);
				wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
				if($rs===false) throw new Exception($mysqli->error);
				if($rs->num_rows<1) continue;
				
				while($row=$rs->fetch_array(MYSQLI_BOTH)){
					$arColumn=array();
					$rescolinfo->data_seek(0);
					while($rowcolinfo=$rescolinfo->fetch_array(MYSQLI_BOTH)){
						switch($rowcolinfo['COLUMN_NAME']){
						case "inactivated":
							array_push($arColumn,"'1'"); break;
						case "rowid":
							array_push($arColumn,"'".strtoupper(dechex($cur++))."'");break;
						case "par_rowid":
							array_push($arColumn,"'{$this->rowid}'"); break;
						case "seqno";
							array_push($arColumn,++$seqno); break;
						case "a4update":
							array_push($arColumn,"'{$key}'"); break;
						case "fname":
							array_push($arColumn,"'{$row['COLUMN_NAME']}'"); break;
						case "dtype":
							array_push($arColumn,"'{$row['DATA_TYPE']}'"); break;
						case "nick":
							array_push($arColumn,"'{$row['COLUMN_NAME']}_{$key}'"); break;
						case "digit":
							if(isString($row['DATA_TYPE'])) {
								array_push($arColumn,(floatval($row['CHARACTER_MAXIMUM_LENGTH'])<128)?$row['CHARACTER_MAXIMUM_LENGTH']:128);
							} else array_push($arColumn,$row['NUMERIC_PRECISION']);
							break;
						case "pixel":
							array_push($arColumn,"0");break;
						case "nullable":
							array_push($arColumn,($row['IS_NULLABLE']=="NO")?"'0'":"'1'"); break;
						case "defvalue":
							if(isString($row['DATA_TYPE']))	$sch="'{$row['COLUMN_DEFAULT']}'";
							else $sch=$row['COLUMN_DEFAULT'];
							array_push($arColumn,$sch); break;
						case "editable":
							array_push($arColumn,"0"); break;
						case "align":
							array_push($arColumn,(isString($row['DATA_TYPE']))?"'left'":"'right'"); break;
						default:
							array_push($arColumn,"''"); break;
						}
						wlog("COLUMN_Name [{$rowcolinfo['COLUMN_NAME']}/{$arColumn[count($arColumn)-1]}]",__LINE__,__FUNCTION__,__FILE__);
					}
					$sql="insert into i_col value (".implode(",",$arColumn).")";
					$rs=$mysqli->query($sql);
					wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
					if($rs===false) throw new Exception($mysqli->error);
				}
			}
		} catch(Exception $e){
			wlog("error [{$e->getMessage()}]");
		}
	}
}

function getOnlyField($table,$return_field,$cond_field,$cond_value,$ln="",$func="",$filename=""){
	global $mysqli;
	
	if( !is_numeric($cond_value) ) $cond_value = "'{$cond_value}'";
	$sql = "select {$return_field} from {$table} where {$cond_field}={$cond_value}";
	$rs=$mysqli->query($sql);
	wlog("{$sql} [{$rs->num_rows}]",$ln,$func,$filename);
	if($rs===false || $rs->num_rows<1) {
		wlog("null returned",__LIINE__,__FUNCTION__,__FILE__);
		return "";
	}
	else {
		$row=$rs->fetch_array(MYSQLI_BOTH);
		
// 		wlog(htmlspecialchars($row[$return_field]),__LINE__,__FUNCTION__,__FILE__);
		return $row[$return_field];
	}
}

function chkMemShip($_e){
	global $mysqli;
	if($_SESSION['member_id']=="12480F802448") return 0; // xaexal@gmail.com

	$sql = "select level from a_mem_par where member_id='{$_SESSION['member_id']}' and party='{$_e}'";
	$rs=$mysqli->query($sql);
	wlog("{$sql} [{$rs->num_rows}]",__LINE__,__FUNCTION__,__FILE__);
	if($rs===false || $rs->num_rows<1) return -1;
	else {
		$row=$rs->fetch_assoc();
		return intval($row['level']);
	}
}

function getGrid($_p,$self){
}

function isString($pstr){
	switch($pstr){
	case "char": case "varchar":case"longtext":case"text":case"element":case"mediumtext":case "timestamp":case "date":
		return true;
	default: return false;
	}
}

function removeButtonWith($src,$tgt){
	wlog("origin {$src}",__LINE__,__FUNCTION__,__FILE__);
	$n=strpos($src,$tgt);
	$front=substr($src,0,$n);
	$front=substr($front,0,strrpos($front,"<"));
	$rear=substr($src,$n+strlen($tgt));
	$rear=substr($rear,strpos($rear,">")+1);
	$src=$front.$rear;
	wlog("final {$src}",__LINE__,__FUNCTION__,__FILE__);
}
//------------------------------ End of File -------------------------------------------
?>