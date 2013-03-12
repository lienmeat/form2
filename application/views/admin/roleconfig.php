<?php
$menu = array(
	anchor('admin', 'Admin Dashboard'),
	anchor('admin/forms', 'Form Admin'),			
);
$this->load->view('header', array('title'=>$form->name, 'banner_menu'=>$menu));	
?>
<link rel="stylesheet" href="<?php echo base_url();?>application/views/CSS/autoSuggest.css">
<style>
.delete {
	color: red;
}

#rolemanagetools_contain {
	display: none;
}

.assoclist {
	list-style: none;
}
</style>

<script type="text/javascript" src="<?php echo base_url();?>application/views/JS/jquery.autoSuggest.js"></script> 
<script>
var currentRole = null;

var role_suggest_conf = {
	neverSubmit: true,
	minChars: "2",
	selectedItemProp: "name",
	searchObjProps: "name",
	selectionLimit: "1",
	asHtmlID: "roleselection",
	selectionAdded: roleSelectionAdded,
	selectionRemoved: roleSelectionRemoved,
};

var permissions_suggest_conf = {
	neverSubmit: true,
	minChars: "2",	
	selectedItemProp: "name",
	searchObjProps: "name",	
	asHtmlID: "permissionsselection",
};

var forms_suggest_conf = {
	neverSubmit: true,
	minChars: "2",	
	selectedItemProp: "name",
	searchObjProps: "name",	
	asHtmlID: "formsselection",
};

$(function(){
	$("#rolesuggest").autoSuggest("<?php echo site_url('admin/searchRoles'); ?>", role_suggest_conf);
	$("#addpermissiontorole").autoSuggest("<?php echo site_url('admin/searchPermissions'); ?>", permissions_suggest_conf);
	$("#addformtorole").autoSuggest("<?php echo site_url('admin/searchForms'); ?>", forms_suggest_conf);
});


function addRole(){
	var role_name = $("#addrole_txt").val() || null;
	var role_description = $("#addroledescription_txt").val() || null;

	var err = false;
	if(!role_name){
		alert('You must have a role name set!');
		err = true;
	}
	if(!err && !role_description){
		alert('You must have a role description set!');
		err = true;
	}
	if(!err){
		doAjax('admin/addRole', {'role': role_name, 'description': role_description}, doAddRole);
	}
}

function doAddRole(resp){	
	if(resp.error){
		alert(resp.error);
	}else{
		$("#addrole_txt").val('');
		$("#addroledescription_txt").val('');
		$("#roleselection").val(resp.role.role);
	}
}


function roleSelectionAdded(elem){
	var values = getAutoCompleteValuesAsArray($('#as-values-roleselection').val());
	currentRole = values[0];
	doAjax('admin/getAssociatedToRole/'+values[0], {}, setRolesLists);	
}

function setRolesLists(resp){
	setRoleDescription(resp.role);
	setRolesUsers(resp.users);
	setRolesPermissions(resp.permissions);
	setRolesForms(resp.forms);
	$('#rolemanagetools_contain').show();
}

function setRoleDescription(role){
	if(role.description && role.description.length > 1){
		$('#roledescription_contain').show();		
	}else{
		$('#roledescription_contain').hide();
	}
	$('#roledescription').html(role.description);
	
}

function setRolesPermissions(permissions){
	var permissions = permissions || [];
	clearRolesPermissions();
	renderRolesPermissions(permissions);	
}

function renderRolesPermissions(permissions){
	var permissions = permissions || [];
	var html = $('#rolepermissionslist').html();
	for(i in permissions){
		html+="<li id=\"rp_"+permissions[i].id+"\">"+permissions[i].permission+'&nbsp;<a class="delete" href="javascript:void(0);" onclick="removeRolesPermission(\''+permissions[i].id+'\');">x</a></li>';
	}
	$('#rolepermissionslist').html(html);
}



function clearRolesPermissions(){
	$('#rolepermissionslist').html('');
	clearAutoComplete('as-selections-permissionsselection');
}

function addPermissionsToRole(){
	var ids = getAutoCompleteValuesAsArray($('#as-values-permissionsselection').val());
	doAjax('admin/addPermissionsToRole/'+currentRole, {permission_ids:ids}, doAddPermissions);	
}



function doAddPermissions(resp){
	setRolesPermissions(resp.permissions);
}

function removeRolesPermission(id){
	doAjax('admin/deletePermissionFromRole/'+currentRole+'/'+id, {}, doRemoveRolesPermission);	
}

function doRemoveRolesPermission(resp){
	$('#rp_'+resp.permission_id).hide();
}

function setRolesUsers(users){
	var users = users || [];
	clearRolesUsers();
	renderRolesUsers(users);	
}

