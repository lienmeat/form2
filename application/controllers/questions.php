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

		$question = $this->question->insert(array('form_id'=>$form_id, 'order'=>'9999'));

	}



	/**
	* Edit an existing question
	*/
	function edit($id){
		$this->authorization->forceLogin();
		$this->load->library('inputs');
		$question = $this->question->getById($id);
		if(empty($question)) $question->type = 'text';
		$type_html = $this->load->view("question/config_question", array('question'=>$question), true);
		$elem_config_html = $this->load->view("element/config_".$question->config->type, array('question'=>$question), true);
		$data = array('html'=>array('question_type'=>$type_html, 'question_config'=>$elem_config_html));
		echo json_encode($data);
	}

	/**
	* Saves a question after editing config, echos out question's edit view on success
	*/
	function savequestion($id){				
		if(!empty($_POST) and $id){
			$question = $_POST;
			$question['id'] = $id;
			$question = $this->question->update($question);
		}
		//if it actually updates...		
		if(is_object($question)){
			$this->load->library('inputs');
			$html = $this->load->view('element/edit_'.$question->config->type, (array) $question, true);
			echo json_encode(array('status'=>'success', 'question_id'=>$question->id, 'html'=>$html));
		}else{//othwise notify of fail
			echo json_encode(array('status'=>'fail', 'question_id'=>$id, 'html'=>'<h3 class="Err">This question failed to save!</h3>'));
		}
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