<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

function l_start_clean(){
	//clean previous buffer, start new output
	ob_end_clean();
	ob_end_clean();

}

function l_start_json(){
	//clean previous buffer, start new output
	ob_end_clean();
	ob_end_clean();
	header('Content-Type: application/json');
}

function l_start_ajax(){
	//clean previous buffer, start new output
	ob_end_clean();
	ob_end_clean();
	header('Content-Type: application/javascript');
}


function l_displayMessage($title,$detail,$class){

	$title = $title;
	$detail = $detail;
	$class = $class;

	require_once L_BASEPATH."app/view/Message.php";
	

}

/* Create link to other pages
 *
 *  $rebuildQuery always safe the paramater for the hyperlink
 *
 *
 */

function l_base_p_url($controller,$method='index',$extra_params=array(),$rebuildQuery=array()){

	global $wp;

	if($wp && is_admin()){
		array_push($rebuildQuery,'page');
	}

	if($controller!='')
		$extra_params['controller']=$controller;

	if($method!='')
		$extra_params['method']=$method;

	return l_base_url('',$rebuildQuery,$extra_params);
}


function l_base_p_redirect($controller,$method='index',$rebuildQuery=array(),$extra_params=array()){

	global $wp;

	if($wp && is_admin()){
		array_push($rebuildQuery,'page');
	}

	$extra_params['controller']=$controller;
	$extra_params['method']=$method;

	l_base_redirect('',$rebuildQuery,$extra_params);
}

function l_base_url($url="",$rebuildQuery=array(),$extra_params=array()){
	$getQuery = "";



	if(sizeof($rebuildQuery)!=0){
		$part = parse_url($url);
		if(isset($part['query'])){

			$base_query = explode('&',$part['query']);

			$url= $part['path'];

			$final_base_query = array();

			//split each query part
			foreach ($base_query as $value) {
				$final_part = explode("=",$value);
				$final_base_query[$final_part[0]]=$final_part[1]; 
			}

			$extra_params = array_merge($extra_params,$final_base_query);

		}

		$additional_get = array();

		foreach ($rebuildQuery as $value) {
			
			if(isset($_GET[$value]))
				$additional_get[$value] = $_GET[$value];
		}

		$extra_params = array_merge($additional_get,$extra_params);

		$getQuery="?";

		$getQuery .= http_build_query($extra_params,'','&');
	}

	$base_url = LConfig::getConfig('base_url');

	//add back slash

	global $wp;

	if($wp && !is_admin()){

		if(substr($base_url, -1) != '/') {
			$base_url = $base_url."/";
		}
	}




	if($wp && !is_admin()){
		if($url!=''&&substr($url, -1) != '/'){ 
			$url = $url."/";
		}
	}


	if($getQuery=="?")
		return $base_url.$url;

	return $base_url.$url.$getQuery;
}

function l_assets_url($file){
	//test wp 
	global $wp;
	if(isset($wp)){
		return plugin_dir_url(L_BASEPATH)."lotus/assets/$file";
	}
	else{
		return l_base_url('asset/'.$file);
	}
}

function l_redirect($url){
	header("Location: $url");
	exit();
}


function l_base_redirect($url,$rebuildQuery=array(),$extra_params=array()){
	$getQuery = "";



	if(sizeof($rebuildQuery)!=0){
		$part = parse_url($url);

		if(isset($part['query'])){

			$base_query = explode('&',$part['query']);

			$url= $part['path'];

			$final_base_query = array();

			//split each query part
			foreach ($base_query as $value) {
				$final_part = explode("=",$value);
				$final_base_query[$final_part[0]]=$final_part[1]; 
			}

			$extra_params = array_merge($extra_params,$final_base_query);

		}

		$additional_get = array();

		foreach ($rebuildQuery as $value) {
			if(isset($_GET[$value]))
				$additional_get[$value] = $_GET[$value];
		}

		$extra_params = array_merge($additional_get,$extra_params);


		$getQuery="?";



		$getQuery .= http_build_query($extra_params,'','&');

	}

	$base_url = LConfig::getConfig('base_url');

	global $wp;

	//add back slash
	if($wp && !is_admin()){

		if(substr($base_url, -1) != '/') {
			$base_url = $base_url."/";
		}

	}

	//wp URL always end in backslash

	if($wp && !is_admin()){
		if(substr($url, -1) != '/') {
			$url = $url."/";
		}
	}

	$final_url = $base_url.$url.$getQuery;



	header("Location: $final_url");
	exit();
}



function l_bench_start(){
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];

	global $start;

	$start = $time;


}

function l_bench_stop(){

	global $start;

	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	return round(($finish - $start), 4);
}

function __t($key,$key2=false){

	return LConfig::getTranslation($key,$key2);
}

function __c($key1,$key2=false){
	return LConfig::getConfig($key1,$key2);
}