function renderRolesUsers(users){
	var users = users || [];
	var html = $('#roleuserslist').html();
	for(i in users){
		html+="<li id=\"ru_"+users[i].user.replace('.','')+"\">"+users[i].user+'&nbsp;<a class="delete" href="javascript:void(0);" onclick="removeRolesUser(\''+users[i].user+'\');">x</a></li>';
	}
	$('#roleuserslist').html(html);
}

function clearRolesUsers(){
	$('#roleuserslist').html('');
	$('#addusertorole').val('');
}

function addUsersToRole(){
	var usernames = getAutoCompleteValuesAsArray($('#addusertorole').val());
	doAjax('admin/addUsersToRole/'+currentRole, {users:usernames}, doAddUsers);	
}


function doAddUsers(resp){
	setRolesUsers(resp.users);
}

function removeRolesUser(username){
	doAjax('admin/deleteUserFromRole/'+currentRole+'/'+username, {}, doRemoveRolesUser);	
}

function doRemoveRolesUser(resp){
	$('#ru_'+resp.user.replace('.','')).hide();
}

function roleSelectionRemoved(elem){
	$('#rolemanagetools_contain').hide();
	currentRole = null;
	elem.remove();
	clearForRoles();
}

function setRolesForms(forms){
	var forms = forms || [];
	clearRolesForms();
	renderRolesForms(forms);	
}

function renderRolesForms(forms){
	var forms = forms || [];
	var html = $('#roleformslist').html();
	for(i in forms){
		html+="<li id=\"rf_"+forms[i].form+"\">"+forms[i].form+'&nbsp;<a class="delete" href="javascript:void(0);" onclick="removeRolesForm(\''+forms[i].form+'\');">x</a></li>';
	}
	$('#roleformslist').html(html);
}



function clearRolesForms(){
	$('#roleformslist').html('');
	clearAutoComplete('as-selections-formsselection');
}

function addFormsToRole(){
	var formnames = getAutoCompleteValuesAsArray($('#as-values-formsselection').val());
	doAjax('admin/addFormsToRole/'+currentRole, {forms:formnames}, doAddForms);
}

function doAddForms(resp){
	setRolesForms(resp.forms);
}

function removeRolesForm(form){
	doAjax('admin/deleteFormFromRole/'+currentRole+'/'+form, {}, doRemoveRolesForm);	
}

function doRemoveRolesForm(resp){
	$('#rf_'+resp.form).hide();
}

function clearForRoles(){
	setRoleDescription({'description': ''});
	clearRolesPermissions();
	clearRolesUsers();
	clearRolesForms();
}

function getAutoCompleteValuesAsArray(rawvalues){
	var rawvalues = rawvalues || '';
	rawvalues = rawvalues.split(',');
	var values = [];
	for(i in rawvalues){
		if(rawvalues[i].length > 0) values.push(rawvalues[i].replace(' ','')); 
	}
	return values;
}

//forces a "click" on each item's close/delete button in the autocomplete that has been selected,
//which removes it and it's values from the autocomplete (whew...so stupid)
function clearAutoComplete(id){
	 $('#'+id+' > li.as-selection-item > a.as-close').each(function(){
		$(this).click();		
	});
}

</script>
<div style="width: 100%;">
	<div style="">
		<div style="float: left; width: 300px;">
			<label>Search for a role to manage:</label>
			<input id="rolesuggest"/>
			<div id="roledescription_contain" style="display: none;">
				<label>Description:</label>
				<div id="roledescription"></div>
			</div>
		</div>
		<div style="float: left; margin-left: 20px;">
			<label>Or add a new role:</label><br />
			Name: <input id="addrole_txt"/><br />Description: <textarea id="addroledescription_txt"></textarea>
			<button id="addrole_but" onclick="addRole();">Add</button>
		</div>
		<div class="clear"></div>
	</div>
	<br />
	<div style="width: 300px;">
		<div id="rolemanagetools_contain">
			<div id="permslist_contain">
				<div style="border: 1px solid black;">
					Permissions assigned to this role:
					<ul id="rolepermissionslist" class="assoclist">								
					</ul>
				</div>
				Add permissions to this role:
				<input id="addpermissiontorole"/><button onclick="addPermissionsToRole();">Add</button>
			</div>
			<br />
			<div id="formslist_contain">	
				<div style="border: 1px solid black;">
					Forms with this role:
					<ul id="roleformslist" class="assoclist">				
					</ul>
				</div>
				Add this role to these forms:
				<input id="addformtorole"/><button onclick="addFormsToRole();">Add</button>
			</div>
			<br />
			<div id="userslist_contain">
				<div style="border: 1px solid black;">
					Users with this role:
					<ul id="roleuserslist" class="assoclist">
					</ul>
				</div>
				Add usernames to this role (comma-separated!):
				<input id="addusertorole"/><button onclick="addUsersToRole();">Add</button>
			</div>
		</div>
	</div>
</div>

<?php
$this->load->view('footer');
?>