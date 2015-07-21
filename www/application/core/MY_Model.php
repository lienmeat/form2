<?php
class MY_Model extends CI_Model {
	protected $table = "";
	protected $dbfields = array();
	protected $pkey = 'id';

	function __construct($table=0, Array $dbfields=null){
		if(empty($this->table) && $table != 0) $this->setTable($table);
		if(empty($this->dbfields) && $dbfields != null) $this->setDBFields($dbfields);
		parent::__construct();
	}

	/**
	* set the name of the primary key of the default table
	*/
	function setPkey($pkey){
		$this->pkey = $pkey;
	}

	function getPkey(){
		return $this->pkey;
	}

	/**
	* set the default table to use
	*/
	function setTable($table){
		$this->table = $table;
	}

	function getTable(){
		return $this->table;
	}

	/**
	* set the fields inserts/updates can modify/use
	*/
	function setDBFields($fields){
		$this->dbfields = $fields;
	}

	function getDBFields(){
		return $this->dbfields;
	}

	//gets all objs from db table
	function getAll($order_by=0){
		$this->db->select('*')->from($this->table);
		if($order_by) $this->db->order_by($order_by);
		$query = $this->db->get();
		$objs = $query->result();
		if($objs){
		  return $this->decodeMany($objs);
		}else return array();
	}

	//function to get objs by a field
	function getBy($field, $value, $order_by=0){
		$this->db->select('*')->from($this->table)->where($field, $value);
		if($order_by) $this->db->order_by($order_by);
		$query = $this->db->get();
		$objs = $query->result();
		if($objs){
		  return $this->decodeMany($objs);
		}else return array();
	}

	//gets obj by id from db
	function getById($obj_id){
		$objs = $this->getBy($this->pkey, $obj_id);
		if(!empty($objs)){
		  $obj = $objs[0];
		  $obj =& $this->decode($obj);
		  return $obj; //only one will be returned, just get that object.
		}
	}
	
	//insert obj having "$data" into db
	function insert($data){
		$data = (object) $data;
		if(is_object($data)){
			$data =& $this->filterData($data);
			$data =& $this->encode($data);
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
			$data =& $this->encode($data);
			return $this->db->update($this->table, $data, array($pkey=>$data->$pkey));
		}
		return false;
	}
	
	//deletes row from the db associated with this obj
	function delete($obj_id){
		return $this->db->delete($this->table, array($this->pkey=>$obj_id));
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
	
	/**
	* Same as decode but with an array of objs
	*/
	function decodeMany(Array $objs){
	  if(!empty($objs)){
	  	$numb = count($objs);
	    for($i=0; $i<$numb; $i++){
	      if(!empty($objs[$i]->config)){
	      	$objs[$i] = $this->decode($objs[$i]);
	      }else{
	      	break;
	      }
	    }
	  }
	  //print_r($objs);
	  return $objs;
	}
	
	/**
	* Same as encode but with an array of objs
	*/
	function encodeMany(Array $objs){
	  if(!empty($objs)){
	  	$numb = count($objs);
	    for($i=0; $i<$numb; $i++){
	      if(!empty($objs[$i]->config)){
	      	$objs[$i] = $this->encode($objs[$i]);
	      }else{
	      	break;
	      }
	    }
	  }
	  return $objs;
	}
	
	/**
	* If a config variable exists, it will be decoded from json to an object/array
	* @param object $obj Object to decode config variable for
	*/
	function decode($obj){
	  if($obj->config and (!is_array($obj->config) and !is_object($obj->config))){
	    $obj->config = json_decode($obj->config);
	  }	  
	  return $obj;
	}
	
	/**
	* If a config variable exists, it will be encoded from object/array to json
	* @param object $obj Object to encode config variable for
	*/
	function encode($obj){
	  if($obj->config and (is_array($obj->config) or is_object($obj->config))){
	    $obj->config = json_encode($obj->config);
	  }
	  return $obj;
	}	
}
?>
