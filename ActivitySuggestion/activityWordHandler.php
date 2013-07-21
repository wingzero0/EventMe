<?php 
/* 
 * Handle the ActivityWord table request:
 * 
 * It provide 2 function
 * 
 * Function:
 * 1. Insert related words (in terms / plain text) to ActivityWord table for the specific ActivityID
 * Input fields (post fields):
 * - "op": str value, must be "insertActivityWordByPlainTxt"
 * - "activityID": int value, the id of target activity
 * - "num": int value, indicate that how many keywords will be queried 
 * 			if it is bigger than 0, it means that there will be 
 * 			"keyword0", "keyword1" ... "keywordX" (X = num - 1)
 * - "keywordX": str value, each one is a term or vocab.
 * - "tfX": int value, it is the corresponding value of "keywordX"
 * Return json:
 * - "ret": if it is (int) 1, the operation is successful. If it is -1, the operation is fail.
 * - "error": If "ret" == -1, it will set to be as error message
 * 
 * 2. Insert related words (in keywords id) to ActivityWord table for the specific ActivityID
 * Input fields (post fields):
 * - "op": str value, must be "insertActivityWordByKeywordID"
 * - "activityID": same as function 1
 * - "num": int value, indicate that how many keywords will be queried 
 * 			if it is bigger than 0, it means that there will be 
 * 			"keywordID0", "keywordID1" ... "keywordIDX" (X = num - 1)
 * - "keywordIDX": int value, the keyword id of x-th keyword
 * - "tfX": int value, it is the corresponding value of "keywordIDX"
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * 
 * 3. Get Activity keyword id and its corresponding TF
 * Input fields (post fields):
 * - "op": str value, must be "getActivityWordWithKeywordID"
 * - "activityID": same as function 1
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * - "objs": an array of keywords' objects
 *  "objs"[x]: the x-th keywords' objects of the target activity 
 *  "objs"[x]["keywordID"]: int val, the id of x-th kewyord in the activity
 *  "objs"[x]["tf"]: int val, term freq of x-th kewyord in the activity
 *  
 *  the return example {"ret":1,"objs":[{"keywordID":1,"tf":5},{"keywordID":2,"tf":55}]}
 *  
 *  
 * 4. Get Activity keyword (plain text), keyword ID and their corresponding TF
 * Input fields (post fields):
 * - "op": str value, must be "getActivityWordWithKeyword"
 * - "activityID": same as function 1
 * Return json:
 * - "ret": same as function 1
 * - "error": same as function 1
 * - "objs": an array of keywords' objects
 *  "objs"[x]: the x-th keywords' objects of the target activity 
 *  "objs"[x]["keyword"]: int val, the plain text of x-th kewyord in the activity
 *  "objs"[x]["keywordID"]: int val, the id of x-th kewyord in the activity
 *  "objs"[x]["tf"]: int val, term freq of x-th kewyord in the activity
 *  
 *  the return example {"ret":1,"objs":[{"keyword":"澳門","keywordID":1,"tf":3},{"keyword":"科技","keywordID":5,"tf":207}]}
 */
require_once __DIR__ . '/utility.php';
require_once CONNECTIONPATH .'/connection.php';
require_once CLASSPATH . '/activityWord.php';

$s_var = array();
$ret = array();
$ret['ret'] = -1;

if (isset($_POST["op"]) && $_POST["op"] == "insertActivityWordByPlainTxt"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("activityID", $s_var, "int");
	Utility::AddslashesToPOSTField("num", $s_var, "int");
	$num = $s_var["num"];
	
	$s_keywords = array();
	$s_tfs = array();
	for ($i = 0;$i<$num ;$i++){
		$s_keywords[$i] = addslashes($_POST["keyword".$i]);
		$s_tfs[$i] = addslashes($_POST["tf".$i]);
	}
	
	$aw = new ActivityWord();
	
	$ret = $aw->InsertActivityWordByPlainTxt($s_var["activityID"], $s_keywords, $s_tfs);
	
	echo json_encode($ret);
}else if (isset($_POST["op"]) && $_POST["op"] == "insertActivityWordByKeywordID"){
	// for insert or update activity keyword 
	
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("activityID", $s_var, "int");
	Utility::AddslashesToPOSTField("num", $s_var, "int");
	$num = $s_var["num"];
	
	$s_keywordIDs = array();
	$s_tfs = array();
	for ($i = 0;$i<$num ;$i++){
		$s_keywordIDs[$i] = intval( addslashes($_POST["keywordID".$i]) );
		$s_tfs[$i] = intval( addslashes($_POST["tf".$i]) );
	}
	
	$aw = new ActivityWord();
	
	$ret = $aw->InsertActivityWordByKeywordID($s_var["activityID"], $s_keywordIDs, $s_tfs);
	
	echo json_encode($ret);
	
}else if (isset($_POST["op"]) && $_POST["op"] == "getActivityWordWithKeywordID"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("activityID", $s_var, "int");
	
	$aw = new ActivityWord();
	$ret = $aw->GetActivityWordWithKeywordID($s_var["activityID"]);
	echo json_encode($ret);
}else if (isset($_POST["op"]) && $_POST["op"] == "getActivityWordWithKeyword"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("activityID", $s_var, "int");
	
	$aw = new ActivityWord();
	$ret = $aw->GetActivityWordWithKeyword($s_var["activityID"]);
	echo Utility::DecodeUnicode(json_encode($ret));
}
?>
