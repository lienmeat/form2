<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permissions extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('permission');
		$this->output->enable_profiler(TRUE);
	}

	function index(){

		echo "Can current user edit 'example-name'? ";
		if($this->authorization->can('edit', 'example-name')){
			echo "yes";
		}else{
			echo "no";
		}

		echo "<br />Does current user have 'superadmin' role? ";
		if($this->authorization->is('superadmin')){
			echo "yes";			
		}else{
			echo "no";
		}
	}
}