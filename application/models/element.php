<?php
/**
* Model controls access to element records
*/
class Element extends MY_Model{
	protected $table = 'elements';
	protected $dbfields = array('id', 'name', 'type');

	function getForDropdown(){
		$this->db->select("name, type")->from($this->table)->order_by('name');
		$query = $this->db->get();
		$elems = $query->result();
		$options = array();
		foreach($elems as $e){
			$options[$e->name] = $e->type;
		}
		return $options;
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
		if(parent::insert($data)){
			return $this->getById($data->id);
		}else{
		    return false;
		}
	}
}

?>