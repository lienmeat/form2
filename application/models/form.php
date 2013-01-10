<?php
/**
* Model controls access to Form records
*/
class Form extends MY_Model{
	protected $table = 'forms';
	protected $dbfields = array('id', 'name', 'title', 'config', 'creator', 'created', 'published', 'disabled');

  /**
  * Get all forms this user created
  * @param string $user Username
  * @return array Forms (may be empty)
  */
	function getByUser($user=null, $order_by=null){
	  if($user){
	    if(!$order_by)	$order_by = 'created DESC';
	    $this->db->select()->from($this->table)->where('user', $user)->order_by($order_by);
	    $query = $this->db->get();
	    return $this->decodeMany($query->results());
	  }else{
	    return array();
	  }
	}
	
	/**
    * Get all forms named $name
    * @param string $name
    * @return array Forms (may be empty)
    */
	function getByName($name=null, $order_by=null){
	  if($name){
      if(!$order_by)	$order_by = 'created DESC';
	    $this->db->select()->from($this->table)->where('name', $name)->order_by($order_by);
	    $query = $this->db->get();
	    return $this->decodeMany($query->result());
	  }else{
	    return array();
	  }
	}
	
	/**
	* Gets the published forms
	* @return array
	*/
	function getPublished($order_by='created DESC'){
	  if(!$order_by) $order_by = 'created DESC';
	  $query = $this->db->query("SELECT * FROM `{$this->table}` WHERE `published` IS NOT NULL AND `published` != 'NULL' ORDER BY $order_by");
	  $res = $query->result();	  
	  if(!empty($res)){
	    return $this->decodeMany($res);
	  }else{
	    return array();
	  }
	}

	/**
	* Gets the published form named $name if it exists
	* @param string $name
	* @return object|bool
	*/
	function getPublishedWithName($name=null, $order_by='created DESC'){
	  if(!$name) return false;
      if(!$order_by) $order_by = 'created DESC';
	  $query = $this->db->query("SELECT * FROM `{$this->table}` WHERE `name` = ? AND (`published` IS NOT NULL AND `published` != 'NULL') ORDER BY $order_by", array($name));
	  $res = $query->result();
	  if(!empty($res)){
	    return $this->decode($res[0]);
	  }else{
	    return array();
	  }
	}
	
	/**
	* Revert a to an older version of a form by the same name (does NOT publish the form reverting to!)
	* (indicates it is the one to focus on for future edits, basically)
	* @param string $form_id
	* @return object Form reverted to
	*/
	function revert($form_id){
	  if(!$form_id) return false;
	  $data = array('id'=>$form_id, 'created'=>date('Y-m-d H:i:s'));
	  return $this->update($data);
	}
	
	/**
	* Publishes a form (makes it live), unpublishing other forms with the same name
  * @param string $form_id ID of form
  * @return object Form
	*/
	function publish($form_id){
	  if(!$form_id) return false;
	  $form = $this->getById($form_id);
	  if(!$form) return false;
	  $form->published = date('Y-m-d H:i:s');
	  //unpublish any forms having the same name
	  $this->db->update($this->table, array('published'=>NULL), array('name'=>$form->name));
	  //publish this form
	  return $this->update($form);
	}
	
	/**
	* Makes a duplicate of a form (anyone can copy a form, only creator should duplicate)
	* Since we are shooting for copy-on-write versioning, basically do this anytime anyone wants to edit a
	* published form (a publish counts as a commit basically)
	* Only duplicates form record NOT records of questions or anything else (controller must handle this)
	* @param string $form_id ID of form to duplicate
	* @return object Duplicate of form
	*/
	function duplicate($form_id){
	  if(!$form_id) return false;
	  $form = $this->getById($form_id);
	  if(!$form) return false;
  	unset($form->id, $form->published, $form->created);
  	return $this->insert($form);
  }
	
	/**
	* Makes a copy of a form, but uses a different name (anyone can copy a form, only creator should duplicate)
	* Only copies form record NOT records of questions or anything else (controller must handle this)
	* @param string $form_id ID of form to copy
	* @param string $creator Username of who owns this copy
	* @return object copy of form
	*/
	function copy($form_id, $creator){
	  if(!$form_id or !$creator) return false;
	  $form = $this->getById($form_id);
	  if(!$form) return false;
  	unset($form->id, $form->published, $form->created, $form->name);
  	$form->title = "Copy Of: ".$form->title;
  	$form->creator = $creator;
  	return $this->insert($form);
	}
	
	//override update to return the actual db record updated
	function update($data){
		if($data and (is_array($data) or is_object($data))){
	    $data = (object) $data;
    }else return false;	  
	  if(parent::update($data)){
	    return $this->getById($data->id);
	  }else{
	    return false;
	  }
	}
	
	//override insert to return the actual db record inserted
	function insert($data){
	  if($data and (is_array($data) or is_object($data))){
	    $data = (object) $data;
    }else return false;
	  $data->created = date('Y-m-d H:i:s');
	  $data->id = uniqid('');
    $data =& $this->encode($data);
    //echo print_r($data, true);
    if(parent::insert($data)){
    	//echo print_r($data, true);
	    //$ret = $this->getById($data->id);
	    //echo print_r($ret, true);
	    return $this->getById($data->id);
	  }else{
	    return false;
	  }
	}	


	/**
	* Finds if a form with given name exists
	* @param string $form_name
	*	@return bool
	*/
	function nameExists($form_name){
		$q = "SELECT `id` FROM `".$this->table."` WHERE `name` = ? LIMIT 1";
		$query = $this->db->query($q, $form_name);
		if($query->num_rows() > 0){
			return true;
		}else{
			return false;
		}
	}
}
?>
