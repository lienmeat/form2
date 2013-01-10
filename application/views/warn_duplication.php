<?php
$headdata = array('title'=>'Are you sure?');
$this->load->view('header', $headdata);
?>
<h1>Duplicate this form?</h1>
<p>Because this form is already published, it can not be edited directly.  Do you wish to duplicate this form and it's contents?  You will then be able to edit the duplicate, and publish it when you are satisfied.</p>
<button onclick="window.location='<?php echo site_url('forms/edit/'.$form->id); ?>?doDuplicate=1'">Yes, duplicate and edit.</button>
<button onclick="window.location='<?php echo site_url('forms/edit/'.$form->name); ?>'">No, this was a mistake!</button>


<?php
$this->load->view('footer');
?>