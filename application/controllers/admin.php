<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends MY_Controller{	

	function __construct(){
		parent::__construct();
		//everything in here requires superadmin!		
		$this->_auth();
	}

	function index(){		
		$this->load->view('admin/dashboard');
	}

	function roles(){
		//do stuff to get whatever is needed
		$this->load->view('admin/roleconfig', $data);
	}

	function forms(){
		//do stuff to get whatever is needed		
		$this->load->view('admin/formadmin', $data);
	}

	/**
	* search for roles by name
	* (ajax)
	*/
	function searchRoles(){
		if(empty($_GET['q'])) return;
		$this->load->model('role');
		$roles = $this->role->getLike($_GET['q']);
		$data = array();		
		if(is_array($roles)){
			foreach($roles as $r){
				$data[] = array('name'=>$r->role, 'value'=>$r->id);
			}
		}
		$this->_jsonresp($data);
	}

	function addRole(){
		//print_r($_POST);
		if($_POST['role'] && $_POST['description']){
			$role = $this->role->getBy('role', $_POST['role']);
			if(!empty($role)){
				$this->_jsonresp(array('error'=>'Role by this name already exists!'));				
			}else{
				$role = $this->role->insert($_POST);
				$this->_jsonresp(array('role'=>$role));
			}
		}
	}

	/**
	* search for forms by name
	* (ajax)
	*/
	function searchForms(){
		if(empty($_GET['q'])) return;
		$this->load->model('form');
		$forms = $this->form->getWithNameLike($_GET['q']);
		$data = array();
		if(is_array($forms)){
			foreach($forms as $f){
				$data[] = array('name'=>$f->name, 'value'=>$f->name);
			}
		}
		$this->_jsonresp($data);
	}

	/**
	* search for permissions by name
	* (ajax)
	*/
	function searchPermissions(){
		if(empty($_GET['q'])) return;
		$this->load->model('permission');
		$permissions = $this->permission->getLike($_GET['q']);
		$data = array();
		if(is_array($permissions)){
			foreach($permissions as $p){
				$data[] = array('name'=>$p->permission, 'value'=>$p->id);
			}
		}
		$this->_jsonresp($data);
	}

	function getAssociatedToRole($role_id){
		$this->load->model('role');
		$data['role'] = $this->role->getById($role_id);
		$data['users'] = $this->role->getUsersOnRole($role_id);
		$data['forms'] = $this->role->getFormsOnRole($role_id);
		$this->load->model('permission');
		$data['permissions'] = $this->permission->getOnRole($role_id);		
		$this->_jsonresp($data);
	}

	function getAssociatedToUser($username){
		//todo
	}

	function getAssociatedToForm($form_name){
		//todo
	}

	function addUsersToRole($role_id){
		$this->load->model('role');
		if($_POST['users']){		
			foreach ($_POST['users'] as $user){
				$this->role->addToUser($role_id, $user);				
			}
		}
		$data['users'] = $this->role->getUsersOnRole($role_id);
		$this->_jsonresp($data);
	}

	function deleteUserFromRole($role_id, $username){
		$this->load->model('role');
		$this->role->deleteFromUser($role_id, $username);
		$this->_jsonresp(array('user'=>$username));
	}

	function addFormsToRole($role_id){
		$this->load->model('role');
		if($_POST['forms']){			
			foreach ($_POST['forms'] as $form){
				$this->role->addToForm($role_id, $form);
			}
		}
		$data['forms'] = $this->role->getFormsOnRole($role_id);
		$this->_jsonresp($data);
	}

	function deleteFormFromRole($role_id, $form_name){
		$this->load->model('role');
		$this->role->deleteFromForm($role_id, $form_name);
		$this->_jsonresp(array('form'=>$form_name));
	}

	function addPermissionsToRole($role_id){
		$this->load->model('permission');
		if($_POST['permission_ids']){			
			foreach ($_POST['permission_ids'] as $id){
				$this->permission->addToRole($id, $role_id);
			}
		}
		$data['permissions'] = $this->permission->getOnRole($role_id);
		$this->_jsonresp($data);
	}

	function deletePermissionFromRole($role_id, $permission_id){
		$this->load->model('permission');
		$this->permission->deleteFromRole($permission_id, $role_id);
		$this->_jsonresp(array('permission_id'=>$permission_id));
	}

	/**
	* Rejects anyone without superadmin role!
	*/
	private function _auth(){
		if(!$this->authorization->is('superadmin')){
			$this->_failAuthResp("You must have global role \"superadmin\" to access this page!  NO ACCESS for user: ".$this->authorization->username()."!");			
		}
	}

	/**
	* send data to client as a json formated response, and exit
	*/
	private function _jsonresp($data){
		echo json_encode($data);
		exit;
	}
}