<?php 

require_once __DIR__ . '/../../utility.php';
require_once LIBPATH . '/simple_html_dom.php';

abstract class EventContainer{
	public $name;
	public $des; // description : string
	public $hostname; // string
	public $location; // string
	public $tel;
	public $GPS; // array length = 2, double
	public $poster; // string
	// public $originalContent; // string
	public $startDate; // string
	public $endDate; // string
	public $xmlStr; // string
	public $xmlObj; // simple html dom;
	public function __construct($xml){
		$this->xmlStr = $xml;
		$this->xmlObj = str_get_html($this->xmlStr);
	}
	public function ToJSON(){
		$phpArray = array(
			'name' => $this->name,
			'description' => $this->des,
			'hostname' => $this->hostname,  // string
			'location' => $this->location,  // string
			'tel' => $this->tel,  // string
			'GPS' => $this->GPS,  // array length = 2, double
			'poster' => $this->poster,  // string
			// 'originalContent' => $this->originalContent,  // string
			'startDate' => $this->startDate,  // string
			'endDate' => $this->endDate  // string
		);  // string
		return Utility::UnicodeJsonEncode($phpArray);
	}
	public abstract function Parse();
	/*
	public function constructWithXML($xml){
		$this->xmlStr = $xml;
		$this->xmlObj = str_get_html($this->xmlStr);
		return $this;
	}
	public function GetXmlDom(){
		if ($this->xmlObj){
			return $this->xmlObj;
		}else if ($this->xmlStr){
			$this->xmlObj = str_get_html($this->xmlStr);
			return $this->xmlObj;
		}
	}*/
}

?>
