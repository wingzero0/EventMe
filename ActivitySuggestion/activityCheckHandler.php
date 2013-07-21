<?php 
/* given the name of activity, check if it is in database
 * POST parameter:
 * 	"name": the activity name
 * 
 * return value
 * return 1 if activity exists, 0 if activity does not exist
 * 
 */


require_once 'utility.php';
require_once CLASSPATH.'/activity.php';

$s_var = array();
Utility::AddslashesToPOSTField("name", $s_var);
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