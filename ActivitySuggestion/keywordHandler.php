<?php

/* handle the keyword db request:
 * GET parameter
 * 1. "op": str value should be "insert" or "getIDs"
 * 		if value is "insert", program will insert keywords and corresponding idf to db and return 
 * 		their related keyword ids.
 * 		if value is "getIDs", program will retrieve keywords id and corresponding idf from db. if the keyword
 * 		is not exist in db, its related id and idf will be 0 and 0.0;
 * 2. "num": int value indicate that how many keywords will be queried
 * 		if it is bigger than 0, it means that there will be 
 * 			"keyword0", "keyword1" ... "keywordX" (X = num) in the Get parameter for query
 * 3. "keywordX": str value
 * 4. "idfX": double value
 * 		if "op" value equals to "insert", it must be specified the idf value for each keyword. 
 * 		"idfX" is the corresponding value of "keywordX" 
 * 
 * Return value: it will return a json array.
 * For "op" == "insert" 
 * 	json["ret"]: should be 1 if operation is successful
 *  json["ids"]: should be an array of the keywords' id.
 *  json["ids"][x]: is the id of keywordX
 * For "op" == "getIDs"
 * 	json["ret"]: should be 1 if operation is successful
 *  json["objs"]: should be an array of objects
 *  json["objs"][x]["id"]: is the id of kewyordX
 *  json["objs"][x]["idf"]: is the idf of kewyordX
 *  
 * ex path = "keywordHandler.php?op=insert&num=3&keyword0=澳門&keyword1=科技&keyword2=人才&idf0=0.234&idf1=0.456&idf2=789"
 * return json {"ret":1,"ids":[1,5,10]}
 * 
 * ex path = "keywordHandler.php?op=getIDs&num=3&keyword0=澳門&keyword1=科技&keyword2=天才" 
 * return json {"ret":1,"objs":[{"id":1,"idf":0.234},{"id":5,"idf":0.456},{"id":0,"idf":0}]}
 */

require_once "connection.php";
require_once "utility.php";

global $g_mysqli;

if (isset($_GET["op"]) && $_GET["op"] == "insert"){
	// get var
	$s_var = array();
	Utility::AddslashesToGETField("num", $s_var);
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("keyword".$i, $s_var);
		Utility::AddslashesToGETField("idf".$i, $s_var, "double");
	}

	$sql = 'lock tables `Keyword` write';
	$g_mysqli->query($sql);

	if ($g_mysqli->error){
		die( "error sql:".$g_mysqli->error );
	}

	// test if keyword in database
	// if yes, update keyword's idf
	// if no, insert the keyword term and it corresponding idf
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
		if ($exist[$i]){
			$sql = sprintf("update `Keyword` set `InverseDocFreq` = '%lf' where `id` = %d", 
					$s_var["idf".$i], $exist[$i]);
			$result = $g_mysqli->query($sql);
				
			if ($g_mysqli->error){
				UnlockTables();
				die( "error sql:".$g_mysqli->error );
			}
		}else{
			$sql = sprintf("insert into `Keyword` (`Keyword`, `InverseDocFreq`) value ('%s', '%lf')",
					$s_var["keyword".$i], $s_var["idf".$i]);
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
	Utility::AddslashesToGETField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("keyword".$i, $s_var);
	}
	
	// get keyword id in database
	$exist = array();
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("select `id`, `InverseDocFreq` from `Keyword` where `Keyword`.`keyword` like '%s'",
				$s_var["keyword".$i]);
		$result = $g_mysqli->query($sql);
	
		if ($g_mysqli->error){
			die( "error sql:".$g_mysqli->error );
		}
		$exist[$i] = array();
		if ($row = $result->fetch_row()){
			$exist[$i]["id"] = intval($row[0]);
			$exist[$i]["idf"] = doubleval($row[1]);
		}else{
			$exist[$i]["id"] = 0;
			$exist[$i]["idf"] = 0.0;
		}
	}
	$ret['ret'] = 1;
	$ret['objs'] = $exist;
	echo json_encode($ret);
}


function UnlockTables(){
	$sqlQuery = "Unlock Tables";
	global $g_mysqli;
	$result = mysql_query($sqlQuery);
	return;
}


?>


