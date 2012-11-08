<?php
	$this->load->view('header', array('title'=>$name, 'banner_text'=>'Edit this form or else!'));
	echo "<script type=\"text/javascript\" scr=\"".base_url()."application/views/JS/validation.js\"></script>";
	echo "<script type=\"text/javascript\" scr=\"".base_url()."application/views/JS/dependencies.js\"></script>";
	//$this->load->view('JS/formeditor.php', array('form_id'=>$id));
?>

<div id="form_view_contain">
	<!-- form header common to all forms -->
	<div class="form_title">

	</div>

	<?php
	//probably shouldn't have called questions questions,
	//because there is other crap in forms than questions....
	foreach($questions as $question){
		if($question->config->type == 'info'){
			$this->load->view('question/edit_info', (array) $question);
		}elseif($question->config->type == 'heading'){
			$this->load->view('question/edit_heading', (array) $question);
		}elseif($question->config->type == 'workflow'){
			$this->load->view('question/edit_workflow', (array) $question);		
		}else{
			$this->load->view('question/edit_question', (array) $question);
		}
	}
	?>

	<!-- form footer -->
	<div class="form_footer">
		<!-- Maybe a standard submit footer or something...idk -->
	</div>
</div>

<div id="form_config_editor"></div>

<div id="question_config_editor"></div>

<?php
	$this->load->view('footer');
?>