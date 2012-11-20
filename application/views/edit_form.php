<?php
	$this->load->view('header', array('title'=>$form->name, 'banner_text'=>"Edit \"$form->name\"!"));	
	$this->load->library('inputs');	
	$this->load->view('JS/edit_form');
	$this->load->view('JS/formeditor.php');
	//$this->load->view('CSS/edit_form');
?>

<style>

.form_question, .form_answer{
	min-width: 463px;
}

</style>

<script type="text/javascript">
$(document).ready(function(){ var form_questions_val = new Validation('form_questions_form'); });
$(document).ready(function(){ var form_config_val = new Validation('form_config_form'); });
</script>
<a href="javascript:void();" onclick="FormEditor.openEditForm();">Edit form configuration</a>
<div id="form_view_contain">	
	<div class="form_title edit">
		<h2><?php echo $form->title." ($form->name)"; ?></h2>
	</div>
	<form id="form_questions_form">
	<ul class="form_contain edit_mode" id="form_questions">
	<?php
	//probably shouldn't have called questions questions,
	//because there is other crap in forms than questions....
	if(!is_array($form->questions)) $form->questions = array();
	foreach($form->questions as $question){
		$this->load->view('question/edit_question', (array) $question);		
	}
	?>
	</ul>
	</form>
</div>

<div id="form_config_editor">
	<form name="form_config" method="POST" id="form_config_form">
		<ul class="form_contain">		
			<?php
				$this->load->view('config_form', array('form'=>$form, 'mode'=>'edit'));
			?>
		</ul>
	<?php //echo "<pre>".print_r($form, true)."</pre>"; ?>
	</form>
</div>

<div id="question_config_editor">
	<form id="question_config_form">
		<ul class="form_contain" id="question_type_contain">
		</ul>
		<ul class="form_contain" id="question_config_contain">
		</ul>
	</form>
</div>

<?php
	//print_r($form);
	$this->load->view('footer');
?>