<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('form');
	}

	/**
	* Make a new form
	*/
	function add(){
		$this->authorization->forceLogin();
		
		//this shouldn't be possible, but as a precaution
		if(!$this->authorization->username()){
			$this->_failAuthResponse("You must be logged in to complete this action!");
			return;
		}
		if(empty($_POST)){
			//ask user to create the form
			$this->load->view('add_form');
		}else{
			$form = array_merge($_POST, array('creator'=>$this->authorization->username()));
			$this->form->insert($form);
			$this->redirect(site_url('forms/edit/'.$form->id));
		}
	}



	/**
	* Edit an existing form
	*/
	function edit($name_or_id){
		die('ok');
		//$this->authorization->forceLogin();
		$form = $this->form->getById($name_or_id);
		die(print_r($form, true));
		if(!$form){
			//get forms by name and ask which one
			$forms = $this->form->getByName($name_or_id);
			//$this->load->view('which_form', array('path'=>'forms/edit/:id:'));
			return;
		}

		//edit rights need to be tested here!
		if($form->creator != $this->authorization->username()){
			$this->_failAuthResponse('You do not have sufficient rights to edit this form!');
		}

		//a published form CANNOT be edited directly for safty reasons(you could un-publish...)
		if($form->published){
			$form = $this->_duplicateForm($form->id);
			die('what');
		}else{
			//get stuff needed when editing a form
			$form->questions = $this->_getQuestions($form->id);
		}

		$this->load->view('edit_form',array('form'=>$form));
	}

	/**
	* Copy a form (forces a new name)
	*/
	function copy($name_or_id){

	}

	/**
	* Get results for a form
	*/
	function results($name_or_id){

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

	/**
	* Duplicate a form and it's parts (when edit a published form)
	* @param string $form_id ID of form
	* @return object Form object with everything it duplicated (questions...)
	*/
	private function _duplicateForm($form_id){
		$form = $this->form->duplicate($form_id);
		$this->load->model('question');
		//copy all the questions belonging to this form to our new form
		$form->questions = $this->question->copyAllInForm($form_id, $form->id);

		//anything else we have to do in the future (roles, tags, folders...etc)
		//although, thinking of it, I may decide to make those name-based...
	}

	/**
	* Save form data
	*/
	private function _saveForm($form){

	}

	/**
	* Validate a submitted form
	*/
	private function _validateForm(){

	}
}
?>