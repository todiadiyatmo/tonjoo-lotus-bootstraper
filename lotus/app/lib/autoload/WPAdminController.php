<?php

class WPAdminController extends LController{
	function __construct(){
		parent::__construct();
		
		if(!is_admin()){
			l_base_redirect('');			
		}
	}

}