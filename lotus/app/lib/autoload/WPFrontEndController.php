<?php

class WPFrontEndController extends LController{
	function __construct(){
		parent::__construct();

		if(is_admin()){
			  $options = get_option('lf_settings'); 
			$slug = sanitize_title_with_dashes(trim($options['page_title']));
			l_base_redirect('?page='.$slug);
		}
	}

}