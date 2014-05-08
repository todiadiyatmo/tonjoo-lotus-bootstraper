<?php  if ( ! defined('L_BASEPATH')) exit('No direct script access allowed');

class LData extends LSingletonData{

	/*
	 *  Every Flashdata can only be used once 
	 *  Userdata session will persist until flush is called
	 */

	function __construct(){
		if(!session_id())
			session_start();
	}


	/*
	 * Set userdata (session), available_next_request mean the session will persist on redirect
	 */

	function setFlash($key,$value,$available_next_request=false){
		//use session data
		if($available_next_request){
			//ENCRYPT :D


			$_SESSION['L_Userdata'][$key]=$this->encrypt($value);

			return;
		}
		//use Singleton Data
		$LData = LSingletonData::getData('LData');
		$LData[$key]=$value;
		LSingletonData::updateData('LData',$LData);
	}

	function getFlash($key){

		//First test the singleton data
		$LData = LSingletonData::getData('LData');




		if(is_array($LData) && array_key_exists($key, $LData)){

			//unset session data (if persist)
			if(array_key_exists('L_Userdata',$_SESSION)&&array_key_exists($key,$_SESSION['L_Userdata'])){
				unset($_SESSION['L_Userdata'][$key]);
			}

			return $LData[$key];
		}
		//if not exist it will be likely on session

		if(array_key_exists('L_Userdata',$_SESSION)&&array_key_exists($key,$_SESSION['L_Userdata'])){

			$output =  $this->decrypt($_SESSION['L_Userdata'][$key]);
			//flush the session
			unset($_SESSION['L_Userdata'][$key]);

			// //transfer to local data so can be used many time
			$this->setFlash($key,$output);

			return $output;
		}

		//return false if not exist
		return false;
	}

	private function encrypt($value){
		$secret = LConfig::getConfig('secret');
		$iv = md5(md5($secret));


		$value= serialize($value);

		$value = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($secret), $value, MCRYPT_MODE_CBC, $iv);
		return base64_encode($value);
	}

	private function decrypt($value){

		$secret = LConfig::getConfig('secret');
		$iv = md5(md5($secret));
	
		$value = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($secret), base64_decode($value), MCRYPT_MODE_CBC, $iv);

		$value = unserialize($value);



		if(is_array($value))
			return $value;

		return rtrim($value);
	}

	public function setUserdata($key,$value){
		$_SESSION['L_UserdataSession'][$key] = $this->encrypt($value);
	}

	public function getUserdata($key=''){
		if(array_key_exists($key,$_SESSION['L_UserdataSession'])){
			return $this->decrypt($_SESSION['L_UserdataSession'][$key]);
		}
		return false;
	}
	
	public function getAllUserdata(){
		if($_SESSION['L_UserdataSession'])
		{
			$session = $_SESSION['L_UserdataSession'];
			foreach($session AS $key => $data)
			{
				$value[$key] = $this->decrypt($data);
			}
			return $value;
		}
		return false;
	}
	

	public function flushAll(){
		unset($_SESSION['L_UserdataSession']);
	}

	public function flushUserData($key){
		unset($_SESSION['L_UserdataSession'][$key]);
	}

}
