<?php


function l_get_instance(){

	require_once 'license.php';

	/*
	 * Get Lotus Loader Instance
	 */

	return LotusFactory::getLoaderInstance();

}