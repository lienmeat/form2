<?php
$this->load->view('header', array('title'=>'FormIt2 Dashboard', 'banner_text'=>'Dashboard: Search and manage your forms.'));

echo "<h1>Someday this will be something akin to the /form/my/ page, but better.</h1>
".anchor('forms/add', 'Make a Form')."
<ul>";
foreach ($forms as $form) {
	if($form->published and $form->published != "0000-00-00 00:00:00") $published = "&nbsp;(published)";
	else $published = "";
	echo "<li>ID: $form->id ".$form->name.$published.'&nbsp;&nbsp;<a href="'.site_url('forms/viewid/'.$form->id).'">View</a>&nbsp;<a href="'.site_url('forms/edit/'.$form->id).'">Edit</a>&nbsp;<a href="'.site_url('forms/results/'.$form->name).'">Results</a></li>';
}
echo "</ul><br />".anchor('forms/add', 'Make a Form');

$this->load->view('footer');

?>