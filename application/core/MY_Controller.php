<?php
class MY_Controller extends CI_Controller{

  function __construct(){
    parent::__construct();    
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
  protected function log($message, $data=null, $category=null, $category_id=null){
    if($data){
      if(is_object($data)) $data = (array) $data;
      if(!is_array($data)) $data = null;
      else{
        $message = $message.": ".print_r($data, true);
      }
    }    
    $this->load->model('log');
    $this->log_model->insert(array('message'=>$message, 'category'=>$category, 'category_id'=>$category_id));
  }

  /**
  * Sets up validation lib with our preferences todo: move to external validation lib
  * @param array|null $config Form validation config array (see CI documentation)
  */
  protected function initValidation(Array $config=null){
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<div class="Err">', '</div>');
    if($config) $this->form_validation->set_rules($config);
  }
  
  /**
  * Fails with a message if called
  * @param string $message Message to yell at person
  */
  private function failAuthResp($message=null){
      if($message) $data['message'] = $message;
      else $data['message'] = 'You are not authorized to perform this action!';
		  $this->render('failauth', $data);
      exit;      
  }

  /**
  * Render content to either ajax (json), or regular browser based on get string
  * @param string $view View to load
  * @param array|object $data Data to send to view
  * @param array|object $send_data Data to jsonify and send with response
  */
  function render($view, $data=null, $send_data=null){
    $html = $this->load->view($view, $data, true);
    if($_GET['wifi_info']){      
      $tosend = array('html'=>$html);
      if($send_data) $tosend['data'] = $send_data;
      echo json_encode($tosend);
    }else{
      if($send_data){
        $html.="<pre>".print_r($send_data, true)."</pre>";
      }
      echo $html;
    }
  }
}
