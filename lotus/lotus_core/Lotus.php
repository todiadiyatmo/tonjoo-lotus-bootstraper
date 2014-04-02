<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');


class Lotus{

	//Controller and method name

	private $method;

	private $operation_mode;

	private $first_method;

	private $controller;

	private $callable_controller_name;

	private $controllerInstance;

	private $error=false;


	function __construct(){


		$this->common = new LCommon();
		$this->input = new LInput();

		$this->operation_mode = LConfig::getConfig('operation_mode');

		// Check operation mode
		if($this->operation_mode=='get_param'){

			$this->controller = $this->input->get('controller'); 

		}else{
			$this->controller = $this->common->getURLPart(1);	
		}

		if(!$this->controller){
			$this->controller = LConfig::getConfig('default_route');
			
		}


		//safety, reserved controller name
		if(preg_match('/^[a-zA-Z0-9_]+$/', $this->controller)==0){

			$this->displayError('Reserved Controller Name',"<p>Controller name must only contain <b>a-zA-Z0-9_</b></p>");
			return;
		}

		//call the Controller


		$this->callable_controller_name = $this->controller."Controller";

		

		//exit if controller not exist
		try {
			if(!$this->common->load_file("app/controller/{$this->callable_controller_name}.php")){



				$this->error=true;
				throw new Exception('Controller or method not found');  
			}


			//check if class exist in the file
			if(class_exists($this->callable_controller_name)){
				$this->controllerInstance = new $this->callable_controller_name();
			}else{
				$this->error=true;
				throw new Exception('Controller or method not found');  
				return;
			}

		} catch (Exception $e) {
			$this->display404($e,$this->callable_controller_name,'index');

			return;

		}


		
		//get method
		if($this->operation_mode=='get_param'){

			$this->method = $this->input->get('method'); 


		}
		else{
			$this->method = $this->common->getURLPart(2);
		}
		

		//if no method found, assume this is a call to method index
		if(!$this->method||$this->method==''){
			$this->method = 'index';
		}


		$this->first_method = $this->method;

		//call controller method, with the argument
		try{


			$functionExist = (int) method_exists($this->controllerInstance, $this->method);
			


			//if function not exist try index and argument
			if(!$functionExist){

				$this->method='index';
				//check function is exist
				$functionExist = (int) method_exists($this->controllerInstance, $this->method);
			}

			
			// if index function not exist
			if($functionExist==0){
				$this->method=$this->first_method;

				throw new Exception('Controller or method not found');  
				return;
			}

			//check if function is private

			$functionPublic = (int) is_callable(array($this->controllerInstance, $this->method));




			if($functionPublic==0){
				$this->method=$this->first_method;
				throw new Exception('Controller or method not found');  
				return;
			}


		}
		catch (Exception $e) {

			$this->display404($e,$this->callable_controller_name,$this->method);

			$this->error=true;
		}

	}

	function getMethod(){
		return $this->method;
	}

	function getController(){
		return $this->controller;
	}

	function route(){
		//dont do anything if error -> should be a error page
		if($this->error==true)
			return;
		
		if($this->operation_mode=='get_param'){

			//pass method parameter , ?p1=input1&p2=input2&p3=input3 etc
			$params_array  = array();

			for ($counter=1; $counter <=6 ; $counter++) { 

				$param  = $this->input->get("p{$counter}");

				if($param)
					array_push($params_array, $param);
				else
					break;
			}


			call_user_func_array(array($this->controllerInstance, $this->method),$params_array);
		}
		else
			call_user_func_array(array($this->controllerInstance, $this->method),$this->common->getRemainingURLPieces($this->method));
	}

	private function displayError($title,$message){

		if(__c('404','mode')=='custom'){
			l_redirect(__c('404','url'));
		}
		
		
		if(__c('debug')==true){
			l_displayMessage($title,$message,'notice');
		}
		else{
			require L_BASEPATH.'app/view/404.php';
		}

	}  


	private function display404($e,$controller,$method){



		if(__c('debug')==true){
			$title = $e->getMessage();
			$message = "<p>
			Controller <b><i>$controller</i></b> did not have method <b><i>$method</i></b></p>
			</p>Please check your app/controller/$controller.php file
			</p>
			";

			$this->displayError($title,$message);
			return;
		}

		if(__c('404','mode')=='custom'){
			l_redirect(__c('404','url'));
		}

		require_once L_BASEPATH."app/view/404.php";
		return;
	}	


}

