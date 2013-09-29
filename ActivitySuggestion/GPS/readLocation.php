<?php
require_once "../connection.php";

$path = './location/';
if ($handle = opendir($path)) {
	while (false !== ($entry = readdir($handle))) {
		if ($entry != "." && $entry != "..") {
			echo "$entry\n";
			$rows = ParseJsonFromFile($path . $entry);
			InsertDB($rows);
		}
	}
	closedir($handle);
}

function InsertDB($rows){
	global $g_mysqli;
	$errorPattern = "/Duplicate entry (.*) for key 'PRIMARY'/"; 
	foreach ($rows as $index => $row){
		if ( !isset($row->data) ){ // some error message is in the txt files
			print_r($row);
			echo "\n";
			continue;
		}
		foreach ($row->data as $index2 => $locationObj){
			$categoryListName = null;
			$categoryListId = null;
			foreach ($locationObj->category_list as $cIndex => $categoryObj){
				$categoryListName .= $categoryObj->name.";";
				$categoryListId .= $categoryObj->id.";";
			}
			/*
			print_r($locationObj->location->street);
			print_r($locationObj->location->zip);
			*/
			if (empty($locationObj->location->street)){
				$streetTerm = "NULL";
			}else{
				$streetTerm = "'".addslashes($locationObj->location->street)."'";
			}
			$sql = sprintf("insert into POIFacebook (id, name, latitude, longitude, street, category, category_list, category_list_id) 
					value ('%s', '%s', %s, %s, %s, '%s', '%s', '%s')", 
					$locationObj->id, addslashes($locationObj->name),
					$locationObj->location->latitude,$locationObj->location->longitude,
					$streetTerm, addslashes($locationObj->category), addslashes($categoryListName), $categoryListId
					);
			
			$g_mysqli->query($sql);
			$error = $g_mysqli->error;
			if ( $error ){
				$flag = preg_match($errorPattern, $error, $matches);
				if ( !$flag ){
					echo $sql."\n";
					echo $g_mysqli->error."\n";
				}
			} 
		}
	}
}
function ParseJsonFromFile($filename){
	$fp = fopen($filename, "r");
	$rows = array();
	while($line = fgets($fp)){ // skip the first line
		//echo $line;
		$line = fgets($fp);
		$rows[] = json_decode($line);
		//$rows[] = json_decode($line, true);
		//print_r($rows[count($rows) - 1]);
	}
	fclose($fp);
	return $rows;
}


?>