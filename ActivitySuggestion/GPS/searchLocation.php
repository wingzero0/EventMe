<?php 
require_once __DIR__ . '/../utility.php';

require_once CLASSPATH . '/fb.php';

$search = new FBApp();
$s_var = array();
Utility::AddslashesToGETField("latitude", $s_var, "double");
Utility::AddslashesToGETField("longitude", $s_var, "double");

$result = $search->SearchLocation($s_var["latitude"], $s_var["longitude"]);

echo "<pre>";
foreach ($result["data"] as $record){
	print_r($record);
	echo "\n";
}
echo "</pre>";

?>