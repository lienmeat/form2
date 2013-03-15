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
	'validation'=>'required',
);

$this->load->view('question/view_question', array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$question_config =(object) array(			
	'text'=>'Form name: ',
	'alt'=>'Determines the URL of the form (letters, numbers, and hyphens only!)',
	'name'=>'name',
	'type'=>'text',
);

if($mode != 'edit'){
	$question_config->validation = 'required|formnameformat|newformname';
}else{
	$question_config->value = $form->name;
	$question_config->attributes->disabled = 'disabled';
}

$this->load->view('question/view_question', array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));		


/*  Replaced by a more complex/sophisticated control
$question_config =(object) array(			
	'text'=>'Editors: (Comma separated usernames of people who can edit this form)',
	'alt'=>'(Do not put your own username here! You automatically have rights!)',
	'type'=>'text',
	'name'=>'config[editors]',
	'value'=>$form->config->editors,	
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));
*/


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

/*
$inputs = array(
	(object) array('type'=>'radio', 'value'=>'1', 'label'=>'Yes', 'selected'=>array($form->config->login_required)),
	(object) array('type'=>'radio', 'value'=>'0', 'label'=>'No', 'selected'=>array($form->config->login_required)),
);
*/
if(!$form->config->login_required) $form->config->login_required = 'N';
$question_config =(object) array(			
	'text'=>'Is a login required?',
	'alt'=>'(ensures only one answer per person, but requires them to login)',
	'name'=>'config[login_required]',
	'type'=>'radio',
	'options'=>array('Yes'=>'Y', 'No'=>'N'),
	'selected'=>array($form->config->login_required),
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

if(!$form->config->ad_groups) $form->config->ad_groups = array("*");
$question_config =(object) array(
	'text'=>'If a login is required, what WWU Active Directory user groups are permitted to view this form?',	
	'name'=>'config[ad_groups][]',
	'type'=>'checkbox',
	'options'=>array('All'=>'*', 'Student'=>'student', 'Staff'=>'staff', 'Faculty'=>'faculty', 'Administration'=>'administration'),
	'selected'=>$form->config->ad_groups,
	'dependencies'=>'config[login_required]=Y',
	'validation'=>'required',
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));

$question_config =(object) array(
	'text'=>'If limiting form viewing by AD groups isn\'t sufficient, you can permit certain users by username:',
	'alt'=>'(one per line, do not include yourself)',
	'name'=>'config[viewers]',
	'type'=>'textarea',
	'dependencies'=>'config[login_required]=Y',
	'value'=>$form->config->viewers,
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));
?>