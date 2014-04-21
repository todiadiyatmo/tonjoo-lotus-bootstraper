<?php

class LotusFactory {
	
	private static $lotus = false;
	private static $lezsql = false;
	private static $lotus_loader = false;

	/*
	 * Loader Instace, method to load LF outside WP (To Do)
	 */

	public static function getLoaderInstance(){

		if(self::$lotus_loader==false){

			foreach(glob(L_BASEPATH."lotus_core/*.php") as $file){
				require_once $file;
			}

			//Vendor File
			foreach(glob(L_BASEPATH."app/lib/autoload/*.php") as $file){
				require_once $file;
			}

			if(__c('database','on'))
				require_once  L_BASEPATH.'lotus_core/database/DBDriver.php';

			//Common Library
			require_once  L_BASEPATH.'lotus_core/lib/LCommon.php';


			//load dummy controller
			require_once  L_BASEPATH.'lotus_core/loader/LotusLoader.php';


			self::$lotus_loader = new LotusLoader();

			return self::$lotus_loader;
		}
	}

	public static function getInstance(){

		

		if(self::$lotus==false){
			

			//start page load benchmark
			$time = microtime();
			$time = explode(' ', $time);
			$time = $time[1] + $time[0];

			global $start;

			$start = $time;

			$starttime = microtime(true);

			//Load required file

			//Core File
			foreach(glob(L_BASEPATH."lotus_core/*.php") as $file){
				require_once $file;
			}

			//Load WP only Controller
			global $wp;
			if($wp){
				require_once L_BASEPATH."app/lib/autoload/WPAdminController.php";
				require_once L_BASEPATH."app/lib/autoload/WPFrontEndController.php";
			}


			//Vendor File
			foreach(glob(L_BASEPATH."app/lib/autoload/*.php") as $file){
				require_once $file;
			}

			if(__c('database','on'))
				require_once  L_BASEPATH.'lotus_core/database/DBDriver.php';

			//Common Library
			require_once  L_BASEPATH.'lotus_core/lib/LCommon.php';

			//URL Library
			require_once  L_BASEPATH.'lotus_core/lib/LURL.php';


			//Input Library
			require_once  L_BASEPATH.'lotus_core/lib/LInput.php';


			self::$lotus = new Lotus();

		}



		return self::$lotus;
	}

		//db object
	public  static function getDb(){

		self::dbInitialize();

		return self::$lezsql;
	}

	private static function dbInitialize(){
		if(self::$lezsql==false){

			if(__c('database','driver')=='mysql'||__c('database','driver')=='wp'){

			//load mysql-ezql

				require_once L_BASEPATH.'lotus_core/lib/db/lezsql/lotus_ezsql_mysql.php';
			//load mysql-lotus-ezql

				if(__c('database','driver')=='wp'){
					self::$lezsql = new lotus_ezsql_mysql(DB_USER,DB_PASSWORD,DB_NAME,DB_HOST);
				}	
				else
					self::$lezsql = new Lotus_ezql_mysql(__c('database','user'),__c('database','password'),__c('database','name'),__c('database','host'));

			//debug config
				if(__c('debug')==true)
					self::$lezsql->show_errors(); 


			}	
		}
	}



}