<?php
if($question->config->dependencies){
	$depend = "dependencies=\"{$question->config->dependencies}\"";
}else{
	$depend = '';
}
?>
<li class="form_row result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>" <?php echo $depend; ?>>
	<?php $this->load->view('element/result_'.$question->config->type, array('question'=>$question, 'formresult'=>$formresult)); ?>
</li>