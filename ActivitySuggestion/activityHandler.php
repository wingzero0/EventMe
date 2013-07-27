<?php 
/*
 * it offer 6 function
 * 
 * 1. Get the recent activity ID, 
 * input parameter:
 * - "op": string value, must be "default"
 * Return json:
 * - "ret": if it is (int) 1, the operation is successful. If it is -1, the operation is fail.
 * - "ids": the array of result ids
 * - "ids"[x]: the x-th activity id.
 * - "error": If "ret" == -1, it will set to be as error message 
 *  
 * 2. Insert the user comment for a activity
 * input parameter:
 * - "op": string value, must be "insertActivityComment"
 * - "id": int value, the activity id;
 * - "userID": int value, the user id (who made the comment);
 * - "comment": string value, the comment issued form specific user to a specific activity
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 *   
 * 3. Return the users comment for a activity 
 * Input parametr:
 * - "op": string value, must be "getActivityComment"
 * - "id": same as function 2
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * - "objs": the array of result objs
 * - "objs"[x]: the x-th comment object
 * - "objs"[x]["UserID"]: the user id (who make the comment) of the x-th result.
 * - "objs"[x]["Comment"]: the comment of the x-th result.
 * 
 * 4. insert the record that user set "like" for a activity
 * Input parameter:  
 * - "op": string value, must be "setLike"
 * - "id": same as function 2
 * - "userID": int value, the user id (who made "like");
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * 
 * 5. delete the record of user like for a activity
 * Input parameter:  
 * - "op": string value, must be "unsetLike"
 * - "id": same as function 4
 * - "userID": same as function 4
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * 
 * 6. get like count for a acativity
 * Input parameter:  
 * - "op": string value, must be "getActivityLikeCount"
 * - "id": same as function 1
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * 
 * 7. check if the user like a specific activity or not
 * Input parameter:  
 * - "op": string value, must be "checkLike"
 * - "id": same as function 4
 * - "userID": same as function 4
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * - "val": if user likes the activity, "val" = 0, otherwise, "val" = 0. 
 * 
 */


require_once 'utility.php';
require_once CLASSPATH.'/activity.php';

$s_var = array();
Utility::AddslashesToPOSTField("op", $s_var);

if ($s_var["op"] == "default"){
	$actObj = new Activity();
	$ret = $actObj->GetActivityByDefaultTimeInterval();
	if ( !empty($ret["sqlResult"])){
		//$ret["sqlResult"];
		$ret["ids"] = array();
		foreach ($ret["sqlResult"] as $i => $row){
			foreach ($row as $colName => $value){
				$ret["ids"][] = $value;
			}
		}
		unset($ret["sqlResult"]);
	}
	echo json_encode($ret);
}else if ($s_var["op"] == "getActivityDescription"){
	Utility::AddslashesToPOSTField("id", $s_var, "int");
	Utility::AddslashesToPOSTField("operator", $s_var);
	if (empty($s_var["operator"])){
		$s_var["operator"] = ">=";
	}
	$actObj = new Activity();
	$ret = $actObj->GetActivityDescriptionByID($s_var["id"], $s_var["operator"]);
	if ( isset($ret["sqlResult"])){
		$ret["objs"] = $ret["sqlResult"]; // rename the index
		unset($ret["sqlResult"]);
	}
	echo Utility::DecodeUnicode(json_encode($ret));
}else if ($s_var["op"] == "insertActivityComment"){
	Utility::AddslashesToPOSTField("id", $s_var, "int");
	Utility::AddslashesToPOSTField("userID", $s_var, "int");
	Utility::AddslashesToPOSTField("comment", $s_var);
	
	$actObj = new Activity();
	$ret = $actObj->InsertActivityComment($s_var["id"], $s_var["userID"], $s_var["comment"]);
	echo json_encode($ret);
	
}else if ($s_var["op"] == "getActivityComment"){
	Utility::AddslashesToPOSTField("id", $s_var, "int");
	$actObj = new Activity();
	$ret = $actObj->GetActivityComment($s_var["id"]);
	if ( isset($ret["sqlResult"])){
		$ret["objs"] = $ret["sqlResult"]; // rename the index
		unset($ret["sqlResult"]);
	}
	echo Utility::DecodeUnicode(json_encode($ret));
}else if ($s_var["op"] == "setLike" || $s_var["op"] == "unsetLike"){
	Utility::AddslashesToPOSTField("id", $s_var, "int");
	Utility::AddslashesToPOSTField("userID", $s_var, "int");
	$actObj = new Activity();
	$ret = $actObj->SetUnsetActivityLike($s_var["id"], $s_var["userID"], $s_var["op"] == "setLike");
	echo json_encode($ret);
}else if ($s_var["op"] == "getActivityLikeCount"){
	Utility::AddslashesToPOSTField("id", $s_var, "int");
	$actObj = new Activity();
	$ret = $actObj->GetActivityLikeCount($s_var["id"]);
	if ( isset($ret["sqlResult"])){
		$ret["val"] = $ret["sqlResult"]; // rename the index
		unset($ret["sqlResult"]);
	}
	echo json_encode($ret);
}else if ($s_var["op"] == "checkLike"){
	Utility::AddslashesToPOSTField("id", $s_var, "int");
	Utility::AddslashesToPOSTField("userID", $s_var, "int");
	$actObj = new Activity();
	$ret = $actObj->IsActivityLikeByUser($s_var["id"], $s_var["userID"]);
	if ( isset($ret["sqlResult"])){
		$ret["val"] = $ret["sqlResult"]; // rename the index
		unset($ret["sqlResult"]);
	}
	echo json_encode($ret);
}
?>