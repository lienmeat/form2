<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Questions extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('question');
	}

	/**
	* Make a new question on form $form_id
	* @param string $form_id
	*/
	function add($form_id){
		$this->authorization->forceLogin();
		
		//this shouldn't be possible, but as a precaution
		if(!$this->authorization->username()){
			$this->_failAuthResponse("You must be logged in to complete this action!");
			return;
		}
		/*
		if(empty($_POST)){ //todo: server-side validation!
			//ask user to create the form
			$this->load->view('add_question');			
		}else{
			$form = array_merge($_POST, array('creator'=>$this->authorization->username()));
			$form = $this->_saveForm($form);
			$this->_redirect(site_url('forms/edit/'.$form->id));
			//print_r($form);
		}
		*/		
	}



	/**
	* Edit an existing question
	*/
	function edit($id){
		$this->authorization->forceLogin();
		$this->load->library('inputs');
		$question = $this->question->getById($id);		
		$html = $this->load->view("question/config_question", array('question'=>$question));
		$data = array('html'=>array('question_config'=>$html));
		echo json_encode($data);
	}

	/**
	* Copy a question
	*/
	function copy($id){

	}

	/**
	* Gets all questions belonging to a form
	* @param string $form_id ID of form
	* @return array
	*/
	private function _getByForm($form_id){
		$this->load->model('question');
		return $this->question->getByForm($form_id);
	}

	/**
	* Reorders questions based on posted "question_ids" order
	* (called from ajax on edit form mode)
	*/
	function reorder(){
		if(!empty($_POST['question_ids']) && is_array($_POST['question_ids'])){
			$this->question->reorder($_POST['question_ids']);
			echo json_encode(array('status'=>'success'));
		}else{
			echo json_encode(array('status'=>'fail'));
		}
	}
}
?>