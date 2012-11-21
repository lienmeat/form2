<?php
	$this->load->view('header', array('title'=>$form->name, 'banner_text'=>'Fill out this form or else!'));
	//$this->load->view('JS/validation.js');
	//$this->load->view('JS/dependencies.js');
	$this->load->library('inputs');
	echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
?>
<script type="text/javascript">
	$(document).ready(function(){
		var form_val = new Validation('<?php echo $form->id; ?>');
	});
</script>

<!-- form header common to all forms -->
<form name="<?php echo $form->name; ?>" id="<?php echo $form->id; ?>" method="POST">
<div class="form_title">

</div>

<ul class="form_contain view_mode">

<?php
//probably shouldn't have called questions questions,
//because there is other crap in forms than questions....
foreach($form->questions as $question){
	$this->load->view('question/view_question', array('question'=>$question));
}
?>

</ul>

<!-- form footer -->
<div class="form_footer">
	<!-- Maybe a standard submit footer or something...idk -->
</div>
</form>
<?php
	$this->load->view('footer');
?>