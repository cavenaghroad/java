  /*
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23573945-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })(); 
  
  var exeSCR2000a = "exedbaccess.php";
 
   */

/* 
 * 필드목록은 comma로 분리
 * 필드배열의 첫 요소는 option value, 그 이후는 option text
 * strDelimeter는 각 필드사이에 넣어질 구분자
 * 필드배열의 첫 요소는 option value이므로, strDelimeter의 길이는 column와 같거나 하나 작다.
 */

var BaseFolder='/mips';
//var xLayout;
var dbg = true;
var sqldbg = true;	// debug only for SQL statement.
var gScreenURL='';
var gdToday = new Date();
var gSpinner = false;
var arMessage = new Array();
var member_id='';

var alertseq = 0;
var bSuccess = true;

//var BASE_BGCOLOR = 'white';
//var MOUSEOVER_BGCOLOR = 'yellow';
//var CLICKED_BGCOLOR = '#33ff99';

if( $.browser.msie )	dbg = false;	// if IE, wlog is disabled.

var GRID_BGCOLOR_HEADER = '#0033CC';
var GRID_BGCOLOR_TOOLBAR = '#98AF3D';
var GRID_BGCOLOR_REQUIRED_FIELD = '#ff99ff';
var goMsg={};	// global object for error/warning messages

var KEY_ESCAPE = 27;
var KEY_TAB = 9;
var KEY_ENTER = 13;

// table view type
var VERT = '0';
var HRZN = '1';
var TREE = '2';
var DLOG = '3';
var FORM = '4';
var TREE_INDEX_START = 4;	// this gap is caused by enterprise,ent_name,rowid,parent_id

var evenColor = '#d3e9ff';
var oddColor = '#aad4ff';
// tree table
var OPEN = '../images/expand.png';
var CLOSE = '../images/collapse.png';
var LEAF = '../images/file.png';

// for GRID.layout
var cell_left='<tr><td></td>';
var cell_right='<td></td></tr>';
var cell_only='<tr><td></td></tr>';

var FMT_KOR='\d{1,4}\-\d{1,2}\-\d{1,2}';
var FMT_ENU='\d{1,2}\-\d{1,2}\-\d{1,4}';

/*
 * the index of current Grid with input cursor(focus).
 * This should be changed when clicking/double-clicking on the Grid.
 */
//var clicks = 0, timer = null, DELAY = 400;
var thispage=null;
//var objArr = [];
var NextCancel=false;
var NextContinue=true;

var garPage=[];
var garGrid=[]; 
var seqno=0;
var nTimer;
