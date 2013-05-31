<?php

class Resulttag extends RetRecord_Model{
	protected $table = 'resulttags';
	protected $dbfields = array('id', 'tag', 'creator');
	

	/**
	* Adds a tag to a result
	* @param string $tag_id
	* @param string $result_id
	* @param string $creator (username of person who created the association)
	*/
	function addToResult($tag_id, $result_id, $creator){
		$existing = $this->db->select()->from('results_resulttags')->where(array('resulttag_id'=>$tag_id, 'result_id'=>$result_id))->get()->result();
		if(!empty($existing)) return true;
		else{
			return $this->db->insert('results_resulttags', array('resulttag_id'=>$tag_id, 'result_id'=>$result_id, 'creator'=>$creator));
		}
	}

	/**
	* Removes a tag from a result
	* @param string $tag_id
	* @param string $result_id	
	*/
	function removeFromResult($tag_id, $result_id){
		$existing = $this->db->select()->from('results_resulttags')->where(array('resulttag_id'=>$tag_id, 'result_id'=>$result_id))->get()->result();
		if($existing){
			$res = $this->db->delete('results_resulttags', array('id'=>$existing[0]->id));
			return true;
		}
		return false;
	}

	/**
	* Adds a tags to results
	* @param array $tag_ids
	* @param array $result_ids
	* @param string $creator (username of person who created the associations)
	*/
	function addTagsToResults(Array $tag_ids, Array $result_ids, $creator){
		foreach($tag_ids as $tag){
			foreach($result_ids as $result){
				$this->addToResult($tag, $result, $creator);
			}
		}
	}

	/**
	* Gets Tags by form result id
	* @param string $result_id
	* @return array
	*/
	function getByResult($result_id){
		return $this->db->select($this->table.".*")->distinct()->from($this->table)->join('results_resulttags', "{$this->table}.id = results_resulttags.resulttag_id", 'left')->where('results_resulttags.result_id', $result_id)->get()->result();
	}

	/**
	* Gets Tags by tag field
	* @param string $tag
	* @return array
	*/
	function getByTag($tag){
		return $this->db->select()->from($this->table)->where('tag', $tag)->get()->result();
	}

	function insert($data){
		if(is_array($data)) $data = (object) $data;
		elseif(!is_object($data)) return false;
		if($data->tag){
			$data->tag = strtoupper(trim($data->tag));
		}
		return parent::insert($data);
	}

	function update($data){
		if(is_array($data)) $data = (object) $data;
		elseif(!is_object($data)) return false;
		if($data->tag){
			$data->tag = strtoupper(trim($data->tag));
		}
		return parent::update($data);
	}
}
?>