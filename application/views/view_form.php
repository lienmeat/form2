<?php
	$this->load->view('header', array('title'=>$name, 'banner_text'=>'Fill out this form or else!'));
	$this->load->view('JS/validation.js');
	//$this->load->view('JS/dependencies.js');
?>

<!-- form header common to all forms -->
<form name="<?php echo $name; ?>" id="<?php echo $id; ?>" method="POST">
<div class="form_title">

</div>

<?php
//probably shouldn't have called questions questions,
//because there is other crap in forms than questions....
foreach($questions as $question){
	if($question->config->type == 'info'){
		$this->load->view('question/view_info', (array) $question);
	}elseif($question->config->type == 'heading'){
		$this->load->view('question/view_heading', (array) $question);
	}elseif($question->config->type == 'workflow'){
		$this->load->view('question/view_workflow', (array) $question);		
	}else{
		$this->load->view('question/view_question', (array) $question);
	}
}
?>

<!-- form footer -->
<div class="form_footer">
	<!-- Maybe a standard submit footer or something...idk -->
</div>
</form>
<?php
	$this->load->view('footer');
?>