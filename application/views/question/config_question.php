<?php
/**
* config_question.php
* Used when editing the config of a question
*/

$question_type_options = array(
	'Checkbox (select many)'=>'checkbox',
	'Dropdown'=>'select',
	'Dropdown Multi-Select'=>'multipleselect',
	'Heading'=>'heading',
	'Hidden'=>'hidden',
	'Information'=>'info',
	'Password'=>'password',
	'Radio Button (select one)'=>'radio',
	'Text (single-line)'=>'text',
	'Textarea (multi-line)'=>'textarea',
);

$question_config =(object) array(
	'text'=>'Type: ',
	'name'=>'config[type]',
	'type'=>'select',
	'attributes'=>array('validation'=>'required', 'onchange'=>'loadElementConfig(this);'),
	'options'=>$question_type_options,
	'selected'=>array($question->config->type),	
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));

$question_config =(object) array(
	'type'=>'text',
	'text'=>'Question Text: ',	
	'name'=>'config[text]',	
	'value'=>$question->config->text,
	'attributes'=>array('validation'=>'required'),
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));
?>