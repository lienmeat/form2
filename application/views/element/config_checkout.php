<?php
/**
* config_checkout.php
* Used when editing the config of a checkout element
* added after question config options!
*/

//Payment_Type
//text field  //render hidden
$conf = (object) array(
	'text'=>'Payment/Donation Type (Student Acct, Other, etc.):',
	'type'=>'text',
	'value'=>$question->config->Payment_Type,
	'name'=>'config[Payment_Type]',
	'validation'=>'required|max_length[50]',
);
$this->questionconfig->renderQuestion($conf);

//Payment_for
//text field  //render hidden
$conf = (object) array(
	'text'=>'SHORT description of what this payment is for or Project-Object code:',
	'type'=>'text',
	'value'=>$question->config->Payment_for,
	'name'=>'config[Payment_for]',
	'validation'=>'required|max_length[50]',
);
$this->questionconfig->renderQuestion($conf);

//Pay_to_Account_Number
//text field //render as text or hidden if pre-filled
$conf = (object) array(
	'text'=>'WWU account number to credit (7-digit AR #):',
	'type'=>'text',
	'value'=>$question->config->Pay_to_Account_Number,
	'name'=>'config[Pay_to_Account_Number]',
	'validation'=>'max_length[50]',
);
$this->questionconfig->renderQuestion($conf);

//Payment_Amount
//text field  //render as readonly text
if($question->config->Payment_Amount){
	$amount = $question->config->Payment_Amount;
}else{
	$amount = '0.00';
}
$conf = (object) array(
	'text'=>'Payment Amount:',
	'alt'=>'(Will be overridden if using checkout items to calculate price instead of this fixed price!)',
	'type'=>'text',
	'value'=>$amount,
	'name'=>'config[Payment_Amount]',
	'validation'=>'max_length[10]',
);
$this->questionconfig->renderQuestion($conf);

//is amount editable by person filling out form?
if($question->config->amount_editable){
	$selected = (array) $question->config->amount_editable;	
}else{
	$selected = array('no');
}
$conf = (object) array(
	'text'=>'Should amount be editable by person filling out form?',	
	'type'=>'radio',
	'name'=>'config[amount_editable]',
	'validation'=>'required',
	'options'=>(object) array('No'=>'no', 'Yes'=>'yes'),
	'selected'=>$selected,
);
$this->questionconfig->renderQuestion($conf);

//Payment Methods useable with this payment
//payment_methods
//checkbox creditcard, echeck //render as radio button
if($question->config->payment_methods){
	$selected = (array) $question->config->payment_methods;	
}

$conf = (object) array(
	'text'=>'Payment Methods:',
	'alt'=>'(How will the person be able to pay?)',
	'type'=>'checkbox',	
	'name'=>'config[payment_methods]',
	'validation'=>'required',
	'options'=>(object) array('Credit Card'=>'creditcard', 'e-Check'=>'echeck'),
	'selected'=>$selected,
);
$this->questionconfig->renderQuestion($conf);

//cupon_codes
//textarea to define what codes exist/discount amounts //render as textarea

//$this->questionconfig->renderDependenciesField($question);

//echeck or cc info inputs dependent on payment method selected on view mode!
//js validation of cc or ec inputs
//js ajax to send payment data to payments controller
//failed payment to block form submition
?>