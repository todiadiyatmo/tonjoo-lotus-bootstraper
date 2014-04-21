<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LCommon{
	//URL pieces of the current request
	private  $pieces;

	function load_file($file){

		
		$file = L_BASEPATH.$file;

		$file_exist = file_exists($file);

		if($file_exist)
			require_once $file;

		return $file_exist;
	}

}