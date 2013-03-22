<?php
/**
* This library exists to separate controler code out into a library for doing things with questions' configs (formating configs, rendering)
* NOTICE!  EVERY SINGLE QUESTION TYPE MUST IMPLEMENT A format<Questiontype>() method in order to be saved to DB after an edit!
* This is so we can streamline all our rendering for config and formating to database in one place, and not have tons of separate ways of doing it!
*/
class Questionconfig{
	//codeigniter instance
	private $CI;

	function __construct(){
		$this->CI =& get_instance();
	}

	/**
	* Formats a text question into what should be saved to db
	* @param array $question
	* @return array
	*/
	function formatText($question){
		$question = $this->formatValidationField($question);
		$question = $this->formatDependenciesField($question);
		return $question;
	}

	/**
	* Formats a textarea question into what should be saved to db
	* @param array $question
	* @return array
	*/
	function formatTextarea($question){
		$question = $this->formatValidationField($question);
		$question = $this->formatDependenciesField($question);
		return $question;
	}

	/**
	* Formats the select field for db insert
	*/
	function formatSelect($question){
		$question = $this->formatOptions($question);
		$question = $this->formatSelected($question);
		$question = $this->formatRequiredField($question);
		$question = $this->formatDependenciesField($question);		
		return $question;
	}

	function formatCheckbox($question){
		$question = $this->formatOptions($question);
		$question = $this->formatSelected($question);
		$question = $this->formatRequiredField($question);
		$question = $this->formatDependenciesField($question);		
		return $question;
	}

	function formatRadio($question){
		$question = $this->formatOptions($question);
		$question = $this->formatSelected($question);
		$question = $this->formatRequiredField($question);
		$question = $this->formatDependenciesField($question);		
		return $question;
	}

	function formatInfo($question){
		$question = $this->formatDependenciesField($question);
		return $question;
	}

	function formatDivider($question){
		$question = $this->formatDependenciesField($question);
		return $question;
	}

	function formatAddress($question){
		$question = $this->formatDependenciesField($question);
		$question = $this->formatRequiredField($question);
		return $question;
	}

	/**
	* Formats "unparsed" attribute string into object
	* @param string $attributes
	* @return object
	*/
	function formatUnparsedAttributes($attributes){
		$attrs = explode('\n', $attributes);
		$attributes = array();
		foreach ($attrs as $a) {
			$attr = explode('=', $a);
			$attributes[$attr[0]] = $attr[1];			
		}
		return (object) $attributes;
	}	

	function renderNameField($question){
		$name = $question->config->name;
		if(!$name) $name = "element_".$question->id;
		$question_config =(object) array(			
		'type'=>'text',
		'name'=>'config[name]',
		'text'=>'Element Name: ',
		'alt'=>'(a-z, 0-9, -, _)',
		'value'=>$name,
		'validation'=>'required|alpha_dash',
		);

		$this->renderQuestion($question_config);		
	}

	function renderTextField($question){
		$question_config =(object) array(
			'type'=>'textarea',
			'text'=>'Question Text: ',	
			'name'=>'config[text]',	
			'value'=>$question->config->text,
			'validation'=>'required',
		);

		$this->renderQuestion($question_config);
	}

	function renderAltField($question){
		$question_config =(object) array(
			'type'=>'textarea',
			'text'=>'Alt Text: ',
			'alt'=>'(Text like this describing a question)',	
			'name'=>'config[alt]',	
			'value'=>$question->config->alt,
		);

		$this->renderQuestion($question_config);
	}

	function renderValueField($question){
		$question_config =(object) array(	
			'type'=>'textarea',
			'text'=>'Default Answer: (optional)',
			'name'=>'config[value]',
			'value'=>$question->config->value,
		);
		$this->renderQuestion($question_config);
	}

	function renderValidationField($question){
		$question_config =(object) array(			
			'text'=>'Input Validation:',
			'alt'=>"(validation you want to assign to the actual input/s, one per line. ex. required<br />min_length[3]<br />max_length[20])",
			'name'=>'config[validation]',
			'type'=>'textarea',
			'value'=>str_replace('|',"\n",$question->config->validation),
		);
		$this->renderQuestion($question_config);
	}

	function formatValidationField($question){
		$question['config']['validation'] = str_replace("\n",'|',$question['config']['validation']);
		return $question;
	}

