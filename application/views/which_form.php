<?php
$headdata = array('title'=>'Which Version?', 'banner_text'=>'Select which version of this form you mean!');
$this->load->view('header', $headdata);
if(!$forms) $forms = array();
echo "
<style>
.published{
	background-color: lightgreen;
	border-radius: 5px;
}
.pub_div{
	float: right;	
	color: green;
}
ul{
	list-style: none;
}
li{
	padding-right: 3px;
	padding-left: 3px;
}
</style>
<ul>";
foreach($forms as $f){
	if($f->published){
		$published = "class=\"published\"";
		$p_div = "<div class=\"pub_div\">PUBLISHED</div>";
	}else{
		$published = "";
		$p_div = "";
	}
	$url = str_replace(':form_id:', $f->id, site_url($returnpath));
	echo "<li $published><strong>ID:</strong><a href=\"$url\">".$f->id."</a>&nbsp;<strong>Title:</strong>".$f->title."&nbsp;<strong>Created:</strong>".$f->created."$p_div</li>";
}
echo "</ul>";
$this->load->view('footer');
?>