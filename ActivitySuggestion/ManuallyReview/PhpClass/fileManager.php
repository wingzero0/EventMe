<?php

require_once __DIR__ . '/../../utility.php';
require_once LIBPATH . '/simple_html_dom.php';

class FileManager{
	public function ListDocs($folder){
		if ($handle = opendir($folder)) {
			//echo "Directory handle: $handle\n";
			//echo "Entries:\n";
		
			/* This is the correct way to loop over the directory. */
			$entries = array();
			while (false !== ($entry = readdir($handle))) {
				if ($entry == "." || $entry == ".."){
					continue;
				}else if (count($entries)>5){
					break;
				}
				$entries[] = $entry;
			}
			return $entries;
		}else{
			$errorStr = $folder . "can't be open";
			return array("error" => $errorStr);
		}
	}
	public function GetFileRowContent($path){
		// may contain xml format
		if ($fp = fopen($path, "r")){
			$ret = array();
			$ret["content"] = "";
			while($line = fgets($fp)){
				$ret["content"] .= $line;
			}
			return $ret;
		}else{
			$errorStr = $path . "can't be open";
			return array("error" => $errorStr);
		}
	}
	public function GetFilePlainText($path){
		if ($fp = fopen($path, "r")){
			// read from file
			$xmlStr = "";
			while($line = fgets($fp)){
				$xmlStr .= trim($line);
			}
			
			// parse
			$xml = str_get_html($xmlStr);
			if ($xml){
				//echo $xml;
				$ret = array();
				$ret["content"] = $xml->plaintext;
				return $ret;
			}
		}
		
		$errorStr = $path . "can't be open";
		return array("error" => $errorStr);
	}
}
?>