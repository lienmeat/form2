<?php
/**
* Model controls access to Dataprovider records
* This is not the model the Dataprovider library uses to get data with!
* For that, you want dataproviderlib_model.php!
*/
class Dataprovider_model extends MY_Model{
	protected $table = 'dataproviders';
	protected $dbfields = array('id', 'title', 'description', 'method');

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