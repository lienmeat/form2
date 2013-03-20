/**
* JS to control UI for permissions management
*/

function PermissionsUI(container_id, perms_data){
	this.handle = false;	
	this.container_id = container_id;
	this.initial_data = perms_data;
	this.form_name = '';
	this.users = [];
	//list of permissions to render (ignore any others)
	this.permission_names = ['edit', 'admin', 'viewresults'];
	this.createHandle();
	this.__init__();
}

/**
* Creates a handle in global scope in window object
* so we can solve scope issues easier later
*/
PermissionsUI.prototype.createHandle = function(){
	if(!window.permissionsui){
		window.permissionsui = [];
	}
	this.handle = (window.permissionsui.push(this)-1);
	return this.handle;
}

/**
* Gets the global handle in the window object for this object
* So we can run event handlers easily/solve scope issues
*/
PermissionsUI.prototype.getHandle = function(){
	return this.handle;
}

/**
* Set up object, binding events to inputs and anything else
*/
PermissionsUI.prototype.__init__ = function(){
	var handle = this.getHandle();

	this.form_name = this.initial_data.form_name;
	this.users = this.initial_data.users;
	//todo: bind to container and stuff, load data.
	this.renderUI();
}

/**
* Render the UI initially
*/
PermissionsUI.prototype.renderUI = function(){
	var container = this.container_id;
	var handle = this.getHandle();
	var html = "<table><tr>";
	var thead = "<th>Username</th>"
	for(var i=0 in this.permission_names){
		thead+="<th>"+this.permission_names[i];+"</th>";
	}
	html+=thead+"</tr><tbody id=\""+container+"_permslist\"></tbody><tr>"+thead+"</tr></table>";
	html+="Add User: <input id=\""+container+"_username\" type=\"text\"><button onclick=\"window.permissionsui['"+handle+"'].addUser();\">Add</button>";
	$('#'+container).append(html);
	this.renderUsers();
}

/**
* Render the full list of users
*/
PermissionsUI.prototype.renderUsers = function(){
	for(var i=0 in this.users){
		this.renderUser(i, this.users[i]);
	}
}

/**
* Toggle whether a user has a permission or not.
*/
PermissionsUI.prototype.togglePerm = function(elem){
	//use data-roles to store/get username, perm_id stored in value
	if($(elem).is(':checked')){
		//add perm
		doAjax('permissions/addToUser', {username: $(elem).attr('data-username'), permission: $(elem).val(), form: this.form_name}, function(){ void(0); });
	}else{
		//remove perm
		doAjax('permissions/removeFromUser', {username: $(elem).attr('data-username'), permission: $(elem).val(), form: this.form_name}, function(){ void(0); });
	}
}

/**
* Adds a new user to the list from the input
*/
PermissionsUI.prototype.addUser = function(){
	var username = $("#"+this.container_id+"_username").val();
	this.renderUser(username, []);
}

/**
* Remove a user from the list (not sure if I care to...)
*/
PermissionsUI.prototype.removeUser = function(id){
	//todo: make something work for this
}

/**
* Renders one user into list (appends it)
* @param string username Username to add
* @param array permissions Permissions to set
*/
PermissionsUI.prototype.renderUser = function(username, permissions){
	var handle = this.getHandle();
	var username = username.toLowerCase() || null;
	var perms = permissions || [];
	var id = Math.random() * Math.pow(10, 17) + Math.random() * Math.pow(10, 17);
	var out = '';
	if(username){
		out+="<tr id=\""+id+"\"><td>"+username+"</td>";		
		for(var i=0 in this.permission_names){
			var check = false;			
			for(var j=0 in perms){
				if(this.permission_names[i] == perms[j].permission){
					check = true;
					break;
				}				
			}			
			if(check){
				//render this permission as checked
				out+="<td><input type=\"checkbox\" value=\""+this.permission_names[i]+"\" data-username=\""+username+"\" checked=\"checked\" onclick=\"window.permissionsui['"+handle+"'].togglePerm(this);\"></td>";
			}else{
				//render this permission as not checked
				out+="<td><input type=\"checkbox\" value=\""+this.permission_names[i]+"\" data-username=\""+username+"\" onclick=\"window.permissionsui['"+handle+"'].togglePerm(this);\"></td>";
			}			
		}
		$("#"+this.container_id+"_permslist").append(out);
	}else{
		return;
	}
}