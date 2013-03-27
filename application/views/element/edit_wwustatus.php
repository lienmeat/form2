<div class="form_question edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">
	<?php
		$this->load->view('question_edit_tools', array('question'=>$question));
	?>		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>	

<div class="form_answer edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain edit_mode <?php echo $question->config->name; ?>_fi2">
		<?php
		$question->config->value = $this->authorization->status();
		$question->config->type = 'text';
		$this->inputs->setConfig($question->config);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input0");
		$this->inputs->setAttribute('validation', $question->config->validation);
		echo $this->inputs;
		?>
	</div>
</div>