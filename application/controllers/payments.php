<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payments extends MY_Controller {

	public function pay(){
		if(!empty($_POST) && $_POST['f2token'] == $_SESSION['f2']['form_token']){
			switch($_POST['payment_method']){
				case "creditcard":
					echo $this->_wwucc();
					break;
			}
		}
	}

	private function _wwucc(){
		$this->load->library('Paymenthandler');
		$result = $this->paymenthandler->wwucc($_POST);

		
		if($result->status == 'success'){
			return json_encode(array('status'=>'success'));
		}else{
			return json_encode(array('status'=>'fail'));
		}
	}
}

/* End of file  */
/* Location: ./application/controllers/ */