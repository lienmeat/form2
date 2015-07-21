<?php
/**
* Example of how you might set up a model by extending MY_Model
*/
class Device extends MY_Model{
	protected $table = 'devices';
	protected $dbfields = array('id', 'user_id', 'ssid', 'bssid', 'ip', 'public_ip', 'deleted', 'banned', 'last_hit', 'last_connected');

	function save($data){
		$data = (object) $data;		
		//hack to automatically change a mac to id.
		if($data->mac && !$data->id) $data->id = $data->mac;
		if(is_object($data)){			
			$data->last_hit = date('Y-m-d H:i:s'); //last time hit our server
			//if connected to wifi, set this time as last time connected
			if(!empty($data->ip) && $data->ip != '0.0.0.0'){ 
				$data->last_connected = date('Y-m-d H:i:s');
			}
			$data =& $this->filterData($data);
			if(!empty($data) and $data->id){ //id is required to insert/update
				$fields = "(";
				$values_ins = "(";
				$values_arr = array();
				$first = true;
				foreach ($data as $key => $value) {
					if($first) $first = false;
					else{
						$fields.=", ";
						$values_ins.=", ";
						$update_args.=", ";
					}
					$fields.="`$key`";
					$values_ins.="?";
					$values_arr[] = $value;
					$update_args.="`$key`=?";					
				}
				$fields.=")";
				$values_ins.=")";
				//double the arguments for replacements, in order, after each other
				$values_arr = array_merge($values_arr, $values_arr);
				$q="INSERT INTO ".$this->table." $fields VALUES $values_ins ON DUPLICATE KEY UPDATE $update_args";
				$this->db->query($q, $values_arr);
				if($this->db->affected_rows()){
					return $data->id;
				}
			}
		}
		return false;
	}
}

?>