<?php
	$menu = array(
		anchor('forms/results/'.$form->name, 'View Results'),
		anchor('forms/edit/'.$form->name, 'Edit Form'),
		anchor('forms/manage/'.$form->name, 'Manage Form'),
	);
	
	$this->load->view('header', array('title'=>$form->name, 'banner_menu'=>$menu, 'embedded'=>$embedded_form));
	
	echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/jquery.eventually.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/dependencies.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
	echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/tiny_mce/jquery.tinymce.js\"></script>";	
	echo "<link rel=\"stylesheet\" href=\"".base_url()."/application/views/JS/tiny_mce/themes/simple/skins/default/ui.css\">";
?>
<script type="text/javascript">
	$(document).ready(function(){
		var form_deps = new Dependencies('<?php echo $form->id; ?>');
		var form_val = new Validation('<?php echo $form->id; ?>');
		$(document).tinymce({
			script_url : '<?php echo base_url();?>/application/views/JS/tiny_mce/tiny_mce.js',
			mode : "none",
			theme : "advanced",			
			plugins : "autosave"
		});
	});
	
	
</script>
<div class="form_title_contain edit_mode">
	<h2 id="form_title"><?php echo $form->title." ($form->name)"; ?></h2>
</div>
<!-- form header common to all forms -->
<form name="<?php echo $form->name; ?>" id="<?php echo $form->id; ?>" method="POST"  action="<?php echo site_url('forms/postForm/'.$form->id); ?>" enctype="multipart/form-data">

	<input type="hidden" name="f2token" value="<?php echo $_SESSION['f2']['form_token'];?>">
<?php	
	if($embedded_form){
		echo "<input type=\"hidden\" name=\"embedded_form\" value=\"true\">";
	}
?>

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
				<input type="submit" name="submit_fi2" value="Submit">&nbsp;&nbsp;<input type="button" onclick="window.print();" value="Print" />&nbsp;&nbsp;<input type="button" onclick="saveDraft('<?php echo $form->id; ?>');" value="Save as Draft" />
			</div>
		</div>
	</li>
</ul>

</form>
<?php
	$this->load->view('footer', array('embedded'=>$embedded_form));
?>
