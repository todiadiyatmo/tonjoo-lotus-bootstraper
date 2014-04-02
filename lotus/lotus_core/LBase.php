<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LBase {

	function __construct(){

		$this->load = new LLoad($this);

	}	

	//lazy load db
}

