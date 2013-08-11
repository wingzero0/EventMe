<?php 
require_once __DIR__ . "/utility.php";

$hostname_cnn = "localhost";
$database_cnn = "ActivityDB";
$username_cnn = "ActivityDB";
$password_cnn = "GDRcaPZKfEKNWtxc";
$activity_cnn = mysql_pconnect($hostname_cnn, $username_cnn, $password_cnn) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_query("SET NAMES utf8");

mysql_select_db($database_cnn,$activity_cnn);

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
	mysql_query($query) or die( "error sql:".mysql_error() );
	
	$query = sprintf("select `ID` from `ActivityCategory` where `Name` like '%s' ", $category);
	$result = mysql_query($query) or die( "error sql:".mysql_error().Unlock_Tables() );
	
	if ($row = mysql_fetch_row($result)){
		$categoryID = intval($row[0]);
	}else {
		// insert a new category 
		$query = sprintf ("insert into `ActivityCategory` (`Name`) value ('%s')", $category);
		mysql_query($query) or die( "error sql:".mysql_error().Unlock_Tables() );
		$categoryID = mysql_insert_id();
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
	mysql_query($query) or die( "error sql:".mysql_error().UnlockTables() );
	
	$id = mysql_insert_id();
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("insert into `ActivityTimeSlot` (`ReferenceActivityID`, `StartTime`, `EndTime`) 
				value(%d, '%s', '%s')", $id, $startDate[$i], $endDate[$i]);
		mysql_query($sql) or die( "error sql:".mysql_error().Unlock_Tables() );
	}
	
	$repeatDates = RepeatTimeSlot();
	if ($repeatDates) {
		for ($i = 0;$i<count($repeatDates["startDate"]);$i++){
			$sql = sprintf("insert into `ActivityTimeSlot` (`ReferenceActivityID`, `StartTime`, `EndTime`)
				value(%d, '%s', '%s')", $id, $repeatDates["startDate"][$i], $repeatDates["endDate"][$i]);
			mysql_query($sql) or die( "error sql:".mysql_error().Unlock_Tables() );
		}
	}
	UnlockTables();
	
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
		/*
		echo "<pre>";
		print_r($startDate);
		print_r($endDate);
		echo "</pre>";
		*/
		return array("startDate"=>$startDate, "endDate"=>$endDate);
	}else{
		return NULL;
	}
}

function UnlockTables(){
	$sqlQuery = "Unlock Tables";
	$result = mysql_query($sqlQuery);
	return;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>




  
  <meta content="text/html; charset=utf-8" http-equiv="content-type"><title>Simple Activity Insert</title>
  

  
  
  <link href="css/smoothness/jquery-ui-1.9.2.custom.css" rel="stylesheet">

  
  <script src="js/jquery-1.8.3.js"></script>
  
  <script src="js/jquery-ui-1.9.2.custom.js"></script>
  
  <script src="js/jquery-ui-timepicker.js"></script>
  
  <script scr="js/main.js"></script>
  
  <script>
	$(document).ready(function(){
		/*
		$("#datepickerStart1").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#datepickerEnd1").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});
		*/
		$("#applyStartDate").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#applyEndDate").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});	
		$("#addTimeSlot").button().click(addTimeSlot);
		$("#addRepeatTimeSlot").button().click(addRepeatTimeSlot);
		$.get('error.php', function(data) {
			  alert(data);
			});
	});
	function addRepeatTimeSlot(event){
		event.preventDefault();
		var num = parseInt($("#numRepeatTimeSlot").val());
		num += 1;
		$( "#numRepeatTimeSlot" ).val(num);
		var newRepeatStart = $( "<input/>", {id:"repeatStart" + num, name:"repeatStart" + num, type:"text"});
		$("#repeatTimeSlotBlock").append("開始時間");
		$("#repeatTimeSlotBlock").append(newRepeatStart);

		var newRepeatEnd = $( "<input/>", {id:"repeatEnd" + num, name:"repeatEnd" + num, type:"text"});
		$("#repeatTimeSlotBlock").append("結束時間");
		$("#repeatTimeSlotBlock").append(newRepeatEnd);
		
		var newRepeatWeek = $( "<input/>", {id:"repeatWeek" + num, name:"repeatWeek" + num, type:"text", value:2});
		$("#repeatTimeSlotBlock").append("重覆週數");
		$("#repeatTimeSlotBlock").append(newRepeatWeek);
		
		
		var i;
		for (i=1;i<=7;i++){
			var checkBox = $( "<input/>", {name:"checkBox" + num + i, type:"checkBox", value:i});
			$("#repeatTimeSlotBlock").append(i);
			$("#repeatTimeSlotBlock").append(checkBox);
		}

		$("#repeatTimeSlotBlock").append("<br>");

		$("#repeatStart" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#repeatEnd" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});
	}
	function addTimeSlot(event){
		event.preventDefault();
		var num = parseInt($( "#numTimeSlot" ).val());
		num +=1;
		var newStart = $( "<input/>", {id:"datepickerStart" + num, name:"datepickerStart" + num  });
		var newEnd = $( "<input/>", {id:"datepickerEnd" + num, name:"datepickerEnd" + num });
		$("#timeSlotBlock").append("活動時間" + num);
		$("#timeSlotBlock").append(newStart);
		$("#timeSlotBlock").append("至");
		$("#timeSlotBlock").append(newEnd);
		$("#timeSlotBlock").append("<br>");
		$( "#numTimeSlot" ).val(num);

		$("#datepickerStart" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#datepickerEnd" + num).datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});
	}
  </script></head><body>
<form method="post" action="SimpleActivityInsert.php" name="ActivityInsert">
名稱<input maxlength="255" name="name"><br>
類型<input name="category"><br>
簡介<textarea cols="50" rows="10" name="description"></textarea><br>
報名時間<input id="applyStartDate" name="applyStartDate">至<input id="applyEndDate" name="applyEndDate"><br>
  <div id="timeSlotBlock">
	<!--
  	個別活動時間<input id="datepickerStart1" name="datepickerStart1">至<input id="datepickerEnd1" name="datepickerEnd1"><br>
	!-->
  </div>
  <div id="repeatTimeSlotBlock">
  </div>
  <button id="addTimeSlot" name="addTimeSlot">增加活動時段</button><br>
  <button id="addRepeatTimeSlot" name="addRepeatTimeSlot">增加重覆時段</button><br>
參與機構<input name="hostName"><br>
人物<textarea cols="50" rows="5" name="people"></textarea><br>
地點<textarea cols="50" rows="5" name="location"></textarea><br>
地理座標 x:<input name="geoLocationLongitude"> y:<input name="geoLocationLatitude"><br>
費用<input name="fee"><br>
網站<input name="website"><br>
海報連結<input name="poster"><br>
電話<input name="tel"><br>
  <button name="submit">送出</button>
  <input name="numTimeSlot" value="0" id="numTimeSlot" type="hidden">
  <input name="numRepeatTimeSlot" value="0" id="numRepeatTimeSlot" type="hidden">
</form>

</body></html>
