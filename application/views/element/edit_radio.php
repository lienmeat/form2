<div class="form_question edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">	
	<?php
		$this->load->view('question_edit_tools', array('question'=>$question));
	?>	
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  edit_mode <?php echo $question->config->name; ?>_fi2">
		<?php
		if($question->config->options->dataprovider){
			$question->config->options = $this->dataprovider->run($question->config->options->dataprovider->method);
		}		
		$radio_input_count = 0;
		if(!$question->config->options) $question->config->options = array();
		foreach($question->config->options as $label=>$value){
			$input = array(
				'name'=>$question->config->name,
				'type'=>'radio',
				'value'=>$value,
				'label'=>$label,
				'selected'=>$question->config->selected,
			);		

			$this->inputs->setConfig($input);

			$this->inputs->setAttribute('validation', $question->config->validation);
			$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');			
			$this->inputs->setAttribute('id', $question->id."_input".$check_input_count);
			echo $this->inputs;
			$check_input_count++;
		}
		unset($radio_input_count);
		?>
	</div>
</div>