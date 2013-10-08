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
	private function _view($form, $embedded=false){
		//get get args and register them properly so we can know what is readonly or force-filled
		$this->_captureGetArgs();

		$_SESSION['f2']['form_token'] = uniqid('token',true);

		//check rights to view this form version (if unpublished)
		if(!$form->published) {
			
			//You will not make it past here if the person is not logged in...
			$this->authorization->forceLogin();

			//otherwise, they may be allowed to view this form, if they logged in
			if( !$this->authorization->can('edit', $form->name) 
				&& !$this->authorization->can('admin', $form->name) ){
				$this->_failAuthResp('You must have edit or admin permissions on a form to view it\'s non-published versions!');
			}
		}

		//check that this person has the right qualifications according to the form view settings itself
		$this->_checkViewRights($form);

		//show the form...
		$form->questions = $this->_getQuestions($form->id);
		$this->load->view('view_form', array('form'=>$form, 'embedded_form'=>$embedded));
	}

	function viewEmbedded($name){
		$form = $this->form->getPublishedWithName($name);
		if($form){
			$this->_view($form, true);
		}else{
			echo "<h1>No form exists with the name \"$name\"!</h1>";
		}
	}

	function postForm($id){
		$post = $this->input->post(NULL, true);
		if(!empty($post) && $post['f2token'] == $_SESSION['f2']['form_token']){
			//something was actually posted, get the form
			$form = $this->form->getById($id);

			//make sure that form was found
			if(!$form) $this->_failAuthResp('That form could not be found!');

			//make sure user was allowed to post that form
			if(!$form->published &&
				( !$this->authorization->can('edit', $form->name)
		 		&& !$this->authorization->can('admin', $form->name) )
			){
				$this->_failAuthResp('You must have edit or admin permissions on a form to view it\'s non-published versions!');
			}

			//check that this person has the right qualifications according to the form view settings itself
			$this->_checkViewRights($form);
		}else{
			$this->_failAuthResp('NOTHING WAS POSTED! Please fill the form out again!');
		}

		$this->_checkUniquePost($id);

		$post = $this->_doUploads($post); //if there were uploads, we have to handle them first
		//if we made it here it means we posted and have rights
		$result = $this->_saveResult($form);
		//these need to be run always
		$this->_bindFormSuccess('_doNotify');
		$this->_bindFormSuccess('_addAutoResultTags');		
		$this->_bindFormSuccess('_clearCapturedGetArgs');		
		$this->_bindFormSuccess('_doWorkflows');
		$this->_bindFormSuccess('_forwardResult');
		$this->_bindFormSuccess('_doRedirect');
		$this->_bindFormSuccess('_showFormResult');


		//provides a way of hijacking success behavior in other funcitons
		//as we go.
		$this->_doOnFormSuccess($form, $result);
	}

	function ajaxSearch(){
		$post = $this->input->post(NULL, true);
		if(isset($post['search'])){
			if($post['search'] == "*" or empty($post['search'])){
				$forms = $this->form->getAll('`name` ASC, `created` DESC');
			}else{ 
				$forms = $this->form->search($post['search']);
			}
			$html = $this->load->view('formlist', array('forms'=>$forms), true);
			echo json_encode(array('status'=>'success', 'html'=>$html));
		}else{
			echo json_encode(array('status'=>'fail'));
		}
	}


	private function _doUploads($post_data){
		if($_FILES && !empty($_FILES)){
			ini_set('upload_max_filesize', '100M');
			$config = array(
				'upload_path'=>'./uploads/',
				'allowed_types'=>'*',
				'max_size'=>'102400', //100M
				'max_filename'=>0,
				'encrypt_name'=>TRUE,
				'remove_spaces'=>TRUE,
			);
			$this->load->library('upload', $config);

			foreach($_FILES as $key=>$f){
				if(!$this->upload->do_upload($key)){
					$post_data[$key] = $this->upload->display_errors();
				}else{
					$data = $this->upload->data();
					$post_data[$key] = "<a href=\"".base_url()."uploads/".$data['file_name']."\">".$data['orig_name']."</a>";
				}
			}
		}

		return $post_data;
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
	* get get args and register them properly so we can know what is readonly or force-filled
	*/
	private function _captureGetArgs(){
		if(!empty($_GET)){
			$this->_clearCapturedGetArgs();
			foreach($_GET as $key=>$value){
				if(strpos($value, "~") !== 0){
					if(strpos($value, "|")) $value = explode("|", $value);
					$this->prefill->setForcefilled($key, $value); 
				}else{
					$count = 1;
					$value = str_replace("~", '', $value, &$count);
					if(strpos($value, "|")) $value = explode("|", $value);
					$this->prefill->setReadonly($key, $value);
				}
			}			
			$this->_redirect(str_replace("?".$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));
		}		
	}

	private function _doWorkflows($form){
		$this->load->library('workflows');
		$this->workflows->doWorkflows($form, $form->result);
	}

	/**
	* clears out what _captureGetArgs put in prefill (or really anything in there)
	*/
	private function _clearCapturedGetArgs(){
		$this->prefill->clearReadOnlys();
		$this->prefill->clearForcefilleds();		
	}

	/**
	* tries to make sure this isn't a dupicate post
	*/
	private function _checkUniquePost($form_id){
		$post = $this->input->post(NULL, true);
		if($_SESSION['f2']['posts'][$form_id]){
			if($_SESSION['f2']['posts'][$form_id] == $post){
				$this->_redirect(site_url('forms/indenticalPost/'.$form_id));
			}
		}
		$_SESSION['f2']['posts'][$form_id] = $post;
	}

	function indenticalPost($form_id){
		$post = $this->input->post(NULL, true);
		//catch case where someone might hit refresh on the identicalPost page!
		if($_SESSION['f2']['postconfirm'][$form_id]){
			unset($_SESSION['f2']['postconfirm'][$form_id]);
			$this->_redirect(base_url());
		}

		if($post['identconfirm'] == 'Yes'){
			$_SESSION['f2']['postconfirm'][$form_id] = true;
			//set post to what was posted
			$post = $_SESSION['f2']['posts'][$form_id];
			//unset the saved post so it doesn't warn again
			unset($_SESSION['f2']['posts'][$form_id]);
			//run function that handles form posts again
			$this->postForm($form_id);
		}elseif($post['identconfirm'] == 'No'){
			//they messed up or something, go back to blank form
			$this->_redirect(site_url('forms/viewid/'.$form_id));
		}else{
			$this->load->view('identicalpost', array('form_id'=>$form_id));
		}

	}

	/**
	* Saves form resutls to database
	*/
	private function _saveResult($form){
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
		$post = $this->input->post(NULL, true);
		$input_names = explode(',',$post['dependhiddeninputs']);
		if(is_array($input_names)){
			foreach($input_names as $name){
				$name = str_replace('[]','', $name);
				unset($post[$name]);
			}
		}
		$post = $this->_obscurePaymentData($post);
		return $post;		
	}

	private function _obscurePaymentData($post_data){
		if($post_data['card_accountNumber']){
			$post_data['card_accountNumber'] = '************'.substr($post_data['card_accountNumber'], (strlen($post_data['card_accountNumber']) - 4));
			unset($post_data['card_cvNumber']);
		}elseif($post_data['Account_Number']){
			$post_data['Account_Number'] = '************'.substr($post_data['Account_Number'], (strlen($post_data['Account_Number']) - 4));
			unset($post_data['Routing_Number']);
		}
		return $post_data;
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
	* Add any tags we might want to have automatically on certain results
	*/
	private function _addAutoResultTags($form){
		$this->load->model('resulttag');

		//add NEW tag to all submitted results
		$tags = $this->resulttag->getByTag('NEW');
		if(!empty($tags)){
			$this->resulttag->addToResult($tags[0]->id, $form->result->id, 'F2');
		}
	}

	/**
	*	Forwards form result to a URL via POST (cURL)
	* @param object $form
	*/
	private function _forwardResult($form){
		if(!empty($form->config->forward_to)){
			$this->load->library('simplecurl');
			//minimize duplicate data, but make everything available
			//in the most obvious fashion		
			$data['result'] = $form->result;
			unset($form->result);
			$data['form'] = $form;
			try{
				$this->simplecurl->post($form->config->forward_to, $data);
			}catch(Exception $e){
				//oh well, their URL is probably wrong...Silly them.
			}
		}
	}

	/**
	*	Redirects to a URL, storing data in session
	* @param object $form
	*/
	private function _doRedirect($form){
		if(!empty($form->config->redirect_to)){			
			//minimize duplicate data, but make everything available
			//in the most obvious fashion
			$_SESSION['f2']['lastresult'] = $form->result;
			unset($form->result);
			$_SESSION['f2']['lastform'] = $form;
			header('location: '.$form->config->redirect_to);
			exit;
			die("Should have location: ".$form->config->redirect_to);
		}
	}	

	/**
	* Shows user the form result when form is submitted
	* @param object $form
	*/
	private function _showFormResult($form){		
		//todo: detect when it shouldn't run!  ex: pass to external script instead...
		if($this->input->post('embedded_form') == 'true'){
			$embedded = true;
		}else{
			$embedded = false;
		}
		//$this->load->library('workflows');
		$this->load->view('result_form', array('form'=>$form, 'topmessage'=>$form->config->thankyou, 'embedded_form'=>$embedded, 'hide_management'=>true));
	}

	private function _doNotify($form){
		if(!empty($form->config->notify)){
			$this->load->library('email');
			$this->email->to(str_replace("\n", ', ', $form->config->notify));
			$now = date('Y-m-d H:i:s');
			$this->email->from('webservices@wallawalla.edu','FormIt2');
			$this->email->reply_to('noreply@wallawalla.edu','FormIt2'); 
			$subject = $form->name." submitted $now";
			if($this->authorization->username()){
				$subject.=" by ".$this->authorization->username();
				$message = $form->name." was submitted by ".$this->authorization->username().".\n";				
			}else{
				$message = $form->name." was submitted @ $now.\n";
			}
			$message.="You can view the submitted form here:\n";
			$message.=site_url('results/view/'.$form->result->id);
			$this->email->subject($subject);
			$this->email->message($message);
			$this->email->send();
		}
		
	}	

	/**
	* Make a new form
	*/
	function add(){
		$this->authorization->forceLogin();
		if(!$this->authorization->is('formcreator') && !$this->authorization->is('superadmin')){
			$this->_failAuthResp('You must have the "formcreator" role to create new forms!  Access Denied!');
		}
		$post = $this->input->post();
		if(empty($post)){ //todo: server-side validation!
			//ask user to create the form
			$this->load->view('add_form');			
		}else{
			$form = $post;
			$form = $this->_saveForm($form);
			$this->_redirect(site_url('forms/edit/'.$form->id));
			//print_r($form);
		}		
	}

	/**
	* Edit an existing form
	*/
	function edit($name_or_id=false){		
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

	/**
	* Delete a form
	*/
	function delete($id){
		$this->authorization->forceLogin();		
		$form = $this->form->getById($id);

		if($form->creator != $this->authorization->username() && !$this->authorization->can('admin', $form)){
			$this->_failAuthResp('You do not have sufficient rights to delete this form!  You must be the creator of this form, or have the admin permission on this form!');
			return;
		}

		if($this->input->post('deleteconfirm') == 'yes') {
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

	/**
	* Publish a form (make it viewable by people)
	*/
	function publish($id){
		$this->authorization->forceLogin();
		$form = $this->form->getById($id);

		if($form->creator != $this->authorization->username() && !$this->authorization->can('edit', $form) && !$this->authorization->can('admin', $form)){
			$this->_failAuthResp('You do not have sufficient rights to publish this form!  You must be the creator of this form, or have edit or admin permissions on this form!');
			return;
		}

		if($this->input->post('publishconfirm') == 'yes'){
			$this->form->publish($form->id);
			$this->load->view('redirect', array('location'=>'forms/view/'.$form->name, 'message'=>'Form id:'.$form->id.' published successfully!'));
			return;
		}else{
			$this->load->view('publish_form', array('form'=>$form));
			return;
		}
	}

	/**
	* Manage form named $name
	* (edit perms, see versions, etc...)
	*/
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

	/**
	* saves the form configuration (ajax)
	*/
	function saveconfig($id){
		if(!$this->authorization->isLoggedIn()){
			echo json_encode(array('loggedout'=>true));
			return;
		}

		$tmp = $this->form->getById($id);
		if(!$this->authorization->can('edit', $tmp) && !$this->authorization->can('admin', $tmp)){
			echo json_encode(array('insufficientpermissions'=>true));
			return;
		}

		$post = $this->input->post();

		if(!empty($post) and $id){
			$form = $post;
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

	function rename($form_name){
		$this->authorization->forceLogin();
		$forms = $this->form->getByName($form_name);
		$form = $forms[0];
		if($form->creator != $this->authorization->username() && !$this->authorization->can('edit', $form) && !$this->authorization->can('admin', $form)){
			$this->_failAuthResp('You do not have sufficient rights to rename this form!  You must be the creator of this form, or have edit or admin permissions on this form!');
			return;
		}

		if($this->input->post('renameconfirm') == 'yes'){
			$this->form->rename($form_name, $this->form->input('new_form_name') );
			$this->load->view('redirect', array('location'=>'forms/view/'.$this->input->post('new_form_name'), 'message'=>'Forms renamed successfully!'));
			return;
		}else{
			$this->load->view('rename_form', array('form'=>$form));
			return;
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
	function results($name_or_id=false){
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
		$this->load->model('resulttag');
		$formresults = array();
		$global_result_tags = array();
		$form_ids = array();
		foreach ($forms as $f) {
			$form_ids[] = $f->id;
		}		
		$formresults = $this->result->getOnForms($form_ids);		
		foreach($formresults as $fr){
			$fr->resulttags = $this->resulttag->getByResult($fr->id);
			//array_merge couldn't get me there!  Had to write my own!
			foreach($fr->resulttags as $rt){
				if(!in_array($rt, $global_result_tags)){
					$global_result_tags[] = $rt;
				}
			}
		}
		
	
		$this->load->view('formresults', array('forms'=>$forms, 'formresults'=>$formresults, 'resulttags'=>$global_result_tags));		
	}

	/**
	* I'm pretty sure this is going to only be handled by the results controller...
	*/
	function viewresult($result_id){
		$result = $this->result->getById($id);
		$form = $this->form->getById($result->form_id);
		$form->questions = $this->_getQuestions($form->id);
		$form->result = $result;
		$this->load->view('result_form', array('form'=>$form, 'hide_management'=>true));
	}

	/**
	* Saves a partially completed form as a draft
	* @param string $id What form the draft is from
	*/
	function saveDraft($id){
		$post = $this->input->post(NULL, true);
		if(!empty($post)){
			$this->load->model('draft');
			$data = array(
				'form_id'=>$id,
				'post'=>json_encode($this->_obscurePaymentData($post['formdata'])),
				'readonlys'=>json_encode($this->prefill->getReadonlys()),
				);			
			$draft = $this->draft->insert($data);
			echo json_encode(array('status'=>'success', 'url'=>site_url('forms/draft/'.$draft->id)));		
		}
	}

	/**
	* Gets draft and populates all info it needs to into prefill lib,
	* Then redirects to the form so you can finish filling it out.
	*/
	function draft($id){
		$this->load->model('draft');
		$draft = $this->draft->getById($id);
		if($draft){
			if(!empty($draft->readonlys)){
				$this->prefill->setReadOnlys((array) json_decode($draft->readonlys));
			}
			$this->prefill->setForcefilleds((array) json_decode($draft->post));
			$this->_redirect(site_url('forms/viewid/'.$draft->form_id));
		}else{
			$this->_failAuthResp("The draft '$id' does not exist!");
		}
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
	* Validate a submitted form (might never care to do this)
	*/
	private function _validateForm(){

	}
}
?>