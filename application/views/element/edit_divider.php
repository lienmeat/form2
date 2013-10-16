<div class="form_divider edit_mode <?php echo $question->config->divider_bg_class; ?>" id="<?php echo $question->id; ?>_question">
	<?php
		$this->load->view('question_edit_tools', array('question'=>$question));
	?>		
	<div class="form_divider_text">
	<<?php echo $question->config->heading; ?>>
	<?php
		echo $question->config->text;
	?>
	</<?php echo $question->config->heading; ?>>
	</div>
</div>