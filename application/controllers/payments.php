<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payments extends MY_Controller {

	public function wwucc(){
		$this->load->library('payments');
		$result = $this->payments->wwucc($_POST['payment_data']);
		$result = json_decode($result);
		if($result->status == 'success'){
			echo json_encode(array('status'=>'success'));
		}else{
			echo json_encode(array('status'=>'fail'));
		}
	}

}

/* End of file  */
/* Location: ./application/controllers/ */