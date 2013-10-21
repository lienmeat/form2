<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
		<?php		
		$this->inputs->setConfig($question->config);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('validation', $question->config->validation);
		if($question->config->size){
			$this->inputs->setAttribute('size', $question->config->size);
		}else{
			$this->inputs->setAttribute('size', '20');
		}
		if($question->config->maxlength){
			$this->inputs->setAttribute('maxlength', $question->config->maxlength);
		}
		$this->inputs->setAttribute('id', $question->id."_input0");
		echo $this->inputs;
		?>
	</div>
</div>