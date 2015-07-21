<?php
/**
 * Authentication library used by formit2 MUST (be loaded before any output!)
 */
class Authentication
{

    /**
     * Current user object
     */
    private $user = false;
    /**
     * CI object
     */
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        //require_once('wwcauth.php');
        $this->setUser();
    }

    /**
     * Sets current browsing person as user, or someone else if passed!
     * @param object|array $user Set your own user (masquerade as someone else, or whatever)
     */
    public function setUser($user = null)
    {
        $user = new stdClass();
        $user->username = 'demo_user';
        $user->wwuid = 1234567;
        $user->email = 'demo@localhost';
        $user->firstname = 'Demo';
        $user->lastname = 'User';
        $user->fullname = 'Demo User';
        $user->directory = 'Staff';
        $user->dn = 'staff';
        $user->member_of = ['staff'];
        $user->status = 'active';
        $this->user = $user;
        // if($user and (is_array($user) or is_object($user))){
        //     $this->user = (object) $user;
        // }else{
        //     $this->user = (object) DA_Client::getUser();
        // }
    }

    /**
     * Get set user
     * @return object|false
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Tests if a user is authenticated with a certain method
     * @param string $function One of the auth methods (dirlogin, or nonwwulogin probably)
     * @return bool
     */
    public function isLoggedInWith($function = null)
    {
        return true;
        // if($function){
        //     return in_array(strtolower($function), DA_Client::getSuccessfulAuthMethods());
        // }else{
        //     return false;
        // }
    }

    /**
     * Tests to see if user is logged in (has username)
     * @return bool
     */
    public function isLoggedIn()
    {
        return true;
        // if(!empty($this->user->username)) return true;
        // else return false;
    }

    /**
     * Force a login to occur if not logged in
     * @param string $functions Comma separated list of login types to allow(dirlogin, nonwwulogin...)
     */
    public function login($functions = 'dirlogin')
    {
        // $functions = strtolower($functions);
        // if(!$functions) $functions = 'dirlogin';
        // if(strpos($functions, 'nonwwulogin') !== false){ $userInfo = nonWWULogin(); }
        // if((!$userInfo || empty($userInfo)) && (strpos($functions, 'dirlogin') !== false) ){
        //     $userInfo = dirLogin();
        // }
        // if($userInfo){
        //     $this->setUser($userInfo);
        // }
        return $this->getUser();
    }

    /**
     * Logs out current user
     * @param string $returnPath Url to return to if a login is successful after logout
     */
    public function logout($returnPath = null)
    {
        if (!$returnPath) {
            $returnPath = base_url();
        }

        DA_Client::logoutClient($returnPath);
    }

    /**
     * Keepalive for login (outputs javascript!)
     */
    public function keepAlive()
    {
        //from DA_Client.php
        //ajaxKeepAlive();
    }
}

function username()
{
    return strtolower($this->user->username);
}

function wwuid()
{
    return $this->user->wwcid;
}

function email()
{
    return strtolower($this->user->email);
}

function firstname()
{
    return $this->user->firstname;
}

function lastname()
{
    return $this->user->lastname;
}

function fullname()
{
    return $this->user->fullname;
}

function directory()
{
    return $this->user->directory;
}

function dn()
{
    return $this->user->dn;
}

function memberof()
{
    return $this->user->memberof;
}

function status()
{
    return $this->user->status;
}
