<?php

class LSingletonData{
	private static $data;
	private static $initialized;

	private static function initialize()
	{
		if (self::$initialized)
			return;

		self::$data = array();
		self::$initialized = true;
	}


	protected static function getData($key) {
		self::initialize();

		if(array_key_exists($key,self::$data))
			return self::$data[$key];
		else
			return false;
	}

	protected static function updateData($key,$data){
		self::initialize();
		self::$data[$key]=$data;

	}
}