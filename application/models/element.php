<?php
/**
* Model controls access to element records
*/
class Element extends RetRecord_Model{
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
}

?>