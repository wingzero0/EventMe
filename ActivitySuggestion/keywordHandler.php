<?php

/* handle the keyword db request:
 * GET parameter
 * 1. "op": str value should be "insert" or "getIDs"
 * 		if value is "insert", program will insert keywords to db and return 
 * 		their related keyword ids.
 * 		if value is "getIDs", program will retrieve keywords id from db. if the keyword
 * 		is not exist in db, its related id will be 0;
 * 2. "num": int value indicate that how many keywords will be queried
 * 		if it is bigger than 0, it means that there will be 
 * 			"keyword0", "keyword1" ... "keywordX" (X = num) in the Get parameter for query
 * 3. "keywordX": str value
 * 
 * return value: it will return a json array.
 * 	json index "ret": should be 1 if operation is successful
 *  json index "ids": should be an array of the keywords' id. 
 * 
 * ex path = "keywordHandler.php?op=insert&num=3&keyword0=澳門&keyword1=科技&keyword2=人才"
 * return json {"ret":1,"ids":[1,9,10]}
 */

require_once "connection.php";

global $g_mysqli;

if (isset($_GET["op"]) && $_GET["op"] == "insert"){
	// get var
	$s_var = array();
	AddslashesToGETField("num", $s_var);
	$num = intval($s_var["num"]);
	for ($i = 0;$i<$num ;$i++){
		AddslashesToGETField("keyword".$i, $s_var);
	}

	$sql = 'lock tables `Keyword` write';
	$g_mysqli->query($sql);

	if ($g_mysqli->error){
		die( "error sql:".$g_mysqli->error );
	}

	// test if keyword in database
	$exist = array();
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("select `id` from `Keyword` where `Keyword`.`keyword` like '%s'", 
				$s_var["keyword".$i]);
		//echo $sql."<br>";
		$result = $g_mysqli->query($sql);

		if ($g_mysqli->error){
			UnlockTables();
			die( "error sql:".$g_mysqli->error );
		}

		if ($row = $result->fetch_row()){
			$exist[$i] = intval($row[0]);
		}else{
			$exist[$i] = 0;
		}
	}

	// insert if not exist
	for ($i = 0;$i<$num;$i++){
		if (!$exist[$i]){
			$sql = sprintf("insert into `Keyword` (`keyword`) value ('%s')",
					$s_var["keyword".$i]);
			//echo $sql;
			$result = $g_mysqli->query($sql);
			
			if ($g_mysqli->error){
				UnlockTables();
				die( "error sql:".$g_mysqli->error );
			}
			$exist[$i] = $g_mysqli->insert_id;
		}
	}

	UnlockTables();
	$ret['ret'] = 1;
	$ret['ids'] = $exist;
	echo json_encode($ret);	
}else if (isset($_GET["op"]) && $_GET["op"] == "getIDs"){
	// get var
	$s_var = array();
	AddslashesToGETField("num", $s_var);
	$num = intval($s_var["num"]);
	for ($i = 0;$i<$num ;$i++){
		AddslashesToGETField("keyword".$i, $s_var);
	}
	
	// get keyword id in database
	$exist = array();
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("select `id` from `Keyword` where `Keyword`.`keyword` like '%s'",
				$s_var["keyword".$i]);
		$result = $g_mysqli->query($sql);
	
		if ($g_mysqli->error){
			die( "error sql:".$g_mysqli->error );
		}
	
		if ($row = $result->fetch_row()){
			$exist[$i] = intval($row[0]);
		}else{
			$exist[$i] = 0;
		}
	}
	$ret['ret'] = 1;
	$ret['ids'] = $exist;
	echo json_encode($ret);
}


function UnlockTables(){
	$sqlQuery = "Unlock Tables";
	global $g_mysqli;
	$result = mysql_query($sqlQuery);
	return;
}

function AddslashesToGETField($index, &$retAssoc){
	if (isset($_GET[$index])){
		$retAssoc[$index] = addslashes($_GET[$index]);
	}else{
		$retAssoc[$index] = "";
	}
	return $retAssoc;
}

?>


