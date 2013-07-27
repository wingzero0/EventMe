<?php 
require_once __DIR__."/dbBase.php";

class User extends DbBase{
	public function CreateUser($email, $type){
		// return user id if successful
	}
	public function getUidByEmail($s_email){
		$sql = sprintf("select `id` where email like '%s'", $s_email);
		
		$ret = $this->InitRetArray();
		
		$result = $this->mysqli->query($sql);
		
		if ($this->mysqli->error){
			$ret['error'] = "sql error:" . $sql . " ". $this->mysqli->error;
			return $ret;
		}
		
		if ($row = $result->fetch_row()){
			$ret["sqlResult"] = intval($row[0]);
		}
		$ret["ret"] = 1;
		return $ret;
	}
}
?>