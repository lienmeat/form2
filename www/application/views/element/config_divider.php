<?php
/**
* config_info.php
* Used when editing the config of a divider element
* added after question config options!
*/
$this->questionconfig->renderTextField($question);

$conf = (object) array(
	'text'=>'Size of divider text?',
	'alt'=>'(smaller number, bigger text)',
	'name'=>'config[heading]',
	'type'=>'radio',
	'options'=>(object) array('H1'=>'h1', 'H2'=>'h2', 'H3'=>'h3', 'H3'=>'h3', 'H4'=>'h4', 'H5'=>'h5', 'H6'=>'h6'),
	'selected'=>array($question->config->heading),
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

$conf = (object) array(
	'text'=>'Color of divider background?',	
	'name'=>'config[divider_bg_class]',
	'type'=>'radio',
	'options'=>(object) array('<div class="divider_bg_1" style="display: inline-block; width: 20px; height: 20px;"></div>'=>'divider_bg_1', '<div class="divider_bg_2" style="display: inline-block; width: 20px; height: 20px;"></div>'=>'divider_bg_2'),
	'selected'=>array($question->config->divider_bg_class),
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

$this->questionconfig->renderDependenciesField($question);
?>