<?php
/**
* config_question.php
* Used when editing the config of a question
*/

$question_config =(object) array(
	'text'=>'Type: ',
	'name'=>'config[type]',
	'type'=>'select',
	'validation'=>'required',
	'attributes'=>array('onchange'=>'FormEditor.loadElementConfig(\''.$question->id.'\', $(this).val());'),
	'options'=>$element_type_options, //we get this in Questions->edit()...
	'selected'=>array($question->config->type),	
);

$this->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));
?>