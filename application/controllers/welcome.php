<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index(){

		$this->load->model('form');
		if($_GET['f']){
			$form = $this->form->getPublishedWithName($_GET['f']);

			if($form) $this->_redirect(site_url('forms/view/'.$form->name));
			return;
		}

		$forms = $this->form->getAll();
		$this->load->view('dashboard', array('forms'=>$forms));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */