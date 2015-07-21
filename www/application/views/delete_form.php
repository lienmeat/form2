<?php
$headdata = array('title'=>'Delete Form');
$this->load->view('header', $headdata);
?>
<h1>Delete this form?</h1>
<?php
if($form->published && $form->published != "0000-00-00 00:00:00"){
	echo "<h2 style=\"color: red;\">WARNING: This is a published form!  If you delete this form, nobody will be able to fill it out, and any links referencing ".$form->name." will be invalid!</h2>";
}
?>
<p>If you delete this form, the configuration, and it's questions, results, everything will be lost!  It will only affect this specific version of the form, however, so if there are other forms of the same name, it will not affect them.</p>
<form method="POST">	
<input type="radio" name="deleteconfirm" value="yes"> Yes, delete this form!<br />
<input type="submit" name="del" value="Delete Form">
<input type="button" name="dumbname" value="No, this was a mistake!" onclick="window.location='<?php echo site_url('forms/edit/'.$form->id); ?>'">
</form>



<?php
$this->load->view('footer');
?>