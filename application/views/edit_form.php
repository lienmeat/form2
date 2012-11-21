<?php
	$this->load->view('header', array('title'=>$form->name, 'banner_text'=>"Edit \"$form->name\"!"));	
	$this->load->library('inputs');	
	$this->load->view('JS/edit_form');
	$this->load->view('JS/formeditor.php', array('form_id'=>$form->id));
	//$this->load->view('CSS/edit_form');
?>
<style>

.form_question, .form_answer{
	min-width: 463px;
}

</style>

<a href="javascript:void(0);" onclick="FormEditor.openEditForm();">Edit form configuration</a>
<div id="form_view_contain">	
	<div class="form_title_contain edit_mode">
		<h2 id="form_title"><?php echo $form->title." ($form->name)"; ?></h2>
	</div>
	<form id="form_questions_form">
	<div id="add_question_tool_contain">
		<span class="icon">
			<img src="<?php echo base_url(); ?>application/views/IMG/arrow-insert.gif" onclick="FormEditor.addQuestion(false);"/>
		</span>
	</div>
	<ul class="form_contain edit_mode" id="form_questions">
	<?php
	//probably shouldn't have called questions questions,
	//because there is other crap in forms besides questions....
	if(!is_array($form->questions)) $form->questions = array();
	
	foreach($form->questions as $question){
		$this->load->view('question/edit_question', array('question'=>$question));		
	}

	?>
	</ul>
	</form>
</div>

<div id="form_config_editor">
	<form name="form_config" method="POST" id="form_config_form">
		<ul id="form_config_contain" class="form_contain">		
			<?php
				$this->load->view('config_form', array('form'=>$form, 'mode'=>'edit'));
			?>
		</ul>	
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
	
	$this->load->view('footer');
?>