<?php 
/* 
 * Handle the ActivityWord table request:
 * GET parameter (input parameter)
 * 1. "op": string value, should be "insert" or "get"
 * 		if value equals to "insert", GET parameter "activityID", "num", "keywordIDX", "tfX" are also required
 * 		if value equals to "get", GET parameter "activityID" are also required 
 * 2. "activityID": int value, the reference of activity id in table `Activity` 
 * 3. "num": int value, indicate that how many keywords and their related term frequency will appear.
 * 		"keywordID0", "keywordID1", ..., "keywordIDX" (X = num -1) will be required
 * 		"tf0", "tf1", ..., "tfX" (X = num -1) will be required
 * 4. "keywordIDX": int value, the reference of keyword id in table `Keyword`
 * 5. "tfX": int value, indicate the term frequency that the keywordX appear in the related activity.
 * 
 * Return value 
 * For "op" == "insert" 
 * 	json["ret"]: should be 1 if operation is successful
 * For "op" == "get"
 * 	json["ret"]: should be 1 if operation is successful
 *  json["objs"]: should be an array of objects
 *  json["objs"][x]["keywordID"]: is the id of kewyordX of the target activity
 *  json["objs"][x]["tf"]: is term freq of kewyordX of the target activity
 *  
 * insert example
 * path = http://140.112.29.228/ActivitySuggestion/activityWordHandler.php?op=insert&num=2&activityID=1&keywordID0=1&keywordID1=5&tf0=10&tf1=222
 * return json {"ret":1}
 * 
 * get example
 * path = http://140.112.29.228/ActivitySuggestion/activityWordHandler.php?op=get&activityID=1
 * return json {"ret":1,"objs":[{"keywordID":1,"tf":10},{"keywordID":5,"tf":222}]}
 */
require_once 'utility.php';
require_once 'connection.php';

$s_var = array();
$ret = array();
$ret['ret'] = -1;

if (isset($_GET["op"]) && $_GET["op"] == "insert"){
	// for insert or update activity keyword 

	// get var
	$s_var = array();
	Utility::AddslashesToGETField("activityID", $s_var, "int");
	Utility::AddslashesToGETField("num", $s_var, "int");
	$num = $s_var["num"];
	
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToGETField("keywordID".$i, $s_var, "int");
		Utility::AddslashesToGETField("tf".$i, $s_var, "int");
	}
	
	// start transaction;
	$flag = $g_mysqli->autocommit(false);
	if (!$flag){
		$ret['error'] = "db error, can't disable autocommit";
		echo json_encode($ret);
		return;
	}
	
	// test if "activity, keyword" pair is in database
	// if yes, update keyword's tf
	// if no, insert the "activity, keyword" pair and its corresponding tf
	$exist = array();
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("select `id` from `ActivityWord` where `ActivityID` = %d AND `KeywordID` = %d",
				$s_var["activityID"], $s_var["keywordID".$i]);
		//echo $sql."<br>";
		$result = $g_mysqli->query($sql);
	
		if ($g_mysqli->error){
			$ret['error'] = "sql error:" . $sql . " ". $g_mysqli->error;
			$flag = $g_mysqli->rollback();
			if (!$flag){
				$ret['error'] .= " db error, rollback fail";
			}
			echo json_encode($ret);
			return;
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
			$sql = sprintf("update `ActivityWord` set `TermFreq` = %d where `id` = %d",
					$s_var["tf".$i], $exist[$i]);
			$result = $g_mysqli->query($sql);
	
			if ($g_mysqli->error){
				$ret['error'] = "sql error:" . $sql . " ". $g_mysqli->error;
				$flag = $g_mysqli->rollback();
				if (!$flag){
					$ret['error'] .= " db error, rollback fail";
				}
				echo json_encode($ret);
				return;
			}
		}else{
			$sql = sprintf("insert into `ActivityWord` (`ActivityID`, `KeywordID`, `TermFreq`) value (%d, %d, %d)",
					$s_var["activityID"], $s_var["keywordID".$i], $s_var["tf".$i]);
			//echo $sql;
			$result = $g_mysqli->query($sql);
				
			if ($g_mysqli->error){
				$ret['error'] = "sql error:" . $sql . " ". $g_mysqli->error;
				$flag = $g_mysqli->rollback();
				if (!$flag){
					$ret['error'] .= " db error, rollback fail";
				}
				echo json_encode($ret);
				return;
			}
		}
	}
	
	$flag = $g_mysqli->commit();
	if (!$flag){
		$ret['error'] = " db error, commit fail";
	}else{
		$ret['ret'] = 1;
	}
	echo json_encode($ret);
	return;
}else if (isset($_GET["op"]) && $_GET["op"] == "get"){
	// get var
	$s_var = array();
	Utility::AddslashesToGETField("activityID", $s_var, "int");
	
	$sql = sprintf("select `KeywordID`, `TermFreq` from `ActivityWord` where `ActivityID` = %d order by `KeywordID`",
				$s_var["activityID"]);
	$result = $g_mysqli->query($sql);
	
	if ($g_mysqli->error){
		$ret['error'] = "sql error:" . $sql . " ". $g_mysqli->error;
		echo json_encode($ret);
		return;
	}

	$ret["objs"] = array();
	$i = 0;
	while ($row = $result->fetch_row()){
		$ret["objs"][$i]['keywordID'] = intval($row[0]);
		$ret["objs"][$i]['tf'] = intval($row[1]);
		$i++;
	}
	
	$ret['ret'] = 1;
	
	echo json_encode($ret);
	return;
}
?>
