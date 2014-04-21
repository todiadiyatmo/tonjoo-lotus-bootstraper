<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

require_once L_BASEPATH.'lotus_core/LBaseLoad.php';

class LViewLoad extends LBaseLoad {



	var $current_loaded_class;
	var $current_load_type;
	var $loaded_class = array();

	function __construct($LBase){

		parent::__construct($LBase);
		$this->LBase = $LBase;


	}

	

	/*
	 * Helper object are available on View / Helper by deffault
	 */

	function helper($name,$params=array()){

		$this->load_path = L_BASEPATH."app/helper/{$name}Helper.php";

		set_error_handler(array($this,'errorHandler'));
		@require_once $this->load_path;

		//if controller call than do not create object
		// array_push($this->loadedHelper, "{$name}Helper");  
		$name= $name."Helper";

		$this->current_load_type='helper';
		$this->current_loaded_class = $name;

		$this->loaded_class[$name] = new $name($params);
		restore_error_handler();
		
		$this->LBase->$name = &$this->loaded_class[$name];
	}

	

	function getLoadedClass(){
		return $this->loaded_class;
	}


}

