<div class="form_information edit_mode" id="<?php echo $question->id; ?>_question">
	<?php
		$this->load->view('question_edit_tools', array('question'=>$question));
	?>		
	<div class="form_information_text"><?php echo $question->config->text; ?></div>
</div>