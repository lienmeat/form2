<?php
/**
* config_textarea.php
* Used when editing the config of a textarea element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$this->questionconfig->renderTextField($question);

$this->questionconfig->renderAltField($question);

$this->questionconfig->renderValueField($question);


$conf = (object) array(
	'text'=>'Width of textarea:',
	'alt'=>'(in characters)',
	'name'=>'config[cols]',
	'type'=>'text',
	'value'=>($question->config->cols)?$question->config->cols:'50',
	'validation'=>'required|is_natural_no_zero',
);
$this->questionconfig->renderQuestion($conf);

$conf = (object) array(
	'text'=>'Rows of text (height):',
	'alt'=>'(in characters)',
	'name'=>'config[rows]',
	'type'=>'text',
	'value'=>($question->config->rows)?$question->config->rows:'5',
	'validation'=>'required|is_natural_no_zero',
);
$this->questionconfig->renderQuestion($conf);

$conf = (object) array(
	'text'=>'Max # of characters allowed in textarea:',	
	'name'=>'config[maxlength]',
	'type'=>'text',
	'value'=>($question->config->maxlength)?$question->config->maxlength:'0',
	'validation'=>'required|is_natural',
);
$this->questionconfig->renderQuestion($conf);

$this->questionconfig->renderValidationField($question);

$this->questionconfig->renderDependenciesField($question);
?>