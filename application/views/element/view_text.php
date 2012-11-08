<?php
//has only one input, others could have MANY more...
$this->inputs->setConfig($config->element->inputs[0]);
$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$config->name.'_fi2');
$this->inputs->setName($config->name);
$this->inputs->setAttribute('id', $id."_input0");
echo $tthis->inputs;
?>

<!--<input class="questionName_fi2" type="text" name="questionname" onclick="" validation="trim|min_length[3,4,5,6]" validationmessage="Test Message">-->