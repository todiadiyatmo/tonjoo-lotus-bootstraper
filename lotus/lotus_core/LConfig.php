<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');


class LConfig{

	private static $config;
	private static $translation ;
	private static $initialized = false;

	private static function initialize()
	{
		if (self::$initialized)
			return;

		//check if it is running on Wordpress
		global $wp;
		if(isset($wp))
			//run Lotus Framework wp config
			require L_BASEPATH.'config/config.wp.php';

		//run standar Lotus Framework confing
		else
			require L_BASEPATH.'config/config.php';
		
		self::$config = $l_config;
		self::$initialized = true;

		if($l_config['language']=='')
			$l_config['language']='en_EN';

		require L_BASEPATH."config/translation/{$l_config['language']}.php";


		self::$translation = $l_translation;

	}

	public static function getConfigs(){
		self::initialize();
		return self::$config;
	}

	public static function getConfig($key1,$key2=false){
		self::initialize();
		if($key2){
			$return = isset(self::$config[$key1][$key2]) ? self::$config[$key1][$key2] : false;

			return $return;
		}

		$return = isset(self::$config[$key1]) ? self::$config[$key1] : false;

		return $return;
	}

	public static function getTranslation($key1,$key2=false){
		self::initialize();
		if($key2){
			$return = isset(self::$translation[$key1][$key2]) ? self::$translation[$key1][$key2] : false;

			return self::$return;
		}
		$return = isset(self::$translation[$key1]) ? self::$translation[$key1] : false;

		return $return;
	}

}