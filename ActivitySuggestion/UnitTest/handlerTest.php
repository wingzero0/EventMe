<?php

require_once __DIR__ . '/../utility.php';
require_once LIBPATH . '/HttpClient.class.php';

class HandlerTest extends PHPUnit_Framework_TestCase{
	public function testActivityWordHandler(){
		$client = new HttpClient('localhost', 80);
		//$client->setDebug(true);
		
		$path = "/ActivitySuggestion/activityWordHandler.php";
		
		$postData = array();
		$postData["op"] = "insertActivityWordByPlainTxt";
		$postData["num"] = "2";
		$postData["activityID"] = "1";
		$postData["keyword0"] = "澳門";
		$postData["keyword1"] = "科技";
		$postData["tf0"] = "3";
		$postData["tf1"] = "207";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1}', $pageContents);
		
		$postData = array();
		$postData["op"] = "insertActivityWordByPlainTxt";
		$postData["num"] = "2";
		$postData["activityID"] = "2";
		$postData["keyword0"] = "澳門";
		$postData["keyword1"] = "消防車";
		$postData["tf0"] = "3";
		$postData["tf1"] = "207";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":-1,"error":"sql error:insert into `ActivityWord` (`ActivityID`, `KeywordID`, `TermFreq`) value (2, 0, 207) Cannot add or update a child row: a foreign key constraint fails (`ActivityDB`.`ActivityWord`, CONSTRAINT `ActivityWord_ibfk_2` FOREIGN KEY (`KeywordID`) REFERENCES `Keyword` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)"}', $pageContents);
		
		$postData = array();
		$postData["op"] = "insertActivityWordByKeywordID";
		$postData["num"] = "2";
		$postData["activityID"] = "3";
		$postData["keywordID0"] = "1";
		$postData["keywordID1"] = "2";
		$postData["tf0"] = "5";
		$postData["tf1"] = "55";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1}', $pageContents);
		
		$postData = array();
		$postData["op"] = "insertActivityWordByKeywordID";
		$postData["num"] = "2";
		$postData["activityID"] = "4";
		$postData["keywordID0"] = "1";
		$postData["keywordID1"] = "0";
		$postData["tf0"] = "3";
		$postData["tf1"] = "207";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":-1,"error":"sql error:insert into `ActivityWord` (`ActivityID`, `KeywordID`, `TermFreq`) value (4, 0, 207) Cannot add or update a child row: a foreign key constraint fails (`ActivityDB`.`ActivityWord`, CONSTRAINT `ActivityWord_ibfk_2` FOREIGN KEY (`KeywordID`) REFERENCES `Keyword` (`id`) ON DELETE CASCADE ON UPDATE CASCADE)"}', $pageContents);
		
		$postData = array();
		$postData["op"] = "getActivityWordWithKeywordID";
		$postData["activityID"] = "3";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"objs":[{"keywordID":1,"tf":5},{"keywordID":2,"tf":55}]}', $pageContents);
		
		$postData = array();
		$postData["op"] = "getActivityWordWithKeyword";
		$postData["activityID"] = "1";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"objs":[{"keyword":"澳門","keywordID":1,"tf":3},{"keyword":"科技","keywordID":5,"tf":207}]}', $pageContents);
	}
	public function testProfileHandler(){
		$client = new HttpClient('localhost', 80);
		//$client->setDebug(true);
		
		$path = "/ActivitySuggestion/profileHandler.php";
		
		$postData = array();
		$postData["op"] = "insert";
		$postData["num"] = "2";
		$postData["userID"] = "1";
		$postData["keywordID0"] = "1";
		$postData["keywordID1"] = "2";
		$postData["weight"] = "5.5";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1}', $pageContents);
		
		$postData = array();
		$postData["op"] = "get";
		$postData["userID"] = "2";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"objs":[{"profileID":3,"keywordID":1,"weight":5.5},{"profileID":3,"keywordID":2,"weight":6.6},{"profileID":4,"keywordID":1,"weight":7.7},{"profileID":4,"keywordID":2,"weight":8.8}]}', $pageContents);
	}
	public function testActivityCheckHandler(){
		$client = new HttpClient('localhost', 80);
		//$client->setDebug(true);
		
		$path = "/ActivitySuggestion/activityCheckHandler.php";
		
		$postData = array();
		$postData["name"] = "快快樂樂寫程式";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('1', $pageContents);
		
		$postData = array();
		$postData["name"] = "沒有這個活動";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('0', $pageContents);
	}
	public function testKeywordHandler(){
		$client = new HttpClient('localhost', 80);
		//$client->setDebug(true);
		
		$path = "/ActivitySuggestion/keywordHandler.php";
		
		$postData = array();
		$postData["op"]="insert";
		$postData["num"]="3";
		$postData["keyword0"]="澳門";
		$postData["keyword1"]="科技";
		$postData["keyword2"]="人才";
		$postData["idf0"] = "0.234";
		$postData["idf1"] ="0.456";
		$postData["idf2"] ="789";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"ids":[1,5,6]}', $pageContents);
		
		$postData = array();
		$postData["op"]="getIDFByTerm";
		$postData["num"]="3";
		$postData["keyword0"]="澳門";
		$postData["keyword1"]="科技";
		$postData["keyword2"]="天才";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"val":[0.234,0.456,0]}', $pageContents);
		
		$postData = array();
		$postData["op"]="getIDFByID";
		$postData["num"]="3";
		$postData["id0"]="1";
		$postData["id1"]="2";
		$postData["id2"]="30";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"val":[0.234,0.1,0]}', $pageContents);
		
		$postData = array();
		$postData["op"]="getIDByTerm";
		$postData["num"]="3";
		$postData["keyword0"]="澳門";
		$postData["keyword1"]="科技";
		$postData["keyword2"]="天才";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"val":[1,5,0]}', $pageContents);
		
		$postData = array();
		$postData["op"]="getTermByID";
		$postData["num"]="3";
		$postData["id0"]="1";
		$postData["id1"]="5";
		$postData["id2"]="30";
		
		$ret = $client->post($path, $postData);
		$pageContents = $client->getContent();
		var_dump($pageContents);
		$this->assertEquals('{"ret":1,"val":["澳門","科技",""]}', $pageContents);
	}
}

?>