<?php

require_once __DIR__ . '/../utility.php';
require_once CONNECTIONPATH . "/connection.php";

class Keyword{
	protected $mysqli;
	public function __construct($sqlObj = NULL){
		if ($sqlObj != NULL){
			$this->mysqli = $sqlObj;
		}else{
			global $g_mysqli;
			$this->mysqli = $g_mysqli;
		}
	}
	public function Insert($s_keywords, $s_idfs){
		$ret = array();
		$ret["ret"] = -1;
		
		$sql = 'lock tables `Keyword` write';
		$this->mysqli->query($sql);
		
		if ($this->mysqli->error){
			$ret["error"] = "error sql:".$this->mysqli->error;
			return $ret;
		}
		
		// test if keyword in database
		// if yes, update keyword's idf
		// if no, insert the keyword term and it corresponding idf
		$exist = array();
		$num = count($s_keywords);
		for ($i = 0;$i< $num;$i++){
			$sql = sprintf("select `id` from `Keyword` where `Keyword`.`keyword` like '%s'",
					$s_keywords[$i]);
			
			$result = $this->mysqli->query($sql);
		
			if ($this->mysqli->error){
				Utility::UnlockTables($this->mysqli);
				$ret["ret"] = "error sql:".$this->mysqli->error;
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
				$sql = sprintf("update `Keyword` set `InverseDocFreq` = '%lf' where `id` = %d",
						$s_idfs[$i], $exist[$i]);
				$result = $this->mysqli->query($sql);
		
				if ($this->mysqli->error){
					Utility::UnlockTables($this->mysqli);
					$ret["ret"] = "error sql:".$this->mysqli->error;
					return $ret;
				}
			}else{
				$sql = sprintf("insert into `Keyword` (`Keyword`, `InverseDocFreq`) value ('%s', '%lf')",
						$s_var["keyword".$i], $s_var["idf".$i]);
				//echo $sql;
				$result = $this->mysqli->query($sql);
					
				if ($this->mysqli->error){
					Utility::UnlockTables($this->mysqli);
					$ret["ret"] = "error sql:".$this->mysqli->error;
					return $ret;
				}
				$exist[$i] = $this->mysqli->insert_id;
			}
		}
		
		Utility::UnlockTables($this->mysqli);
		$ret['ret'] = 1;
		$ret['ids'] = $exist;
		return $ret;
	}
	public function GetIDByTerm($s_term){
		$ret = $this->GetByTerm($s_term);
		if ($ret["ret"] == 1 && !empty($ret["sqlResult"]) ){
			$ret["value"] = $ret["sqlResult"]["id"];
		}else if ($ret["ret"] == 1 && empty($ret["sqlResult"]) ){
			$ret["value"] = 0;
		}
		
		if (isset($ret["sqlResult"])){
			unset($ret["sqlResult"]);
		}
		return $ret;
	}
	public function GetTermByID($s_id){
		$ret = $this->GetByID($s_id);
		if ($ret["ret"] == 1 && !empty($ret["sqlResult"]) ){
			$ret["value"] = $ret["sqlResult"]["keyword"];
		}else if ($ret["ret"] == 1 && empty($ret["sqlResult"]) ){
			$ret["value"] = "";
		}
		
		if (isset($ret["sqlResult"])){
			unset($ret["sqlResult"]);
		}
		return $ret;
	}
	public function GetIDFByTerm($s_term){
		$ret = $this->GetByTerm($s_term);
		if ($ret["ret"] == 1 && !empty($ret["sqlResult"]) ){
			$ret["value"] = $ret["sqlResult"]["idf"];
		}else if ($ret["ret"] == 1 && empty($ret["sqlResult"]) ){
			$ret["value"] = 0.0;
		}
		
		if (isset($ret["sqlResult"])){
			unset($ret["sqlResult"]);
		}
		return $ret;
	}
	public function GetIDFByID($s_id){
		$ret = $this->GetByID($s_id);
		if ($ret["ret"] == 1 && !empty($ret["sqlResult"]) ){
			$ret["value"] = $ret["sqlResult"]["idf"];
		}else if ($ret["ret"] == 1 && empty($ret["sqlResult"]) ){
			$ret["value"] = 0.0;
		}
		
		if (isset($ret["sqlResult"])){
			unset($ret["sqlResult"]);
		}
		return $ret;
	}
	private function GetByTerm($s_term){
		$whereClause = sprintf("where `k`.`keyword` like '%s'", $s_term);
		$ret = $this->GetDB($whereClause);
		return $ret;
	}
	private function GetByID($s_id){
		$whereClause = sprintf("where `k`.`id` = %d", $s_id);
		$ret = $this->GetDB($whereClause);
		return $ret;
	}
	private function GetDB($whereClause){
		$sql = sprintf(
				"SELECT `k`.`id`, `k`.`keyword`, `k`.`InverseDocFreq` AS `idf`
				FROM `Keyword` as `k` %s", $whereClause);
		
		$ret = array();
		$ret["ret"] = -1;
		$result = $this->mysqli->query($sql);
		if ($this->mysqli->error){
			$ret["error"] = $this->mysqli->error;
			return $ret;
		}
		$ret["sqlResult"] = array();
		//$i = 0;
		if ($row = $result->fetch_assoc()){
			$ret["sqlResult"]["id"] = intval($row["id"]);
			$ret["sqlResult"]["keyword"] = $row["keyword"];
			$ret["sqlResult"]["idf"] = doubleval($row["idf"]);
		}
		
		$ret["ret"] = 1;
		return $ret;
	}
}

?>