<div class="form_question result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  result_mode <?php echo $question->config->name; ?>_fi2">
		<?php
		 	if($question->config->name){
				$name = $question->config->name;
				$name_ts = $name."_ts";			
				echo $formresult->post->$name." @ ".$formresult->post->$name_ts;
			}
		?>
	</div>
</div>