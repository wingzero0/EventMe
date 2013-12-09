<?php 

require_once __DIR__ . "/../utility.php";
require_once CLASSPATH . "/dbBase.php";

global $g_mysqli;
$ret = array();
$ret['ret'] = -1;

if (isset($_POST["submit"])){
	
	$s_var = array();
	Utility::AddslashesToPOSTField("applyStartDate", $s_var);
	Utility::AddslashesToPOSTField("applyEndDate", $s_var);
	//print_r($s_var);
	
	$num = intval( $_POST["numTimeSlot"] );
	for ($i = 0;$i< $num;$i++){
		$index = $i + 1;
		$startDate[$i] = addslashes( $_POST["datepickerStart".$index] );
		$endDate[$i] = addslashes( $_POST["datepickerEnd".$index] );
	}
	$name = addslashes( $_POST["name"] );
	$description = addslashes( $_POST["description"] );
	$people = addslashes( $_POST["people"] );
	$hostName = addslashes($_POST["hostName"]);
	$location = addslashes( $_POST["location"] );
	
	$geoLocationLongitude = doubleval($_POST["geoLocationLongitude"]);
	$geoLocationLatitude = doubleval($_POST["geoLocationLatitude"]);
	$category = addslashes($_POST["category"]);
	$fee = intval( addslashes($_POST["fee"]) );
	$tel = addslashes($_POST["tel"]);
	$website = addslashes($_POST["website"]);
	$poster = addslashes($_POST["poster"]);
	if (empty($poster)){
		$posterTerm = "NULL";
	}else{
		$posterTerm = "'".$poster."'";
	}
	
	$query = sprintf("Lock Tables `ActivityCategory` Write, `Activity` Write, `ActivityTimeSlot` Write");
	$g_mysqli->query($query);
	if ($g_mysqli->error){
		$ret["error"] = $g_mysqli->error;
		Unlock_Tables();
		echo json_encode($ret);
		return;
	}
	
	$query = sprintf("select `ID` from `ActivityCategory` where `Name` like '%s' ", $category);
	$result = $g_mysqli->query($query);
	if ($g_mysqli->error){
		$ret["error"] = $g_mysqli->error;
		Unlock_Tables();
		echo json_encode($ret);
		return;
	}
	
	if ($row = $result->fetch_row()){
		$categoryID = intval($row[0]);
	}else {
		// insert a new category 
		$query = sprintf ("insert into `ActivityCategory` (`Name`) value ('%s')", $category);
		$g_mysqli->query($query);
		if ($g_mysqli->error){
			$ret["error"] = $g_mysqli->error;
			Unlock_Tables();
			echo json_encode($ret);
			return;
		}
		$categoryID = $mysqli->insert_id;
	}
	
	
	$query = sprintf(
		"Insert Into `Activity` (
			`Name`, 
			`Description`, 
			`HostName`, 
			`People`, 
			`Location`, 
			`Longitude`,
			`Latitude`,
			`ApplyStartDate`, 
			`ApplyEndDate`,
			`Tel`,
			`WebSite`, 
			`Poster`,
			`Fee`, 
			`Category`) 
		value ('%s', '%s', '%s', '%s', '%s', 
			%lf, %lf, '%s', '%s', '%s', '%s', %s, %d, '%s'
		)",
			$name, 
			$description, 
			$hostName, 
			$people, 
			$location, 
			$geoLocationLongitude, 
			$geoLocationLatitude, 
			$s_var["applyStartDate"],
			$s_var["applyEndDate"],
			$tel,
			$website,
			$posterTerm,
			$fee,
			$categoryID);

	$g_mysqli->query($query);
	if ($g_mysqli->error){
		$ret["error"] = $g_mysqli->error;
		Unlock_Tables();
		echo json_encode($ret);
		return;
	}
	
	$id = $g_mysqli->insert_id;
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("insert into `ActivityTimeSlot` (`ReferenceActivityID`, `StartTime`, `EndTime`) 
				value(%d, '%s', '%s')", $id, $startDate[$i], $endDate[$i]);
		$g_mysqli->query($sql);
		if ($g_mysqli->error){
			$ret["error"] = $g_mysqli->error;
			Unlock_Tables();
			echo json_encode($ret);
			return;
		}
	}
	
	$repeatDates = RepeatTimeSlot();
	if ($repeatDates) {
		for ($i = 0;$i<count($repeatDates["startDate"]);$i++){
			$sql = sprintf("insert into `ActivityTimeSlot` (`ReferenceActivityID`, `StartTime`, `EndTime`)
				value(%d, '%s', '%s')", $id, $repeatDates["startDate"][$i], $repeatDates["endDate"][$i]);
			$g_mysqli->query($sql);
			if ($g_mysqli->error){
				$ret["error"] = $g_mysqli->error;
				Unlock_Tables();
				echo json_encode($ret);
				return;
			}
		}
	}
	UnlockTables();
	
	$ret["ret"] = 1;
	echo json_encode($ret);
}

