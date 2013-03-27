<?php
/**
* config_wwustatus.php
* Used when editing the config of a wwustatus element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$this->questionconfig->renderTextField($question);

$this->questionconfig->renderAltField($question);

$this->questionconfig->renderRequiredField($question);

$this->questionconfig->renderDependenciesField($question);

$this->questionconfig->renderVisibilityField($question);
?>