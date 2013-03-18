<?php
$this->load->view('header', array('title'=>'F2 Dashboard', 'banner_text'=>'Dashboard: Search and manage your forms.'));

echo "<h1>Someday this will be something akin to the /form/my/ page, but better.</h1>
".anchor('forms/add', 'Make a Form')."
<ul>";
foreach ($forms as $form) {
	if($form->published and $form->published != "0000-00-00 00:00:00"){
		$published = "class=\"published\"";
		$p_div = "<div class=\"pub_div\">PUBLISHED</div>";		
	}else{
		$published = "";
		$p_div = "";
	}
	echo "<li $published>ID: $form->id ".$form->name.'&nbsp;&nbsp;<a href="'.site_url('forms/viewid/'.$form->id).'">View</a>&nbsp;<a href="'.site_url('forms/edit/'.$form->id).'">Edit</a>&nbsp;<a href="'.site_url('forms/results/'.$form->name)."\">Results</a>$p_div</li>";
}
echo "</ul><br />".anchor('forms/add', 'Make a Form');

$this->load->view('footer');

?>