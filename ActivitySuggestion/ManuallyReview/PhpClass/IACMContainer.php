<?php

require_once __DIR__ . '/eventContainer.php' ;

class IACMContainer extends EventContainer{
	private $urlPrefix;
	//private $pureText;
	public function __construct($xml){
		parent::__construct($xml);
		$this->urlPrefix = 'http://www.iacm.gov.mo/';
	}
	public function Parse(){
		$this->ParseName();
		$this->ParseDescription();
		$this->ParsePoster();
		$this->ParseStartDate();
		$this->ParseEndDate();
		$this->ParseTel();
		return $this->ToJSON();
		
	}
	private function ParseTel(){
		$pattern = "/查詢電話 :(.{9,9})/";
		$ret = preg_match($pattern, $this->xmlObj->plaintext, $matches);
		if ($ret) {
			$this->tel = $matches[1];
			return $this->tel;
		}
		return null;
	}
	private function ParsePoster(){
		$imgTag = $this->xmlObj->find('img', 0);
		
		if ($imgTag){
			$this->poster = $this->urlPrefix . $imgTag->src;
			return $this->poster;
		}
		return null;
	}
	private function ParseName(){
		$nameTag = $this->xmlObj->find('h1',0);
		if ($nameTag){
			$this->name = $nameTag->innertext;
			return $this->name;
		}
		return null;
	}
	private function ParseDescription(){
		$desNode = $this->xmlObj->find("div[id=contentDetail]", 0);
		if ($desNode){
			$this->des = $desNode->plaintext;
			return $this->des;
		}
		return null;
	}
	private function ParseStartDate(){
		$pattern = "/開始日期 :(.*?)結束日期/";
		$ret = preg_match($pattern, $this->xmlObj->plaintext, $matches);
		if ($ret) {
			$pattern = "#/#";
			$this->startDate = preg_replace($pattern, "-", $matches[1]) . " 00:00";
			return $this->startDate;
		}
		return null;
	}
	private function ParseEndDate(){
		$pattern = "/結束日期 :(.{10,10})/";
		$ret = preg_match($pattern, $this->xmlObj->plaintext, $matches);
		if ($ret) {
			$pattern = "#/#";
			$this->endDate = preg_replace($pattern, "-", $matches[1]) . " 23:59";
			return $this->endDate;
		}
		return null;
	}
}
?>