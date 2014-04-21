<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');


class LView {

	var $params = array();

	var $first_run = true;

	function __construct($load){
		$this->loaded_LLoad = $load;
		$this->load = new LViewLoad($this);
	}	

	function render($name,$params=array()){

		$this->params = array_merge($this->params,$params);

		
			//load local variable
		foreach ($this->params as $key => $value) {

			${$key} = $value;
		}

		if($this->first_run){
			//load helper method
			$loaded_class =  $this->loaded_LLoad->getLoadedClass();
			foreach ($loaded_class as $key=>$value ) {
			
				$this->$key = &$loaded_class[$key];


			}
			$this->first_run = false;
		}
		

		//check if fail
		if(!include L_BASEPATH."app/view/{$name}"){
			
			l_displayMessage('Rendering error',"File ".L_BASEPATH."app/view/{$name} not found",'notice');
		}			
				

	}

}