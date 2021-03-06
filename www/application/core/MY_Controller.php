<?php
class MY_Controller extends CI_Controller{

  function __construct(){
    parent::__construct();
    if($_GET['action'] == 'logout') $this->_logout();
    if(ENVIRONMENT == 'development'){
      //$this->output->enable_profiler(true);
    }    
  }

  /**
  * Register magic call methods in here
  * @param string $name Method name
  * @param array $arguments Arguments sent
  */
  function __call($name, $arguments){
    //handle input validation within an external library    
    if(stripos($name, "validate_")){
      $this->load->library('inputvalidation');
      $input = $arguments[0] || '';
      $this->form_validation->set_message($name, $this->inputvalidation->get_message($name));
      return $this->inputvalidation->validate($name, $input);
    }
  }

  /**
  * Logging functionality.  Logs to DB.
  * @param string $message Log message to set
  * @param array|object $data Any associated data to log with event
  * @param string $category A category (recordtype) to associate with this message
  * @param string $category_id Id of record in category to associate with this log
  */
  protected function _log($message, $data=null, $category=null, $category_id=null){
    if($data){
      if(is_object($data)) $data = (array) $data;
      if(!is_array($data)) $data = null;
      else{
        $message = $message.": ".print_r($data, true);
      }
    }    
    $this->load->model('log');
    $this->log->insert(array('message'=>$message, 'category'=>$category, 'category_id'=>$category_id));
  }

  /**
  * Shows a message and then goes to a app url
  * @param string $message
  * @param string $location Location to redirect to (as an app url path ex: /forms/add)
  * @param int $duration How long this message shows before redirecting (in seconds. default: 10)
  * @param string $color Color to display message in (default: black)
  */
  protected function _splash($message, $location, $duration=10, $color='black'){
    $data = array('message'=>$message, 'location'=>$location, 'timeout'=>$duration, 'color'=>$color);
    echo $this->load->view('splashmessage', $data, true);
    exit;
  }

  /**
  * Fails with a message if called
  * @param string $message Message to yell at person
  */
  protected function _failAuthResp($message=null){
      if($message) $data['message'] = $message;
      else $data['message'] = 'You are not authorized to perform this action!';
		  echo $this->load->view('failauth', $data, true);
      exit;
  }

  /**
  * Render content replacing any hook variables with data
  * @param string $view View to load
  * @param array|object $data Data to send to view  
  * @param bool $echo Whether to echo data or return it
  */
  protected function _render($view, $data=null, $echo=true){
    $output = $this->load->view($view, $data, true);
    //todo: write lib for hooking data into random HTML and use it on content here
    
  }

  protected function _redirect($url){    
    header('location: '.$url);
    exit;
    echo "Location should be: ".$url;
  }

  /**
  * This function forces a logout when a person clicks Sign Out in the menu!
  */
  protected function _logout(){
    //ok, so this person is actually logged in
    if($this->authorization->isLoggedIn()){
      $this->authorization->logout();
    }else{ //not really logged in, don't log out!
      $url = str_replace("action=logout",'',current_url());
      $this->_redirect($url);  
    }
  }
}
