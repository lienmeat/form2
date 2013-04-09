<?php
/**
* Handles doing things with workflows, like running them when a form is submitted, knowing what view to render (in result mode),
* and capturing input from people filling out workflow elements on a form result.
*/

class Workflows{
	protected $CI; //codeigniter instance
	protected $wf; //workflow model

	function __construct(){
		$this->CI =& get_instance();
		$this->CI->load->model('workflow');
		$this->wf =& $this->CI->workflow;
	}

	/**
	* Initiates the workflow process on a form, running the first workflow, or a subsequent one if one or more are marked "completed", notifying the person they need to pay attention
	* @param object $form Form object with questions property
	* @param object $result Form result object
	*/
	function doWorkflows($form, $result){
		$workflowelems = $this->getWorkFlowElementsFromForm($form);


		$workflowrows = $this->getWorkflowsForResult($result);

		$first_non_completed = true;
		foreach($workflowelems as $wfe) {
			$row = false;
			foreach($workflowrows as $wfr){
				if($wfe->id == $wfr->question_id){					
					$row = $wfr;
					break;
				}
			}
			if(!$row){ //if a row does not correspond to this element/question
				if($first_non_completed){
					//create the row
					$row = $this->addWorkflow($wfe->id, $result->id);
					//run this workflow as it's the first non-completed one!
					$first_non_completed = false;
					$this->doWorkflow($wfe, $row);
				}
			}
			/*
			else{
				if($first_non_completed && !$row->completed){
					//run this workflow as it's the first non-completed one!
					$first_non_completed = false;
					//$this->doWorkflow($wfe, $row);
				}
			}
			*/
		}		
	}

	/**
	* Do a specific workflow
	* @param object $question
	* @param object $workflow Row representing the workflow
	*/
	function doWorkflow($question, $workflow){
		//todo: whatever needs to be done to workflows....
		echo $workflow->id."<br />";
	}

	/** 
	* Inserts a new row associating a workflow element/question with a formresult
	* @param string $workflow_question_id ID of a $question_id relating to a workflow element
	* @param string $formresult_id ID of a formresult using this workflow
	* @return object
	*/
	function addWorkflow($workflow_question_id, $formresult_id){
		return $this->wf->insert(array('question_id'=>$workflow_question_id, 'formresult_id'=>$formresult_id));
	}

	/**
	* Get all the workflow elements/questions in the form
	* @param object $form Object with questions property set containing all questions
	* @return array Workflow elements/questions
	*/
	function getWorkFlowElementsFromForm($form){
		$workflows = array();
		if($form->questions){
			foreach($form->questions as $q){
				if($q->config->type == "workflow"){
					$workflows[] = $q;
				}
			}
		}
		return $workflows;
	}

	/**
	* Get a workflow row that belongs to both a question and result
	* @param string $question_id
	* @param string $formresult_id
	*/
	function getWorkflowOnQuestionAndResult($question_id, $formresult_id){
		$result = $this->wf->getOnQuestionAndResult($question_id, $formresult_id);
		if($result){
			return $result;
		}else{
			return false;
		}
	}


	/**
	* Get all the workflow records on a result
	* @param object $result
	* @param bool $completed Whether to return completed workflow rows
	* @return Array
	*/
	function getWorkflowsForResult($result){
		return $this->wf->getOnResult($result->id);
	}
}

?>