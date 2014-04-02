<?php



class LBaseLoad{

	function __construct($LObject){
		$this->LObject = $LObject;


	}

	function library($name,$params=array()){

		//set loading state

		$load_name = ucfirst($name);

		$name = strtolower($name);

		$this->current_load_type='library';

		$this->current_loaded_class = $load_name;

		
		if($load_name!='Db'){

			$load_path = L_BASEPATH."lotus_core/lib/L{$load_name}.php";

			//check file exist, if not exist try in app folder
			if(!file_exists($load_path))
				$load_path= L_BASEPATH."app/lib/L{$load_name}.php";
			
			if(!file_exists($load_path))
				$load_path= L_BASEPATH."app/lib/{$load_name}.php";

			set_error_handler(array($this,'errorHandler'));
			require_once $load_path;
			restore_error_handler();

			$load_name = "L".$name;

			//test is class exist
			if(class_exists($load_name)){

				//Create dynamic object
				$this->loaded_class[$name] = new $load_name($params);
				$this->LObject->$name = &$this->loaded_class[$name];
			}

			return;

		}

		//database case
		$this->loaded_class[$name] = LotusFactory::getDb();
		$this->LObject->$name = &$this->loaded_class[$name];
	}



	function errorHandler($errno, $errstr, $errfile, $errline) {

		if(!__c('debug')==true)
		return;

		if(__c('real_debug')==true)
			$error_message = "<br><p>Error : $errstr </p><p><b>$errfile</b> line <b>$errline</b></p>";

		if($this->current_load_type=='library')
			l_displayMessage("Library '$this->current_loaded_class' load Failed","<p>Please check your spelling , if it is a custom library please check /app/library/ folder. </p>$error_message",'notice');
		if($this->current_load_type=='model')
			l_displayMessage("'$this->current_loaded_class' load Failed","<p>Please check your spelling and /app/model/ folder. </p>$error_message",'notice');	
		if($this->current_load_type=='helper')
			l_displayMessage("'$this->current_loaded_class' load Failed","<p>Please check your spelling and /app/helper/ folder. </p>$error_message",'notice');
	}

}