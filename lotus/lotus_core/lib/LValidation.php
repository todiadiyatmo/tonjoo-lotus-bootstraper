<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LValidation extends LLibrary{
	

	var $session_flushed = false;
	var $data;
	var $rules = array();
	var $checkData = array();


	/*
	 * Rules Definition  (Message,function to call)
	 */

	

	function __construct(){
		parent::__construct();
		//load validation message
		$validation_message = __t('validation');

		$this->load->library('data');


		// message,function to call, function to call argument (boolean) 
		$this->rules['required'] = array($validation_message['required'],'vRequired');
		$this->rules['minimal'] = array($validation_message['minimal'],'vMinimal');
		$this->rules['maximal'] = array($validation_message['maximal'],'vMaximal');
		$this->rules['minimal_maximal'] = array($validation_message['minimal_maximal'],'vMinimalMaximal');
		$this->rules['alpha_numeric'] = array($validation_message['alpha_numeric'],'vAlphaNumeric');
		$this->rules['numeric'] = array($validation_message['numeric'],'vNumeric');
		$this->rules['integer'] = array($validation_message['integer'],'vInteger');
		$this->rules['alpha_dash'] = array($validation_message['alpha_dash'],'vAlphaDash');
		$this->rules['alpha'] = array($validation_message['alpha'],'vAlpha');
		$this->rules['email'] = array($validation_message['email'],'vValidEmail');
		$this->rules['unique'] = array($validation_message['unique'],'vUnique');
		$this->rules['matches'] = array($validation_message['matches'],'vMatches');
		$this->rules['url'] = array($validation_message['url'],'vURL');
		$this->rules['date'] = array($validation_message['date'],'vDate');
		
	}

 	/*
 	 *  Add validation rules to framework
 	 * 
 	 *  Params :
 	 * 
 	 *  function addRules($key,$calling_name,$rules)
 	 *
	 *  $this->validation->addRules('username','Username','required|minimal[5]|maximal[10]|alpha_numeric',$type);
	 *
	 *	Custom Validation Function
	 *  
	 *  1. Make file in /app/lib/ folder
	 *  2. Create a class with the same name of the file
	 *  3. Create custom function within the class
	 *  4. Add rules start with custom__{your custom class}_{your_custom_function}[$param_1,$param_2,$param_3,etc] 
	 *  5. Class name must match file name , function must not contain whitespace or '_'
	 *
	 *  $this->validation->addRules('username','Username','custom_[Class_Name]_[Class_Function_Name[$param_1,$param_2,$param_3,etc]]');
	 *
	 * Sample Validation function. 
	 *  1. The calling argument will passed to the custom function with this order $field_value,$messsage,$param_1,$param_2,$param_3,etc
	 *  2. The result must be array, the $result[0] is the result and the $result[1] is the message
	 * 
	 *	function usernameExist($field_value,$messsage,$param_1,$param_2,$param_3,etc){
	 *		$result[0] = result;
	 *		$result[1] = $messsage;
     *
     *
	 * 	if(username_exists($field_value))
	 *			$result[0] = false;
	 *	
	 *		return $result;
	 *	}
	 * 
	 *  $type = ['SERVER_REQUEST_METHOD','POST','GET']
 	 *
	 */

 	function addRules($key,$calling_name,$rules,$type='SERVER_REQUEST_METHOD'){
 		$rules = ltrim ($rules,'|');


		//Single rule
 		if(!strpos ($rules,'|')){


 			$this->extractSingleRule($key,$calling_name,$rules,$type);
 		}
		//Multiple Rule
 		else{
 			$rules = explode("|",$rules);
 			foreach ($rules as $rule ) {
 				$this->extractSingleRule($key,$calling_name,$rule,$type);
 			}
 		}
 	}

 	function extractSingleRule($key,$name,$rule,$type){

 		



 		$add_rules = $this->data->getFlash('validation');



		//No Existing rules
 		if(!isset($add_rules[$key]))
 			$add_rules[$key] = array();

 		//if rule is required then put in in front
 		if($rule=='required')
 			array_unshift($add_rules[$key],$this->extractArgument($rule,$name,$type));
 		else
 			array_push($add_rules[$key],$this->extractArgument($rule,$name,$type));

 	

 		$this->data->setFlash('validation',$add_rules);
 	}

 	function extractArgument($rule,$name,$type){
 		
		//check if the rules have argument
 		if(!strpos ($rule,'[')){
 			return array($rule,$name,array(),$type);
 		}



		//extract argument
 		preg_match('/\[.*\]/', $rule,$argument);
 		$rule = preg_replace('/\[.*\]/', '', $rule);
 		$argument = $argument[0]; 
 		$argument = str_replace("[","", $argument);
 		$argument = str_replace("]","", $argument);

 		return array($rule,$name,explode(',',$argument),$type);	
 	}

 	function run(){
 		$to_validates = $this->data->getFlash('validation');

 		$error_message = array();
 		$is_valid = true;

 		//is_required is a marker for other validation rule (except custom one). Without is_required the validation will be skip if value == "" (empty string)
 		$is_required = false;

 		//extract each id rules
 		foreach ($to_validates as $key => $to_validate) {



 			if($to_validate[0][0]=='required')
 				$is_required = true;


 			foreach ($to_validate as $execute) {
 				
 				// $execute
 				//  array (size=3)
	            //   0 => string 'minimal' (length=7)
	            //   1 => string 'Username' (length=8)
	            //   2 => 
	            //     array (size=1)
	            //       0 => string '5' (length=1)

 				if(strrpos($execute[0],'custom')===0){

 					$custom_call = explode("_", $execute[0]);

 					$classname = $custom_call[1];
 					$function_call = $custom_call[2];


 					$function_call = preg_replace('/\[.*\]/', '', $function_call);

 					require_once L_BASEPATH."app/lib/$classname.php";

 					$custom_class = new $classname;
 					$message_custom = array();
 					$message_custom[0] = $execute[1];

 					//merge the message and function parameter
 					$argument_custom = array_merge($message_custom,$execute[2]) ;

 					

 					//merge all argument with the calling field name
 					$args = $this->combineHTTPParams($key,$argument_custom,$execute[3]);

 				
 					$result = call_user_func_array(array($custom_class,$function_call),$args);

 					//if validation fail
 					if(!$result[0]){
 						$is_valid = false;
 						$this->setErrorMessage($key,$result[1],true);
 					}

 				}
 				else{

 					//combine HTTP Form Data with validation parameter
 					$args = $this->combineHTTPParams($key,$execute[2],$execute[3]);

 					if(trim($args[0])==''&&$is_required==false)
 						continue;

 					$result = call_user_func(array($this,$this->rules[$execute[0]][1]),$args );

 					//if validation fail
 					if(!$result){
 						$is_valid = false;
 						$this->setErrorMessage($key,$execute);
 					}
 				}
 			}
 			//reset is_required to false
 			$is_required = false;
 		}

 		//copy to session flash data !


 		$this->data->setFlash('validation_message',$this->data->getFlash('validation_message'),true);

 		return $is_valid;
 	}



 	function combineHTTPParams($key,$args,$type){

 		// Wheter to use the request method from user or force to use request method in $type parameter from add_rules function
 		if($type=='SERVER_REQUEST_METHOD'){
 			$swith_method = $_SERVER['REQUEST_METHOD'];
 		}else{

 			$type = strtoupper($type);

 			$swith_method = $type;
 		}

	 		switch ($swith_method) {
	 			case 'PUT':
	 			$value[0] = isset($_PUT[$key])? $_PUT[$key] : '';
	 			break;
	 			case 'POST':
	 			$value[0] = isset($_POST[$key])? $_POST[$key] : '' ;
	 			break;
	 			case 'GET':
	 			$value[0]= isset($_GET[$key])? $_GET[$key] : '';
	 			break;
	 			case 'HEAD':
	 			$value[0]= isset($_HEAD[$key])? $_HEAD[$key] : '';
	 			break;
	 			case 'DELETE':
	 			$value[0] = isset($_DELETE[$key])? $_DELETE[$key] : '';
	 			break;
	 			case 'OPTIONS':
	 			$value[0] = isset($_OPTIONS[$key])? $_OPTIONS[$key] : '';
	 			break;
	 			default:
	 			$value[0] = isset($_GET[$key])? $_GET[$key] : '';
	 			break;
	 		}


 		//trim
 		if(!is_array($value[0]))
 			$value[0] = trim($value[0]);

 		return array_merge($value,$args);

 	}

	// Sample data
    // validation_message[username] = true
    // validation_message[all][username][0] = message 1
    // validation_message[all][username][1] = message 2
 	private function setErrorMessage($key,$argument,$custom=false){
 		$validation_message = $this->data->getFlash('validation_message');

 		$validation_message[$key] = true;

 		//handle normal validation
 		if(!$custom){
 			//Replace error message with friendly name
 			$message = str_replace('$data_name',$argument[1], $this->rules[$argument[0]][0]);

	 		//If the validation function contain parameter, replace the parameter string in the message
 			if(!empty($argument[2])){
 				$number = 1;
 				foreach ($argument[2] as $val) {
 					$message = str_replace("$"."$number",$val, $message);
 					$number = $number+1;
 				}
 			}
 		}
 		//handle custom validation
 		else{
 			$message = $argument;
 		}
 		if(!isset($validation_message['all'][$key]))
 			$validation_message['all'][$key] = array();


 		array_push($validation_message['all'][$key],$message);

 		//make it available on next request
 		$this->data->setFlash('validation_message',$validation_message);

 	}


 	/*
 	 * Getting the error message
 	 *
 	 */

 	function getError($name){

 		$message =  $this->data->getFlash('validation_message');

 		if(isset($message[$name])&&$message[$name]==true){

 			return $message['all'][$name][0];
 		}

 		return false;
 	}

 	//Create Unordered List Error Style
 	function getAllError($number_of_message=-1,$print = false){

 		$message =  $this->data->getFlash('validation_message');

 		if(!empty($message['all'])){


 			if($number_of_message==-1)
 				$number_of_message=sizeof($message['all']);
 			else{
 				$message['all'] = array_slice($message['all'],0,$number_of_message);
 			}

 			if($print){
 				echo "<ul>";
 				foreach ($message['all'] as $data_name) {

 					if(is_array($data_name))
 						$data_name = array_slice($data_name,0,1);

 					foreach ($data_name as $message) {
 						echo "<li>$message</li>";
 					}
 				}
 				echo "</ul>";
 			}
 			else{
 				return $message['all'];
 			}

 		}
 		
 		return false;
 	}

	/*
	 *  Validation Library
	 */

	function vValidEmail($args){
		$email = $args[0];
		return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@.+\./', $email);
	}

	function vAlphaNumeric($args){

		$input = $args[0];

		$args[1] = isset($args[1]) ? $args[1] : false;

		//permit whitespace
		if($args[1]=='true')
			$hasil =  preg_match('/^[ a-z0-9A-Z_+-_]+$/', $input);
		else
			$hasil =  preg_match('/^[a-z0-9A-Z_+-_]+$/', $input);
		return $hasil;
	}
	function vAlphaDash($args){
		$input = $args[0];

		$args[1] = isset($args[1]) ? $args[1] : false;

		//permit whitespace
		if($args[1]=='false')
			
			$hasil  = preg_match('/^[a-z0-9A-Z_+()-|]+$/', $input);
		else
			$hasil  = preg_match('/^[a-z0-9A-Z_ +()-|]+$/', $input);
		 

		return $hasil;
	}

	function vAlpha($args){
		$input = $args[0];

		$args[1] = isset($args[1]) ? $args[1] : false;

		//permit whitespace
		if($args[1]=='true')
			$hasil  = preg_match('/^[ a-zA-Z0-9]+$/', $input);
		else
			$hasil  = preg_match('/^[a-zA-Z0-9]+$/', $input);

		return $hasil;
	}

	function vNumeric($args){
		$input = $args[0];

		$hasil  = preg_match('/^[0-9,-.+]+$/', $input);

		return $hasil;
	}

	function vInteger($args){
		$input = $args[0];

		$hasil  = preg_match('/^[0-9-]+$/', $input);

		return $hasil;
	}
	function vMaximal($args){
		$input = $args[0];
		$lenght= $args[1];

		$lenght_real = strlen($input);

		if($lenght_real>=$lenght)
			return false;

		return true;

	}
	function vMinimal($args){
		$input = $args[0];
		$lenght= $args[1];

		$lenght_real = strlen($input);

		if($lenght_real>=$lenght)
			return true;

		return false;

	}

	function vMinimalMaximal($args){

		$input = $args[0];
		$minimal = $args[1];
		$maximal = $args[2];

		return preg_match('/^.{'.$minimal.','.$maximal.'}$/', $input);
	}

	function vRequired($args){
		$input = $args[0];

		return preg_match('/.+/', $input);
	}

	//unfinished function
	function vUnique($args){
		$input = $args[0];
		$input = $args[1];



		return preg_match('/.+/', $input);
	}

	function vURL($args){

		if(filter_var($args[0], FILTER_VALIDATE_URL)===false)
			return false;
		else 
			return true;
	}


	function vDate($args){

		$date = $args[0];

		$result = false;

		unset($args[0]);

		foreach ($args as $date_format) {

			$x = date_parse_from_format($date_format,$date);
			if($x['error_count']==0){
				$result = true;
				break;
			}
		}

		return $result;
	}

	function vMatches($args){

		$key = $args[1];

		switch ($_SERVER['REQUEST_METHOD']) {
 			// case 'PUT':
 			// $value = $_PUT[$key];
 			// break;
			case 'POST':
			$value = $_POST[$key];
			break;
			case 'GET':
			$value= $_GET[$key];
			break;
 			// case 'HEAD':
 			// $value= $_HEAD[$key];
 			// break;
 			// case 'DELETE':
 			// $value = $_DELETE[$key];
 			// break;
 			// case 'OPTIONS':
 			// $value = $_OPTIONS[$key]; 
 			// break;
			default:
			$value = $_GET[$key];
			break;
		}


		if($args[0]==$value)
			return true;
		return false;

	}

}