<?php
if(!$forms) $forms = array();
if(!$roles) $roles = array();
$menu_items = array(
	anchor('forms/view/'.$forms[0]->name, 'View Form'),
	anchor('forms/edit/'.$forms[0]->name, 'Edit Form'),
	anchor('forms/results/'.$forms[0]->name, 'View Results'),
);
$headdata = array(
	'title'=>'Manage '.$forms[0]->name,
	'banner_text'=>"Manage {$forms[0]->title} ({$forms[0]->name})",
	'banner_menu'=>$menu_items
);
$this->load->view('header', $headdata);
?>

<script>
function toggleView(selector){	
	if($(selector).css('display') != 'none'){
		$(selector).hide();
	}else{
		$(selector).show();
	}
}
</script>

<!--
echo "<h1>Someday this will be something akin to the /form/my/ page, but better.</h1>
".anchor('forms/add', 'Make a Form')."
<ul>";
foreach ($forms as $form) {
	if($form->published and $form->published != "0000-00-00 00:00:00") $published = "&nbsp;(published)";
	else $published = "";
	echo "<li>ID: $form->id ".$form->name.$published.'&nbsp;&nbsp;<a href="'.site_url('forms/viewid/'.$form->id).'">View</a>&nbsp;<a href="'.site_url('forms/edit/'.$form->id).'">Edit</a>&nbsp;<a href="'.site_url('forms/results/'.$form->name).'">Results</a></li>';
}
echo "</ul><br />".anchor('forms/add', 'Make a Form');
-->

<div id="form_versions_contain" class="section">
	<div id="form_versions_heading" class="section_heading"><a href="javascript:void(0);" onclick="toggleView('#form_versions');">Versions:</a></div>
	<ul id="form_versions">
	<?php
	foreach ($forms as $form){

		//create ability to View, View Results, Edit, Publish, Delete, right from a row showing basic info about it.
		if($form->published and $form->published != "0000-00-00 00:00:00"){
			$published = "class=\"published\"";
			$p_div = "<div class=\"pub_div\">PUBLISHED</div>";		
		}else{
			$published = "";
			$p_div = "";
		}
		echo "<li $published>ID: ".$form->id.'&nbsp;CREATED: '.$form->created.'&nbsp;&nbsp;<a href="'.site_url('forms/viewid/'.$form->id).'">View</a>&nbsp;<a href="'.site_url('forms/edit/'.$form->id).'">Edit</a>&nbsp;<a href="'.site_url('forms/results/'.$form->name)."\">Results</a>$p_div</li>";
		//echo '<li>'.$form->id.'</li>';
	}
	?>
	</ul>
</div>

<div id="form_roles_contain" class="section">
	<div id="form_roles_heading" class="section_heading"><a href="javascript:void(0);" onclick="toggleView('#form_roles');">Roles:</a></div>
	<ul id="form_roles">
	<?php
	foreach ($roles as $r){
		//View and administer roles from this list
		echo '<li>'.$r->id.'</li>';
	}
	?>
	</ul>
</div>
<?php
$this->load->view('footer');
?>