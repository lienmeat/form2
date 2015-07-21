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
		$this->inputs->setAttribute('id', $question->id."_input0");
		if($question->config->rows){
			$this->inputs->setAttribute('rows', $question->config->rows);
		}else{
			$this->inputs->setAttribute('rows', '5');
		}
		if($question->config->cols){
			$this->inputs->setAttribute('cols', $question->config->cols);
		}else{
			$this->inputs->setAttribute('cols', '50');
		}
		if($question->config->maxlength){
			$this->inputs->setAttribute('maxlength', $question->config->maxlength);
		}
		echo $this->inputs."<span class=\"icon rtebutton\" onclick=\"openRTE('".$question->id."_input0', this);\">Toggle RTE</span>";
		?>
	</div>
</div>