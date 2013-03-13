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
		$question['config']['attributes']['validation'] = $this->formatValidationField($question['config']['validation']);
		return $question;
	}

	/**
	* Formats a textarea question into what should be saved to db
	* @param array $question
	* @return array
	*/
	function formatTextarea($question){
		$question['config']['attributes']['validation'] = $this->formatValidationField($question['config']['validation']);
		return $question;
	}

	/**
	* Formats the select field for db insert
	*/
	function formatSelect($question){
		$question['config']['options'] = $this->formatOptions($question);
		$question['config']['selected'] = $this->formatSelected($question['config']['selected']);
		$question['config']['attributes']['validation'] = $this->formatRequiredField($question['config']['required']);
		unset($question['config']['dataprovider']);
		//if($question['config']['required']) $question['config']['attributes']['validation'] = 'required';				
		return $question;
	}

	function formatCheckbox($question){
		$question['config']['options']=$options=$this->formatOptions($question);
		$question['config']['selected']=$selected=$this->formatSelected($question['config']['selected']);
		$question['config']['validation']=$this->formatRequiredField($question['config']['required']);		
		/*		
		$inputs = array();
		foreach($options as $label=>$value){
			$input = array(
				'name'=>$question['config']['name'],
				'type'=>'checkbox',
				'value'=>$value,
				'label'=>$label,
				'selected'=>$selected,
			);
			$input['attributes']['validation'] = $this->formatRequiredField($question['config']['required']);
			//if($question['config']['required']) $input['attributes']['validation'] = 'required';
			$inputs[] = $input;
		}
		$question['config']['inputs'] = $inputs;
		*/
		return $question;
	}

	function formatRadio($question){
		$question['config']['options']=$options=$this->formatOptions($question);
		$question['config']['selected']=$selected=$this->formatSelected($question['config']['selected']);
		$question['config']['validation']=$this->formatRequiredField($question['config']['required']);
		/*
		$inputs = array();
		foreach($options as $label=>$value){
			$input = array(
				'name'=>$question['config']['name'],
				'type'=>'radio',
				'value'=>$value,
				'label'=>$label,
				'selected'=>$selected,
			);
			$input['attributes']['validation'] = $this->formatRequiredField($qustion['config']['required']);
			//if($question['config']['required'] == "Y") $input['attributes']['validation'] = 'required';
			$inputs[] = $input;
		}		
		$question['config']['inputs'] = $inputs;
		*/
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

	function formatDependencies($dependencies){
		//not yet implemented
	}

	function renderNameField($question){
		$question_config =(object) array(			
		'type'=>'text',
		'name'=>'config[name]',
		'text'=>'Question Name: ',
		'alt'=>'(a-z, 0-9, -, _)',
		'value'=>$question->config->name,
		'attributes'=>array('validation'=>'required|alpha_dash'),
		);

		$this->renderQuestion($question_config);
	}

	function renderExpDP(){
		$question_config = (object) array(
			'text'=>'expdp: ',
			'name'=>'config[expdp]',
			'type'=>'select',		
			'options'=>(object) array('dataprovider'=>(object) array('method'=>'countryOptions')),
		);
				
		$this->renderQuestion($question_config);
	}

	function renderTextField($question){
		$question_config =(object) array(
			'type'=>'textarea',
			'text'=>'Question Text: ',	
			'name'=>'config[text]',	
			'value'=>$question->config->text,
			'attributes'=>array('validation'=>'required'),
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
			'value'=>str_replace('|',"\n",$question->config->attributes->validation),
		);
		$this->renderQuestion($question_config);
	}

	function formatValidationField($validation){
		return str_replace("\n",'|',$validation);
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
				$o_txt.=$label.":".$value;
			}
		}	
		$question_config =(object) array(
			'type'=>'optionsconf',
			'text'=>'Options: ',
			'alt'=>'(In the format: "label:value", one per line. You don\'t need the ":value" if you don\'t require a different value than label. Label is what the user will see, value is what is submitted, OR choose a data provider that will populate the options from a database.)',	
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
			return $option_arr; //use custom options
		}else{
			return array('dataprovider'=>$dataprovider); //use the dataprovider
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
	function formatSelected($selected){
		$s_arr = explode("\n", $selected);
		if(!is_array($s_arr)) $s_arr = array();
		return $s_arr;
	}

	function renderRequiredField($question){
		$selected = array($question->config->required);
		//var_dump($selected);
		$question_config =(object) array(
			'text'=>'Required?',
			'alt'=>'(Is this required to contain a value/be selected?)',	
			'type'=>'radio',
			'name'=>'config[required]',	
			'options'=>array('Yes'=>'Y', 'No'=>'N'),
			'selected'=>$selected,
			/*


				(object) array('type'=>'radio', 'value'=>'Y', 'label'=>'Yes', 'selected'=>$selected, 'attributes'=>(object) array('validation'=>'required')),
				(object) array('type'=>'radio', 'value'=>'N', 'label'=>'No', 'selected'=>$selected, 'attributes'=>(object) array('validation'=>'required')),	
			),
			*/
		);
		$this->renderQuestion($question_config);
	}

	function formatRequiredField($required){
		if($required == 'Y') return 'required';
		else return '';
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