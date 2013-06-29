<?php 
require_once "connection.php";

global $g_mysqli;

if (isset($_GET["submit"])){
	// get var
	$s_var = array();
	AddslashesToGETField("num", $s_var);
	$num = intval($s_var["num"]);
	for ($i = 0;$i<$num ;$i++){
		AddslashesToGETField("keyword".$i, $s_var);
	}

	$sql = 'lock tables `Keyword` write';
	$g_mysqli->query($sql);

	if ($g_mysqli->error){
		die( "error sql:".$g_mysqli->error );
	}

	// test if keyword in database
	$exist = array();
	for ($i = 0;$i< $num;$i++){
		$sql = sprintf("select `id` from `Keyword` where `Keyword`.`keyword` like '%s'", 
				$s_var["keyword".$i]);
		//echo $sql."<br>";
		$result = $g_mysqli->query($sql);

		if ($g_mysqli->error){
			UnlockTables();
			die( "error sql:".$g_mysqli->error );
		}

		if ($row = $result->fetch_assoc()){
			$exist[$i] = 1;
		}else{
			$exist[$i] = 0;
		}
	}

	// insert if not exist
	for ($i = 0;$i<$num;$i++){
		if (!$exist[$i]){
			$sql = sprintf("insert into `Keyword` (`keyword`) value ('%s')",
					$s_var["keyword".$i]);
			//echo $sql;
			$result = $g_mysqli->query($sql);
			
			if ($g_mysqli->error){
				UnlockTables();
				die( "error sql:".$g_mysqli->error );
			}
		}
	}

	UnlockTables();
	$ret['ret'] = 1;
	echo json_encode($ret);	
}


function UnlockTables(){
	$sqlQuery = "Unlock Tables";
	global $g_mysqli;
	$result = mysql_query($sqlQuery);
	return;
}

function AddslashesToGETField($index, &$retAssoc){
	if (isset($_GET[$index])){
		$retAssoc[$index] = addslashes($_GET[$index]);
	}else{
		$retAssoc[$index] = "";
	}
	return $retAssoc;
}

?>


