<div class="form_question result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  result_mode <?php echo $question->config->name; ?>_fi2">
		<?php
			$name = $question->config->name;
			$post_data = $formresult->post->$name;
			if(is_array($post_data)){
				foreach($post_data as $data){
					if($data){
						echo $data.", ";
					}
				}
				
			}else{
				echo $post_data;
			}
		?>
	</div>
</div>