	/**
	* Allows a person to configure options
	*/
	function renderOptionsField($question){
		$o_txt = '';
		$first = true;		
		//we are using a dataprovider for the options!
		if($question->config->options->dataprovider){
			$method = $question->config->options->dataprovider->method;			
		}elseif(is_array($question->config->options) or is_object($question->config->options)){
			foreach($question->config->options as $label=>$value){
				if($first) $first = false;
				else $o_txt.="\n";
				if($label == $value) $o_txt.=$label;
				else $o_txt.=$label.":".$value;
			}
		}	
		$question_config =(object) array(
			'type'=>'optionsconf',
			'text'=>'Options: ',
			'alt'=>'(One option per line, OR choose a set of pre-defined answers. There is also an advanced syntax explanation in the help!)'.f2Help(3),	
			'name'=>'config[options]',
			'value'=>$o_txt,
			'dataprovider'=>$method,
		);
		$this->renderQuestion($question_config);
	}

	/**
	* format the results of the options field
	*/
	function formatOptions($question){
		$options = $question['config']['options'];
		$dataprovider = $question['config']['dataprovider'];
		unset($question['config']['dataprovider']);		
		if(!$dataprovider || empty($dataprovider['method'])){ //only use custom entry if no dataprovider is set
			$raw_o_arr = explode("\n", $options);
			$option_arr = array();
			foreach($raw_o_arr as $o){
				$o_parts = explode(":", $o);
				if(count($o_parts) < 2){//only one thing, set value same as label
					$option_arr[$o_parts[0]] = $o_parts[0];
				}else{
					$option_arr[$o_parts[0]] = $o_parts[1];
				}
			}
			$question['config']['options'] = $option_arr; //use custom options
			return $question;
		}else{
			$question['config']['options'] = array('dataprovider'=>$dataprovider); //use the dataprovider
			return $question;
		}
	}

	function renderSelectionsField($question){
		$o_txt = '';
		$first = true;
		if(is_array($question->config->selected) or is_object($question->config->selected)){
			foreach($question->config->selected as $value){
				if($first) $first = false;
				else $o_txt.="\n";
				$o_txt.=$value;
			}
		}
		$question_config =(object) array(
			'type'=>'textarea',
			'text'=>'Selected (Default value): ',
			'alt'=>'(One per line if multiple items are capable of being selected at once.)',	
			'name'=>'config[selected]',
			'value'=>$o_txt,
		);
		$this->renderQuestion($question_config);	
	}

	/**
	* format the results of the selected field
	*/
	function formatSelected($question){
		$s_arr = explode("\n", $question['config']['selected']);
		if(!is_array($s_arr)) $s_arr = array();
		$question['config']['selected'] = $s_arr;
		return $question;
	}

	function renderRequiredField($question){
		if(strpos('required', $question->config->validation) !== FALSE){
			$selected = array('Y');
		}else{
			$selected = array('N');
		}		
		$question_config =(object) array(
			'text'=>'Required?',
			'alt'=>'(Is this required to contain a value/be selected?)',	
			'type'=>'radio',
			'name'=>'config[required]',	
			'options'=>array('Yes'=>'Y', 'No'=>'N'),
			'selected'=>$selected,
			'validation'=>'required',			
		);
		$this->renderQuestion($question_config);
	}

	function formatRequiredField($question){
		if($question['config']['required'] == 'Y'){
			$question['config']['validation'] = 'required';
		}
		unset($question['config']['required']); 
		return $question;
	}

	function renderDependenciesField($question){
		$depends = str_replace("&&", "\n", $question->config->dependencies);
		$question_config =(object) array(
			'text'=>'Dependent On: ',
			//'alt'=>'(Rules for when this question will show up, based on input of other questions.  One rule per line.  Format: &lt;inputname&gt;=&lt;value&gt;<br />You can use the logical operators =,!=,&lt;,&lt;=,&gt;,&gt;= in the place of =.  Also, you can use "*" as a wildcard in &lt;value&gt;.)'.f2Help(1),
			'alt'=>"One rule per line.<br />Format: inputname=value<br />You can use the logical operators =,!=,<,<=,>,>= in the place of =. Also, you can use \"*\" as a wildcard in the value.".f2Help(1),
			'type'=>'textarea',
			'name'=>'config[dependencies]',				
			'value'=>$depends,
		);
		$this->renderQuestion($question_config);
	}

	function formatDependenciesField($question){
		$question['config']['dependencies'] = str_replace("\n", '&&', $question['config']['dependencies']);
		return $question;
	}

	/**
	* Renders a question given it's config
	* (just convienience so we dont' have to worry about inluding inputs and whatnot...)
	*/
	function renderQuestion($question_config){
		$this->CI->load->view('question/view_question',array('question'=>(object) array('id'=>uniqid(''), 'config'=>$question_config)));
	}
}
?>