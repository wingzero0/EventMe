<?php 
/* get activity by some criteria, such as time interval (which is on going or will be held in one month)
 * GET parameter:
 * 	"op": string value, "default" or ...
 * 
 * Return value 
 * For "op" == "default" 
 * 	json["ret"]: should be 1 if operation is successful
 * 	json["ids"]: the list of activity
 *  json["ids"][x]: the activity id
 *  json["error"]: if "ret" == -1, "error" will cantain the error message 
 * 
 * sample usage
 * http://140.112.29.228/ActivitySuggestion/activityGet.php?op=default
 */


require_once 'utility.php';
require_once CLASSPATH.'/activity.php';

$s_var = array();
Utility::AddslashesToGETField("op", $s_var);

if ($s_var["op"] == "default"){
	$actObj = new Activity();
	$ret = $actObj->GetActivityByDefaultTimeInterval();
	if ( !empty($ret["sqlResult"])){
		//$ret["sqlResult"];
		$ret["objs"] = array();
		foreach ($ret["sqlResult"] as $i => $row){
			foreach ($row as $colName => $value){
				$ret["objs"][] = $value;
			}
		}
		unset($ret["sqlResult"]);
	}
	echo json_encode($ret);

}
?>