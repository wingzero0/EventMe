<?php
/*
 * handle the request of updating profile keyword:
 * GET parameter
 * 1. "op": str value should be "insert"
 * 		if value is "insert", program will insert keywords and their 
 * 		corresponding wegihting to db, all keywords will be treated as a profile.
 * 2. "num": int value indicate that how many keywords will be queried
 * 		if it is bigger than 0, it means that there will be 
 * 			"keywordID0", "keywordID1" ... "keywordIDX", "weight0", "weight1" ... "weightX"
 * 			(X = num) in the Get parameter for query
 * 3. "keywordIDX": int value
 * 		the value must exist in table `Keyword` in database, or it will trigger error.
 * 4. "weightX" : double value
 * 		the value represents the wegihting of keywordIDX in profile.
 * 
 * return value: it will return a json array.
 * 	json index "ret": equal to 1 if successful, equal to -1 if fail.
 *  json index "error": if "ret" is -1, then "error" will be assigned a string for error cause.
 *   
 * sample usage
 * profileHandler.php?op=insert&userID=1&num=2&keywordID0=9&keywordID1=10&weight0=1&weight1=55
 */

require_once 'connection.php';
require_once "utility.php";


$ret = array();
$ret["ret"] = -1;

if (isset($_GET["op"]) && $_GET["op"] == "insert"){
	$s_var = array();

	Utility::AddslashesToGETField("userID", $s_var);
	Utility::AddslashesToGETField("num", $s_var); // number of keyword in the profile
	
	$num = intval($s_var["num"]);
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("keywordID".$i, $s_var, "int"); // each keywordID should be int
		Utility::AddslashesToGETField("weight".$i, $s_var, "double"); // each weighting should be double
	}
	
	$sql = 'lock tables `Keyword` read, `UserProfile` write, `ProfileKeyword` write';
	$g_mysqli->query($sql);
	if ($g_mysqli->error){
		$ret["error"] = $g_mysqli->error;
		echo json_encode($ret);
		return;
	}
	
	// check reference keyword id's existance
	for ($i =0;$i<$num;$i++){
		$sql = sprintf("SELECT `id` FROM  `Keyword` WHERE `id` =%d", 
				$s_var["keywordID".$i]);
		$result = $g_mysqli->query($sql);
		
		if ($g_mysqli->error){
			$ret["error"] = $g_mysqli->error;		
			echo json_encode($ret);
			Utility::UnlockTables($g_mysqli);
			return;
		}else if (  !($result->fetch_row()) ) {
			// no refernce keyword
			$ret["error"] = "keyword id " . $s_var["keywordID".$i] . " doesn't exist";
			echo json_encode($ret);
			Utility::UnlockTables($g_mysqli);
			return;
		}
	}
	
	// create new profile, get profile id,
	$sql = sprintf("insert into `UserProfile` (`UserID`) value ('%s')", $s_var["userID"]);
	$result = $g_mysqli->query($sql);
	
	if ($g_mysqli->error){
		$ret["error"] = $g_mysqli->error;
		echo json_encode($ret);
		Utility::UnlockTables($g_mysqli);
		return;
	}
	$profileID = $g_mysqli->insert_id;
	
	// insert profile id, keyword id to ProfileKeyword table
	for ($i = 0;$i<$num;$i++){
		$sql = sprintf("insert into `ProfileKeyword` (`ProfileID`, `KeywordID`, `Weight`) value (%d, %d, %lf)",
				$profileID, $s_var["keywordID".$i], $s_var["weight".$i]);
		$result = $g_mysqli->query($sql);
		
		if ($g_mysqli->error){
			$ret["error"] = $g_mysqli->error;
			echo json_encode($ret);
			Utility::UnlockTables($g_mysqli);
			return;
		}
	}
	
	$ret["ret"] = 1;
	Utility::UnlockTables($g_mysqli);
	echo json_encode($ret);
}
?>
