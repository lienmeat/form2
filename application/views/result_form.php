<?php
	$this->load->view('header', array('title'=>$name, 'banner_text'=>'Look at this submitted form or else!'));
	//$this->load->view('JS/dependencies.js');
?>

<!-- form header common to all forms -->
<div class="form_title">

</div>

<?php
//probably shouldn't have called questions questions,
//because there is other crap in forms than questions....
foreach($questions as $question){
	if($question->config->type == 'info'){
		$this->load->view('question/result_info', (array) $question);
	}elseif($question->config->type == 'heading'){
		$this->load->view('question/result_heading', (array) $question);
	}elseif($question->config->type == 'workflow'){
		$this->load->view('question/result_workflow', (array) $question);		
	}else{
		$this->load->view('question/result_question', (array) $question);
	}
}
?>

<!-- form footer -->
<div class="form_footer">
	<!-- Maybe a standard submit footer or something...idk -->
</div>
<?php
	$this->load->view('footer');
?>