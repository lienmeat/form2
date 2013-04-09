<div class="form_question result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  result_mode <?php echo $question->config->name; ?>_fi2">
		<?php
			//has 2 modes.  Complete and Incomplete
			$workflow = $this->workflows->getWorkflowOnQuestionAndResult($question->id, $formresult->id);
			if($workflow && $workflow->completed){
				//show the completed workflow
				$this->load->view('element/result_workflow_complete', array('question'=>$question, 'formresult'=>$formresult, 'workflow'=>$workflow));
			}elseif($workflow){
				//show the view to complete the workflow
				$this->load->view('element/result_workflow_incomplete', array('question'=>$question, 'formresult'=>$formresult, 'workflow'=>$workflow));
			}else{
				//workflow doesn't exist/yet...
				echo "...pending other workflows...";
			}
			/*
			$name = $question->config->name;
			echo $formresult->post->$name;
			*/
		?>
	</div>
</div>