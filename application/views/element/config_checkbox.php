<?php
/**
* config_checkbox.php
* Used when editing the config of a checkbox element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$this->questionconfig->renderTextField($question);

$this->questionconfig->renderAltField($question);

$this->questionconfig->renderOptionsField($question);

$conf = (object) array(
	'text'=>'How many columns per row?',
	'alt'=>'(default is 2)',
	'name'=>'config[columns]',
	'type'=>'text',
	'value'=>($question->config->columns) ? "{$question->config->columns}" : "2",
	'validation'=>'required|is_natural_no_zero',
);
$this->questionconfig->renderQuestion($conf);

$this->questionconfig->renderSelectionsField($question);

$this->questionconfig->renderRequiredField($question);

$this->questionconfig->renderDependenciesField($question);
?>