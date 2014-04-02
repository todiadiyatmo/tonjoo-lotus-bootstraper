<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LInput{


	/*
	 * Provide safe way to echo form data, with sanitized output to prevent XSS attack
	 * This is some <b>bold</b> text.
	 */
	function setValue($name,$default='',$clean_code=false){
		
		global $wp;

		//check the default http method 
		$return = $this->getValue($name) == '' ? $default : $this->getValue($name) ;


		
		if(is_array($return)){
			foreach ($return as $key => $value) {
			
				if(!$clean_code)
					$return[$key] =  htmlentities($value,ENT_QUOTES,"UTF-8");
				else
					$return[$key] = html_entity_decode($value);

				$return[$key] =  stripcslashes($return[$key]);
			}
		}

		else{



			if($wp)
				$return = stripcslashes($return); 

			if(!$clean_code)
				$return =   htmlentities($return,ENT_QUOTES,"UTF-8");
			else
				$return =   html_entity_decode($return);
			
			$return= $return==''? $default :  trim($return); 
		}

		return $return;
	}

	/*
	 * Get GET Data
	 */

	function post($name='',$clean_code=false){
		return $this->getCleanData($name,$_POST,$clean_code);	
	}

	/*
	 * Get GET Data
	 */

	function get($name='',$clean_code=false){
		return $this->getCleanData($name,$_GET,$clean_code);	
	}

	/*
	 * Clean user submited data // Need to clean HTML Entities recursively
	 */
	private function getCleanData($name,$user_data,$clean_code){


		global $wp;
	
		//Get All POST
		if($name==''){	
			
			foreach ($user_data as $key=>$value) {

				if(!$clean_code&&!is_array($value)){
					$value=htmlentities($value,ENT_QUOTES ,"UTF-8");
				}elseif($clean_code&&!is_array($value)){
					$value=html_entity_decode($value);
				}
				$user_data[$key]=trim($value);
	
				if($wp){
					$user_data[$key] = stripcslashes($user_data[$key]);
				}


			}
			return $user_data;
		}


		//Get Single POST
		$return = isset($user_data[$name]) ? $user_data[$name] : false ;
		
		//should add functionality to clean array
		if(!$clean_code&&!is_array($return)){
			$return = htmlentities($return,ENT_QUOTES,"UTF-8");
			$return = trim($return);


		}
		elseif($clean_code&&!is_array($return)){
			$return = html_entity_decode($return);
		}
	
		if($wp){
			if(is_array($return))
				array_walk_recursive($return,array($this,'stripcslashes'));
			else
				$return = stripcslashes($return); 
		}

		return $return;
	}

	private function stripcslashes($value,$key){
		return stripcslashes($value);
	}

	/*
	 * Guess user submited data
	 */
	private function getValue($name,$default_value=''){

		switch ($_SERVER['REQUEST_METHOD']) {
			// 
			case 'POST':
			$return = isset($_POST[$name]) ? $_POST[$name] : '';
			break;
			case 'GET':
			$return = isset($_GET[$name]) ? $_GET[$name] : '';
			break;
			default:
			$return = '';
			break;
		}

		

		//try other Method posibilities if return is ''
		if($return==''){

			$test_data= array();
			$test_data = array_merge($test_data,$_GET);
			$test_data = array_merge($test_data,$_POST);


			foreach ($test_data as $key => $value) {

				if($key==$name){
					$return=$value;
				}
					
			}

		}

		if($return=='')
			$return=$default_value;




		return $return;
	}
}