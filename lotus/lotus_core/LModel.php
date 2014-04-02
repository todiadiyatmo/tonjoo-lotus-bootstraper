<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');


class LModel extends LBase{
	function __construct(){
		parent::__construct();
		$this->load->library('db');
	}	
}