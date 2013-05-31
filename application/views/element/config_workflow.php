<?php
/**
* config_text.php
* Used when editing the config of a text element
* added after question config options!
*/
if($question->config->instructions){
	$instructions = $question->config->instructions;
}else{
	$instructions = 'Please select an option, or forward this workflow to someone else.';
}
$conf = (object) array(
	'text'=>'Instructions:',
	'alt'=>'(To tell the person/s who get this workflow what to do.)',
	'name'=>'config[instructions]',
	'type'=>'textarea',
	'value'=>$instructions,
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

if($question->config->email_addresses){
	$email_addresses = implode("\n", $question->config->email_addresses);
}else{
	$email_addresses = $this->authorization->email();
}
$conf = (object) array(
	'text'=>'Email addresses to send workflow to:',
	'alt'=>'(One per line! Person\'s MUST be able to log in to view/edit workflow.)',
	'name'=>'config[email_addresses]',
	'type'=>'textarea',
	'value'=>$email_addresses,	
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

if($question->config->usernames){
	$usernames = implode("\n", $question->config->usernames);
}
$conf = (object) array(
	'text'=>'Usernames permitted to complete workflow:',
	'alt'=>'(One per line! Leave blank to permit any username!)',
	'name'=>'config[usernames]',
	'type'=>'textarea',
	'value'=>$usernames,
);
$this->questionconfig->renderQuestion($conf);

if($question->config->email_subject){
	$email_subject = $question->config->email_subject;
}else{
	$email_subject = 'Workflow requires your attention';
}
$conf = (object) array(
	'text'=>'Subject line of email:',
	'name'=>'config[email_subject]',
	'type'=>'textarea',
	'value'=>$email_subject,
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

if($question->config->email_body){
	$email_body = $question->config->email_body;
}else{
	$email_body = "A workflow requires your attention!\nPlease follow the instructions on the workflow, after following the link below:";
}

$conf = (object) array(
	'text'=>'Body of email:',	
	'alt'=>'(URL to workflow will always be present at the bottom of all workflow notifications!)',
	'name'=>'config[email_body]',
	'type'=>'textarea',
	'value'=>$email_body,
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

if($question->config->options){
	foreach($question->config->options as $label=>$value){
		if($first) $first = false;
		else $o_txt.="\n";
		if($label == $value) $o_txt.=$label;
		else $o_txt.=$label.":".$value;
	}
}
$conf = (object) array(
	'text'=>'Options the person completing the workflow can choose:',
	'alt'=>"(One per line!, examples:<br />Approve<br />Deny)",
	'name'=>'config[options]',
	'value'=>$o_txt,
	'type'=>'textarea',
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

if($question->config->allow_forwarding) $selected = array($question->config->allow_forwarding);
else $selected = array('No');
$conf = (object) array(
	'text'=>'Allow the recipient to forward this workflow to others?',
	'name'=>'config[allow_forwarding]',
	'type'=>'radio',
	'options'=>(object) array('Yes'=>'Yes', 'No'=>'No'),
	'selected'=>$selected,
	'validation'=>'required',
);
$this->questionconfig->renderQuestion($conf);

//$this->questionconfig->renderDependenciesField($question); //meh, this leads to some interesting probs I haven't solved yet.
?>