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

		//create a new blank question
		$question = $this->question->insert(array('form_id'=>$form_id, 'order'=>'9999', 'config'=>array('type'=>'text')));
		
		//array to hold order of question ids after insert
		$orders = array();

		if(!$_POST['below_question_id'] or $_POST['below_question_id'] == "false"){
			//should be at the beginning...
			$orders[] = $question->id;
		}

		//reorder the questions based on the position of new question
		$questions = $this->_getByForm($form_id);
		foreach($questions as $q){
			$orders[] = $q->id;
			//if the current question's id is just before where we inserted it
			//insert the question in order directly after it
			if($q->id == $_POST['below_question_id']){
				$orders[] = $question->id;
			}
		}
		
		//actually do the reorder on the db
		$this->question->reorder($orders);

		//render parts that edit needs
		$this->load->library('inputs');
		$edit_html = $this->load->view("question/edit_question", array('question'=>$question), true);	
		$type_html = $this->load->view("question/config_question", array('question'=>$question), true);
		$elem_config_html = $this->load->view("element/config_".$question->config->type, array('question'=>$question), true);
		$data = array('question'=>$question, 'html'=>array('question_edit'=>$edit_html,'question_type'=>$type_html, 'question_config'=>$elem_config_html));
		echo json_encode($data);
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
			//print_r($_POST);
			$question['id'] = $id;
			$question = $this->question->update($question);
		}
		//print_r($question);
		//if it actually updates...		
		if(is_object($question)){
			$this->load->library('inputs');
			$html = $this->load->view('element/edit_'.$question->config->type, array('question'=>$question), true);
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