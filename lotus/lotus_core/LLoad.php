<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

require_once L_BASEPATH.'lotus_core/LViewLoad.php';

class LLoad extends LViewLoad {



	function __construct($LBase){

		parent::__construct($LBase);
		


	}

	function model($name,$params=array()){
		
		$this->current_load_type='model';
		$this->current_loaded_class = $name."Model";
		$this->load_path = L_BASEPATH."app/model/{$name}Model.php";

		set_error_handler(array($this,'errorHandler'));
		@require_once $this->load_path;
		
		$name = $name."Model";

		//Create dynamic object		
		$this->loaded_class[$name] = new $name($params);
		
		restore_error_handler();
		
		$this->LBase->$name = &$this->loaded_class[$name];

	}



}

