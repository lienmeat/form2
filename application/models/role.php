<?php

class Role extends RetRecord_Model{
	
	protected $table = 'roles';
	protected $dbfields = array('id', 'role', 'description');

	function getLike($q){
		$this->db->select()->from($this->table)->like('role', $q);
		$query = $this->db->get();
		return $query->result();
	}

	function getUsersOnRole($role_id){
		$this->db->select('user')->from('roles_users')->where('role_id', $role_id);
		$query = $this->db->get();
		return $query->result();
	}

	function getFormsOnRole($role_id){
		$this->db->select('form')->from('forms_roles')->where('role_id', $role_id);
		$query = $this->db->get();
		return $query->result();
	}

	/**
	* Get roles belonging to a user
	* @param string $username
	*/	
	function getOnUser($username){
		$q = "SELECT `roles`.*, `roles_users`.`user` FROM `roles` 
		JOIN `roles_users` ON (`roles`.`id` = `roles_users`.`role_id`) 
		WHERE `roles_users`.`user` = ?";

		$query = $this->db->query($q, $username);
		return $query->result();
	}

	/**
	* Gets a Role by name if a user belongs to it (usefull for global roles)
	* @param string $username
	* @param string $role Name of role
	*/	
	function getRoleOnUserByName($username, $role){
		$q = "SELECT `roles`.*, `roles_users`.`user` FROM `roles` 
		JOIN `roles_users` ON (`roles`.`id` = `roles_users`.`role_id`) 
		WHERE `roles_users`.`user` = ? AND `roles`.`role` = ?";

		$query = $this->db->query($q, array($username, $role));
		$res = $query->result();
		if(!empty($res)) return $res[0];
		else return false;
	}

	/**
	* Get roles belonging to a form
	* @param string $form_name
	*/
	function getOnForm($form_name){
		$q = "SELECT `roles`.*, `forms_roles`.`form` FROM `roles` 
		JOIN `forms_roles` ON (`roles`.`id` = `forms_roles`.`role_id`) 
		WHERE `forms_roles`.`form` = ?";

		$query = $this->db->query($q, $form_name);		
		return $query->result();
	}

	/**
	* Get roles belonging to both a form and a user
	* @param string $form_name
	* @param string $username
	*/
	function getOnFormAndUser($form_name, $username){
		$q = "SELECT `roles`.*, `forms_roles`.`form`, `roles_users`.`user` FROM `roles` 
		JOIN `forms_roles` ON (`roles`.`id` = `forms_roles`.`role_id`) 
		JOIN `roles_users` ON (`roles`.`id` = `roles_users`.`role_id`) 
		WHERE `forms_roles`.`form` = ? AND `roles_users`.`user` = ?";

		$query = $this->db->query($q, array($form_name, $username));		
		return $query->result();
	}

	/**
	* Adds a Role to a form
	* @param string $id Role id
	* @param string $form_name Name of form
	*/
	function addToForm($id, $form_name){
		$this->db->select()->from('forms_roles')->where(array('role_id'=>$id, 'form'=>$form_name));
		$res = $this->db->get();
		$result = $res->result();
		if(!empty($result)){
			return false;
		}else{
			return $this->db->insert('forms_roles', array('role_id'=>$id, 'form'=>$form_name));
		}
	}

	/**
	* Deletes a Role from a form
	* @param string $id Role id
	* @param string $form_name Name of form
	*/
	function deleteFromForm($id, $form_name){
		return $this->db->delete('forms_roles', array('role_id'=>$id, 'form'=>$form_name));
	}

	/**
	* Adds a Role to a user
	* @param string $id Role id
	* @param string $username Username
	*/
	function addToUser($id, $username){
		$username = str_replace(' ', '', strtolower($username));
		$this->db->select()->from('roles_users')->where(array('role_id'=>$id, 'user'=>$username));
		$res = $this->db->get();
		$result = $res->result();
		if(!empty($result)){
			return false;
		}else{
			return $this->db->insert('roles_users', array('role_id'=>$id, 'user'=>$username));
		}
	}

	/**
	* Deletes a Role from a user
	* @param string $id Role id
	* @param string $username Username
	*/
	function deleteFromUser($id, $username){
		return $this->db->delete('roles_users', array('role_id'=>$id, 'user'=>$username));
	}

	/**
	* Deletes a Role and associated relations
	* @param string $id $Role id
	*/
	function delete($id){
		$tables = array('forms_roles', 'roles_users');
		if($this->db->delete($this->table, array('id'=>$id))){
			//codeigniter documentation says you can delete from multiple
			//tables like this!  FUN.
			$this->db->where('role_id', $id);
			return $this->db->delete($tables);
		}
		return false;		
	}

	//override insert to add uniqid
	function insert($data){
	  if($data and (is_array($data) or is_object($data))){
	    $data = (object) $data;
	  }else return false;
	  $data->id = uniqid('');
	  return parent::insert($data);	    
	}
}
?>