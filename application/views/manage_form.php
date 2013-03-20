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
echo "<script type=\"text/javascript\" src=\"".base_url()."application/views/JS/permissionsui.js\"></script>";
?>

<script>
var perms_ui_data = {
	form_name: "<?php echo $forms[0]->name; ?>",
	users: <?php echo json_encode($users_with_perms); ?>,
};

$(document).ready(
	function(){
		var form_perms = new PermissionsUI('form_perms', perms_ui_data);		
	}
);
</script>

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

<div id="form_perms_contain" class="section">
	<div id="form_perms_heading" class="section_heading"><a href="javascript:void(0);" onclick="toggleView('#form_perms');">Permissions:</a></div>
	<div id="form_perms">		
	</div>
</div>
<?php
$this->load->view('footer');
?>