<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Workflows extends MY_Controller{

/*	
	function view($id){
		$this->load->model('form');
		$result = $this->result->getById($id);
		$form = $this->form->getById($result->form_id);
		$form->questions = $this->_getQuestions($form->id);
		$form->result = $result;
		$form->result->post = json_decode($form->result->post);
		$form = $this->getWorkflowData($form);
		$this->load->view('result_form', array('form'=>$form));
	}
*/


	private function _getWorkflowData($form){
		$this->load->library('workflows');
		//initialize next step in workflows, whatever it may be
		$this->workflows->doWorkflows($form, $form->result);

		//load up all the workflow table entries into the respective elements/questions
		if($form->questions){
			foreach($form->question as $question) {
				if($question->config->type == 'workflow'){
					$question->workflow = $this->workflows->getWorkflowOnQuestionAndResult($question->id, $form->result->id);
				}
			}
		}
		return $form;
	}

}
?>