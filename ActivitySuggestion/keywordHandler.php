<?php

/* handle the keyword table request:
 * it offers three functions, you can choose different functions by setting field parameter "op"
 * 
 * Function:
 * 1. Insert keyword and idf to dababase
 * Input fields:
 * - "op": str value, must be "insert"
 * - "num": int value, indicate that how many keywords will be queried 
 * 			if it is bigger than 0, it means that there will be 
 * 			"keyword0", "keyword1" ... "keywordX" (X = num - 1)
 * - "keywordX": str value, each one is a term or vocab.
 * - "idfX": double value, it is the corresponding value of "keywordX"
 * Return json:
 * - "ret": if it is (int) 1, the operation is successful. If it is -1, the operation is fail.
 * - "ids": should be an array of the input keywords' id.
 * 	 "ids"[x]: it is the id of x-th keyword in the input
 * - "error": If "ret" == -1, it will set to be as error message
 *  
 * 2. Get the corresponding IDF with the specific term
 * Input fields:
 * - "op": str value, must be "getIDFByTerm"
 * - "objs": same as function 1
 * - "keywordX": same as function 1
 * Return json
 * - "ret": same as function 1
 * - "val": should be an array of idf value
 * 	 "val"[x]: it is the idf of x-th keyword in the input; if keyword not exists, the value will be 0
 * - "error": same as function 1
 *  
 * 3. Get the corresponding IDF with the specific keyword ID
 * Input fields:
 * - "op": str value, must be "getIDFByID"
 * - "num": same as function 1
 * - "idX": int value, it is x-th keyword id in the query.
 * Return json
 * - "ret": same as function 1
 * - "val": same as function 2
 * 	 "val"[x]: same as function 2
 * - "error": same as function 1
 * 
 * 4. Get the corresponding ID with the specific term
 * Input fields:
 * - "op": str value, must be "getIDByTerm"
 * - "num": same as function 1
 * - "keywordX" : same as function 1
 * Return json
 * - "ret": same as function 1
 * - "val": should be an array of keyword id
 * 	 "val"[x]: it is the id of x-th keyword in the input; if keyword not exists, the value will be 0
 * - "error": same as function 1
 * 
 * 
 * 5. Get the corresponding term with the specific ID
 * Input fields: 
 * - "op": strn value, must be "getTermByID"
 * - "num": same as function 1
 * - "idX": same as function 3
 * Return json
 * - "ret": same as function 1
 * - "val": should be an array of keyword term
 * 	 "val"[x]: it is the term of x-th keyword id in the input; if ID does not exists, the value will be a empty string
 * - "error": same as function 1
 * 
 *  
 * http://140.112.29.228/ActivitySuggestion/keywordHandler.php?op=insert&num=3&keyword0=澳門&keyword1=科技&keyword2=人才&idf0=0.234&idf1=0.456&idf2=789
 * {"ret":1,"ids":[1,5,10]}
 * 
 * http://localhost/ActivitySuggestion/keywordHandler.php?op=getIDFByTerm&num=3&keyword0=澳門&keyword1=科技&keyword2=天才
 * {"ret":1,"val":[0.234,0.456,0]}
 * 
 * http://localhost/ActivitySuggestion/keywordHandler.php?op=getIDFByID&num=3&id0=1&id1=2&id2=30
 * {"ret":1,"val":[0.234,0.1,0]}  
 * 
 * http://localhost/ActivitySuggestion/keywordHandler.php?op=getIDByTerm&num=3&keyword0=澳門&keyword1=科技&keyword2=天才
 * {"ret":1,"objs":[1,5,0]}
 * 
 * http://localhost/ActivitySuggestion/keywordHandler.php?op=getTermByID&num=3&id0=1&id1=5&id2=30 
 */

require_once __DIR__ . "/utility.php";
require_once CONNECTIONPATH . "/connection.php";
require_once CLASSPATH . "/keyword.php";

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
}else if (isset($_GET["op"]) && $_GET["op"] == "getIDFByTerm"){
	// get var
	$s_var = array();
	Utility::AddslashesToGETField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("keyword".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetIDFByTerm($s_var["keyword".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo json_encode($ret);
}else if (isset($_GET["op"]) && $_GET["op"] == "getIDFByID"){
	// get var
	$s_var = array();
	Utility::AddslashesToGETField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("id".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetIDFByID($s_var["id".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo json_encode($ret);
}else if (isset($_GET["op"]) && $_GET["op"] == "getIDByTerm"){
	// get var
	$s_var = array();
	Utility::AddslashesToGETField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("keyword".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetIDByTerm($s_var["keyword".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo json_encode($ret);
}else if (isset($_GET["op"]) && $_GET["op"] == "getTermByID"){
	// get var
	$s_var = array();
	Utility::AddslashesToGETField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("id".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetTermByID($s_var["id".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo Utility::DecodeUnicode(json_encode($ret));
}



function UnlockTables(){
	$sqlQuery = "Unlock Tables";
	global $g_mysqli;
	$result = mysql_query($sqlQuery);
	return;
}


?>


