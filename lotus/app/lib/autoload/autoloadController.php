<?php

class autoloadController extends WPFrontEndController{
	function __construct(){
		parent::__construct();


		//WordPress default function
		if(!is_user_logged_in())
			l_base_redirect('user');

		$this->load->library('data');
		$this->load->library('input');
		$this->load->library('validation');
	
	}

}