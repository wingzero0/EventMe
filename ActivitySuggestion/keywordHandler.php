<?php

/* handle the keyword table request:
 * it offers three functions, you can choose different functions by setting field parameter "op"
 * 
 * Function:
 * 1. Insert keyword and idf to dababase
 * Input fields:
 * - "op": str value, must be "insert"
 * - "num": int value, indicate that how many keywords will be queried 
 * 			if it is bigger than 0, it means that there will be 
 * 			"keyword0", "keyword1" ... "keywordX" (X = num - 1)
 * - "keywordX": str value, each one is a term or vocab.
 * - "idfX": double value, it is the corresponding value of "keywordX"
 * Return json:
 * - "ret": if it is (int) 1, the operation is successful. If it is -1, the operation is fail.
 * - "ids": should be an array of the input keywords' id.
 * 	 "ids"[x]: it is the id of x-th keyword in the input
 * - "error": If "ret" == -1, it will set to be as error message
 * Return example {"ret":1,"ids":[1,5,10]}
 * 
 * 2. Get the corresponding IDF with the specific term
 * Input fields:
 * - "op": str value, must be "getIDFByTerm"
 * - "objs": same as function 1
 * - "keywordX": same as function 1
 * Return json
 * - "ret": same as function 1
 * - "val": should be an array of idf value
 * 	 "val"[x]: it is the idf of x-th keyword in the input; if keyword not exists, the value will be 0
 * - "error": same as function 1
 * Return example {"ret":1,"val":[0.234,0.456,0]}
 *  
 * 3. Get the corresponding IDF with the specific keyword ID
 * Input fields:
 * - "op": str value, must be "getIDFByID"
 * - "num": same as function 1
 * - "idX": int value, it is x-th keyword id in the query.
 * Return json
 * - "ret": same as function 1
 * - "val": same as function 2
 * 	 "val"[x]: same as function 2
 * - "error": same as function 1
 * Return example {"ret":1,"val":[0.234,0.1,0]}
 *  
 * 4. Get the corresponding ID with the specific term
 * Input fields:
 * - "op": str value, must be "getIDByTerm"
 * - "num": same as function 1
 * - "keywordX" : same as function 1
 * Return json
 * - "ret": same as function 1
 * - "val": should be an array of keyword id
 * 	 "val"[x]: it is the id of x-th keyword in the input; if keyword not exists, the value will be 0
 * - "error": same as function 1
 * Return exapmle {"ret":1,"val":[1,5,0]}
 * 
 * 5. Get the corresponding term with the specific ID
 * Input fields: 
 * - "op": strn value, must be "getTermByID"
 * - "num": same as function 1
 * - "idX": same as function 3
 * Return json
 * - "ret": same as function 1
 * - "val": should be an array of keyword term
 * 	 "val"[x]: it is the term of x-th keyword id; if ID does not exists, the value will be a empty string
 * - "error": same as function 1
 * Return example {"ret":1,"val":["澳門","科技",""]}
 *  
 */

require_once __DIR__ . "/utility.php";
require_once CONNECTIONPATH . "/connection.php";
require_once CLASSPATH . "/keyword.php";


if (isset($_POST["op"]) && $_POST["op"] == "insert"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("num", $s_var);
	$num = $s_var["num"];
	$s_keywords = array();
	$s_idfs = array(); // idf array
	for ($i = 0;$i<$num ;$i++){
		$s_keywords[$i] = addslashes($_POST["keyword".$i]);
		$s_idfs[$i] = doubleval($_POST["idf".$i]);
	}

	$k = new Keyword();
	$ret = $k->Insert($s_keywords, $s_idfs); 
	echo json_encode($ret);
}else if (isset($_POST["op"]) && $_POST["op"] == "getIDFByTerm"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToPOSTField("keyword".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetIDFByTerm($s_var["keyword".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo json_encode($ret);
}else if (isset($_POST["op"]) && $_POST["op"] == "getIDFByID"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToPOSTField("id".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetIDFByID($s_var["id".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo json_encode($ret);
}else if (isset($_POST["op"]) && $_POST["op"] == "getIDByTerm"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToPOSTField("keyword".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetIDByTerm($s_var["keyword".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo json_encode($ret);
}else if (isset($_POST["op"]) && $_POST["op"] == "getTermByID"){
	// get var
	$s_var = array();
	Utility::AddslashesToPOSTField("num", $s_var, "int");
	$num = $s_var["num"];
	for ($i = 0;$i<$num ;$i++){
		Utility::AddslashesToPOSTField("id".$i, $s_var);
	}
	
	// get keyword id in database
	$ret = array();
	$exist = array();
	$ret['ret'] = -1;
	$k = new Keyword();
	for ($i = 0;$i< $num;$i++){
		$keywordRet = $k->GetTermByID($s_var["id".$i]);
		if ($keywordRet["ret"] != 1){
			$ret["error"] = $keywordRet["error"];
			echo json_encode($ret);
			return $ret;
		}
		$exist[$i] = $keywordRet["value"];
	}
	$ret['ret'] = 1;
	$ret['val'] = $exist;
	echo Utility::DecodeUnicode(json_encode($ret));
}

?>