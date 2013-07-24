<?php
/**
* config_date.php
* Used when editing the config of a date element
* added after question config options!
*/

$this->questionconfig->renderNameField($question);

$this->questionconfig->renderTextField($question);

$this->questionconfig->renderAltField($question);

$minday = (isset($question->config->mindays)) ? $question->config->mindays : 0;
$minmonth = (isset($question->config->minmonths)) ? $question->config->minmonths : 0;
$minyear = (isset($question->config->minyears)) ? $question->config->minyears : 0;
$html = "
<script>
$(document).ready(
	function() {
		$('#mindays').spinner();
		$('#minmonths').spinner();
		$('#minyears').spinner();
		$('#mindate').datepicker({dateFormat: 'yy-mm-dd'});
	}
);
</script>
<input id=\"minyears\" name=\"config[minyears]\" value=\"$minyear\" size=\"3\"> Years,&nbsp;&nbsp;
<input id=\"minmonths\" name=\"config[minmonths]\" value=\"$minmonth\" size=\"3\"> Months,&nbsp;&nbsp;
<input id=\"mindays\" name=\"config[mindays]\" value=\"$minday\" size=\"3\"> Days
<br /> OR  an absolute date: <br />
<input type=\"text\" id=\"mindate\" name=\"config[mindate]\" value=\"{$question->config->mindate}\">
";
$question_config =(object) array(
	'text'=>'Minimum date allowed to be chosen:',
	'alt'=>'(A relative date from the time the user starts filling out the form, or an absolute date)',	
	'name'=>'mindate',
	'type'=>'rawhtml',
	'html'=>$html,
);
$this->questionconfig->renderQuestion($question_config);



$maxday = (isset($question->config->maxdays)) ? $question->config->maxdays : 0;
$maxmonth = (isset($question->config->maxmonths)) ? $question->config->maxmonths : 0;
$maxyear = (isset($question->config->maxyears)) ? $question->config->maxyears : 0;

$html = "
<script>
$(document).ready(
	function() {
		$('#maxdays').spinner();
		$('#maxmonths').spinner();
		$('#maxyears').spinner();
		$('#maxdate').datepicker({dateFormat: 'yy-mm-dd'});
	}
);
</script>
<input id=\"maxyears\" name=\"config[maxyears]\" value=\"$maxyear\" size=\"3\"> Years,&nbsp;&nbsp;
<input id=\"maxmonths\" name=\"config[maxmonths]\" value=\"$maxmonth\" size=\"3\"> Months,&nbsp;&nbsp;
<input id=\"maxdays\" name=\"config[maxdays]\" value=\"$maxday\" size=\"3\"> Days
<br /> OR  an absolute date: <br />
<input type=\"text\" id=\"maxdate\" name=\"config[maxdate]\" value=\"{$question->config->maxdate}\">
";
$question_config =(object) array(
	'text'=>'Maximum date allowed to be chosen:',
	'alt'=>'(A relative date from the time the user starts filling out the form, or an absolute date)',
	'name'=>'maxdate',
	'type'=>'rawhtml',
	'html'=>$html,
);
$this->questionconfig->renderQuestion($question_config);

if( is_array($question->config->disabled_days) ) {
	$value = implode("\n", $question->config->disabled_days);
}else{
	$value = '';
}
$question_config =(object) array(
	'text'=>'Blackout Dates: (unselectable dates)',
	'alt'=>'(One per line. YYYY-MM-DD format!)',
	'name'=>'config[disabled_days]',
	'type'=>'textarea',
	'value'=>$value,
);
$this->questionconfig->renderQuestion($question_config);

$this->questionconfig->renderRequiredField($question);

$this->questionconfig->renderDependenciesField($question);
?>