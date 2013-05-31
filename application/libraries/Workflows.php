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
			}else{
				if(!$row->completed){
					$first_non_completed = false;
				}
			}
		}		
	}

	/**
	* Do a specific workflow
	* @param object $question
	* @param object $workflow Row representing the workflow
	*/
	function doWorkflow($question, $workflow){
		$workflow_id = $workflow->id;
		$stock_body = "A form response with a workflow requires your attention: ".site_url('workflows/view/'.$workflow_id);
		//todo: whatever needs to be done to workflows....
		if(!empty($question->config->email_addresses) && !$workflow->notif_sent){
			$this->CI->load->library('email');
			//email notification
			$to = implode(", ", $question->config->email_addresses);
			$message = $question->config->email_body.$stock_body;
			$subject = $question->config->email_subject;
			$this->CI->email->from('webservices@wallawalla.edu','FormIt2');
			$this->CI->email->reply_to('noreply@wallawalla.edu','FormIt2');
			$this->CI->email->to($to);
			$this->CI->email->subject("FormIt2 workflow $workflow_id");
			$this->CI->email->message($message);
			$this->CI->email->send();			
			$workflow->notif_sent = 1;
			$this->wf->update($workflow);

		}
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

	/**
	* Set the response on a workflow
	* @param string $workflow_id
	* @param string $decision Decision that was made
	* @param string $comments Comments made with this decision.
	* @return object|bool workflow row if existing
	*/
	function setResponse($workflow_id, $decision, $comments){
		$timestamp = date('Y-m-d H:i:s');
		$workflow = $this->wf->getById($workflow_id);		
		$response = json_encode(array(
			'decision'=>$decision,
			'comments'=>$comments,
			'timestamp'=>$timestamp,
		));
		
		$workflow->response = $response;
		$workflow->completed = 1;
		if($workflow->log){
			$log = json_decode($workflow->log);
		}else{
			$log = array();
		}
		$log[] = array('timestamp'=>$timestamp, 'event_type'=>'response', 'username'=>$this->CI->authorization->username(), 'response'=>$response);
		$workflow->log = json_encode($log);
		return $this->wf->update($workflow);
	}

	/**
	* Forward a workflow
	*/
	function forward($workflow_id, $email_addresses, $message){
		$this->CI->load->library('email');
		$workflow = $this->wf->getById($workflow_id);
		$to = str_replace("\n", ", ", $email_addresses);
		$message = "You have been forwarded a form response with a workflow: ".site_url('workflows/view'.$workflow_id)."\n".$message;

		$this->CI->email->from('webservices@wallawalla.edu','FormIt2');
		$this->CI->email->reply_to('noreply@wallawalla.edu','FormIt2');
		$this->CI->email->to($to);
		$this->CI->email->subject("FormIt2 workflow $workflow_id");
		$this->CI->email->message($message);
		$this->CI->email->send();

		if($workflow->log){
			$log = json_decode($workflow->log);
		}else{
			$log = array();
		}
		$log[] = array('timestamp'=>date('Y-m-d H:i:s'), 'event_type'=>'forward', 'username'=>$this->CI->authorization->username(), 'to'=>$to);
		$workflow->log = json_encode($log);
		return $this->wf->update($workflow);
	}
}

?>