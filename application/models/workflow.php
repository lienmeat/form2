<?php
/**
* Model controls access to Workflow records
*/
class Workflow extends MY_Model{
	protected $table = 'workflows';
	protected $dbfields = array('id', 'question_id', 'formresult_id', 'completed', 'response');


	/**
	* Grab workflow rows from table on a question and result
	* @param string $question_id
	* @param string $formresult_id
	* @return object|false
	*/
	function getOnQuestionAndResult($question_id, $formresult_id){
		$args['formresult_id'] = $formresult_id;
		$args['question_id'] = $question_id;		
		$res = $this->db->select()->from($this->table)->where($args)->get();
		$res = $res->result();
		if(!empty($res)){
			return $res[0];
		}else{
			return false;
		}
	}

	/**
	* Grab workflow rows from table on a result
	* @param string $formresult_id
	* @param bool $completed Whether to get completed workflows
	* @return array
	*/
	function getOnResult($formresult_id){
		$args['formresult_id'] = $formresult_id;		
		$res = $this->db->select()->from($this->table)->where($args)->get();
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
			$data->id = uniqid('');
    	if(parent::insert($data)){
			return $this->getById($data->id);
		}else{
		  return false;
		}
	}
}
?>
