<?php
/*
 * handle the request of updating profile keyword:
 * 
 * It provide 2 functions, you can choose different functions by setting field parameter "op"
 * 
 * Function:
 * 1. Insert the profile ( including keywordID, weight) to DB
 * Input fields:(post fields)
 * - "op": str value, must be "insert"
 * - "userID": int value, user id
 * - "num": int value, indicate that how many keywords will be queried 
 * 			if it is bigger than 0, it means that there will be 
 * 			"keywordID0", "keywordID1" ... "keywordIDX" (X = num - 1)
 
 * - "keywordIDX": str value, the x-th keyword id
 * - "weight": double value, the wegihting of each keyword in target profile.
 * Return json:
 * - "ret": if it is (int) 1, the operation is successful. If it is -1, the operation is fail.
 * - "error": If "ret" == -1, it will set to be as error message
 * 
 * 2. Get the user profiles, their keywords (in plain text) and weight  
 * Input fields:(post fields)
 * - "op": str value, must be "insert"
 * - "userID": same as function 1
 * Return json:
 * - "ret": if it is (int) 1, the operation is successful. If it is -1, the operation is fail.
 * - "error": If "ret" == -1, it will set to be as error message
 * - "objs": array of sql result
 *    "objs"[x]: x-th row of sql result
 *    "objs"[x]["profileID"]: int value, profile id of x-th row
 *    "objs"[x]["keywordID"]: int value, keyword id
 *    "objs"[x]["weight"]: double value, the keyword weight in the corresponding profile
 *  p.s. one profile will contain many keywords, one keyword will appear in many profile (of a user)  
 *  
 * sample usage
 * 
 * insert:
 * 	http://140.112.29.228/ActivitySuggestion/profileHandler.php?op=insert&userID=2&num=2&keywordID0=3&keywordID1=2&weight=11.11
 * 	return json {"ret":1}
 * get:
 *  http://140.112.29.228/ActivitySuggestion/profileHandler.php?op=get&userID=2
 *  return json {"ret":1,"objs":[{"profileID":"5","keywordID":"2","weight":"11.11"},{"profileID":"5","keywordID":"3","weight":"11.11"}]}
 */

require_once __DIR__."/utility.php";
require_once CONNECTIONPATH . "/connection.php";
require_once CLASSPATH . "/userProfile.php";

$ret = array();
$ret["ret"] = -1;

if (isset($_POST["op"]) && $_POST["op"] == "insert"){
	$s_var = array();

	Utility::AddslashesToPOSTField("userID", $s_var, "int");
	Utility::AddslashesToPOSTField("num", $s_var, "int"); // number of keyword in the profile
	
	Utility::AddslashesToPOSTField("weight", $s_var, "double"); // weighting should be double, one weighting for each keyword
	for ($i = 0;$i<$s_var["num"] ;$i++){
		Utility::AddslashesToPOSTField("keywordID".$i, $s_var, "int"); // each keywordID should be int
		// Utility::AddslashesToPOSTField("weight".$i, $s_var, "double"); // each weighting should be double
	}
	
	$ret = UserProfile::InsertUserProfile($s_var);
	echo json_encode($ret);
}else if (isset($_POST["op"]) && $_POST["op"] == "get"){
	$s_var = array();
	Utility::AddslashesToPOSTField("userID", $s_var, "int");
	$ret = UserProfile::GetUserProfileKeyword($s_var["userID"]);
	$ret["objs"] = $ret["sqlResult"];
	unset($ret["sqlResult"]);
	echo json_encode($ret);
}
?>