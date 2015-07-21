<?php

class Help extends RetRecord_Model{
	protected $table = 'helps';
	protected $dbfields = array('id', 'searchterms', 'help');

	function search($input){
		$this->db->select()->from($this->table)->like('searchterms', $input);
		$res = $this->db->get();
		return $res->result();
	}	
}
?>