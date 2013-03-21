<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms extends MY_Controller{
	//functions to run on the success of submitted form
	private $on_form_success = array();

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
	* @param object $form
	*/
	private function _view($form){
		//check rights to view this form version (if unpublished)
		if(!$form->published &&
		 ( !$this->authorization->can('edit', $form->name)
		 	&& !$this->authorization->can('admin', $form->name) )
		){
			$this->_failAuthResp('You must have edit or admin permissions on a form to view it\'s non-published versions!');
		}

		//check that this person has the right qualifications according to the form view settings itself
		$this->_checkViewRights($form);

		//set any mandatory functions that will run on successful form submit
		$this->_bindFormSuccess('_showFormResult');		
		

		if(empty($_POST) || $_POST['submit_fi2'] != "Submit"){
			//show the form...
			$form->questions = $this->_getQuestions($form->id);
			$this->load->view('view_form', array('form'=>$form));
		}else{
			$result = $this->_saveResult($form);
			$this->_doOnFormSuccess($form, $result);
		}
	}

	/**
	* Tests login/auth requirements on a form
	* @param object $form
	*/
	private function _checkViewRights($form){
		if($form->config->login_required == 'Y'){
			$this->authorization->forceLogin();
			if($this->authorization->can('edit', $form->name) || $this->authorization->can('admin', $form->name)){
				$can_view = true;
			}
			//format viewers into array
			if(!empty($form->config->viewers)){
				$viewers = explode("\n", $form->config->viewers);
				for($i=0; $i<count($viewers); $i++){
					$viewers[$i] = trim(strtolower($viewers[$i]));
				}
			}
			if(is_array($form->config->ad_groups)){
				if(!in_array("all", $form->config->ad_groups)){  //any valid login will do if this fails
					if(!in_array(strtolower($this->authorization->status()), $form->config->ad_groups) ){
						if(!empty($viewers)){
							if(!in_array(strtolower($this->authorization->username()), $viewers)){
								if($can_view) return;
								else $this->_failAuthResp("You must have one of the following AD statuses to view this form:<br />".implode(", ", $form->config->ad_groups)."<br />OR be one of these users:<br />".implode(", ", $viewers));
							}else{
								//we are one of the allowed users
								return;
							}
						}else{
							if($can_view) return;
							else $this->_failAuthResp("You must have one of the following AD statuses to view this form:<br />".implode(", ", $form->config->ad_groups));
						}
					}else{
						//we have the right status
						return;
					}
				}
			}else{ //no ad_status configured, check viewers
				if(!empty($viewers)){
					if(!in_array(strtolower($this->authorization->username()), $viewers)){
						if($can_view) return;
						else $this->_failAuthResp("You must be one of the following users to access this form:<br />".implode(", ", $viewers));
					}else{
						//we have the right user
						return;
					}
				}else{
					//this is bad.  This means no ad_status is configured, and neither are any viewers!  LET EVERYONE KNOW ABOUT THIS SITUATION!
					$this->_failAuthResp("FORM CONFIGURATION ERROR: No AD Status or users have been allowed to access this form, yet a login is required!  Please notify webservices!");
				}
			}
		}
	}

	/**
	* Saves form resutls to database
	*/
	private function _saveResult($form){
		//todo: decided if we should save based on form config.
		// It might be that the form is ONLY to be sent to a remote addr, not saved.
		$this->load->model('result');
		$post = $this->_filterPost();		
		$result->post = json_encode($post);
		$result->submitter = $this->authorization->username();
		$result->user_agent = $_SERVER['HTTP_USER_AGENT'];
		$result->ip_address = $_SERVER['REMOTE_ADDR'];
		$result->timestamp = date('Y-m-d H:i:s');
		$result->form_id = $form->id;
		return $this->result->insert($result);		
	}

	/**
	* Filters post data so that stuff we don't want doesn't show up in results
	*/
	private function _filterPost(){
		$post = $_POST;
		$input_names = explode(',',$post['dependhiddeninputs']);
		foreach($input_names as $name){
			$name = str_replace('[]','', $name);
			unset($post[$name]);
		}		
		return $post;		
	}

	/**
	* Runs actions needed to be done on a successful submition
	*/
	private function _doOnFormSuccess($form, $result){
		$form->questions = $this->_getQuestions($form->id);
		$form->result = $result;
		$form->result->post = json_decode($form->result->post);

		//do any workflow or other tasks to do on a success
		foreach($this->on_form_success as $fn){
			if(method_exists($this, $fn)){
				$this->$fn($form);
			}
		}
	}

	/**
	* Bind a function in this class to run on form success
	* @param string $function Name of method to run
	*/
	public function _bindFormSuccess($function){
		if(!in_array($function, $this->on_form_success)){
			$this->on_form_success[] = $function;			
		}
	}

	/**
	* Shows user the form result when form is submitted
	* @param object $form
	*/
	private function _showFormResult($form){
		//todo: detect when it shouldn't run!  ex: pass to external script instead...						
		$this->load->view('result_form', array('form'=>$form, 'topmessage'=>$form->config->thankyou));
	}	

	/**
	* Make a new form
	*/
	function add(){
		$this->authorization->forceLogin();		
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
		if($form->creator != $this->authorization->username() && !$this->authorization->can('edit', $form) && !$this->authorization->can('admin', $form)){
			$this->_failAuthResp('You do not have sufficient rights to edit this form! You must have edit or admin permissions!');
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
		$this->authorization->forceLogin();		
		$form = $this->form->getById($id);

		if($form->creator != $this->authorization->username() && !$this->authorization->can('admin', $form)){
			$this->_failAuthResp('You do not have sufficient rights to delete this form!  You must be the creator of this form, or have the admin permission on this form!');
			return;
		}

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
		$this->authorization->forceLogin();
		$form = $this->form->getById($id);

		if($form->creator != $this->authorization->username() && !$this->authorization->can('edit', $form) && !$this->authorization->can('admin', $form)){
			$this->_failAuthResp('You do not have sufficient rights to publish this form!  You must be the creator of this form, or have edit or admin permissions on this form!');
			return;
		}

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
		$this->authorization->forceLogin();
		if(!$this->authorization->can('admin', $name)){
			$this->_failAuthResp('You must have admin rights on a form to access this page!');
		}
		$forms = $this->form->getByName($name, 'created DESC');
		
		//get permissions on this form
		$this->load->model('permission');
		$perms_raw = $this->permission->getOnForm($name);
		$perms = array();
		foreach($perms_raw as $p){
			if(!array_key_exists($p->user, $perms)){
				$perms[$p->user] = array();
			}
			$perms[$p->user][] = $p;
		}

		
		$this->load->view('manage_form', array('forms'=>$forms, 'users_with_perms'=>$perms));		
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
		$this->authorization->forceLogin();
		echo "This method is still under construction";
	}

	/**
	* Get results for a form
	*/
	function results($name_or_id){
		$this->authorization->forceLogin();		
		
		$form = $this->form->getById($name_or_id);
		if($form){
			$forms[] = $form;
		}else{
			$forms = $this->form->getByName($name_or_id);
		}
		if(!$this->authorization->can('admin', $forms[0]) && !$this->authorization->can('edit', $forms[0]) && !$this->authorization->can('viewresults', $forms[0])){
			$this->_failAuthResp('You must have edit, admin, or viewresults permissions on a form to access this page!');
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