<li class="form_row edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>">
	<?php 
		if($question->config->type)
			$this->load->view('element/edit_'.$question->config->type, array('question'=>$question));
	?>
</li>