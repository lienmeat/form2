<?php
if($question->config->dependencies){
	$depend = "dependencies=\"{$question->config->dependencies}\"";
}else{
	$depend = '';
}
if($question->config->text){
	$question->config->text = str_replace("[style]", "<style>", str_replace("[/style]", "</style>", $question->config->text));
}
if($question->config->alt){
	$question->config->alt = str_replace("[style]", "<style>", str_replace("[/style]", "</style>", $question->config->alt));
}
if($question->config->visibility == 'hidden'){
	$visibility = 'visibility_hidden ';
}
?>
<li class="form_row view_mode <?php echo $visibility.$question->config->name; ?>_fi2" id="<?php echo $question->id; ?>" <?php echo $depend; ?>>
	<?php $this->load->view('element/view_'.$question->config->type, array('question'=>$question)); ?>
</li>