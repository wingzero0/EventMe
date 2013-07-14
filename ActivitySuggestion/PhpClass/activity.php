<?php 
/* class Activity
 * provide activity db request
 */

require_once __DIR__."/../utility.php";
require_once CONNECTIONPATH ."/connection.php";

class Activity{
	protected $mysqli;
	public function __construct($sqlObj = NULL){
		if ($sqlObj != NULL){
			$this->mysqli = $sqlObj;
		}else{
			global $g_mysqli;
			$this->mysqli = $g_mysqli; 
		}
	}
	public function InsertActivity(){
		
	}
	public static function SearchActivityWithName($activityName){
		// search the activity with full name match
		global $g_mysqli;
		$ret = array();
		$ret["ret"] = -1;
		$sql = sprintf("select * from Activity where Name like '%s'", $activityName);
		
		$result = $g_mysqli->query($sql);
		if ($g_mysqli->error){
			$ret["error"] = $g_mysqli->error;
			return $ret;
		}
		$ret["sqlResult"] = array(); 
		while($row = $result->fetch_assoc()){
			$ret["sqlResult"][] = $row;
		}
		
		$ret["ret"] = 1;
		return $ret;
	}
	public static function SearchActivityWithMetaName($activityName){
		$metaName = "%" . $activityName . "%";
		$ret = Activity::SearchActivityWithName($metaName);
		return $ret;
	}
	public function GetActivityByDefaultTimeInterval(){
		// return activityIDs
		$dateObj = new DateTime();
		$minEndTime = $dateObj->format("Y-m-d H:i:s");
		$dateObj->add(new DateInterval("P30D")); // 30 days later
		$maxStartTime = $dateObj->format("Y-m-d H:i:s");
		$sql = sprintf(
				"SELECT DISTINCT ats.ReferenceActivityID
				FROM ActivityTimeSlot AS ats
				WHERE ats.EndTime >=  '%s' AND ats.StartTime <= '%s' 
				ORDER BY ats.StartTime",
				$minEndTime, $maxStartTime
			);
		$ret = array();
		$ret["ret"] = -1;
		//$ret["sql"] = $sql;
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["sqlResult"] = array();
		$i = 0;
		while($row = $result->fetch_assoc()){
			$ret["sqlResult"][$i]["activityID"] = intval($row["ReferenceActivityID"]);
			$i++;
		}
		
		$ret["ret"] = 1;
		return $ret;
	}
}
?>