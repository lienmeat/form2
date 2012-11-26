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
	'attributes'=>array('validation'=>'required', 'onchange'=>'FormEditor.loadElementConfig(\''.$question->id.'\', $(this).val());'),
	'options'=>$question_type_options,
	'selected'=>array($question->config->type),	
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));
?>