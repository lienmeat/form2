<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
		<?php		
		if($question->config->options->dataprovider){
			$question->config->options = array_merge(array('Select One'=>''), $this->dataprovider->run($question->config->options->dataprovider->method));
		}
		if(!$question->config->options) $question->config->options = array();
		$this->inputs->setConfig($question->config);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('validation', $question->config->validation);
		$this->inputs->setAttribute('id', $question->id."_input0");
		echo $this->inputs;
		?>
	</div>
</div>