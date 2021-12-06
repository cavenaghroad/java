<?php
$_root_path="/home/hosting_users/xaexal/www/";
include_once($_root_path."/customs/comlib/class_stuff.php");

if( $_GET['table'] == null ) exit;

define("MAPS_HOST", "maps.google.com");
define("KEY", "ABQIAAAALymmQR5OQL1czYXjH3nRghTQUAoZ6hhIbag9ake3dJsmGeJNUBRqCFO5vVOw0tgOuzLtDcZyWT3H4Q");

logwrite("");

// Opens a connection to a MySQL server
$connection = mysql_connect("localhost", "xaexal", "wogud99");
if (!$connection) {
  die("Not connected : " . mysql_error());
}

// Set the active MySQL database
$db_selected = mysql_select_db("xaexal", $connection);
if (!$db_selected) {
  die("Can\'t use db : " . mysql_error());
}

switch( $table ) {
case "property":
	$query = "select * from property where google_lat is null and google_lng is null order by cnum";
	$str_addr = " singapore";
	$str_table = "property";
	$str_order = "cnum";
	break;
case "mrt":
	$query = "select * from mrt where google_lat is null and google_lng is null order by num";
	$str_addr = " mrt station singapore";
	$str_table = "mrt";
	$str_order = "num";
	break;
case "school":
	$query = "select * from school where google_lat is null and google_lng is null order by snum";
	$str_addr = " singapore";
	$str_table = "school";
	$str_order = "snum";
	break;
case "shopmall":
	$query = "select * from shopmall where google_lat is null and google_lng is null order by snum";
	$str_addr = " singapore";
	$str_table = "shopmall";
	$str_order = "snum";
	break;
}

// Select all the rows in the markers table
$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}

// Initialize delay in geocode speed
$delay = 100000;
$base_url = MAPS_HOST . "";

logwrite("RecordCount [".mysql_num_rows($result)."]\n");
echo "RecordCount [".mysql_num_rows($result)."]<br>";

// Iterate through the rows, geocoding each address
while ($row = @mysql_fetch_assoc($result)) {
	$geocode_pending = true;
	$search_key = "name";
	
	while ($geocode_pending) {
		$address = $row[$search_key].$str_addr;

		$request_url = "/maps/geo?output=xml&key=" . KEY."&q=" . urlencode($address);
		
		$out = "GET $request_url HTTP/1.0\r\n\r\n";
	
		$fp = fsockopen($base_url, "80", $errno, $errstr, 30);
		
		if (!$fp) {
			logwrite( "$errno ($errstr)");
			echo "$errno ($errstr)<br>";
			usleep($delay);
			continue;
		}
			
		fputs($fp, $out);
			
		$xmlstr = null; $cnt = 0;
		$doing = false;
		while (!feof($fp)) {
			$doing = true;
			$cnt ++;
			$xmlstr .= fgets($fp, 128);
		}
		fclose($fp);
	
		$i = 0;
		$status_code = getnodevalue("<code>","</code>");
		if( $status_code == "200" ) {
			$postcode = getnodevalue("<PostalCodeNumber>","</PostalCodeNumber>");
			$addr = getnodevalue("<address>","</address>");
			$north = getnodevalue("north=\"","\"");
			$south = getnodevalue("south=\"","\"");
			$east = getnodevalue("east=\"","\"");
			$west = getnodevalue("west=\"","\"");
			$coordinate = explode(",",getnodevalue("<coordinates>","</coordinates>"));
			
			logwrite( "Name [".$address."]\npostcode [".$postcode."]\nlat[".$coordinate[0]."]\nlng[".$coordinate[1]."]\nnorth[".$north."]\nsouth[".$south."]\neast[".$east."]\nwest[".$west."]\n" );
			echo "<br>Name [".$address."]\npostcode [".$postcode."]\nlat[".$coordinate[0]."]\nlng[".$coordinate[1]."]\nnorth[".$north."]\nsouth[".$south."]\neast[".$east."]\nwest[".$west."]<br>" ;
	
			$psql = "update ".$str_table." set address_aux='".$addr."',postcode_aux='".$postcode."',google_lng='".$coordinate[0]."',google_lat='".$coordinate[1].
						"',north='".$north."',south='".$south."',east='".$east."',west='".$west."' where ".$str_order."=".$row[$str_order];
			$results = mysql_query($psql);
			logwrite($psql."\n\n");
			echo "<br>".$psql."<br>";
			
		} else {
			logwrite($xmlstr."\n\n");
			if( $geocode_pending == true && $search_key == "name"  && $_GET['table'] != "mrt" ) {
				$search_key = "address";
				continue;
			}
		}		
			$geocode_pending = false;
		
	}

}
function getnodevalue($starttag,$endtag) {
	global $xmlstr, $i;
	
	$i = strpos($xmlstr,$starttag,$i);
	if( $i === FALSE )	return "";
	$i += strlen($starttag);
	$j = strpos($xmlstr,$endtag,$i+1);
	if( $j === FALSE ) return "";
	$value = substr($xmlstr,$i,$j-$i);
	
	$i = $j+strlen($endtag);
	return $value;
}

?>