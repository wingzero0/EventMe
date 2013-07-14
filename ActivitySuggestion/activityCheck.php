<?php 
/* given the name of activity, check if it is in database
 * GET parameter:
 * 	"name": the activity name
 * 
 * return value
 * return 1 if activity exists, 0 if activity does not exist
 * 
 * sample usage
 * http://140.112.29.228/ActivitySuggestion/activityCheck.php?name=CSI:%20%E7%8A%AF%E7%BD%AA%E8%AA%BF%E6%9F%A5%E9%AB%94%E9%A9%97%E5%B1%95%20(%E6%BE%B3%E9%96%80)
 */


require_once 'utility.php';
require_once CLASSPATH.'/activity.php';

$s_var = array();
Utility::AddslashesToGETField("name", $s_var);
if (!empty($s_var["name"])){
	//echo $s_var["name"];
	$activityRet = Activity::SearchActivityWithName($s_var["name"]);
	//echo json_encode($activityRet);
	$ret = 0;
	if (!empty($activityRet['sqlResult'])){
		$ret = 1;
	}
	echo json_encode($ret);

}
?>