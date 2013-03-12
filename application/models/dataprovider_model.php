<?php
/**
* This is a general-use model to be used use with data providers (which are just things we need to access outside of * the formit2 db)!
* It will not work in the manner that is typical with models, as it needs to be far more flexible (each able to connect to differnt dbs and such)!
*/

class Dataprovider_model extends MY_Model{
	
	/**
	* Connect to the correct database (THIS IS REQUIRED TO RUN!)
	*/
	function connect(Array $config){
		$config['pconnect'] = FALSE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = '';
		if(!$config['dbdriver']) $config['dbdriver'] = 'mysqli';
		if(!$config['hostname']) $config['hostname'] = DATABASE_HOST;

		$this->db = $this->load->database($config, TRUE);	
	}

	function getOptions($query, $label_col, $value_col=null){
		$res = $this->db->query($query);
		$res = $res->result();
		if(!empty($res) and $res[0]->$label_col){
			$options = array();
			foreach($res as $r){
				if($value_col){
					$options[$r->$label_col] = $r->$value_col;
				}else{
					$options[$r->$label_col] = $r->$label_col;
				}
			}
			return $options;
		}else{
			return array();
		}
	}	

	/**
	*  Close this database connection
	*/
	function close(){
		$this->db->close();
	}	
}
?>