<?php
/**
* Model controls access to Question records
*/
class Question extends MY_Model{
	protected $table = 'questions';
	protected $dbfields = array('id', 'form_id', 'order', 'name', 'config');

	/**
  * Get all questions in form (in order)
  * @param string $form_id
  * @return array Questions (may be empty)
  */
	function getByForm($form_id, $order_by=null){
	  if($form_id){
      if(!$order_by)	$order_by = 'order ASC';
	    $this->db->select()->from($this->table)->where('form_id', $form_id)->order_by($order_by);
	    $query = $this->db->get();
	    return $this->decodeMany($query->result());
	  }else{
	    return array();
	  }
	}

	function isUniquelyNamed($question_id, $form_id, $name){
		$q = "SELECT * FROM {$this->table} WHERE `id` != ? AND `form_id` = ? AND `name` != ?";
		$res = $this->db->query($q, array($question_id, $form_id, $name));
		$questions = $res->result();
		if(empty($questions)) return true;
		else return false;
	}

	function deleteByForm($form_id){
		$questions = $this->getByForm($form_id);
		foreach($questions as $q){
			$this->delete($q->id);
		}
	}

	/**	
	* Makes a copy of all questions on a form
	* @param string $from_form ID of form to copy from
	* @param string $to_form ID of form to copy to
	*/
	function copyAllInForm($from_form, $to_form){
		//$this->db->trans_start(); //innodb is pretty slow.  Over doubling execution times!
		$this->db->select()->from($this->table)->where('form_id', $from_form);
		$query = $this->db->get();
		$questions = $query->result();
		if(!empty($questions)){
			$temp = array();
			foreach($questions as $q){
				$q->form_id =& $to_form;
				$q->id = uniqid('');
				$temp[] = (array) $q;				
			}
			unset($questions);
			if($this->db->insert_batch($this->table, $temp)){
				$this->db->select()->from($this->table)->where('form_id', $to_form);
				$query = $this->db->get();
				$res = $query->result();
				//$this->db->trans_complete();
				return $res;
			}
		}
		//$this->db->trans_complete();
		return false;
	}

	/**
	* Updates the order of the questions to be the same order as the ID's passed in
	* @param array $question_ids Array of question ids that need reordering
	*/
	function reorder(Array $question_ids=null){
		if(!$question_ids) return false;
		$data = array();
		foreach($question_ids as $k=>$id){
			$data[] = array('id'=>$id, 'order'=>$k);
		}
		$this->db->update_batch($this->table, $data, 'id');
	}
	
	/**
	* Makes a copy of a question
	* @param string $question_id ID of question to copy
	* @param string $form_id ID of form to associate question with
	* @return object copy of question
	*/
	function copy($question_id, $form_id){
	  if(!$form_id or !$question_id) return false;
	  $query = $this->db->query("SELECT * FROM `{$this->table}` WHERE `id` = ?", array($question_id));
    $res = $query->result();
    if(!$res[0]) return false;
    $res[0]->form_id = $form_id;
  	return $this->insert($res[0]);
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
