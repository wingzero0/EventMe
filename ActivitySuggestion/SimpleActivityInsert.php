<?php $hostname_cnn = "localhost";
$database_cnn = "ActivityDB";
$username_cnn = "ActivityDB";
$password_cnn = "GDRcaPZKfEKNWtxc";
$activity_cnn = mysql_pconnect($hostname_cnn, $username_cnn, $password_cnn) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_query("SET NAMES utf8");

mysql_select_db($database_cnn,$activity_cnn);

if (isset($_GET["submit"])){
	
	$s_var = array();
	AddslashesToGETField("applyStartDate", $s_var);
	AddslashesToGETField("applyEndDate", $s_var);
	//print_r($s_var);
	
	
	$num = intval( $_GET["numTimeSlot"] );
	for ($i = 0;$i< $num;$i++){
		$index = $i + 1;
		$startDate[$i] = addslashes( $_GET["datepickerStart".$index] );
		$endDate[$i] = addslashes( $_GET["datepickerEnd".$index] );
	}
	$name = addslashes( $_GET["name"] );
	$description = addslashes( $_GET["description"] );
	$people = addslashes( $_GET["people"] );
	$hostName = addslashes($_GET["hostName"]);
	$location = addslashes( $_GET["location"] );
	
	$geoLocationLongitude = doubleval($_GET["geoLocationLongitude"]);
	$geoLocationLatitude = doubleval($_GET["geoLocationLatitude"]);
	$category = addslashes($_GET["category"]);
	$fee = intval( addslashes($_GET["fee"]) );
	$tel = addslashes($_GET["tel"]);
	$website = addslashes($_GET["website"]);
	
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
			`Fee`, 
			`Category`) 
		value ('%s', '%s', '%s', '%s', '%s', 
			%lf, %lf, '%s', '%s', '%s', '%s', %d, '%s'
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
			$fee,
			$categoryID);
	mysql_query($query) or die( "error sql:".mysql_error().Unlock_Tables() );
	
	$id = mysql_insert_id();
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("insert into `ActivityTimeSlot` (`ReferenceActivityID`, `StartTime`, `EndTime`) 
				value(%d, '%s', '%s')", $id, $startDate[$i], $endDate[$i]);
		mysql_query($sql) or die( "error sql:".mysql_error().Unlock_Tables() );
	}
	UnlockTables();
}


function UnlockTables(){
	$sqlQuery = "Unlock Tables";
	$result = mysql_query($sqlQuery);
	return;
}

function AddslashesToGETField($index, &$retAssoc){
	$retAssoc[$index] = addslashes($_GET[$index]);
	return $retAssoc;
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
		$("#datepickerStart1").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#datepickerEnd1").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});
		$("#applyStartDate").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});

		$("#applyEndDate").datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-mm-dd"
		});	
		$("#addTimeSlot").button().click(addTimeSlot);
	});
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
<form method="get" action="SimpleActivityInsert.php" name="ActivityInsert">
名稱<input maxlength="255" name="name"><br>
類型<input name="category"><br>
簡介<textarea cols="50" rows="10" name="description"></textarea><br>
報名時間<input id="applyStartDate" name="applyStartDate">至<input id="applyEndDate" name="applyEndDate"><br>
  <div id="timeSlotBlock">
  	活動時間<input id="datepickerStart1" name="datepickerStart1">至<input id="datepickerEnd1" name="datepickerEnd1"><br> 
  </div>
  <button id="addTimeSlot" name="addTimeSlot">增加新時段</button><br>
參與機構<input name="hostName"><br>
人物<textarea cols="50" rows="5" name="people"></textarea><br>
地點<textarea cols="50" rows="5" name="location"></textarea><br>
地理座標 x:<input name="geoLocationLongitude"> y:<input name="geoLocationLatitude"><br>
費用<input name="fee"><br>
網站<input name="website"><br>
電話<input name="tel"><br>
  <button name="submit">送出</button><input name="numTimeSlot" value="1" id="numTimeSlot" type="hidden"><br>
</form>

</body></html>