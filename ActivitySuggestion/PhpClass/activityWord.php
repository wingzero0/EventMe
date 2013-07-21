<?php
require_once __DIR__."/../utility.php";
require_once CONNECTIONPATH ."/connection.php";
require_once __DIR__ . "/keyword.php";

class ActivityWord{
	protected $mysqli;
	public function __construct($sqlObj = NULL){
		if ($sqlObj != NULL){
			$this->mysqli = $sqlObj;
		}else{
			global $g_mysqli;
			$this->mysqli = $g_mysqli;
		}
	}
	public function __destruct(){}
	public function InsertActivityWordByPlainTxt($s_activityID, $s_keywords, $s_tfs){
		$k = new Keyword();
		$s_keywordIDs = array();
		for ($i = 0;$i<count($s_keywords);$i++){
			$tmpRet = $k->GetIDByTerm($s_keywords[$i]);
			if ($tmpRet["ret"] != 1){
				return $tmpRet;
			}
			$s_keywordIDs[$i] = $tmpRet["value"];
		}
		return $this->InsertActivityWordByKeywordID($s_activityID, $s_keywordIDs, $s_tfs);
	}
	public function InsertActivityWordByKeywordID($s_activityID, $s_keywordIDs, $s_tfs){ 
		// input the database safe varible:
		// activityID, array of keyword id, array of tf
		
		// start transaction;
		$flag = $this->mysqli->autocommit(false);
		$ret = array();
		$ret["ret"] = -1;
		if (!$flag){
			$ret['error'] = "db error, can't disable autocommit";
			return $ret;
		}
		
		// test if "activity, keyword" pair is in database
		// if yes, update "activity, keyword's tf"
		// if no, insert the "activity, keyword" pair and its corresponding tf
		$exist = array();
		$num = count($s_keywordIDs);
		for ($i = 0;$i< $num;$i++){
			$sql = sprintf("select `id` from `ActivityWord` where `ActivityID` = %d AND `KeywordID` = %d",
					$s_activityID, $s_keywordIDs[$i]);
			//echo $sql."<br>";
			$result = $this->mysqli->query($sql);
		
			if ($this->mysqli->error){
				$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
				$flag = $this->mysqli->rollback();
				if (!$flag){
					$ret['error'] .= " db error, rollback fail";
				}
				return $ret;
			}
		
			if ($row = $result->fetch_row()){
				$exist[$i] = intval($row[0]);
			}else{
				$exist[$i] = 0;
			}
		}
		
		// insert if not exist
		for ($i = 0;$i<$num;$i++){
			if ($exist[$i]){
				$sql = sprintf("update `ActivityWord` set `TermFreq` = %d where `id` = %d",
						$s_tfs[$i], $exist[$i]);
				$result = $this->mysqli->query($sql);
		
				if ($this->mysqli->error){
					$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
					$flag = $this->mysqli->rollback();
					if (!$flag){
						$ret['error'] .= " db error, rollback fail";
					}
					return $ret;
				}
			}else{
				$sql = sprintf("insert into `ActivityWord` (`ActivityID`, `KeywordID`, `TermFreq`) value (%d, %d, %d)",
						$s_activityID, $s_keywordIDs[$i], $s_tfs[$i]);
				//echo $sql;
				$result = $this->mysqli->query($sql);
		
				if ($this->mysqli->error){
					$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
					$flag = $this->mysqli->rollback();
					if (!$flag){
						$ret['error'] .= " db error, rollback fail";
					}
					return $ret;
				}
			}
		}
		
		$flag = $this->mysqli->commit();
		if (!$flag){
			$ret['error'] = " db error, commit fail";
		}else{
			$ret['ret'] = 1;
		}
		
		$flag = $this->mysqli->autocommit(true);
		if (!$flag){
			$ret['error'] = "db error, can't enable autocommit";
			// although it contains error, but the data is insert successful, so I let ['ret'] = 1
		}
		return $ret;
	}
	public function GetActivityWordWithKeywordID($s_activityID){
		$sql = sprintf("select `KeywordID`, `TermFreq` from `ActivityWord` where `ActivityID` = %d order by `KeywordID`",
				$s_activityID);
		$result = $this->mysqli->query($sql);
		
		$ret = array();
		$ret["ret"] = -1;
		if ($this->mysqli->error){
			$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
			return $ret;
		}
		
		$ret["objs"] = array();
		$i = 0;
		while ($row = $result->fetch_row()){
			$ret["objs"][$i]['keywordID'] = intval($row[0]);
			$ret["objs"][$i]['tf'] = intval($row[1]);
			$i++;
		}
		
		$ret['ret'] = 1;
		return $ret;
	}
	public function GetActivityWordWithKeyword($s_activityID){
		$sql = sprintf(
					"select `Keyword`, `keywordID`, `TermFreq` from `ActivityWord` as `AW`, `Keyword` as `K` 
					where `AW`.`ActivityID` = %d And `AW`.`keywordID` =  `K`.`id` ORDER BY  `K`.`id`",
				$s_activityID);
		$result = $this->mysqli->query($sql);
	
		$ret = array();
		$ret["ret"] = -1;
		if ($this->mysqli->error){
			$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
			return $ret;
		}
	
		$ret["objs"] = array();
		$i = 0;
		while ($row = $result->fetch_row()){
			$ret["objs"][$i]['keyword'] = $row[0];
			$ret["objs"][$i]['keywordID'] = intval($row[1]);
			$ret["objs"][$i]['tf'] = intval($row[2]);
			$i++;
		}
	
		$ret['ret'] = 1;
		return $ret;
	}
}
?>