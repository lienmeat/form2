<?php
/**
* config_form.php
* Used to edit the config of a form
*/
$question_config =(object) array(			
	'text'=>'Form title:',
	'alt'=>'(What the form says at the top when you fill it out)',
	'name'=>'title',
	'type'=>'text', 
	'value'=>$form->title,
	'attributes'=>(object) array('validation'=>'required'),
);

$this->load->view('question/view_question', array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$question_config =(object) array(			
	'text'=>'Form name: ',
	'alt'=>'Determines the URL of the form (letters, numbers, and hyphens only!)',
	'name'=>'name',
	'type'=>'text',
);

if($mode != 'edit'){
	$question_config->attributes->validation = 'required|formnameformat|newformname';
}else{
	$question_config->value = $form->name;
	$question_config->attributes->disabled = 'disabled';
}

$this->load->view('question/view_question', array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));		

$question_config =(object) array(			
	'text'=>'Editors: (Comma separated usernames of people who can edit this form)',
	'alt'=>'(Do not put your own username here! You automatically have rights!)',
	'type'=>'text',
	'name'=>'config[editors]',
	'value'=>$form->config->editors,	
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$question_config =(object) array(
	'text'=>'URL of special receiving script:',
	'alt'=>'(Use this ONLY if processing needs to happen outside of the form application)',
	'type'=>'text',
	'name'=>'config[processing_url]',
	'value'=>$form->config->processing_url
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$question_config =(object) array(			
	'text'=>'URL of script to forward the results to after the form is successfully submitted and saved.',
	'alt'=>'(optional, and not compatible with the receiving script)',
	'type'=>'text',
	'name'=>'config[forward_results_url]',
	'value'=>$form->config->processing_url,
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$question_config =(object) array(
	'text'=>'Thank You Text',
	'alt'=>'(Message given after form has been successfully completed)',
	'type'=>'textarea',
	'name'=>'config[thankyou]',
	'value'=>$form->config->thankyou,
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$inputs = array(
	(object) array('type'=>'radio', 'value'=>'1', 'label'=>'Yes', 'selected'=>array($form->config->login_required)),
	(object) array('type'=>'radio', 'value'=>'0', 'label'=>'No', 'selected'=>array($form->config->login_required)),
);

$question_config =(object) array(			
	'text'=>'Is a login required?',
	'alt'=>'(ensures only one answer per person, but requires them to login)',
	'name'=>'config[login_required]',
	'type'=>'radio',
	'inputs'=>$inputs,
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));
?>