<?php

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
}
