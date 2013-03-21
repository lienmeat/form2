<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Helps extends MY_Controller {

	public function index(){
		//todo: interface to search and browse help by searchterm			
	}

	/**
	* Add a new help entry (ajax)
	*/
	public function add(){
		if($_POST['seachterms'] && $_POST['help']){
			$this->load->model('help');
			$help = $this->help->insert($_POST);
			echo json_encode(array('status'=>'success', 'help'=>$help));
			return;
		}
		echo json_encode(array('status'=>'fail'));
	}

	/**
	* Grab a specific help entry's help text for popup (ajax)
	* @param string $id ID of help row
	*/
	public function getHelpTXT($id){
		$this->load->model('help');
		$help = $this->help->getById($id);
		if($help)
			echo json_encode(array('status'=>'success', 'help'=>$help));
		else
			echo json_encode(array('status'=>'fail'));
	}
}