<?php
/**
* Model controls access to Draft records
*/
class Draft extends MY_Model{
	protected $table = 'drafts';
	protected $dbfields = array('id', 'form_id', 'post', 'readonlys');

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
			$data->id = uniqid('');
    	if(parent::insert($data)){
			return $this->getById($data->id);
		}else{
		  return false;
		}
	}	
}
?>
