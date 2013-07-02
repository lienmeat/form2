<?php
$headdata = array('title'=>'Rename Form');
$this->load->view('header', $headdata);
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/jquery.eventually.js\"></script>";
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/dependencies.js\"></script>";
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
?>
<script type="text/javascript">
	$(document).ready(function(){
		var form_dep = new Dependencies('rename_form');
		var form_val = new Validation('rename_form');
	});
</script>
<h1>Rename this form?</h1>
<p>All forms with the name "<?php echo $form->name; ?>" will be renamed!</p>
<p>Any links to the form will be broken, and must be changed to use the new name!</p>
<form name="rename_form" id="rename_form" method="POST">
<input type="radio" id="renameconfirm" name="renameconfirm" value="yes" validation="required"> Yes, rename this form!<br />
<label for="new_form_name">New Name:</label><input type="text" id="new_form_name" name="new_form_name" validation="required|alpha_dash|newformname" dependencies="renameconfirm=yes"><br />

<input type="submit" name="del" value="Submit">
<input type="button" name="dumbname" value="No, this was a mistake!" onclick="window.location='<?php echo site_url('forms/edit/'.$form->id); ?>'">
</form>



<?php
$this->load->view('footer');
?>