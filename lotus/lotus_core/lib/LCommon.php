<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LCommon{
	//URL pieces of the current request
	private  $pieces;
	private  $lotus=false;


	private function initialize(){
		if(!$this->lotus){
			$this->lotus = LotusFactory::getInstance();
		}
	}


	function load_file($file){

		
		$file = L_BASEPATH.$file;

		$file_exist = file_exists($file);

		if($file_exist)
			require_once $file;

		return $file_exist;
	}

	function getController(){

		$this->initialize();
		
		return $this->lotus->getController();
	}

	function getMethod(){
		$this->initialize();
	
		return $this->lotus->getMethod();
	

	}

	 function getURLPart($number){

		//copy to local var
		$pieces = $this->getURLPieces();

		if(array_key_exists($number, $pieces)){
			return $pieces[$number];
		}
		return false;
	}

	function getRemainingURLPieces($method=''){

		if($method=='')
			$method = $this->getMethod();

		//create local variable
		$pieces = $this->getURLPieces();

		if(strrpos($_SERVER['REQUEST_URI'],$method)) {
			//remove controller only
			unset($pieces[0]);
			unset($pieces[1]);
			unset($pieces[2]);
		}
		else{
		
			//remove controller and method
			unset($pieces[0]);
			unset($pieces[1]);

		}

		//remove empty arguments
		foreach ($pieces as $key => $value) {
			if($value=='')
				unset($pieces[$key]);
		}


		return array_values($pieces);
	}

	function getURIPart($number){

		$part = $this->getRemainingURLPieces();

	

		return $part[$number];
	}



	private function getURLPieces(){
		if(!isset($this->pieces)){
			$actual_link = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

			$parts = parse_url($actual_link);

			$parts['query'] = isset($parts['query']) ? $parts['query'] : '';

			//strip GET Query parameter
			$actual_link =   str_replace('?'.$parts['query'],'',$actual_link);

			$actual_link =   str_replace(LConfig::getConfig('base_url'),'',$actual_link);

		

			if(substr($actual_link, 0,1) != '/') {
				$actual_link = "/".$actual_link;
			}



			$this->pieces = explode("/", $actual_link);

		}

		return $this->pieces;
	}
}