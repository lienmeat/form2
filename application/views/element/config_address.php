<?php
/**
* config_select.php
* Used when editing the config of a address element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$this->questionconfig->renderTextField($question);

$this->questionconfig->renderAltField($question);

$conf = (object) array(
	'text'=>'Do you want to include a full name input?',
	'name'=>'config[fullname]',
	'type'=>'radio',
	'options'=>(object) array('Yes'=>'Y', 'No'=>'N'),
	'selected'=>array($question->config->fullname),
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

$conf->text = 'Do you want to include a dropdown for countries?';
$conf->name =	'config[countries]';
$conf->selected =array($question->config->countries);
$this->questionconfig->renderQuestion($conf);

$this->questionconfig->renderRequiredField($question);

$this->questionconfig->renderDependenciesField($question);
?>