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
}
?>