<?php
	$menu = array(
		anchor('forms/results/'.$form->name, 'View Results'),
		anchor('forms/edit/'.$form->name, 'Edit Form'),
		anchor('forms/manage/'.$form->name, 'Manage Form'),
	);
	$this->load->view('header', array('title'=>$form->name, 'banner_menu'=>$menu));
	//$this->load->view('JS/validation.js');
	//$this->load->view('JS/dependencies.js');
	$this->load->library('inputs');
	echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/dependencies.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
?>
<script type="text/javascript">
	$(document).ready(function(){
		var form_deps = new Dependencies('<?php echo $form->id; ?>');
		var form_val = new Validation('<?php echo $form->id; ?>');
	});
</script>
<div class="form_title_contain edit_mode">
	<h2 id="form_title"><?php echo $form->title." ($form->name)"; ?></h2>
</div>
<!-- form header common to all forms -->
<form name="<?php echo $form->name; ?>" id="<?php echo $form->id; ?>" method="POST">

<ul class="form_contain view_mode">

<?php
//probably shouldn't have called questions questions,
//because there is other crap in forms than questions....
foreach($form->questions as $question){
	$this->load->view('question/view_question', array('question'=>$question));
}
?>

	<li class="form_row form_footer" id="form_footer">
		<div class="form_question">
			<div id="question_id_question_text" class="form_question_text questionName_fi2">						
			</div>
		</div>
		<div class="form_answer">
			<div class="form_element_contain">
				<input type="submit" name="submit_fi2" value="Submit">
			</div>
		</div>
	</li>
</ul>

</form>
<?php
	$this->load->view('footer');
?>