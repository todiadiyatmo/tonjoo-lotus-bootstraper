<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LURL{

	private  $lotus=false;
	//URL read by the router
	private  $internalURL = null;
	//real URL from HTTP Request 
	private  $requestURL = null;

	private $internalURLSegment;

	private $URLSegment;

	function __construct($internalURL=false){
		if($internalURL){
			$this->internalURL = $internalURL;
		}

		$this->initialize();
	}

	private function initialize(){
		if(!isset($this->internalURLSegment)){

			//copy internalURL to requestURL
			if(!$this->internalURL){
				$internalURL = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
				$this->requestURL = $internalURL;
			}
			else{
				$internalURL = $this->internalURL;
				$this->requestURL =  "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
			}

			//copy to local tovariabel
			$requestURL = $this->requestURL;
			

			//Check if the actual link match any routes in routes.php 

			$routes = LConfig::getRoutes();

			//remove default_route and 404_override
			unset($routes['default_route']);
			unset($routes['404_override']);


			foreach ($routes as $route => $target_route) {
				//replace (:any) and (:number)
				$route = str_replace("(:num)","([1-9]*)", $route);

				$route = str_replace("(:any)","([a-zA-Z0-9_]*)", $route);


				//split $route into array, put escape character \ for each segment
				$regexRoute = explode('/',$route);

				//convert to PECL compliant regex
				$regexRoute = implode("\/",$regexRoute);

				$regexRoute ="/".$regexRoute."/";

				//if internalURL match regexRoute, than process the url according to new rule
				preg_match($regexRoute, $internalURL, $matches);

				//process new rule according routes.php
				if($matches && isset($matches[0])){

					$routeArray = explode('/',$route);

					//loop the $route array, remove segment which not begin with ( and end with ).
					foreach ($routeArray as $key => $segment) {
						
						//check leftmost character == '('
						if(strpos($segment,'(')!==0){
							unset($routeArray[$key]);
						}

					}

				//convert currentURL to array
					$currentURLArray = explode('/',$matches[0]);

				//replace $internalURL $matches[0] with $target_route
					$internalURL = str_replace($matches[0], $target_route, $internalURL);

					$i = 1;

				//replace $1 , $2 ,$3 etc with correct
					foreach ($routeArray as $key => $value) {
					# code...

						$internalURL = str_replace("$".$i, $currentURLArray[$key], $internalURL);
						$i = $i + 1;

					}

					continue;

				}// end if match route
			}

			//save $internalURL State
			$this->internalURL = $internalURL;

			//Get URL Segment for internalURL and requestURL
			$partsInteral = parse_url($internalURL);
			$parts = parse_url($requestURL);

			$partsInteral['query'] = isset($partsInteral['query']) ? $partsInteral['query'] : '';
			$parts['query'] = isset($parts['query']) ? $parts['query'] : '';

			//strip GET Query parameter
			$internalURL =   str_replace('?'.$partsInteral['query'],'',$internalURL);
			$requestURL =   str_replace('?'.$parts['query'],'',$requestURL);


			$internalURL =   str_replace(LConfig::getConfig('base_url'),'',$internalURL);
			$requestURL =   str_replace(LConfig::getConfig('base_url'),'',$requestURL);


			//insert stripslashes for WP
			global $wp;

			if($wp){

				if(substr($internalURL,0,1) != '/') {
					$internalURL = "/".$internalURL;
				}

				if(substr($requestURL,0,1) != '/') {
					$requestURL = "/".$requestURL;
				}
			}



			$this->internalURLSegment = explode("/", $internalURL);
			$this->URLSegment = explode("/", $requestURL);

		}
	}

	private function loadLotus(){

		if(!$this->lotus){
			$this->lotus = LotusFactory::getInstance();
		}



	}

	function getInternalURL(){
		return $this->internalURL;
	}

	function getURL(){

		return $this->requestURL;
	}

	function getController(){

		$this->loadLotus();

		return $this->lotus->getController();
	}

	function getMethod(){
		
		$this->loadLotus();

		return $this->lotus->getMethod();
	}


	/*
	 * Get Request URL
	 */

	function getRequestURL(){
		return implode("/",$this->URLSegment);
	}

	/*
	 * Get Internal Request URL
	 */

	function getInteralRequestURL(){
		return implode("/",$this->internalURLSegment);
	}


	/*
	 * Get URL Segment , not applicable for  operation_mode = params
	 */


	function getURLSegment($number){

		//copy to local var
		$URLSegment = $this->convertURLSegment(false);

		if(array_key_exists($number, $URLSegment)){
			return $URLSegment[$number];
		}
		return false;
	}


	/*
	 * Get URL Internal Segment , not applicable for  operation_mode = params
	 */


	function getInternalURLSegment($number){


		//copy to local var
		$internalURLSegment = $this->convertURLSegment();

		if(array_key_exists($number, $internalURLSegment)){
			return $internalURLSegment[$number];
		}
		return false;
	}

	/*
	 * Get URL Segment other than controller and method, not applicable for  operation_mode = params
	 */

	function getInternalRemainingURLSegment($method=''){

		if(LConfig::getConfig('operation_mode')=='get_param')
			return false;

		if($method=='')
			$method = $this->getMethod();

		//create local variable
		$internalURLSegment = $this->convertURLSegment();

		//method is on the url request
		if(strrpos($_SERVER['REQUEST_URI'],$method)) {
			//remove controller and method
			unset($internalURLSegment[0]);
			unset($internalURLSegment[1]);
			unset($internalURLSegment[2]);
		}
		else{
			//remove controller only
			unset($internalURLSegment[0]);
			unset($internalURLSegment[1]);
		}

		//remove empty arguments
		foreach ($internalURLSegment as $key => $value) {
			if($value=='')
				unset($internalURLSegment[$key]);
		}


		return array_values($internalURLSegment);
	}

	// /*
	//  * Get URL Segment other than Controller and Method , not applicable for  operation_mode = params
	//  */

	// function getInternalURLSegment($number){

	// 	if(LConfig::getConfig('operation_mode')=='get_param')
	// 		return false;

	// 	$part = $this->getInternalRemainingURLSegment();
	
	// 	return $part[$number];
	// }

	/*
	 * Convert URL into segment 
	 */

	private function convertURLSegment($internal=true){

		if($internal)
			return $this->internalURLSegment;

		return $this->URLSegment;
	}
}