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
?>
<li class="form_row edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>" <?php echo $depend; ?>>
	<?php 
		if($question->config->type)
			$this->load->view('element/edit_'.$question->config->type, array('question'=>$question));
	?>
</li>