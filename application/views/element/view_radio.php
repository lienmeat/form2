<div class="form_question view_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_question">		
	<div class="form_question_text <?php echo $config->name; ?>_fi2"><?php echo $config->text; ?></div>
	<div class="form_question_alt <?php echo $config->name; ?>_fi2"><?php echo $config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $config->name; ?>_fi2">
		<?php
		$radio_input_count = 0;
		foreach($config->inputs as $input){
			$this->inputs->setConfig($input);
			$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$config->name.'_fi2');
			$this->inputs->setName($config->name);
			$this->inputs->setAttribute('id', $id."_input".$radio_input_count);
			echo $this->inputs;
			$radio_input_count++;
		}
		unset($radio_input_count);
		?>
	</div>
</div>