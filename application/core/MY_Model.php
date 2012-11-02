<?php
class MY_Model extends CI_Model {
	protected $table = "";
	protected $dbfields = array();
	protected $pkey = 'id';

	function __construct($table=0, Array $dbfields=null){
		if(empty($this->table) && $table != 0) $this->table = $table;
		if(empty($this->dbfields) && $dbfields != null) $this->dbfields = $dbfields;
		//if(empty($this->table) || empty($this->dbfields)) die("MY_Model did not recieve either table or dbfields properties!"); 
		parent::__construct();
	}

	//gets all objs from db table
	function getAll($order_by=0){
		$this->db->select('*')->from($this->table);
		if($order_by) $this->db->order_by($order_by);
		$query = $this->db->get();
		return $query->result();
	}

	//function to get objs by a field
	function getBy($field, $value, $order_by=0){
		$this->db->select('*')->from($this->table)->where($field, $value);
		if($order_by) $this->db->order_by($order_by);
		$query = $this->db->get();
		return $query->result();
	}

	//gets obj by id from db
	function getById($obj_id){
		$objs = $this->getBy($this->pkey, $obj_id);
		if(!empty($objs))
			return $objs[0]; //only one will be returned, just get that object.
	}
	
	//insert obj having "$data" into db
	function insert($data){
		$data = (object) $data;
		if(is_object($data)){
			$data =& $this->filterData($data);
			$this->db->insert($this->table, $data);
			$success = $this->db->insert_id();
			if(!$success) $success = $this->db->affected_rows();
			return $success;
		}
	}
	
	//update obj having "$data" in db
	function update($data){
		$data = (object) $data;
		if(is_object($data)){
			$data =& $this->filterData($data);
			$pkey = $this->pkey;
			return $this->db->update($this->table, $data, array($pkey=>$data->$pkey));
		}
	}
	
	//deletes row from the db associated with this obj
	function delete($obj_id){
		$this->db->delete($this->table, array($this->pkey=>$obj_id));
	}

	function filterData($data){
		$data = (object) $data;
		if(is_object($data)){
			foreach($data as $f=>$v){
				if(!in_array($f, $this->dbfields)){
					unset($data->$f);
				}
			}
		}
		return $data;
	}	
}
?>