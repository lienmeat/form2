<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Roles extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('role');
	}

	function index(){
		$user = $this->role->getOnUser('fake.user');
		$form = $this->role->getOnForm('example-name');
		$form_and_user = $this->role->getOnFormAndUser('example-name', 'fake.user');

		echo '<pre>On User "fake.user":';
		print_r($user);
		echo "\n".'On Form "example-name":';
		print_r($form);
		echo "\n".'On Both:';
		print_r($form_and_user);
		echo '</pre>';
	}
}