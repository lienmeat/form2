<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resulttags extends MY_Controller {

	public function __construct() {
		parent::__construct();		
	}

	public function index()	{
		
	}

	function addResultTagsToResults(){
		if($_POST['resulttags'] && $_POST['results']){
			$this->load->model('resulttag');
			$creator = $this->authorization->username();
			$this->resulttag->addTagsToResults($_POST['resulttags'], $_POST['results'], $creator);
		}
	}

	function addResultTagsToResult(){
		if($_POST['resulttags'] && $_POST['result']){
			$this->load->model('resulttag');
			$tags = array();
			$resulttags = $_POST['resulttags'];
			if(!is_array($resulttags)){ $resulttags = array(); }
			foreach($_POST['resulttags'] as $tag){
				$res = false;
				$res = $this->resulttag->getByTag($tag);
				if(!$res){
					//add a tag
					$tag_tmp = $this->resulttag->insert(array('tag'=>$tag, 'creator'=>$this->authorization->username()));
				}else{
					$tag_tmp = $res[0];
				}
				if($tag_tmp){ //we had better have one!
					$creator = $this->authorization->username();
					$this->resulttag->addToResult($tag_tmp->id, $_POST['result'], $creator);
				}
				$tags[] = $tag_tmp;
			}
			echo json_encode(array('status'=>'success', 'tags'=>$tags, 'result'=>$_POST['result']));			
		}
	}


	function addResultTagToResultByTag(){
		if($_POST['resulttagname'] && $_POST['result']){
			$this->load->model('resulttag');
			$res = $this->resulttag->getByTag($_POST['resulttagname']);
			if(!$res){
				//add a tag
				$tag = $this->resulttag->insert(array('tag'=>$_POST['resulttagname'], 'creator'=>$this->authorization->username()));
			}else{
				$tag = $res[0];
			}
			if($tag){ //we had better have one!
				$creator = $this->authorization->username();
				$this->resulttag->addToResult($tag->id, $_POST['result'], $creator);
				echo json_encode(array('status'=>'success', 'tag'=>$tag, 'result'=>$_POST['result']));
			}
		}
	}

	function removeResultTagFromResult(){
		if($_POST['resulttag_id'] && $_POST['result']){
			$this->load->model('resulttag');			
			$res = $this->resulttag->removeFromResult($_POST['resulttag_id'], $_POST['result']);
			echo json_encode(array('status'=>'success', 'resulttag_id'=>$_POST['resulttag_id'], 'result'=>$_POST['result'], 'res'=>$res));			
		}
	}
}

/* End of file  */
/* Location: ./application/controllers/ */