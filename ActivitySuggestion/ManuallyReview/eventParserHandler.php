<?php

require_once __DIR__ . "/PhpClass/fileManager.php";
require_once __DIR__ . '/PhpClass/IACMContainer.php';
require_once __DIR__ . '/../utility.php';


$s_var = array();
Utility::AddslashesToGETField("op", $s_var);
Utility::AddslashesToGETField("source", $s_var);
Utility::AddslashesToGETField("text", $s_var);

$fm = new fileManager();
if ($s_var["op"] == "parseXML"){
	// test URL http://localhost/ActivitySuggestion/ManuallyReview/eventParserHandler.php?op=parseXML&source=ICAM&text=94b43a8b-c608-4c70-a7df-488c00d351b6.xml

	$xmlStr = $fm->GetTrimContent("ArticleTmp/" . $s_var["source"] . "/" . $s_var["text"]);
	$xml = $fm->ToXML($xmlStr);
	
	if ($xml){
		$iacm = new IACMContainer($xml);
		$jsonContent = $iacm->Parse();
		echo $jsonContent;
	}else{
		if ($xmlStr == null){
			echo json_encode(array('error' => 'file not found'));
		}else if ($xml == null){
			echo json_encode(array('error' => 'xml encode fail'));
		}else{
			echo json_encode(array('error' => 'unknown error'));
		}
	}
}
?>
