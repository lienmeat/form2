<?php
/**
* Authentication library used by formit2 MUST (be loaded before any output!)
*/
class Authentication{
	
	/**
	* Current user object
	*/
	private $user = false;

	function __construct(){
		require_once('wwcauth.php');
		$this->setUser();
	}

	/**
	* Sets current browsing person as user, or someone else if passed!
	* @param object|array $user Set your own user (masquerade as someone else, or whatever)
	*/
	function setUser($user=null){
		if($user and (is_array($user) or is_object($user))){
			$this->user = (object) $user;
		}else{
			$this->user = (object) DA_Client::getUser();
		}
	}

	/**
	* Get set user
	* @return object|false
	*/
	function getUser(){
		return $this->user;
	}
	
	/**
	* Tests if a user is authenticated with a certain method
	* @param string $function One of the auth methods (dirlogin, or nonwwulogin probably)
	* @return bool
	*/
	function isLoggedInWith($function=null){
		if($function){
			return in_array(strtolower($function), DA_Client::getSuccessfulAuthMethods());	    
		}else{
			return false;
		}
	}
	
	/**
	* Tests to see if user is logged in (has username)
	* @return bool
	*/
	function isLoggedIn(){
		if(!empty($this->user->username)) return true;
		else return false; 
	}

	/**
	* Force a login to occur if not logged in
	* @param string $functions Comma separated list of login types to allow(dirlogin, nonwwulogin...)
	*/
	function login($functions='dirlogin'){
		$functions = strtolower($functions);
		if(!$functions) $functions = 'dirlogin';
		if(strpos('nonwwulogin', $functions) !== false) $userInfo = nonWWULogin();
		if(!$userInfo && strpos('dirlogin', $functions) !== false){
			$userInfo = dirLogin();
		}
		if($userInfo){
			$this->setUser($userInfo);
		}
		return $this->getUser();
	}

	/**
	* Logs out current user
	* @param string $returnPath Url to return to if a login is successful after logout
	*/
	function logout($returnPath=null){
		if(!$returnPath) $returnPath = base_url();
			DA_Client::logoutClient($returnPath);
	}

	/**
	* Keepalive for login (outputs javascript!)
	*/
	function keepAlive(){
		//from DA_Client.php
		//ajaxKeepAlive();
	}
}
?>
