<?php
/**
* Model controls access to Question records
*/
class Result extends RetRecord_Model{
	protected $table = 'formresults';
	protected $dbfields = array('id', 'form_id', 'timestamp', 'post', 'submitter', 'user_agent', 'ip_address', 'deleted');

	/**
	* Get results by form_id
	* @param string $form_id
	*	
	*/
	function getByForm($form_id, $deleted=false, $order_by=null){
	  if($deleted) $deleted = 1;
	  else $deleted = 0;
	  if($form_id){
      	if(!$order_by) $order_by = 'timestamp DESC';
	    $this->db->select()->from($this->table)->where(array('form_id'=>$form_id, 'deleted'=>$deleted))->order_by($order_by);
	    echo $order_by."<br />";
	    $query = $this->db->get();
	    return $query->result();
	  }else{
	    return array();
	  }
	}

	/**
	* Get all results in forms with one of the IDs
	* @param string $form_name	
	*/
	function getOnForms(Array $form_ids, $deleted=false, $order_by=null){
	  if($deleted) $deleted = 1;
	  else $deleted = 0;
	  if($form_ids){
      	if(!$order_by) $order_by = 'timestamp DESC';
	    $this->db->select()->from($this->table)->where(array('deleted'=>$deleted))->where_in('form_id', $form_ids)->order_by($order_by);	    
	    $query = $this->db->get();
	    return $query->result();
	  }else{
	    return array();
	  }
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