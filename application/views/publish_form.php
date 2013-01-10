<?php
$headdata = array('title'=>'Publish Form');
$this->load->view('header', $headdata);
?>
<h1>Publish this form?</h1>

<p>If you publish this form, it will be able to be filled out by navigating to "<?php echo base_url()."?f=".$form->name; ?>".  Any other published form of this name will be un-published!</p>
<form method="POST">	
<input type="radio" name="publishconfirm" value="yes"> Yes, publish this form!<br />
<input type="submit" name="del" value="Publish Form">
<input type="button" name="dumbname" value="No, this was a mistake!" onclick="window.location='<?php echo site_url('forms/edit/'.$form->id); ?>'">
</form>



<?php
$this->load->view('footer');
?>