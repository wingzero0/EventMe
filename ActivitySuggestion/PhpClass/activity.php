<?php 
/* class Activity
 * provide activity db request
 */

require_once __DIR__."/dbBase.php";

class Activity extends DbBase{
	protected $mysqli;
	private $uid;
	public function __construct($sqlObj = NULL){
		parent::__construct($sqlObj);
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
	public function GetActivityByDefaultTimeInterval($limit = 0, $startOffset = 0){
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
		
		if ($limit > 0){
			$sql .= sprintf(" limit %d, %d", $startOffset, $limit);
		}
		
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
	public function GetActivityByCategoryAndDefaultTimeInterval($categoryID, $limit = 0, $startOffset = 0){
		// return activityIDs
		$dateObj = new DateTime();
		$minEndTime = $dateObj->format("Y-m-d H:i:s");
		$dateObj->add(new DateInterval("P30D")); // 30 days later
		$maxStartTime = $dateObj->format("Y-m-d H:i:s");
		$sql = sprintf(
				"SELECT DISTINCT act.id
				FROM Activity as act left join ActivityTimeSlot as ats
				ON act.id = ats.ReferenceActivityID
				WHERE act.Category = %d AND ats.EndTime >=  '%s' AND ats.StartTime <= '%s'
				ORDER BY ats.StartTime",
				$categoryID, $minEndTime, $maxStartTime
		);
	
		if ($limit > 0){
			$sql .= sprintf(" limit %d, %d", $startOffset, $limit);
		}
		$ret = $this->InitRetArray();
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["sqlResult"] = array();
		$i = 0;
		while($row = $result->fetch_assoc()){
			$ret["sqlResult"][$i]["activityID"] = intval($row["id"]);
			$i++;
		}
	
		$ret["ret"] = 1;
		return $ret;
	}
	public function GetActivityDescriptionByID($id, $operator = ">=" ,$limit = "30"){
		// for preprocessing, get content
		$sql = sprintf(
				"SELECT id, Description
				FROM Activity
				WHERE id %s %d
				limit %d",
				$operator, $id, $limit
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
			$ret["sqlResult"][$i]["id"] = intval($row["id"]);
			$ret["sqlResult"][$i]["description"] = $row["Description"];
			$i++;
		}
		
		$ret["ret"] = 1;
		return $ret;
	}
	public function GetActivityRowByID($s_id){
		$sql = sprintf(
				"SELECT A.id, A.Name, A.Description, A.HostName, A.People,
				A.Location, A.Longitude, A.Latitude, A.ApplyStartDate, A.ApplyEndDate,
				A.Tel, A.WebSite, A.Fee, A.Poster, C.Name as Category
				FROM Activity as A left join ActivityCategory as C
				ON A.Category = C.id
				WHERE A.ID = %d",
				$s_id);
	
		$ret = $this->InitRetArray();
	
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["ret"] = 1;
		if ($row = $result->fetch_assoc()){
			$ret["sqlResult"][] = $row;
		}
		return $ret;
	}
	public function GetActivityTimeSlot($s_id){
		$sql = sprintf(
				"SELECT id, StartTime, EndTime
				FROM ActivityTimeSlot 
				WHERE ReferenceActivityID = %d",
				$s_id);
		
		$ret = $this->InitRetArray();
		
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["ret"] = 1;
		if ($row = $result->fetch_assoc()){
			$row["id"] = intval($row["id"]);
			$ret["sqlResult"][] = $row;
		}
		return $ret;
	}
	public function InsertActivityComment($s_id, $s_uid,$s_comment){ // id for activity id
		$sql = sprintf(
				"insert into ActivityComment (ActivityID, UserID, Comment) value
				(%d, %d, '%s')", 
				$s_id, $s_uid, $s_comment);
		$ret = $this->InitRetArray();
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["ret"] = 1;
		return $ret;
	}
	
	public function GetActivityComment($s_id){
		$sql = sprintf(
				"select UserID, Comment from ActivityComment where ActivityID = %d",
				$s_id);
		
		$ret = $this->InitRetArray();
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["sqlResult"] = array();
		$i = 0;
		while($row = $result->fetch_assoc()){
			$ret["sqlResult"][$i]["UserID"] = intval($row["UserID"]);
			$ret["sqlResult"][$i]["Comment"] = $row["Comment"];
			$i++;
		}
		$ret["ret"] = 1;
		return $ret;
	}
	
	public function GetActivityLikeCount($s_id){
		$sql = sprintf(
				"select count(*) from ActivityLike where ActivityID = %d",
				$s_id);
		
		return $this->ActivityLike($sql);
	}
	public function IsActivityLikeByUser($s_id, $s_uid){
		$sql = sprintf(
				"select count(*) from ActivityLike where ActivityID = %d and UserID = %d",
				$s_id, $s_uid);
		return $this->ActivityLike($sql);
	}
	public function SetUnsetActivityLike($s_id, $s_uid, $boolFlag){
		
		$ret = $this->InitRetArray();
		
		
		if (!$boolFlag){ // unset
			$sql = sprintf(
					"DELETE FROM ActivityLike WHERE ActivityID = %d AND UserID = %d",
					$s_id, $s_uid);
			$result = $this->mysqli->query($sql);
		}else{ // set
			$sql = "Lock Tables ActivityLike Write";
			$this->mysqli->query($sql);
			if ($this->mysqli->error){
				$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
				return $ret;
			}

			// check before set
			$sql = sprintf(
					"select * from ActivityLike where ActivityID = %d and UserID = %d",
					$s_id, $s_uid);
			$result = $this->mysqli->query($sql);
			
			// if not exists
			if ( !$result->fetch_row()){
				$sql = sprintf(
						"Insert into ActivityLike (ActivityID,UserID) value (%d, %d)",
						$s_id, $s_uid);
				$result = $this->mysqli->query($sql);
			}
			
			if ($this->mysqli->error){
				$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
				$this->UnlockTables();
				return $ret;
			}else{
				$this->UnlockTables();
			}
		}
		
		$ret["ret"] = 1;
		return $ret;
	}
	private function ActivityLike($sql){
		$ret = $this->InitRetArray();
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		if ($row = $result->fetch_row()){
			$ret["sqlResult"] = intval($row[0]);
		}
		$ret["ret"] = 1;
		return $ret;
	}
	/*
	private function _GetActivityComment($sql){
		$ret = $this->InitRetArray();
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["sqlResult"] = array();
		$i = 0;
		while($row = $result->fetch_assoc()){
			$ret["sqlResult"][$i] = $row;
		}
		$ret["ret"] = 1;
		return $ret;
	}
	public function GetActivityCommentByUserID($s_id, $s_uid){
		$sql = sprintf(
				"select Comment from ActivityComment where id = %d and UserID = %d",
				$s_id, $s_uid);
		return $this->GetActivityComment($sql);
	}
		
	public function GetActivityCommentByEmail($s_id, $s_mail){
		$sql = sprintf(
				"select Comment from ActivityComment where id = %d and UserID in 
					(select id from User where Email like '%s')",
				$s_id, $s_mail);
		return $this->GetActivityComment($sql);
		
	}*/
}
?>
