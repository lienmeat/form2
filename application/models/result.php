<?php
/**
* Model controls access to Question records
*/
class Result extends MY_Model{
	protected $table = 'formresults';
	protected $dbfields = array('id', 'form_id', 'timestamp', 'post', 'submitter');

	/**
  * Get all questions in form (in order)
  * @param string $form_id
  * @return array Questions (may be empty)
  */
	function getByForm($form_id, $deleted=false, $order_by=null){
	  if($deleted) $deleted = 1;
	  else $deleted = 0;
	  if($form_id){
      	if(!$order_by) $order_by = 'timestamp ASC';
	    $this->db->select()->from($this->table)->where(array('form_id'=>$form_id, 'deleted'=>$deleted))->order_by($order_by);
	    $query = $this->db->get();
	    return $query->result();
	  }else{
	    return array();
	  }
	}
	
	
  //override update to return the actual db record inserted
	function update($data){
		if($data and (is_array($data) or is_object($data))){
	    $data = (object) $data;
	  }
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
	  $data->id = uniqid('');
	  if(parent::insert($data)){
	    return $this->getById($data->id);
	  }else{
	    return false;
	  }
	}
}

?>