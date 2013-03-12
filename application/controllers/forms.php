<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('form');
	}

	/**
	* look and search for forms
	*/
	function index(){
		$data['forms'] = $this->form->getPublished();
		$this->load->view('formcatalog', $data);
	}

	/**
	* view a form by name (must be published)
	*/
	function view($name){
		$form = $this->form->getPublishedWithName($name);
		if($form){
			$this->_view($form);
		}else{
			$this->load->view('redirect', array('message'=>'There is no form with that name, or it is not currently published!', 'location'=>'forms'));
		}
	}

	/**
	* view a form by it's id
	*/
	function viewid($id){
		$form = $this->form->getById($id);
		if($form){
			$this->_view($form);
		}
	}


	/**
	* does the logic of rendering and saving form results
	*/
	private function _view($form){		
		if(empty($_POST)){
			//show the form...
			$form->questions = $this->_getQuestions($form->id);
			$this->load->view('view_form', array('form'=>$form));
		}else{
			$result = $this->_saveResult($form);
			$this->_doOnSuccess($form, $result);
		}
	}

	/**
	* Saves form resutls to database
	*/
	private function _saveResult($form){
		$this->load->model('result');
		$result->post = json_encode($_POST);
		$result->submitter = $this->authorization->username();
		$result->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$result->ip_address = $_SERVER['REMOTE_ADDR'];
		$result->timestamp = date('Y-m-d H:i:s');
		$result->form_id = $form->id;
		return $this->result->insert($result);		
	}

	/**
	* Runs actions needed to be done on a successful submition
	*/
	private function _doOnSuccess($form, $result){
		//do any workflow or other tasks to do on a success

		//print thank you message
		//print link back to form result

		//print normal form result view
		$form->questions = $this->_getQuestions($form->id);
		$form->result = $result;
		$form->result->post = json_decode($form->result->post);
		$this->load->view('result_form', array('form'=>$form, 'topmessage'=>$form->config->thankyou));
		//echo "Good job. ".anchor('forms/results/'.$form->name, 'Form Results');

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
		if(empty($_POST)){ //todo: server-side validation!
			//ask user to create the form
			$this->load->view('add_form');			
		}else{
			$form = array_merge($_POST, array('creator'=>$this->authorization->username()));
			$form = $this->_saveForm($form);
			$this->_redirect(site_url('forms/edit/'.$form->id));
			//print_r($form);
		}		
	}

	/**
	* Edit an existing form
	*/
	function edit($name_or_id){
		//todo: make sure user has permissions
		$this->authorization->forceLogin();
		$form = $this->form->getById($name_or_id);		
		if(!$form){
			//get forms by name and ask which one
			$forms = $this->form->getByName($name_or_id, "created DESC");			
			//if there are more than one form, we need to know which they actually want to edit!
			if(count($forms) > 1){
				$this->load->view('which_form', array('returnpath'=>'forms/edit/:form_id:', 'forms'=>$forms));
				return;
			}else{
				$form = $forms[0];
			}
		}

		//edit rights need to be tested here!
		if($form->creator != $this->authorization->username()){
			$this->_failAuthResponse('You do not have sufficient rights to edit this form!');
			return;
		}

		//a published form CANNOT be edited directly for safty reasons
		if($form->published && $_GET['doDuplicate']){			
			$form = $this->_duplicateForm($form->id);
			$this->_redirect(site_url('forms/edit/'.$form->id));
			return;
		}elseif($form->published && !$_GET['doDuplicate']){
			//warn that editing this form will cause it to be duplicated
			$forms = $this->form->getByName($form->name, "created DESC");
			$this->load->view('warn_duplication', array('form'=>$form, 'forms'=>$forms));
			return;
		}else{
			//get stuff needed when editing a form
			$form->questions = $this->_getQuestions($form->id);
			$this->load->library('inputs');
			$this->load->view('edit_form',array('form'=>$form));
			return;
		}		
	}

	function delete($id){
		//todo: make sure user has permissions
		$form = $this->form->getById($id);
		if($_POST['deleteconfirm'] == 'yes'){
			$this->form->delete($form->id);
			$this->load->model('question');
			$this->question->deleteByForm($form->id);
			$this->load->view('redirect', array('location'=>'forms/', 'message'=>'Form id:'.$form->id.' deleted successfully!'));
			return;
		}else{
			$this->load->view('delete_form', array('form'=>$form));
			return;
		}
	}

	function publish($id){
		//todo: make sure user has permissions
		$form = $this->form->getById($id);
		if($_POST['publishconfirm'] == 'yes'){
			$this->form->publish($form->id);
			$this->load->view('redirect', array('location'=>'forms/view/'.$form->name, 'message'=>'Form id:'.$form->id.' published successfully!'));
			return;
		}else{
			$this->load->view('publish_form', array('form'=>$form));
			return;
		}
	}

	function manage($name){
		$forms = $this->form->getByName($name, 'created DESC');
		
		//get roles on this form


		//get people who have roles on this form


		//echo "<h1>This method is still under construction!<h1>";
		$this->load->view('manage_form', array('forms'=>$forms));
	}


	function saveconfig($id){
		if(!empty($_POST) and $id){
			$form = $_POST;
			$form['id'] = $id;			
			$form = $this->form->update($form);
		}		
		//if it actually updates...		
		if(is_object($form)){
			$this->load->library('inputs');
			$html = $this->load->view('config_form', array('form'=>$form, 'mode'=>'edit'), true);
			echo json_encode(array('status'=>'success', 'form'=>$form, 'html'=>array('form_config_form'=>$html)));
		}else{//othwise notify of fail
			echo json_encode(array('status'=>'fail'));
		}
	}

	/**
	* Copy a form (forces a new name)
	*/
	function copy($name_or_id){
		echo "This method is still under construction";
	}

	/**
	* Get results for a form
	*/
	function results($name_or_id){
		//todo: make sure user has permissions
		$form = $this->form->getById($name_or_id);
		if($form){
			$forms[] = $form;
		}else{
			$forms = $this->form->getByName($name_or_id);
		}
		if(!$forms) $forms = array();
		$this->load->model('result');
		$formresults = array();
		foreach($forms as $f){
			$f->formresults = $this->result->getByForm($f->id);
			$formresults = array_merge($formresults, $f->formresults);
		}
		$this->load->view('formresults', array('forms'=>$forms, 'formresults'=>$formresults));
	}

	function viewresult($result_id){
		$result = $this->result->getById($id);
		$form = $this->form->getById($result->form_id);
		$form->questions = $this->_getQuestions($form->id);
		$form->result = $result;
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
		return $form;
	}

	/**
	* Save form data
	*/
	private function _saveForm($form){		
		if(is_array($form)) $form = (object) $form;
		if($form->id){
			return $this->form->update($form);
		}else{
			return $this->form->insert($form);
		}
	}

	/**
	* Validate a submitted form
	*/
	private function _validateForm(){

	}
}
?>