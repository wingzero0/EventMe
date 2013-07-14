<?php
/* handle the db requst that involve with table UserProfile
 */

require_once 'connection.php';
require_once 'utility.php';

class UserProfile{
	public function __construct(){
		
	}
	public static function GetUserProfileKeyword($uid, $returnByKeywordID = true){
		global $g_mysqli;
		
		if ($returnByKeywordID){
			$sql = sprintf(
				"SELECT U.ProfileID as profileID, PK.keywordID as keywordID, PK.weight as weight
				FROM UserProfile U, ProfileKeyword PK
				WHERE U.UserID = %d
				AND U.ProfileID = PK.profileID",
				$uid);
		}else{
			$sql = sprintf(
				"SELECT U.ProfileID as profileID, K.Keyword as keyword, PK.weight as weight
				FROM UserProfile U, ProfileKeyword PK, Keyword K
				WHERE U.UserID = %d
				AND U.ProfileID = PK.profileID
				AND PK.keywordID = K.id",
				$uid);
		}
		$result = $g_mysqli->query($sql);
		
		$ret = array();
		$ret['ret'] = -1;
		if ($g_mysqli->error){
			$ret['error'] = $g_mysqli->error;
			return $ret;
		}
		
		$keywords = array();
		if ($returnByKeywordID){
			$i = 0;
			while($row = $result->fetch_assoc()){
				$keywords[$i]["profileID"] = intval($row["profileID"]);
				$keywords[$i]["keywordID"] = intval($row["keywordID"]);
				$keywords[$i]["weight"] = doubleval($row["weight"]);
				$i++;
			}
		}else{
			$i = 0;
			while($row = $result->fetch_assoc()){
				$keywords[$i]["profileID"] = intval($row["profileID"]);
				$keywords[$i]["keyword"] = $row["keyword"];
				$keywords[$i]["weight"] = doubleval($row["weight"]);
				$i++;
			}
		}
		$ret['ret'] = 1;
		$ret['sqlResult'] = $keywords;
		return $ret;
	}
	public static function InsertUserProfile($s_var){
		// s_var should contain value in index "num", "keywordIDX", "userID";
		global $g_mysqli;
		
		$num = $s_var["num"];
		$sql = 'lock tables `Keyword` read, `UserProfile` write, `ProfileKeyword` write';
		$g_mysqli->query($sql);
		if ($g_mysqli->error){
			$ret["error"] = $g_mysqli->error;
			//echo json_encode($ret);
			return $ret;
		}

		// check reference keyword id's existance
		for ($i =0;$i<$num;$i++){
			$sql = sprintf("SELECT `id` FROM  `Keyword` WHERE `id` =%d", 
					$s_var["keywordID".$i]);
			$result = $g_mysqli->query($sql);

			if ($g_mysqli->error){
				$ret["error"] = $g_mysqli->error;		
				//echo json_encode($ret);
				Utility::UnlockTables($g_mysqli);
				return $ret;
			}else if (  !($result->fetch_row()) ) {
				// no refernce keyword
				$ret["error"] = "keyword id " . $s_var["keywordID".$i] . " doesn't exist";
				//echo json_encode($ret);
				Utility::UnlockTables($g_mysqli);
				return $ret;
			}
		}

		// create new profile, get profile id,
		$sql = sprintf("insert into `UserProfile` (`UserID`) value ('%s')", $s_var["userID"]);
		$result = $g_mysqli->query($sql);

		if ($g_mysqli->error){
			$ret["error"] = $g_mysqli->error;
			//echo json_encode($ret);
			Utility::UnlockTables($g_mysqli);
			return $ret;
		}
		$profileID = $g_mysqli->insert_id;

		// insert profile id, keyword id to ProfileKeyword table
		for ($i = 0;$i<$num;$i++){
			$sql = sprintf("insert into `ProfileKeyword` (`ProfileID`, `KeywordID`, `Weight`) value (%d, %d, %lf)",
					//$profileID, $s_var["keywordID".$i], $s_var["weight".$i]);
				$profileID, $s_var["keywordID".$i], $s_var["weight"]);
			$result = $g_mysqli->query($sql);

			if ($g_mysqli->error){
				$ret["error"] = $g_mysqli->error;
				//echo json_encode($ret);
				Utility::UnlockTables($g_mysqli);
				return $ret;
			}
		}

		$ret["ret"] = 1;
		Utility::UnlockTables($g_mysqli);
		//echo json_encode($ret);
		return $ret;

	}
}

?>
