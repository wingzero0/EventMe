<?php
/*
 * handle the request of updating profile keyword:
 * GET parameter (Input)
 * 1. "op": str value should be "insert" or "get"
 * 		if value is "insert", GET fields "userID", "num", "keywordIDX", "wight" will also be required
 * 			this program will insert keywords and their corresponding wegihting to db, 
 * 			all keywords will be treated as a profile.
 * 		if value is "get", GET fields "userID" will be required
 * 			the program will retrieve all profile keywordIDs of the user.
 * 2. "num": int value indicate that how many keywords will be queried
 * 		if it is bigger than 0, it means that there will be 
 * 			"keywordID0", "keywordID1" ... "keywordIDX", "weight0", "weight1" ... "weightX"
 * 			(X = num) in the Get parameter for query
 * 3. "keywordIDX": int value
 * 		the value must exist in table `Keyword` in database, or it will trigger error.
 * 4. "weight" : double value
 * 		the value represents the wegihting of keywordIDX in profile.
 *
 * Return value it will return a json array.
 * For "op" = "insert"  
 *	json["ret"]: equal to 1 if successful, equal to -1 if fail.
 *  json["error"]: if "ret" is -1, then "error" will be assigned a string for error cause.
 * 
 * For "op" = "get"
 *  json["ret"]: equal to 1 if successful, equal to -1 if fail.
 *  json["error"]: if "ret" is -1, then "error" will be assigned a string for error cause.
 *  json["objs"]: array of sql result
 *  json["objs"][x]["profileID"]: int value, profile id
 *  json["objs"][x]["keywordID"]: int value, keyword id
 *  json["objs"][x]["weight"]: double value, the keyword weight in the corresponding profile
 *  p.s. one profile will contain many keywords, one keyword will appear in many profile (of a user)  
 *  
 * sample usage
 * 
 * insert:
 * 	http://140.112.29.228/ActivitySuggestion/profileHandler.php?op=insert&userID=2&num=2&keywordID0=3&keywordID1=2&weight=11.11
 * 	return json {"ret":1}
 * get:
 *  http://140.112.29.228/ActivitySuggestion/profileHandler.php?op=get&userID=2
 *  return json {"ret":1,"sqlResult":[{"profileID":"5","keywordID":"2","weight":"11.11"},{"profileID":"5","keywordID":"3","weight":"11.11"}]}
 */

require_once __DIR__."/utility.php";
require_once CONNECTIONPATH . "/connection.php";
require_once CLASSPATH . "/userProfile.php";

$ret = array();
$ret["ret"] = -1;

if (isset($_GET["op"]) && $_GET["op"] == "insert"){
	$s_var = array();

	Utility::AddslashesToGETField("userID", $s_var, "int");
	Utility::AddslashesToGETField("num", $s_var, "int"); // number of keyword in the profile
	
	Utility::AddslashesToGETField("weight", $s_var, "double"); // weighting should be double, one weighting for each keyword
	for ($i = 0;$i<$s_var["num"] ;$i++){
		Utility::AddslashesToGETField("keywordID".$i, $s_var, "int"); // each keywordID should be int
		// Utility::AddslashesToGETField("weight".$i, $s_var, "double"); // each weighting should be double
	}
	
	$ret = UserProfile::InsertUserProfile($s_var);
	echo json_encode($ret);
}else if (isset($_GET["op"]) && $_GET["op"] == "get"){
	$s_var = array();
	Utility::AddslashesToGETField("userID", $s_var, "int");
	$ret = UserProfile::GetUserProfileKeyword($s_var["userID"]);
	$ret["objs"] = $ret["sqlResult"];
	unset($ret["sqlResult"]);
	echo json_encode($ret);
}
?>