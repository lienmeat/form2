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

<div id="form_versions_contain" class="section">
	<div id="form_versions_heading" class="section_heading"><a href="javascript:void(0);" onclick="toggleView('#form_versions');">Versions:</a></div>
	<ul id="form_versions">
	<?php
	foreach ($forms as $f){
		//create ability to View, View Results, Edit, Publish, Delete, right from a row showing basic info about it.
		echo '<li>'.$f->id.'</li>';
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