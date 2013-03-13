<?php
/**
* config_radio.php
* Used when editing the config of a radio element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$this->questionconfig->renderTextField($question);

$this->questionconfig->renderAltField($question);

$this->questionconfig->renderOptionsField($question);

$this->questionconfig->renderSelectionsField($question);

$this->questionconfig->renderRequiredField($question);

$this->questionconfig->renderDependenciesField($question);
?>