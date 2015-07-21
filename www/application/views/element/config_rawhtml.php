<?php
/**
* config_rawhtml.php
* Used when editing the config of a rawhtml element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$question_config =(object) array(
	'text'=>'Text:',
	'alt'=>'(optional) (What should this element say?)',
	'name'=>'config[text]',
	'type'=>'textarea',
	'value'=>$question->config->text,
);
$this->questionconfig->renderQuestion($question_config);

$question_config =(object) array(
	'text'=>'Alt Text:',
	'alt'=>'(optional) (Text like this describing a question)',
	'name'=>'config[alt]',
	'type'=>'textarea',
	'value'=>$question->config->alt,
);
$this->questionconfig->renderQuestion($question_config);

$question_config =(object) array(
	'text'=>'Html Source:',
	'alt'=>'(HTML, CSS, Javascript code which will be rendered to the form. For a form value for an input to show in the result view, it\'s "name" attribute must correspond to the name of this element!)',
	'name'=>'config[html]',
	'type'=>'textarea',
	'value'=>$question->config->html,
);
$this->questionconfig->renderQuestion($question_config);

$this->questionconfig->renderDependenciesField($question);
?>