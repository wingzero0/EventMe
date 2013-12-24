<?php
require_once __DIR__ . '/eventContainer.php' ;

class QoosContainer extends EventContainer{
	private $urlPrefix;
	private $metaDataNode;
	public function __construct($xml){
		parent::__construct($xml);
		$this->urlPrefix = 'http://events.qoos.com/';
		$this->metaDataNode = $this->xmlObj->find("div[class=qoos_event_detail_date]",0);
	}
	public function Parse(){
		$this->ParseName();
		$this->ParseDescription();
		$this->ParsePoster();
		$this->ParseStartDate();
		$this->ParseEndDate();
		$this->ParseTel();
		$this->ParseLocation();
		$this->ParseGPS();
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
			$pattern = "/http/";
			$ret = preg_match($pattern, $imgTag->src);
			if ($ret){
				// other site image
				$this->poster = $imgTag->src;
			}else{
				$this->poster = $this->urlPrefix . $imgTag->src;
			}
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
		$desNode = $this->xmlObj->find("div[class=qoos_event_detail_bookbox]", 0);
		if ($desNode){
			$pattern = "/內容簡介/";
			$this->des = preg_replace($pattern, "", $desNode->plaintext);
			return $this->des;
		}
		return null;
	}
	private function ParseStartDate(){
		return null;
		$pattern = "/(日期:|時間:)(.+?)場地|(日期:|時間:)(.+?)/";
		$ret = preg_match($pattern, $this->metaDataNode->plaintext, $matches);
		if ($ret) {
			//$pattern = "#/#";
			//$this->startDate = preg_replace($pattern, "-", $matches[1]) . " 00:00";
			$this->startDate = $matches[2];
			
			return $this->startDate;
		}
		return null;
	}
	private function ParseEndDate(){
		$this->endDate = null;
		return null;
	}
	private function ParseLocation(){
		$this->location = null;
		$pattern = "/場地名稱(.*?)(地址:(.+)費用|地址:(.+))/";
		$ret = preg_match($pattern, $this->metaDataNode->plaintext, $matches);
		if ($ret) {
			$this->location = $matches[2] . $matches[1];

			$pattern = "/地址:/";
			$this->location = preg_replace($pattern, "", $this->location);
		}
		return $this->location;
	}
	private function ParseGPS(){
		// qoos_event_detail_temp
		$tmpTag = $this->xmlObj->find("div[class=qoos_event_detail_temp]",0);
		if ($tmpTag){
			$javascript = $tmpTag->find("script[type=text/javascript]", 2);
			if ($javascript){
				$this->GPS["geoLocationLatitude"] = "0.0";
				$this->GPS["geoLocationLongitude"] = "0.0";
				$pattern = "/lat:(.*?),/";
				$ret = preg_match($pattern, $javascript->outertext, $matches);
				if ($ret){
					$this->GPS["geoLocationLatitude"] = $matches[1];
				}
				$pattern = "/lng:(.*?),/";
				$ret = preg_match($pattern, $javascript->outertext, $matches);
				if ($ret){
					$this->GPS["geoLocationLongitude"] = $matches[1];
				}
			}
		}
		return $this->GPS;
	}
}
?>