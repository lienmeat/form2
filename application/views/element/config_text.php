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

$this->questionconfig->renderValidationField($question);

$this->questionconfig->renderDependenciesField($question);
?>