function RepeatTimeSlot(){
	if ( isset($_POST["numRepeatTimeSlot"]) && intval($_POST["numRepeatTimeSlot"]) > 0 ){
		$num = intval($_POST["numRepeatTimeSlot"]);
		//echo $num."<br>";
		$retAssoc = array();
		$startDate = array();
		$endDate = array();
		for ($i = 1;$i <= $num; $i++){
			Utility::AddslashesToPOSTField("repeatStart".$i, $retAssoc);
			Utility::AddslashesToPOSTField("repeatEnd".$i, $retAssoc);
			$repeatWeek = intval($_POST["repeatWeek".$i]);
			//echo $repeatWeek."<br>";
			//echo $retAssoc["repeatStart".$i]."<br>";
			$date = DateTime::createFromFormat("Y-m-d H:i:s",$retAssoc["repeatStart".$i]);
			$startWeekDay = intval($date->format("N"));
			//echo $startWeekDay."<br>";
			$flag = true; // flag for testing repeatStart is included in checkbox or not
			for ($j = 0;$j < $repeatWeek; $j++){
				for ($k = 0;$k <7;$k++){
					$targetWeekDay = ($startWeekDay + $k -1) % 7 +1 ; // weekday shift back to 0-6 and shift to 1-7
					//echo "weekDay".$targetWeekDay."<br>";
					if (isset($_POST["checkBox".$i.$targetWeekDay])){
						if ($flag == true){
							if ($j != 0 || $k != 0){
								// repeatStart is not included in checkbox
								// add to $startData array manually.
								$date = DateTime::createFromFormat("Y-m-d H:i:s",$retAssoc["repeatStart".$i]); // re-create
								$startDate[] = $date->format("Y-m-d H:i:s");
								
								$date = DateTime::createFromFormat("Y-m-d H:i:s",$retAssoc["repeatEnd".$i]); // re-create
								$endDate[] = $date->format("Y-m-d H:i:s");
							}
							$flag = false; // no need to check again
						}
						$interval = sprintf("P%dD", $j * 7 + $k);
						$date = DateTime::createFromFormat("Y-m-d H:i:s",$retAssoc["repeatStart".$i]); // re-create
						$date->add(new DateInterval($interval));
						$startDate[] = $date->format("Y-m-d H:i:s");
						
						$date = DateTime::createFromFormat("Y-m-d H:i:s",$retAssoc["repeatEnd".$i]); // re-create
						$date->add(new DateInterval($interval));
						$endDate[] = $date->format("Y-m-d H:i:s");
					}
				}
			}
		}
		return array("startDate"=>$startDate, "endDate"=>$endDate);
	}else{
		return NULL;
	}
}

function UnlockTables(){
	global $g_mysqli;
	$sqlQuery = "Unlock Tables";
	$result = $g_mysqli->query($sqlQuery);
	return;
}

?>