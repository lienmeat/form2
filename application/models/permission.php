<?php
/**
* Example of how you might set up a model by extending MY_Model
*/
class Permission extends MY_Model{
	protected $table = 'permissions';
	protected $dbfields = array('id', 'user', 'object_type', 'object_id', 'permission');

  /**
  * Get all permissions for this user
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
	* Get all permissions assigned to an object
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
	* Get permissions a user has on a particular object
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
	* Tells if user has a certain permission on a particular object
	* @param string $user Username
	* @param string $permission Permission
	* @param string $object_type Singular form of one of the database table names (form, formresult, answer, etc...)
	* @param string $object_id The id that corresponds to that object (form->id for instance)
	* @param number $limit number of results (default is all)
	* @return array Rows
	*/
	function hasPermissionOnObject($user, $permission, $object_type, $object_id){
    $this->db->select()->from($this->table)->where(array('user'=>$user, 'permission'=>$permission, 'object_type'=>$object_type, 'object_id'=>$object_id));	  
	  $query = $this->db->get();
	  $res = $query->result();
	  if($res and !empty($res)) return true;
	  else return false;
  }
	
	/**
	* Deletes permissions that a user has (all of them!)
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
	* Deletes permissions that a user has on an object (all of them!)
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
	* Deletes permissions matching $permission that a user has(all of them!)
	* @param string $user Username
	* @param string $permission Permission
	*/
	function deletePermissionByUser($user, $permission){
	  if($user and $permission){
	    return $this->db->delete($this->table, array('user'=>$user,  'permission'=>$permission));
	  }else{
	    return false;
	  }
	}

}

?>
