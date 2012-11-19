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
class Validate extends MY_Controller{

		function __construct(){
			parent::__construct();
			//profiler will break our ajax!
			$this->output->enable_profiler(false);
			$this->load->library('Validation');
		}

		function index(){
			$validations = $_POST['validations'];
			$values = array();
			$lim = count($validations);
			for($i=0; $i<$lim; $i++){
				$val =& $validations[$i];
				//grab previously-modified value for this input
				if($values[$val['input_id']]){
					$val['value'] = $values[$val['input_id']];
				}
				try{
					$ret = $this->validation->validateFunction($val['function'], $val['value'], $val['params']);
					if($ret === true){ //this function validated properly
						$val['status'] = "pass";
					}elseif($ret === false){ //we failed validation!
						$val['error_message'] = $this->validation->getValidationMessage($val['function']);
						$val['status'] = "fail";
					}else{ //we got a string back
						$val['value'] = $ret;
						$values[$val['input_id']] = $ret;
						$val['status'] = "value_change";						
					}
				}catch(Exception $e){
					$val['status'] = "undefined_function";
				}
			}
			
			//tell our client the result
			$this->load->helper('json');
			jsonResponse($validations);
		}		
}

?>