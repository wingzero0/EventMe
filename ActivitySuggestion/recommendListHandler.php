<?php
/*
 * Return the interested list of a user
 * GET parameter
 * 1. "uid": int value
 * 		the target user id
 * 
 * return value: json int array
 * 		it will return the an array of interested activity ID for the target user
 *  
 * sample usage:
 * http://140.112.29.228/ActivitySuggestion/recommendListHandler.php?uid=1
 * [386,410,399,412,388,422,401,458,411,391]
 */

require_once __DIR__ . '/utility.php';
require_once CONNECTIONPATH . '/connection.php';
require_once CLASSPATH . '/solr.php';

class RecommendList{
	public function __construct(){
		
	}
	public function QueryByUserProfile($uid){
		// bug here, it will search activity with all profile keywords concatenated into a single query
		// it should search activity once with each profile. 
		global $g_mysqli;
		$sql = sprintf(
				"SELECT U.ProfileID, K.Keyword, PK.weight
				FROM UserProfile U, ProfileKeyword PK, Keyword K
				WHERE U.UserID = %d
				AND U.ProfileID = PK.profileID
				AND PK.keywordID = K.id
				limit 0, 30
				",
				$uid);
		$result = $g_mysqli->query($sql);
		$keywords = array();
		while($row = $result->fetch_assoc()){
			$keywords[] = $row['Keyword'];
		}
		//$docIDs = $this->Query($keywords);
		$solrObj = new solr();
		$activtyIDs = $solrObj->KeywordSearch($keywords);
		return $activtyIDs;
	}
}


/*
$keywords = array();
$keywords[0] = $argv[1];
*/
/*
$s_var = array();
Utility::AddslashesToGETField("uid", $s_var, "int");
$re = new RecommendList();
$ids = $re->QueryByUserProfile($s_var["uid"]);

echo json_encode($ids);
*/
?>
