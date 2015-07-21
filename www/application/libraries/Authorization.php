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
  
  
  function __construct(){
    $this->CI =& get_instance();
    //autoloaded, we can take this for granted
    $this->auth =& $this->CI->authentication;
    //store our user for later use (shorthand)
    $this->user = $this->auth->getUser();    
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

  function email(){
    return strtolower($this->user->email);
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

  function forceLogin($functions='dirlogin,nonwwulogin'){
    return $this->auth->login($functions);
  }

  function logout(){
    $this->auth->logout();
  }
  
  /**
  * Finds if current user can perform an action on a form
  * @param string $permission Name of permission (edit, admin, view, etc...)
  * @param string $form Name of form
  * @return bool
  */
  function can($permission, $form){

    $this->CI->load->model('permission');
    $this->CI->load->model('form');
    if(!is_object($form)){
      $res = $this->CI->form->getByName($form);
      $formname = $res[0]->name;
      if($res[0]->creator == $this->username()) return true;
    }else{
      $formname = $form->name;
      if($form->creator == $this->username()) return true;      
    }

    //first check granular permissions on a form
    $res = $this->CI->permission->hasPermissionOnUserAndForm($permission, $this->username(), $formname);
    if($res) return $res;
    
    //see if we have a role with the perm
    $this->CI->load->model('role');
    $roles = $this->CI->role->getOnFormAndUser($formname, $this->username());
    foreach($roles as $r){
      if($this->CI->permission->hasPermissionOnRole($permission, $r->id)) return true;
    }

    //see if person has one of the global roles
    $supereditor = $this->is('supereditor');
    if($supereditor && $this->CI->permission->hasPermissionOnRole($permission, $supereditor->id)) return true;
    else if($this->is('superadmin')){ //superadmin can do ALL
      return true;
    }
    return false;
  }
  
  /**
  * Find out if a global role (or any other really) is assigned to current user
  * (global roles are going to be things like superadmin & supereditor)
  * @param string $role_name
  * @return object|false Role on success, false otherwise
  */
  function is($role_name){
    $this->CI->load->model('role');
    $role = $this->CI->role->getRoleOnUserByName($this->username(), $role_name);
    if(!empty($role)) return $role;
    else return false;
  }    
}
?>
