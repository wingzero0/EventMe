<?php 
require_once __DIR__ . '/../utility.php';

require_once CLASSPATH . '/fb.php';

$search = new FBApp();

//$result = $search->SearchLocation($s_var["latitude"], $s_var["longitude"]);

//echo "<pre>";
$longMin = 113.528913; // longitude from min to max
$longMax = 113.598534;
$longInt = ($longMax - $longMin)/50;  
$latMax = 22.217066; // latitude from max to min 
$latMin = 22.110054; 
$latInt = ($latMax - $latMin)/100;

$flag = 0;
if ($fp = fopen("lastRecord.txt", "r+")){
	//echo "read lastRecord.txt\n";
	$line = fgets($fp);
	$flag = preg_match("/lastRecord:(.*),(.*)/", trim($line), $matches);
	if ($flag){
		$lastLat=doubleval($matches[1]);
		$lastLong = doubleval($matches[2]);
		//echo "start from:".$lastLat." ".$lastLong."\n";
	}
}

$i = 0;
$maxQuery = 500;
if ($flag){
	$x = $lastLat;
}else{
	$x = $latMax;
}

for (; $x >= $latMin;$x -= $latInt){
	if ($flag){
		$y = $lastLong + $longInt;
	}else{
		$y = $longMin;
	}
	for (; $y <= $longMax; $y += $longInt){
		echo "facebook query:" . $x ." ". $y . "\n";
		$result = $search->SearchLocation($x, $y);
		echo Utility::DecodeUnicode(json_encode($result))."\n";
		$i++;
		if ($i >= $maxQuery){
			break;
		}
		sleep(1);
	}
	
	$flag = 0; // unset the flag. let y to start form the min broader
	if ($i >= $maxQuery){
		//fseek($fp, 0);
		ftruncate($fp, 0);
		fseek($fp, 0);
		fprintf($fp, "lastRecord:".$x.",".$y ); // save last record, it will resume from the next of it.
		break;
	}
}

//echo "</pre>";

?>
