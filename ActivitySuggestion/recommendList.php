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
 * recommendList.php?uid=1
 * [386,410,399,412,388,422,401,458,411,391]
 */

require_once 'HttpClient.class.php';
require_once 'simple_html_dom.php';
require_once 'connection.php';
require_once 'utility.php';

class RecommendList{
	public function __construct(){
		
	}
	public function QueryByUserProfile($uid){
		global $g_mysqli;
		$sql = sprintf(
				"SELECT U.ProfileID, K.Keyword, PK.weight
				FROM UserProfile U, ProfileKeyword PK, Keyword K
				WHERE U.UserID = %d
				AND U.ProfileID = PK.profileID
				AND PK.keywordID = K.id",
				$uid);
		$result = $g_mysqli->query($sql);
		$keywords = array();
		while($row = $result->fetch_assoc()){
			$keywords[] = $row['Keyword'];
		}
		$docIDs = $this->Query($keywords);
		return $docIDs;
	}
	public function Query($keywords){
		// return list of relevant event id
		$query = "";
		foreach ($keywords as $keyword){
			$query .= $keyword . " ";
		}
		$client = new HttpClient('140.112.29.228', 8983);
		//$client->setDebug(true);
		
		$path = sprintf("/solr/collection1/select/?indent=on&q=%s&fl=id+score", urlencode($query));
		
		if (!$client->get($path)) {
			die('An error occurred: '.$client->getError());
		}
		$pageContents = $client->getContent();
		
		//echo $pageContents;
		$docIDs = $this->PageParsing($pageContents);
		return $docIDs;
	}
	private function PageParsing($pageContents){
		// read the xml str and parsing it and output the relevant doc id
		$xml = str_get_html($pageContents);
		$docs = $xml->find("doc");
		$ids = array(); 
		foreach ($docs as $doc){
			$id = $doc->find("str[name=id]");
			if ($id) { $ids[] = intval($id[0]->innertext); }
		}
		return $ids;
	}
}


/*
$keywords = array();
$keywords[0] = $argv[1];
*/

$s_var = array();
Utility::AddslashesToGETField("uid", $s_var, "int");
$re = new RecommendList();
$ids = $re->QueryByUserProfile($s_var["uid"]);

echo json_encode($ids);

?>
