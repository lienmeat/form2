<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
		<?php		
		$textarea->config = $question->config;
		$textarea->config->type = 'textarea';
		$this->inputs->setConfig($textarea->config);
		if($textarea->config->dataprovider){
			$textarea->config->value = '';
		}
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input0");
		echo "<div><label>Available Answers: </label><br />".$this->inputs."</div>";		
		
		$select->config = $question->config;
		$select->config->type = 'select';
		$select->config->name="config[dataprovider][method]";

		if($select->config->dataprovider){
			$select->config->selected = array($select->config->dataprovider);
		}
		$select->config->options = $this->dataprovider->run('dataproviders');

		$this->inputs->setConfig($select->config);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input1");
		echo '<div>OR <label>Pre-defined Answers: </label><br />'.$this->inputs."</div>";		
		?>
	</div>
</div>