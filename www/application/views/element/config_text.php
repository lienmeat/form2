<?php
/**
* config_text.php
* Used when editing the config of a text element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$this->questionconfig->renderTextField($question);

$this->questionconfig->renderAltField($question);

$this->questionconfig->renderValueField($question);

$conf = (object) array(
	'text'=>'Size of text box:',
	'alt'=>'(in characters)',
	'name'=>'config[size]',
	'type'=>'text',
	'value'=>($question->config->size)?$question->config->size:'20',
	'validation'=>'required|is_natural_no_zero',
);
$this->questionconfig->renderQuestion($conf);

$conf = (object) array(
	'text'=>'Max # of characters allowed in text box:',	
	'name'=>'config[maxlength]',
	'type'=>'text',
	'value'=>($question->config->maxlength)?$question->config->maxlength:'0',
	'validation'=>'required|is_natural',
);
$this->questionconfig->renderQuestion($conf);

$this->questionconfig->renderValidationField($question);

$this->questionconfig->renderDependenciesField($question);
?>