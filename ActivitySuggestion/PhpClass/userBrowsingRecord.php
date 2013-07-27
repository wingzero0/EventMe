<?php 


require_once __DIR__ . '/../utility.php';
require_once CONNECTIONPATH . "/connection.php";

class UserBrowsingRecord{
	protected $mysqli;
	public function __construct($sqlObj = NULL){
		if ($sqlObj != NULL){
			$this->mysqli = $sqlObj;
		}else{
			global $g_mysqli;
			$this->mysqli = $g_mysqli;
		}
	}
	public function GetActivityIDByUID($s_uid){
		$whereClause = sprintf("Where `userID` = %d", $s_uid);
		$ret = $this->GetDB($whereClause);
		
		return $ret;
	}
	private function GetDB($whereClause){
		$sql = sprintf(
				"SELECT userID, ActivityID FROM UserBrowsingRecord %s", 
				$whereClause);
	
		$ret = array();
		$ret["ret"] = -1;
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["sqlResult"] = array();
		
		$i = 0;
		while ($row = $result->fetch_assoc()){
			$ret["sqlResult"][$i]["userID"] = intval($row["userID"]);
			$ret["sqlResult"][$i]["ActivityID"] = intval($row["ActivityID"]);
			$i++;
		}
	
		$ret["ret"] = 1;
		return $ret;
	}
}
?>