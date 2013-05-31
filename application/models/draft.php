<?php
/**
* Model controls access to Draft records
*/
class Draft extends RetRecord_Model{
	protected $table = 'drafts';
	protected $dbfields = array('id', 'form_id', 'post', 'readonlys');

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
