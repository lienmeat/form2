<?php
/**
* Example of how you might set up a model by extending MY_Model
*/
class Permission extends MY_Model{
	protected $table = 'permissions';
	protected $dbfields = array('id', 'permission', 'description');

	function getLike($q){
		$this->db->select()->from($this->table)->like('permission', $q);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	* Get permissions set on a user
	* @param string $username
	*/	
	function getOnUser($username){
		$q = "SELECT `permissions`.*, `permissions_users`.`user`, `permissions_users`.`form` FROM `permissions` 
		JOIN `permissions_users` ON (`permissions`.`id` = `permissions_users`.`permission_id`) 
		WHERE `permissions_users`.`user` = ?";

		$query = $this->db->query($q, $username);
		return $query->result();
	}

	/**
	* Get permissions set on a form
	* @param string $form_name
	*/
	function getOnForm($form_name){
		$q = "SELECT `permissions`.*, `permissions_users`.`form`, `permissions_users`.`user` FROM `permissions` 
		JOIN `permissions_users` ON (`permissions`.`id` = `permissions_users`.`permission_id`) 
		WHERE `permissions_users`.`form` = ? ORDER BY `permissions_users`.`user` ASC";

		$query = $this->db->query($q, $form_name);
		return $query->result();
	}


	/**
	* Get permissions set on a role
	* @param string $role_id
	*/
	function getOnRole($role_id){
		$q = "SELECT `permissions`.*, `permissions_roles`.`role_id`, `permissions_roles`.`permission_id` FROM `permissions` 
		JOIN `permissions_roles` ON (`permissions`.`id` = `permissions_roles`.`permission_id`) 
		WHERE `permissions_roles`.`role_id` = ?";

		$query = $this->db->query($q, $role_id);
		return $query->result();
	}

	/**
	* Says if permission is set on both a form and a user by name
	* @param string $permission Permission
	* @param string $form_name
	* @param string $username
	*/
	function getOnFormAndUser($form_name, $username){
		$q = "SELECT `permissions`.*, `permissions_users`.`user`, `permissions_users`.`form` FROM `permissions` 
		JOIN `permissions_users` ON (`permissions`.`id` = `permissions_users`.`permission_id`) 
		WHERE `permissions_users`.`form` = ? AND `permissions_users`.`user` = ?";

		$query = $this->db->query($q, array($form_name, $username));		
		return $query->result();
	}

	/**
	* Is a permission assigned to role
	* @param string $permission Permission name
	* @param string $role_id
	*/	
	function hasPermissionOnRole($permission, $role_id){
		$q = "SELECT `permissions`.*, `permissions_roles`.`role_id`, `permissions_roles`.`permission_id` 
		FROM `permissions` 
		JOIN `permissions_roles` ON (`permissions`.`id` = `permissions_roles`.`permission_id`) 
		WHERE `permissions_roles`.`role_id` = ? AND `permissions`.`permission` = ?";

		$query = $this->db->query($q, array($role_id, $permission));
		$res = $query->result();
		if(empty($res)) return false;
		else return true;
	}

	/**
	* Get by name on role
	* @param string $permission Permission name
	* @param string $username
	* @param string $form_name
	*/	
	function hasPermissionOnUserAndForm($permission, $username, $form_name){
		$q = "SELECT `permissions`.*, `permissions_users`.`user`, `permissions_users`.`form` 
		FROM `permissions` 
		JOIN `permissions_users` ON (`permissions`.`id` = `permissions_users`.`permission_id`) 
		WHERE `permissions_users`.`user` = ? AND `permissions_users`.`form` = ? AND `permissions`.`permission` = ?";

		$query = $this->db->query($q, array($username, $form_name, $permission));
		$res = $query->result();
		if(empty($res)) return false;
		else return true;
	}

	/**
	* Adds a Permission to a user on a form
	* @param string $id Permission id
	* @param string $username
	* @param string $form_name 
	*/
	function addToUser($id, $username, $form_name){
		return $this->db->insert('permissions_users', array('permission_id'=>$id, 'user'=>$username, 'form'=>$form_name));
	}

	/**
	* Deletes a Permission from a form for a user
	* @param string $id Permission id
	* @param string $username
	* @param string $form_name
	*/ 
	function deleteFromUser($id, $username, $form_name){
		return $this->db->delete('permissions_users', array('permission_id'=>$id, 'user'=>$username, 'form'=>$form_name));
	}

	/**
	* Deletes a Permission from a form
	* @param string $id Permission id
	* @param string $form_name
	*/ 
	function deleteFromForm($id, $form_name){
		return $this->db->delete('permissions_users', array('permission_id'=>$id, 'form'=>$form_name));
	}

	/**
	* Adds a Permission to a role
	* @param string $id Permission id
	* @param string $role_id
	*/
	function addToRole($id, $role_id){
		//see if we already have this permission on this role
		$this->db->select()->from('permissions_roles')->where(array('role_id'=>$role_id, 'permission_id'=>$id));
		$res = $this->db->get();
		$result = $res->result();
		if(!empty($result)){
			return false;
		}else{
			return $this->db->insert('permissions_roles', array('role_id'=>$role_id, 'permission_id'=>$id));
		}
	}

	/**
	* Deletes a Permission from a role
	* @param string $id Permission id
	* @param string $role_id
	*/ 
	function deleteFromRole($id, $role_id){
		return $this->db->delete('permissions_roles', array('permission_id'=>$id, 'role_id'=>$role_id));
	}

	/**
	* Deletes a Role and associated relations
	* @param string $id $Role id
	*/
	function delete($id){
		$tables = array('permissions_roles', 'permissions_users');
		if($this->db->delete($this->table, array('id'=>$id))){
			//codeigniter documentation says you can delete from multiple
			//tables like this!  FUN.
			$this->db->where('permission_id', $id);
			return $this->db->delete($tables);
		}
		return false;		
	}
}

?>
