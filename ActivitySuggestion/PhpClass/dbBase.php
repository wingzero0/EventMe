<?php 
require_once __DIR__."/../utility.php";
require_once CONNECTIONPATH ."/connection.php";

class DbBase{ // Batabase base class
	protected $mysqli;
	public function __construct($sqlObj = NULL){
		if ($sqlObj != NULL){
			$this->mysqli = $sqlObj;
		}else{
			global $g_mysqli;
			$this->mysqli = $g_mysqli;
		}
	}
	public function __destruct(){
		
	}
	public function UnlockTables(){
		$sql = "Unlock Tables";
		$result = $this->mysqli->query($sql);
		return;
	}
	public function InitRetArray(){
		$ret = array();
		$ret["ret"] = -1;
		return $ret;
	}
}
?>