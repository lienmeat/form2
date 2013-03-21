<?php

class Help extends MY_Model{
	protected $table = 'helps';
	protected $dbfields = array('id', 'searchterms', 'help');

	function search($input){
		$this->db->select()->from($this->table)->like('searchterms', $input);
		$res = $this->db->get();
		return $res->result();
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