<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
		<?php
		$fullname = $this->authorization->fullname();
		if($fullname){
			$validation = "required|equals[{$fullname}]";
		}else{
			$validation = "required|min_length[3]";
		}
		$ts = date('Y-m-d H:i:s');
		$config = $question->config;
		$config->type = 'text';		
		$this->inputs->setConfig($config);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('validation', $validation);
		$this->inputs->setAttribute('id', $question->id."_input0");
		echo $this->inputs.$fullname."&nbsp;&nbsp;".$ts;

		//save timestamp for later viewing
		$config = (object) array(
			'type'=>'hidden',
			'name'=>$question->config->name."_ts",
			'value'=>$ts,			
		);
		$this->inputs->setConfig($config);
		echo $this->inputs;
		?>
	</div>
</div>