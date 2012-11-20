<?php
/**
* config_text.php
* Used when editing the config of a text element
* added after question config options!
*/

$question_config =(object) array(			
	'type'=>'text',
	'name'=>'config[name]',
	'text'=>'Question Name: ',
	'alt'=>'(a-z, 0-9, -, _)',
	'value'=>$question->config->name,
	'attributes'=>array('validation'=>'required|alpha_dash'),
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));

$question_config =(object) array(
	'type'=>'textarea',
	'text'=>'Question Text: ',	
	'name'=>'config[text]',	
	'value'=>$question->config->text,
	'attributes'=>array('validation'=>'required'),
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));

$question_config =(object) array(
	'type'=>'textarea',
	'text'=>'Alt Text: ',
	'alt'=>'(Text like this describing a question)',	
	'name'=>'config[alt]',	
	'value'=>$question->config->alt,
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));


$question_config =(object) array(	
	'type'=>'textarea',
	'text'=>'Default Answer: (optional)',
	'name'=>'config[value]',
	'value'=>$question->config->value,
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));

$question_config =(object) array(			
	'type'=>'textarea',
	'text'=>'Input Attributes:',
	'alt'=>'(any html attribute you want to assign to the actual input itself, one per line. ex. style=color: red;)',
	'name'=>'config[attributes]',
	'value'=>json_encode($question->config->attributes),
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));

$question_config =(object) array(			
	'text'=>'Input Validation:',
	'alt'=>'(validation you want to assign to the actual input itself, separated by "|". ex. required|min_length[3])',
	'name'=>'config[attributes][validation]',
	'type'=>'textarea',
	'value'=>$question->config->attributes->validation,
);

$this->load->view('question/view_question',array('id'=>uniqid(''), 'config'=>$question_config));
?>