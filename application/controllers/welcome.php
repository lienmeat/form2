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
	public function index()
	{
		
		$this->load->library('inputs');
		//$options = array('Label1'=>'', 'Label2'=>'value2');
		$selected = array('harpoon2');
		$input = array('type'=>'radio', 'name'=>'butt', 'value'=>'harpoon', 'selected'=>$selected);
		$this->inputs->setConfig(json_decode(json_encode($input)));
		echo $this->inputs."&nbsp;1";
		$input['value'] = 'harpoon2';
		$this->inputs->setConfig(json_decode(json_encode($input)));
		echo $this->inputs."&nbsp;2";
		echo "<pre>".print_r($this->inputs, true)."</pre>";
		
		/*
		 //demo of form copy/dup
		$this->load->model('form');
		
		//create a form
		$form = array('name'=>'fake', 'title'=>'A TITLE', 'config'=>array('test'=>'yep'), 'creator'=>'eric.lien');
		$form = $this->form->insert($form);
		
		$this->load->model('question');
		//generate some questions
		$questions = array();
		for($i=0; $i<10; $i++){
			$questions[] = $this->question->insert(array('form_id'=>$form->id, 'order'=>$i, 'config'=>array('test'=>'yep_'.$i)));
		}		
		
		//duplicate the form
		$f_dup = $this->form->duplicate($form->id);
		
		$q_dups = $this->question->copyAllInForm($form->id, $f_dup->id);
		
		//delete forms 
		$this->form->delete($form->id);
		$this->form->delete($f_dup->id);

		//delete questions
		foreach($q_dups as $q){
			$this->question->delete($q->id);	
		}

		foreach($questions as $q){
			$this->question->delete($q->id);	
		}
		
		//print out results
		echo "<pre>\$form: ".print_r($form, true)." \$questions: ".print_r($questions, true)." \$f_dup: ".print_r($f_dup, true)." \$q_dups: ".print_r($q_dups, true)."</pre>";
		
		//end demo of form clone/dup
		//$this->load->view('welcome_message');
		*/
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */