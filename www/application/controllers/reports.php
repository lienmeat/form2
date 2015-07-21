<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MY_Controller{	

	function create($form_name){
		$this->load->model('form');
		$this->load->model('question');
		$form = $this->form->getPublishedWithName($form_name);
		$form->questions = $this->question->getByForm($form->id);
		$this->load->view('edit_report', array('form'=>$form));
	}

	function edit($id){
		$this->load->model('form');
		$this->load->model('question');
		$this->load->model('report');
		$report = $this->report->getById($id);
		$form = $this->form->getPublishedWithName($report->form);
		$form->questions = $this->question->getByForm($form->id);
		$this->load->view('edit_report', array('form'=>$form, 'report'=>$report));
	}	

	function save(){
		$post = $this->input->post(NULL, true);
		if($post['form'] && $post['title'] && $post['fields']){
			$post['fields'] = json_encode($post['fields']);
			$this->load->model('report');			
			try{
				$rep = $this->report->save($post);
			}catch(Exception $e){
				echo json_encode(array('status'=>'fail', 'err'=>$e));
			}			
			if($rep){
				echo json_encode(array('status'=>'success', 'report'=>$rep));
			}else{
				echo json_encode(array('status'=>'fail'));
			}
		}else{
			echo json_encode(array('status'=>'fail'));
		}
	}

	function view($form_name){
		$this->load->model('report');
		$reports = $this->report->getByForm($form_name, 'title ASC');
		$this->load->view('list_reports', array('reports'=>$reports));
	}

	function toCSV($report_id){

	}

	function toStats($report_id){

	}
}
?>