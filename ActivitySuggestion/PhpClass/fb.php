<?php

require_once __DIR__ . '/../utility.php';
require_once __DIR__ . '/../connection.php';
require_once LIBPATH . '/facebook.php';

class FBApp{
	private $fbObj; 
	public function __construct(){
		global $g_fbID;
		global $g_fbSecret;
		$config = array();
		$config['appId'] = $g_fbID;
		$config['secret'] = $g_fbSecret;
		$config['fileUpload'] = false; // optional
		
		$this->fbObj = new Facebook($config);
	}
	public function Login(){
		$this->fbObj->getLoginUrl();
	}
	public function SearchLocation($latitude, $longitude){
		$location = sprintf("%lf,%lf", $latitude, $longitude );
		try {
			$result = $this->fbObj->api('/search','GET',
					array(
							'type'=>'place',
							'center'=>$location,
							'distance'=> '500',
							'limit' => '50'
					)
			);
			
			//print_r($result);
			return $result;
		} catch(FacebookApiException $e) {
			$result = $e->getResult();
			error_log(json_encode($result));
			print_r($result);
			
			return null;
		}
	}
}

?>