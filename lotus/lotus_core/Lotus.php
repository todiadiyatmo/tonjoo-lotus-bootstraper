<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');


class Lotus{

	//Controller and method name

	private $method;

	private $operation_mode;

	private $tryMethod;

	private $controller;

	private $callable_controller_name;

	private $controllerInstance;

	private $error=false;

	private $find404 = 0;

	function __construct(){


		$this->common = new LCommon();
		$this->URL = new LURL();
		$this->input = new LInput();

		$this->operation_mode = LConfig::getConfig('operation_mode');

		$this->findRoute();

	}

	private function findRoute(){

		if($this->find404==1){
			$error_message = "404 Routes Error : Controller or method not found";
		}else{
			$error_message = "Controller or method not found";
		}

		

		if($this->find404>1){
			return;
		}

		// Check operation mode
		if($this->operation_mode=='get_param'){

			$this->controller = $this->input->get('controller'); 

		}else{
			// $this->controller = $this->common->getURLPart(1);	
			$this->controller = $this->URL->getInternalURLSegment(1);	
		}

		if(!$this->controller){
			$this->controller = LConfig::getRoute('default_route');
		}


		//safety, reserved controller name
		if(preg_match('/^[a-zA-Z0-9_]+$/', $this->controller)==0){

			$this->show404('Reserved Controller Name',"<p>Controller name must only contain <b>a-zA-Z0-9_</b></p>");
			return;
		}

		//call the Controller
		$this->callable_controller_name = $this->controller."Controller";

		

		//Try to call controller, will throw error if the Controller class not exist 
		try {
			if(!$this->common->load_file("app/controller/{$this->callable_controller_name}.php")){



				$this->error=true;
				throw new Exception($error_message);  
				return;
			}


			//check if class exist in the file
			if(class_exists($this->callable_controller_name)){
				$this->controllerInstance = new $this->callable_controller_name();
			}else{



				$this->error=true;
				throw new Exception($error_message);  
				return;
			}

		} catch (Exception $e) {

		

			 $this->showErrorMessage($e,$this->callable_controller_name,'index');

			 $this->error = true;

			return;

		}


		
		//get method
		if($this->operation_mode=='get_param'){

			$this->method = $this->input->get('method'); 


		}
		else{
			$this->method = $this->URL->getInternalURLSegment(2);
		}
		

		//if no method found, assume this is a call to method index
		if(!$this->method||$this->method==''){
			$this->method = 'index';
		}


		$this->tryMethod = $this->method;

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
				$this->method=$this->tryMethod;

				throw new Exception($error_message);  
				return;
			}

			//check if function is private

			$functionPublic = (int) is_callable(array($this->controllerInstance, $this->method));




			if($functionPublic==0){
				$this->method=$this->tryMethod;
				throw new Exception($error_message);  
				return;
			}


		}
		catch (Exception $e) {

			 $this->showErrorMessage($e,$this->callable_controller_name,$this->method);

			 $this->error = true;

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
		if($this->error==true){
		
			return;
		}

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
		else{

			call_user_func_array(array($this->controllerInstance, $this->method),$this->URL->getInternalRemainingURLSegment($this->method));
		}
	}

	private function show404($title,$message){

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


	private function showErrorMessage($e,$controller,$method){


		//check if route override exist
		if(LConfig::getRoute('404_override')&&LConfig::getRoute('404_override')!=''&&$this->find404<1){
			
			


			$url = $this->URL->getInternalURL();

			$startPosition = strpos($url, $this->controller);

			$url = substr($url,0,$startPosition);

			$url = $url.LConfig::getRoute('404_override');

			$this->URL = new LURL($url);

			$this->find404 = 1;

			$this->findRoute();

			return;
		}

		if(__c('debug')==true){
			$title = $e->getMessage();
			$message = "<p>
			Controller <b><i>$controller</i></b> did not have method <b><i>$method</i></b></p>
			</p>Please check your app/controller/$controller.php file
			</p>
			";

			$this->show404($title,$message);
			return true;
		}

		require_once L_BASEPATH."app/view/404.php";
		return true;
	}	


}

