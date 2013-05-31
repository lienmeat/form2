<?php
/**
* Library for running payments against epayments rest api
*/
class Payments{
	
	private $client_id = "519bb6b78d4e2";
	private $api_key = "8612151OdI0C393RxfWt64K58hH9Ak5nJ37pug60aBsr8vy1772lbq2400Pi7cG6519bb6b78adbd";
	private $epayment_base_url = "https://www.wallawalla.edu/epayment/";
	private $ci;

	function __construct(){
		$this->ci =& get_instance();		
	}

	/**
	* Attempts a cc payment
	*/
	function wwucc(Array $payment_data){
		$this->ci->load->library('simplecurl');
		$url = $this->epayment_base_url."externalpayment/restPayment";
		$result = json_decode($this->ci->simplecurl->post($url, $payment_data));
		
	}
}
?>