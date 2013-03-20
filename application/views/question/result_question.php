<?php
if($question->config->text){
	$question->config->text = str_replace("[style]", "<style>", str_replace("[/style]", "</style>", $question->config->text));
}
if($question->config->alt){
	$question->config->alt = str_replace("[style]", "<style>", str_replace("[/style]", "</style>", $question->config->alt));
}
?>
<li class="form_row result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>" >
	<?php $this->load->view('element/result_'.$question->config->type, array('question'=>$question, 'formresult'=>$formresult)); ?>
</li>