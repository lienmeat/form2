<?php
$headdata = array('title'=>'Browse Forms', 'banner_text'=>'Browse and search for forms.');
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
	/*
	if($f->published){
		$published = "class=\"published\"";
		$p_div = "<div class=\"pub_div\">PUBLISHED</div>";
	}else{
		$published = "";
		$p_div = "";
	}
	*/
	$url = site_url('forms/view/'.$f->name);
	echo "<li><a href=\"$url\">".$f->title." (".$f->name.")</a>&nbsp;&nbsp;<a href=\"".site_url('forms/edit/'.$f->name)."\">[Edit]</a></li>";
}
echo "</ul>";
$this->load->view('footer');
?>