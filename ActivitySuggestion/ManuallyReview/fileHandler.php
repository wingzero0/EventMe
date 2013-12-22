<?php 

require_once __DIR__ . '/../utility.php';
require_once __DIR__ . "/PhpClass/fileManager.php";

$s_var = array();
Utility::AddslashesToGETField("op", $s_var);
Utility::AddslashesToGETField("source", $s_var);
Utility::AddslashesToGETField("text", $s_var);

$fm = new fileManager();
if ($s_var["op"] == "getSourceList"){
	// test URL http://localhost/ActivitySuggestion/ManuallyReview/fileHandler.php?op=getSourceList&source=IACM
	$entries = $fm->ListDocs("ArticleTmp/" . $s_var["source"] . "/");
	echo json_encode($entries);
	//var_dump($entries);
}else if ($s_var["op"] == "getPlainText"){
	// test URL http://localhost/ActivitySuggestion/ManuallyReview/fileHandler.php?op=getPlainText&source=IACM&text=94b43a8b-c608-4c70-a7df-488c00d351b6.xml
	
	$ret["content"] = $fm->GetTrimContent("ArticleTmp/" . $s_var["source"] . "/" . $s_var["text"]);
	$ret["ret"] = -1;
	//echo "ArticleTmp/" . $s_var["source"] . "/" . $s_var["text"];
	if (isset($ret["content"])){
		$ret["ret"] = 1;
		echo Utility::DecodeUnicode(json_encode($ret));
		// echo addslashes($ret["content"]);
	}else{
		echo json_encode($ret);
	}
}else if ($s_var["op"] == "clearDoc" || $s_var["op"] == "skipDoc"){
	// test URL http://localhost/ActivitySuggestion/ManuallyReview/fileHandler.php?op=clearDoc&source=IACM&text=test.xml
	$src = "ArticleTmp/" . $s_var["source"] . "/" . $s_var["text"];
	if ($s_var["op"] == "clearDoc"){
		$des = "ArticleComplete/" . $s_var["source"] . "/" . $s_var["text"];
	}else{
		$des = "ArticleSkip/" . $s_var["source"] . "/" . $s_var["text"];
	}
	
	$flag = $fm->MoveDoc($src, $des);
	$ret = array("ret" => -1);
	if ($flag == true){
		$ret["ret"] = 1;
	}else{
		$ret["error"] = "operation fail";
	}
	echo json_encode($ret);
}

?>
