<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Results extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('result');
	}

	function view($id){
		$this->load->model('form');
		$this->load->model('resulttag');
		$result = $this->result->getById($id);
		$result->resulttags = $this->resulttag->getByResult($result->id);
		$form = $this->form->getById($result->form_id);
		$form->questions = $this->_getQuestions($form->id);		
		$form->result = $result;
		$form->result->post = json_decode($form->result->post);
		$form = $this->_getWorkflowData($form);
		$this->load->view('result_form', array('form'=>$form));
	}


	private function _getWorkflowData($form){
		$this->load->library('workflows');
		//initialize next step in workflows, whatever it may be
		$this->workflows->doWorkflows($form, $form->result);

		//load up all the workflow table entries into the respective elements/questions
		if($form->questions){
			foreach($form->questions as $question) {
				if($question->config->type == 'workflow'){
					$question->workflow = $this->workflows->getWorkflowOnQuestionAndResult($question->id, $form->result->id);
				}
			}
		}
		return $form;
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
	* Handles the answering of a workflow element
	* @param string $workflow_id
	*/
	function workflowresponse($workflow_id){
		$this->load->library('workflows');
		if($_POST['response']['decision']){
			$resp = $this->workflows->setResponse($workflow_id, $_POST['response']['decision'], $_POST['response']['comments']);
			$this->_splash('Your response was saved! Thank you!', $_SERVER['HTTP_REFERER']);			
		}else{
			$this->_splash('Something went really wrong!', $_SERVER['HTTP_REFERER'], 10, 'red');
		}
	}

	/**
	* Handles the forwarding of a workflow element
	* @param string $workflow_id
	*/
	function workflowfwd($workflow_id){
		$this->load->library('workflows');
		if($_POST['email_addresses']){
			$resp = $this->workflows->forward($workflow_id, $_POST['email_addresses'], $_POST['email_message']);
			$this->_splash('This workflow has been forwared!  Thank you!', $_SERVER['HTTP_REFERER']);
		}else{
			$this->_splash('Something went really wrong!', $_SERVER['HTTP_REFERER'], 10, 'red');
		}	
	}
}
?>