<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Permissions extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('permission');
		//$this->output->enable_profiler(TRUE);
	}

	function index(){

		echo "Can current user edit 'example-name'? ";
		if($this->authorization->can('viewresults', 'example-name')){
			echo "yes";
		}else{
			echo "no";
		}

		echo "<br />Does current user have 'superadmin' role? ";
		if($this->authorization->is('superadmin')){
			echo "yes";			
		}else{
			echo "no";
		}
	}

	function addToUser(){		
		if($_POST['username'] && $_POST['permission'] && $_POST['form']){
			$res = $this->permission->hasPermissionOnUserAndForm($_POST['permission'], $_POST['username'], $_POST['form']);
			if(!empty($res)){ echo json_encode(array('status'=>'fail')); return; }
			$perms = $this->permission->getBy('permission', $_POST['permission']);
			if(!empty($perms)){
				$this->permission->addToUser($perms[0]->id, $_POST['username'], $_POST['form']);
				echo json_encode(array('status'=>'success'));
				return;
			}
		}
		echo json_encode(array('status'=>'fail'));
	}

	function removeFromUser(){
		if($_POST['username'] && $_POST['permission'] && $_POST['form']){
			$res = $this->permission->hasPermissionOnUserAndForm($_POST['permission'], $_POST['username'], $_POST['form']);
			if(!empty($res)){  
				$perms = $this->permission->getBy('permission', $_POST['permission']);
				if(!empty($perms)){
					$this->permission->deleteFromUser($perms[0]->id, $_POST['username'], $_POST['form']);
					echo json_encode(array('status'=>'success'));
					return;
				}			
			}		
		}
		echo json_encode(array('status'=>'fail'));
	}

	function removeAllFromUser(){
		if($_POST['username'] && $_POST['form']){
			$this->permission->deleteAllOnUserAndForm($_POST['username'], $_POST['form']);					
			echo json_encode(array('status'=>'success'));
			return;
		}
		echo json_encode(array('status'=>'fail'));
		return;		
	}
}