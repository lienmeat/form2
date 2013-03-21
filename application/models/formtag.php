<?php

class Formtag extends MY_Model{
	protected $table = 'formtags';
	protected $dbfields = array('id', 'tag');

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