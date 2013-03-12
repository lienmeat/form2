<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Results extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('result');
	}

	function view($id){
		$this->load->model('form');
		$result = $this->result->getById($id);
		$form = $this->form->getById($result->form_id);
		$form->questions = $this->_getQuestions($form->id);
		$form->result = $result;
		$form->result->post = json_decode($form->result->post);
		$this->load->view('result_form', array('form'=>$form));
	}

	/**
	* Gets all questions belonging to a form
	* @param string $form_id ID of form
	* @return array
	*/
	private function _getQuestions($form_id){
		$this->load->model('question');
		return $this->question->getByForm($form_id);
	}
}
?>