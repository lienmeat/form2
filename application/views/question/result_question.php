<li class="form_row result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>">
	<?php $this->load->view('element/result_'.$question->config->type, array('question'=>$question)); ?>
</li>