<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');


class LHelper extends LBase{
	function __construct(){
		parent::__construct();
		$this->view = new LView($this->load);
	}	
}