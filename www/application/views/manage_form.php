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
<p>You can link to the published version of this form with the following URL: <br /><input readonly size="100" value="<?php echo base_url().$forms[0]->name; ?>"></p>

<p>If you wish, you can <?php echo anchor("forms/rename/".$forms[0]->name, 'change the name of the form'); ?>.</a></p>

<div id="form_versions_contain" class="section">
	<div id="form_versions_heading" class="section_heading">Versions:</div>
	<?php
		$this->load->view('formlist', array('forms'=>$forms));
	?>
</div>
<br />
<div id="form_perms_contain" class="section">
	<div id="form_perms_heading" class="section_heading"><a href="javascript:void(0);" onclick="toggleView('#form_perms');">Permissions:</a></div>
	<div id="form_perms">		
	</div>
</div>

<br />
<div>
	<div id="embed_code_heading" class="section_heading"><a href="javascript:void(0);" onclick="toggleView('#embed_code');">Embed Code:</a></div>
	<div id="embed_code" style="display: none;">
		<p>You can use the following code to include this form on any web page, by just pasting into the existing html.</p>
		<textarea readonly><iframe src="<?php echo base_url(); ?>forms/viewEmbedded/<?php echo $forms[0]->name; ?>" height="100%;" width="100%;"></iframe></textarea>
		<p>If you have problems with the embedded form breaking the page due to size, put it inside a div styled to be the size you want.
		</p>
	</div>
</div>
<?php
$this->load->view('footer');
?>