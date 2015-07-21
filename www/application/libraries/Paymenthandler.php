<?php
/**
* Library for running payments against epayments rest api
*/
class Paymenthandler{
	
	private $client_id = "519bb6b78d4e2";
	private $api_key = "3HTu9917IJwVLjZDBb304M21Qm2R61qdaKgvfS55se2xl75Yt3888k4rUP9W209651afc014be01d";
	private $epayment_base_url = "https://www.wallawalla.edu/epayment/";
	private $ci;

	function __construct(){
		$this->ci =& get_instance();		
	}

	/**
	* Attempts a cc payment
	*/
	function wwucc(Array $payment_data){
		$payment_data['client_id'] = $this->client_id;
		$payment_data['api_key'] = $this->api_key;
		$payment_data['payment_method'] = 'creditcard';

		$this->ci->load->library('simplecurl');
		$url = $this->epayment_base_url."externalpayment/restPayment";
		$resp = $this->ci->simplecurl->post($url, $payment_data);
		//pull the json out of what is returned!\
		$this->ci->load->helper('json');		
		return json_decode(getJSONFromCurl($resp));
	}
}
?>