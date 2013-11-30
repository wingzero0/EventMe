<?php 

require_once __DIR__ . '/../utility.php';
require_once __DIR__ . "/PhpClass/fileManager.php";

$s_var = array();
Utility::AddslashesToGETField("op", $s_var);
Utility::AddslashesToGETField("source", $s_var);
Utility::AddslashesToGETField("text", $s_var);

$fm = new fileManager();
if ($s_var["op"] == "getSourceList"){
	// test URL http://localhost/ActivitySuggestion/ManuallyReview/fileHandler.php?op=getSourceList&source=ICAM
	$entries = $fm->ListDocs("ArticleTmp/" . $s_var["source"] . "/");
	echo json_encode($entries);
	//var_dump($entries);
}else if ($s_var["op"] == "getPlainText"){
	// test URL http://localhost/ActivitySuggestion/ManuallyReview/fileHandler.php?op=getPlainText&source=ICAM&text=94b43a8b-c608-4c70-a7df-488c00d351b6.xml
	
	$ret = $fm->GetFilePlainText("ArticleTmp/" . $s_var["source"] . "/" . $s_var["text"]);
	//echo "ArticleTmp/" . $s_var["source"] . "/" . $s_var["text"];
	if (isset($ret["content"])){
		echo Utility::DecodeUnicode(json_encode($ret["content"]));
	}else{
		echo json_encode($ret);
	}
}

?>