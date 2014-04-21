<?php
class welcomeController extends LController{

	function __construct(){
		parent::__construct();

		$this->load->library('data');
	}

	function index(){


		$this->view->render('index.php',array('template'=>'welcome/index.php'));
	}


}