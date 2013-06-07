<?php
/**
* config_text.php
* Used when editing the config of a text element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);


$conf = (object) array(
	'type'=>'textarea',
	'text'=>'Text that will display',
	'alt'=>'Due to liability concerns, this text cannot be edited!',
	'name'=>'config[text]',
	'value'=>"<p>Walla Walla University's use of your electronic signature requires your consent. You have the right to have this document provided to you in paper form at no charge. You also have the right to withdraw your consent, to use your electronic signature, by contacting a University representative. Consequences for withdrawing your consent, if any, will be discussed with you when the withdrawal request is received.</p>
<p>By typing in your name and clicking on the \"Submit\" button below, you are providing electronic consent to use your electronic signature and agree that all information you are providing to Walla Walla University on this document is true, complete and correct to the best of your knowledge.</p>",
	'attributes'=>(object) array('readonly'=>'readonly'),
);
$this->questionconfig->renderQuestion($conf);

$this->questionconfig->renderDependenciesField($question);
?>