<div class="form_question result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  result_mode <?php echo $question->config->name; ?>_fi2">
		<?php
			$name = $question->config->name;
			$value = $formresult->post->{$name};
			if($formresult->post->{$name."_".$value}){
				//echo the value of the write in (see view_radio.php for why this works)
				echo $formresult->post->{$name."_".$value};				
			}else{
				//echo the value of a regular radio
				echo $value;
			}
		?>
	</div>
</div>