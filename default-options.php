<?php

function tonjoo_lf_load_default($options){

	if(!isset($options['frontpage_hook']))
		$options['frontpage_hook']='false';

	if(!isset($options['enable_backend']))
		$options['enable_backend']='false';


	if(!isset($options['page_title']))
		$options['page_title']='';


	if(!isset($options['menu_title']))
		$options['menu_title']='';

	if(!isset($options['image']))
		$options['image']='';

	if(!isset($options['position']))
		$options['position']=66;


	if(!isset($options['submenu_page']))
		$options['submenu_page']=array();



	return $options;
}