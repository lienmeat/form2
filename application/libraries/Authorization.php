<?php
/**
* Library useful for authorizing actions/access for users
*/
class Authorization{
  /**
  * Code Igniter instance (so we can touch other libs and models...)
  */
  private $CI;
  
  /**
  * Authentication object (shorthand access)
  */
  private $auth;
  
  /**
  * Current user
  */
  private $user;
  
  /**
  * Permissions model
  */
  private $perms;
  
  function __construct(){
    $this->CI =& get_instance();
    //autoloaded, we can take this for granted
    $this->auth =& $this->CI->authentication;
    $this->user = $this->auth->getUser();
    $this->perms =& $this->CI->load->model('permission');
  }
  
  /**
  * The following are all just convienience methods to grab info about current user
  */
  function username(){
    return strtolower($this->user->username);
  }
  
  function wwuid(){
    return $this->user->wwcid;
  }
  
  function firstname(){
    return $this->user->firstname;
  }
  
  function lastname(){
    return $this->user->lastname;
  }
  
  function fullname(){
    return $this->user->fullname;
  }
  
  function directory(){
    return $this->user->directory;
  }
  
  function dn(){
    return $this->user->dn;
  }
  
  function memberof(){
    return $this->user->memberof;
  }
  
  function status(){
    return $this->user->status;
  }
  
  function successful_auth_methods(){
    return $this->user->successful_auth_methods;
  }
  
  function getUser(){
    return $this->user;
  }
  // End user convienience methods
  
  /**
  * Is user logged in
  */
  function isLoggedIn(){
    return $this->auth->isLoggedIn();
  }
  
  /**
  * Did user authenticate with an approved method? (see Authentication lib)
  * @param string $function Auth method
  */
  function isLoggedInWith($function){
    return $this->auth->isLoggedInWith($function);
  }

  function forceLogin($functions='dirlogin'){
    return $this->auth->login($functions);
  }

  function logout(){
    $this->auth->logout();
  }
  
  
  //todo: consider refactoring for roll-based permissions system!
  /**
  * Tells if user has a certain permission on an object
  * @param string $permission Permission
  * @param string $object_type Singular 
  * @param string $object_id
  * @return bool
  */  
  function hasPermissionOnObject($permission, $object_type, $object_id){
    return $this->perm->hasPermissionOnObject($this->username, $permission, $object_type, $object_id);    
  }
  
    
}
?>
