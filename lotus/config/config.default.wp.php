<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');
//Global WP

// Base URL

if(is_admin()){
	$l_config['base_url']= admin_url('admin.php');	
}
else{
	$l_config['base_url']= get_permalink(LF_POST_HOOK_ID);
}


/* ---------------
 * Preload Library
 * ---------------
 * All library must be in L_BASEPATH/core/lib folder
 */

$l_config['library'] = array('url');


/* ---------------
 * Language Translation
 * ---------------
 * All translation is located on /app/config/translation.
 * The default is en (English) which refer to /app/config/translation/en.php file
 */

$l_config['language'] = WPLANG;


/* --------
 * 404 Mode
 * --------
 * 'default' , use HTML template for error notification
 * 'custom' , use custom url for 404
  *
 * Default ~ show EF 404 page :
 * $l_config['404']['mode']='default'
 * 
 * Custom ~ predefined 404 page:
 * $l_config['404']['mode']='custom'
 * $l_config['404']['url'] = 'http://example.com/404.html'
 *
 * Custom ~ route to other Controller
 * $l_config['404']['mode']='custom'
 * $l_config['404']['url'] = 'http://example.com/index.php/welcome'
 */

$l_config['404']['mode'] = 'default';


/* -------------
 * Rewrite unkwown function to index  
 * ------------ 
 * $l_config['rewrite_to_index']=false; 
 */ 

$l_config['rewrite_to_index']=false;

/* ---------------
 * Database Driver
 * ---------------
 * Database is not enabled by default, to enable :
 * $l_config['database']['on'] = true
 */

$l_config['database']['driver'] = 'wp';

//$l_config[url] = 'localhost';
//$l_config[database] = 'ef';
//$l_config[username] = 'root';
//$l_config[password] = '';

/*
 * Secret Key , To Encrypt Data
 */

$l_config['secret']=AUTH_SALT;


/*
 * Real Debug, display error from LF core
 */
$l_config['debug']=true;
$l_config['framework_debug']=true;

/*
 *  Lotus Framework Operation Mode
 *
 *  (1) 'default' -> normal operation : http://your_base_url/controller/method/params1/params2/
 *  (2) 'get_params' -> normal operation : http://your_base_url?controller=welcome&method=index&params1=ABCD&params2=ABCD/
 * 
 */

/*
 *  Lotus Framework Operation Mode
 *
 *  (1) 'default' -> normal operation : http://your_base_url/controller/method/params1/params2/
 *  (2) 'get_params' -> normal operation : http://your_base_url?controller=welcome&method=index&params1=ABCD&params2=ABCD/
 * 
 */

if(is_admin()){
	$l_config['operation_mode'] = 'get_param';
}else{
	$l_config['operation_mode'] = 'default';
}