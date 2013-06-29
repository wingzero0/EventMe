<?php

require_once 'HttpClient.class.php';
require_once 'simple_html_dom.php';
require_once 'connection.php';

class RecommendList{
	public function __construct(){
		
	}
	public function QueryByUserProfile($uid){
		global $g_mysqli;
		$sql = sprintf("SELECT K.Keyword FROM UserProfile U, Keyword K 
				WHERE U.UserID = %d AND U.KeywordID = K.id", $uid);
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
$re = new RecommendList();
$ids = $re->QueryByUserProfile(1);
//$ids = $re->Query($keywords);

echo json_encode($ids);

?>
