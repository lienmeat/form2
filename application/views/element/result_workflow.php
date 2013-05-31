<?php
$workflow = $question->workflow;
$rf_out = '';

if($workflow->log){
	$log=json_decode($workflow->log);
	if(is_array($log)){
		$rf_out.="<div class=\"workflow_log_contain\"><fieldset><legend>Workflow Log:</legend>";
		$first=true;
		foreach($log as $l){
			if(!$first) $rf_out.="<hr>";
			else $first = false;
			if($l->event_type == "response"){
				$rf_out.=$this->load->view('workflow_log_response', array('log'=>$l), true);
			}else{
				$rf_out.=$this->load->view('workflow_log_forward', array('log'=>$l), true);
			}
		}
		$rf_out.="</fieldset></div>";
	}	
}

if(!empty($workflow)){
	if(empty($question->config->usernames) or in_array($this->authorization->username(), $question->config->usernames)){
		
		if($question->config->options && ( is_array($question->config->options) || is_object($question->config->options) ) ){
			$rf_out.="<form method=\"POST\" name=\"workflow\" action=\"".site_url('results/workflowresponse/'.$workflow->id)."\"><h2 class=\"workflow_instructions\">".$question->config->instructions."</h2><div>";
			foreach($question->config->options as $o){
				$config = array(
					'name'=>'response[decision]',
					'value'=>$o,
					'label'=>$o,
					'type'=>'radio',				
				);
				$this->inputs->setConfig($config);
				$rf_out.="<div class=\"input_multiple\">$this->inputs</div>";
			}
			$rf_out."</div>";

			$config = array(
				'name'=>'response[comments]',
				'value'=>'',
				'type'=>'textarea',
			);
			$this->inputs->setConfig($config);
			$rf_out.="<div><label>Comments:</label><br />".$this->inputs."</div>";

			$config = array(
				'name'=>'submitwfresp',
				'value'=>'Submit',
				'type'=>'submit',
			);
			$this->inputs->setConfig($config);
			$rf_out.="<div>".$this->inputs."</div>";
			$rf_out.="</form>";
		}

		if($question->config->allow_forwarding == 'Yes'){
			$rf_out.="<form method=\"POST\" name=\"workflow_forward\" action=\"".site_url('results/workflowfwd/'.$workflow->id)."\"><div><h2>You may forward this workflow to someone else.</h2>";
			$config = array(
				'name'=>'email_addresses',
				'value'=>'',
				'type'=>'textarea',
			);
			$this->inputs->setConfig($config);
			$rf_out.="<div><label>Email Addresses to forward this workflow to: (one per line)</label><br />".$this->inputs."</div>";
			$config = array(
				'name'=>'email_message',
				'value'=>'',
				'type'=>'textarea',
			);
			$this->inputs->setConfig($config);
			$rf_out.="<div><label>Instructions or explanation to include in email: </label><br />".$this->inputs."</div>";
			$config = array(
				'name'=>'submitwffwrd',
				'value'=>'Email',
				'type'=>'submit',
			);
			$this->inputs->setConfig($config);
			$rf_out.="<div>".$this->inputs."</div>";
			$rf_out.="</div></form>";
		}

	}
}



if(!empty($rf_out)){
	echo "<div class=\"form_answer result_mode ".$question->config->name."_fi2\" id=\"".$question->id."_answer\"><div class=\"form_element_contain  result_mode ".$question->config->name."_fi2\">".$rf_out."</div></div>";
}

?>