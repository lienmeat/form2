<?php

class Role extends MY_Model{
	
	protected $table = 'roles';
	protected $dbfields = array('id', 'role');

  /**
  * Get all roles for this user
  * @param string $user Username
  */
	function getAllByUser($user=null){
	  if($user){
	    return $this->getBy('user', $user);
	  }else{
	    return array();
	  }
	}
	
	/**
	* Get all roles assigned to an object
	* @param string $object_type Singular form of one of the database table names (form, formresult, answer, etc...)
	* @param string $object_id The id that corresponds to that object (form->id for instance)
	* @param number $limit number of results (default is all)
	* @return array Rows
	*/
	function getOnObject($object_type, $object_id, $limit=false){
    $this->db->select()->from($this->table)->where(array('object_type'=>$object_type, 'object_id'=>$object_id))->order_by('user');
	  if($limit) $this->db->limit($limit);
	  $query = $this->db->get();
	  return $query->result();
	}
	
	/**
	* Get roles a user has on a particular object
	* @param string $user Username
	* @param string $object_type Singular form of one of the database table names (form, formresult, answer, etc...)
	* @param string $object_id The id that corresponds to that object (form->id for instance)
	* @param number $limit number of results (default is all)
	* @return array Rows
	*/
	function getByUserOnObject($user, $object_type, $object_id, $limit=false){
    $this->db->select()->from($this->table)->where(array('user'=>$user, 'object_type'=>$object_type, 'object_id'=>$object_id));
	  if($limit) $this->db->limit($limit);
	  $query = $this->db->get();
	  return $query->result();
	}
	
	/**
	* Tells if user has a certain role on a particular object
	* @param string $user Username
	* @param string $role Role
	* @param string $object_type Singular form of one of the database table names (form, formresult, answer, etc...)
	* @param string $object_id The id that corresponds to that object (form->id for instance)
	* @param number $limit number of results (default is all)
	* @return array Rows
	*/
	function hasRoleOnObject($user, $role_id, $object_type, $object_id){
    $this->db->select()->from($this->table)->where(array('user'=>$user, 'role_id'=>$role_id, 'object_type'=>$object_type, 'object_id'=>$object_id));	  
	  $query = $this->db->get();
	  $res = $query->result();
	  if($res and !empty($res)) return true;
	  else return false;
  }
	
	/**
	* Deletes roles that a user has (all of them!)
	* @param string $user Username
	*/
	function deleteByUser($user){
	  if($user){
	    return $this->db->delete($this->table, array('user'=>$user));
	  }else{
	    return false;
	  }	  
	}
	
  /**
	* Deletes roles that a user has on an object (all of them!)
	* @param string $user Username
	* @param string $object_type Singular form of one of the database table names (form, formresult, answer, etc...)
	* @param string $object_id The id that corresponds to that object (form->id for instance)
	*/
	function deleteByUserOnObject($user, $object_type, $object_id){
	  if($user and $object_type and $object_id){
	    return $this->db->delete($this->table, array('user'=>$user, 'object_type'=>$object_type, 'object_id'=>$object_id));
	  }else{
	    return false;
	  }
	}
	
	/**
	* Deletes roles matching $role that a user has(all of them!)
	* @param string $user Username
	* @param string $role Role
	*/
	function deletePermissionByUser($user, $role){
	  if($user and $role){
	    return $this->db->delete($this->table, array('user'=>$user,  'permission'=>$permission));
	  }else{
	    return false;
	  }
	}

		
}
?>