<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
		<?php
		$check_input_count = 0;
		foreach($question->config->inputs as $input){
			$this->inputs->setConfig($input);
			$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
			$this->inputs->setName($question->config->name);
			$this->inputs->setAttribute('id', $question->id."_input".$check_input_count);
			echo $this->inputs;
			$check_input_count++;
		}
		unset($check_input_count);
		?>
	</div>
</div>