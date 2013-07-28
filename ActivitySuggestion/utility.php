<?php

define("CLASSPATH", "/var/www/ActivitySuggestion/PhpClass/");
define("LIBPATH", "/var/www/ActivitySuggestion/PhpLib/");
define("CONNECTIONPATH", "/var/www/ActivitySuggestion/");

define("SOLRDOCPATH", "/home/wingzero/workspace/EventMe/Search/solr-4.3.0/EventMe/exampledocs/");

class Utility{
	public static function UnlockTables($sqlObj){
		$sql = "Unlock Tables";
		$result = $sqlObj->query($sql);
		return;
	}

	public static function AddslashesToGETField($index, &$retAssoc, $dataType = "string"){
		if (isset($_GET[$index])){
			switch ($dataType){
				case "int":
					$retAssoc[$index] = intval($_GET[$index]);
					break;
				case "double":
					$retAssoc[$index] = doubleval($_GET[$index]);
					break;
				case "string":
				default:
					$retAssoc[$index] = addslashes($_GET[$index]);
					break;
			}
		}else{
			switch ($dataType){
				case "int":
					$retAssoc[$index] = 0;
					break;
				case "double":
					$retAssoc[$index] = 0.0;
					break;
				case "string":
				default:
					$retAssoc[$index] = "";
					break;
			}
		}
		return $retAssoc;
	}
	public static function AddslashesToPOSTField($index, &$retAssoc, $dataType = "string"){
		if (isset($_POST[$index])){
			switch ($dataType){
				case "int":
					$retAssoc[$index] = intval($_POST[$index]);
					break;
				case "double":
					$retAssoc[$index] = doubleval($_POST[$index]);
					break;
				case "string":
				default:
					$retAssoc[$index] = addslashes($_POST[$index]);
					break;
			}
		}else{
			switch ($dataType){
				case "int":
					$retAssoc[$index] = 0;
					break;
				case "double":
					$retAssoc[$index] = 0.0;
					break;
				case "string":
				default:
					$retAssoc[$index] = "";
					break;
			}
		}
		return $retAssoc;
	}
	public static function DecodeUnicode($str)
	{
		return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
				create_function('$matches','return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'),
				$str);
	}
}
