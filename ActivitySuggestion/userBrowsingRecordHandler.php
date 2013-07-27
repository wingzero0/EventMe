<?php
 
require_once __DIR__ . "/utility.php";
require_once CLASSPATH. "/userBrowsingRecord.php";

if (isset($_POST["op"]) && $_POST["op"] == "insert"){
	$s_var = array();
	Utility::AddslashesToPOSTField("num", $s_var);
	Utility::AddslashesToPOSTField("uid", $s_var, "int");
	for ($i = 0;$i<$num ;$i++){
		$s_activityIDs[$i] = addslashes($_POST["activityID".$i]);
	}
	$ub = new UserBrowsingRecord();
}else if (isset($_POST["op"]) && $_POST["op"] == "getActivityIDByUID"){
	$s_var = array();
	Utility::AddslashesToPOSTField("uid", $s_var, "int");
	
	$ub = new UserBrowsingRecord();
	$ret = $ub->GetActivityIDByUID($s_var["uid"]);
	
	$ret["activityID"] = array();
	if ($ret["ret"] == 1 && isset($ret["sqlResult"]) && !empty($ret["sqlResult"]) ){
		foreach ($ret["sqlResult"] as $row){
			$ret["activityID"][] = $row["ActivityID"];
		}
	}
	
	if (isset($ret["sqlResult"])){
		unset($ret["sqlResult"]);
	}
	echo json_encode($ret);
}
?>