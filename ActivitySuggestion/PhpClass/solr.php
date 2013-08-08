<?php
/* a class that create the access interface of solr server
 * 
 */

require_once __DIR__ . '/../utility.php';
require_once LIBPATH . 'HttpClient.class.php';
require_once LIBPATH .'simple_html_dom.php';

require_once __DIR__ . '/activity.php';

class solr{
	public static function ProfileKeywordSearch($s_uid){
		
	}
	public function UpdateSolr(){
		// dump recent activity to XML format
		$a = new Activity();
		$activityIDRet = $a->GetActivityByDefaultTimeInterval();
		if ($activityIDRet["ret"] == 1){
			$pattern = "/&/";
			$replacement = "&amp;";
			$xml = "<add>\n";
			foreach ($activityIDRet["sqlResult"] as $i => $row){
				$activityRow = $a->GetActivityRowByID($row["activityID"]);
				$activityRow["sqlResult"][0]["Description"] = preg_replace($pattern, $replacement, $activityRow["sqlResult"][0]["Description"]);
				//var_dump($activityRow);
				$xml .= "\t<doc>";
				foreach ($activityRow["sqlResult"][0] as $index => $value){
					$xml .= sprintf("<field name=\"%s\">%s</field>", $index, $value);
				}
				$xml .= "\n\t</doc>\n";
			}
			$xml .= "</add>";
		}
		echo $xml;
		
		// clean solr index
		system('java -Ddata=args -Dcommit=true -jar '. SOLRDOCPATH . 'post.jar "<delete><query>*:*</query></delete>"');
		
		// add solr index
		$xmlFile = SOLRDOCPATH."recentActivity.xml";
		$fp = fopen($xmlFile, "w");
		if ($fp == NULL){
			echo $xmlFile . "can't be save\n";
			return -1;
		}
		fwrite($fp, $xml);
		fclose($fp);
		system('java -jar '. SOLRDOCPATH .'post.jar '.$xmlFile);
	}
	public function KeywordSearch($s_keywords){
		// return list of relevant event id
		$s_query = "";
		foreach ($s_keywords as $s_keyword){
			$s_query .= $s_keyword . " ";
		}
		$client = new HttpClient('140.112.29.228', 8983);
		//$client->setDebug(true);
	
		$path = sprintf("/solr/collection1/select/?indent=on&q=%s&fl=id+score", urlencode($s_query));

		$ret = array();
		$ret["ret"] = -1;
		if (!$client->get($path)) {
			$ret["error"] = $client->getError();
			return $ret;
		}
		$pageContents = $client->getContent();
		
		//echo $pageContents;
		//$ret["activityIDs"] = $this->PageParsing($pageContents);
		$ret["ids"] = $this->PageParsing($pageContents);
		$ret["ret"] = 1;
		return $ret;
	}
	private function PageParsing($pageContents){
		// read the xml str and parsing it and output the relevant doc id
		$xml = str_get_html($pageContents);
		if (!$xml){
			return array();
		}
		$docs = $xml->find("doc");
		$ids = array();
		foreach ($docs as $doc){
			$id = $doc->find("str[name=id]");
			if ($id) {
				$ids[] = intval($id[0]->innertext);
			}
		}
		return $ids;
	}
}
?>
