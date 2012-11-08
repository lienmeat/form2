<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* This controllers only purpose is to receive validation requests
* from ajax/clientside, and send back responses.
*
* We do this so we don't have to depend on every validation
* function to be duplicated in JS (we can do some interesting things this way.
* If the funciton doesn't exist, it can request php to validate for us.
* Examples where this could be crutial is things like checking the digisig
* or that a certain person has rights, or something.
*
*/
class Validation extends MY_Controller{

		function __construct(){
			parent::__construct();
			$this->load->library('Validation');
		}

		function validate(){
			$validations = $_POST['validations'];

			/*
			$ret = $this->validation->check($_POST['function'], $_POST['input']);
			
			if($ret === true){ //this function validated properly

			}elseif($ret === false){ //we failed validation!

			}elseif($ret == "undefined_validation_function"){ //function was not defined!

			}else{ //we got a string back

			}
			*/
			$response = $validations;
			//tell our client the result
			$this->load->helper('json');
			jsonResponse($response);
		}		
}

?>