<?php
/**
 * To be clear an "Input" is anything in a form that gets posted.  Select, Textareas, are inputs just the
 * same as <input type="text">
 */

/**
 * This interface should be implemented by ALL inputs.  No exceptions!
 * We have to keep these things compatible!
 */
interface iInput{
	//sets the tag of the element (makes selects and textareas easier)
	function setTag($tag);
	function getTag();
	//sets the name of element (needed, because some types need to append [])
	function setName($name);
	function getName();
	//sets the type of element (text, radio, checkbox, select, textarea...)
	function setType($type);
	function getType();
	//sets the default value of the element
	function setValue($value);
	function getValue();
	//sets an attribute of the html (id, style, anything really)
	function setAttribute($name, $value);
	//set all attributes via assoc array
	function setAttributes($attributes);
	//gets the value of an attribute if set, else false
	function getAttribute($name);
	//get all attributes
	function getAttributes();
	//get attributes as string
	function attributesToString();
	//print input to html
	function __toString();
	//make the POST var not override the value
	function ignorePost($ignore=true);
	//make input read only
	function setReadonly($readonly=true);
	function getReadonly();
}

//todo: Write a "selectable" or "multichoice" interface for selects, radios, checks
// so there is a similar way of setting selected/checked options, and also for giving
// them data to render from.


/**
* Checkboxes, radios, and selects are considered "selectable"
*/
interface iSelectable{
	//sets the element/option with value or label $value to be selected/checked
	function setSelected($value);
	//gets values/labels that are selected
	function getSelected();

	//set multiple elements/options to be selected
	function setSelections(Array $selections);
	//find out if a value is selected
	function isSelected($value);
}

/**
* This class forms the base of the rest of the inputs
* It implements most methods of iInput in a sufficient manner for general use.
* Extend it to deliniate different input types in code, and also make things
* More convienient code-wise later on.
*/
class Base_Input implements iInput{

	/**
	* Tag to be used for this input default: span (cause you shouldn't use it directly) (ex. input, select, textarea...)
	*/
	private $tag = 'span';

	/**
	* The name of the input (ex. input name="myname" or select name="myname")
	*/
	private $name;

	/**
	* The type of the input (ex. input type="text")
	*/
	private $type;

	/**
	* The value this input has (can be overridden by post)
	*/
	private $value;

	/**
	* The post value associated with this input
	*/
	private $post;

	/**
	* Whether this input should ignore the POST value for it's $name
	* if not, it will insert the POST[$name] value for itself into it's value
	*/	
	protected $ignore_post = false;

	/**
	* array of attributes (most things will be attributes)
	*/
	private $attributes = array();
	
	/**
	* Is the input readonly? (disabled)
	*/
	private $readonly = false;

	function __construct($config){
		if(is_object($config)) $config = (array) $config;
		elseif(!is_array($config)) return;
		extract($config);
		$this->setTag($tag);
		$this->setName($name);
		$this->setType($type);
		$this->setValue($value);
		if($attributes) $this->setAttributes($attributes);
		$this->setPost();
		if($ignore_post) $this->ignorePost(); 
	}
	
	/**
	* Set Tag for the input
	* @param string $tag html tag name
	*/
	function setTag($tag){
		if($tag){
			$this->tag = $tag;
		}
	}

	/**
	* Get the html tag name for input
	* @return string
	*/
	function getTag(){
		return $this->tag;
	}

	/**
	* Set the name for this input (sets attributes['name'] as well)
	* @param string $name Name of input
	*/
	function setName($name){
		$this->name = $name;
		$this->setAttribute('name', $name);
	}

	/**
	* Get name of input
	* @return string
	*/
	function getName(){
		return $this->getAttribute('name');
	}

	/**
	* Set type of input (textareas, selects should not render this property)
	* @param string $type Input type
	* 
	*/
	function setType($type){
		$this->type = $type;
		$this->setAttribute('type', $type);		
	}

	/**
	* Get type of input
	* @return string
	*/
	function getType(){
		return $this->getAttribute('type');		
	}

	/**
	* Set the value of input
	* @param string $value 
	*/
	function setValue($value){
		$this->value = $value;
		$this->setAttribute('value', $value);
	}

	/**
	* Get value of input
	* @return string
	*/
	function getValue(){
		return $this->getAttribute('value');
	}
	
	/**
	* Set an attribute to be a value
	* @param string $name Name of attr
	* @param string $value Value of attr
	*/
	function setAttribute($name, $value){
		$this->attributes[$name] = $value;
	}
	
	/**
	* Set multiple attributes at once
	* @param array|object $attributes Key=>Value pairs representing attributes
	*/
	function setAttributes($attributes){
		if(is_array($attributes) or is_object($attributes)){
			foreach($attributes as $a=>$v){
				$this->setAttribute($a, $v);
			}
		}
	}

	/**
	* Get an attribute by name
	* @param string $name
	* @return string|false
	*/
	function getAttribute($name){
		if($this->attributes[$name]) return $this->attributes[$name];
		else return false;
	}

	/**
	* Get all attributes
	* @return array
	*/
	function getAttributes(){
		return $this->attributes;
	}

	/**
	* Make xhtml string of attributes array
	* @return string
	*/
	function attributesToString(){
		$out = "";
		if(!empty($this->attributes)){
			foreach($this->attributes as $a=>$v){
				$out.=" $a=\"$v\"";
			}
		}
		return $out;
	}
	
	function setPost(){
		$name = str_replace('[]','',$this->getName());
		if(isset($_POST[$name])) $this->post = $_POST[$name];
		else $this->post = false;
	}
	
	function getPost(){
		return $this->post;
	}
	
	function __toString(){
		return "<".$this->getTag()." ".$this->attributesToString().">".$this->getValue()."</".$this->getTag().">";
	}
	
	function ignorePost($ignore=true){
		if($ignore===false) $this->ignore_post = false;
		else $this->ignore_post = true;
	}

	function setReadonly($readonly=true){
		if($readonly !== true or $readonly !== false) $readonly = true;
		$this->readonly = $readonly;
	}

	function getReadonly(){
		return $this->readonly;
	}

}

class Input_Input extends Base_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setTag('input');		
	}

	function __toString(){
		$post = $this->getPost();
		if($post !== false && !$this->ignore_post) $this->setAttribute('value', $post);
		return "<input ".$this->attributesToString().">";
	}
}

class Text_Input extends Input_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('text');
	}
}

class Password_Input extends Input_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('password');
	}
}

class Hidden_Input extends Input_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('hidden');
	}
}

class Button_Input extends Input_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('button');
	}
}

class Submit_Input extends Input_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('submit');
	}
}

class Radio_Input extends Input_Input{
	private $selected = array();
	private $label = '';

	function __construct($config=null){
		parent::__construct($config);
		$this->setType('radio');
		if($config->selected){
			$this->setSelections($config->selected);			
		}
		if($config->label){
			$this->setLabel($config->label);
		}
	}
	
	function __toString(){
		$post = $this->getPost();
		if($this->getValue() == $post && !$this->ignore_post) $this->setSelected($this->getValue());
		if($this->isSelected($this->getValue()) || $this->isSelected($this->getLabel())){
			$this->setAttribute('checked', 'checked');
		}
		return "<div class=\"input_multiple\"><input ".$this->attributesToString()."/>".$this->getLabel()."</div>";
	}

	function setLabel($label){
		if($label){
			$this->label = $label;
		}
	}

	function getLabel(){
		return $this->label;
	}

	function setSelected($selected=null){
		if($selected !== null) $this->selected[] = $selected;
		else $this->selected = array();
	}

	function getSelected(){
		return $this->selected;
	}

	function setSelections(Array $selections){
		$this->selected = $selections;
	}

	function isSelected($value){		
		if(is_array($this->selected) or is_object($this->selected)){
			foreach ($this->selected as $k=>$s) {
				if((string) $s === (string) $value){					
					return true;					
				}				
			}
		}
		return false;
		//return (in_array((string) $value, $this->selected) or in_array($value, $this->selected));
	}
}

class Checkbox_Input extends Input_Input implements iSelectable{
	private $selected = array();
	private $label = '';

	function __construct($config=null){
		parent::__construct($config);
		$this->setType('checkbox');
		if($config->selected){
			$this->setSelections($config->selected);
		}
		if($config->label){
			$this->setLabel($config->label);
		}
	}

	function setLabel($label){
		if($label){
			$this->label = $label;
		}
	}

	function getLabel(){
		return $this->label;
	}
	
	//because checkboxes are always multiselect...force them to be
	function setName($name){
		if(strpos($name, '[]') === false) $name.='[]';
		parent::setName($name);
	}
	
	function __toString(){
		$post = $this->getPost();
		if(!is_array($post)) $post = array($post);
		if(in_array($this->getValue(), $post) && !$this->ignore_post) $this->setSelected($this->getValue());
		if($this->isSelected($this->getValue()) || $this->isSelected($this->getLabel())){
			$this->setAttribute('checked', 'checked');
		}
		return "<div class=\"input_multiple\"><input ".$this->attributesToString()."/>".$this->getLabel()."</div>";
	}

	function setSelected($selected=null){
		if($selected !== null) $this->selected[] = $selected;
		else $this->selected = array();
	}

	function getSelected(){
		return $this->selected;
	}

	function setSelections(Array $selections){
		$this->selected = $selections;
	}

	function isSelected($value){
		if(is_array($this->selected) or is_object($this->selected)){
			foreach ($this->selected as $k=>$s) {
				if((string) $s === (string) $value){					
					return true;					
				}				
			}
		}
		return false;
		//return in_array($value, $this->selected, true);
	}
}

class Textarea_Input extends Base_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setTag('textarea');
		$this->setType('textarea');
	}

	function getType(){
		return $this->type;
	}

	function setType($type){
		$this->type = $type;
	}

	function setValue($value){
		$this->value = $value;
	}

	function getValue(){
		return $this->value;
	}
	
	function __toString(){
		$post = $this->getPost();
		if($post !== false && !$this->ignore_post) $this->setValue($post);
		return '<textarea '.$this->attributesToString().'>'.$this->getValue().'</textarea>';
	}
}

class Select_Input extends Base_Input implements iSelectable{
	private $options = array();
	private $selected = array();
	
	function __construct($config=null){
		parent::__construct($config);
		$this->setTag('select');
		$this->setType('select');
		if(!empty($config->options)) $this->setOptions($config->options);
		if(!empty($config->selected)) $this->setSelections($config->selected);
		$post = $this->getPost();
		if($post !== false && !$this->ignore_post) $this->setSelected($post);
	}

	function getType(){
		return $this->type;
	}

	function setType($type){
		$this->type = $type;
	}

	function setValue($value){
		$this->setSelected($value);
	}

	function getValue(){
		return $this->getSelected();
	}

	function setOption($label, $value=null){
		$this->options[$label] = $value;
	}

	function setOptions($options=null){
		$this->options = array();
		if($options and (is_array($options) or is_object($options))){
			foreach($options as $l=>$v){
				$this->setOption($l, $v);
			}
		}
	}

	function setSelected($selected=null){
		if($selected !== null) $this->selected[] = $selected;
		else $this->selected = array();
	}

	function getSelected(){
		return $this->selected;
	}

	function setSelections(Array $selections){
		$this->selected = $selections;
	}

	function isSelected($value){
		if(is_array($this->selected) or is_object($this->selected)){
			foreach ($this->selected as $k=>$s) {
				if((string) $s === (string) $value){					
					return true;					
				}				
			}
		}
		return false;
		//return in_array($value, $this->selected, true);
	}

	function optionsToString(){
		$out = '';
		foreach($this->options as $l=>$v){
			if($this->isSelected($l) or $this->isSelected($v)) $sel = ' selected="selected"';
			else $sel = '';
			if($v === null) $val = '';
			else $val = "value=\"$v\"";
			$out.="<option $val$sel>$l</option>\n";
		}
		return $out;
	}

	function __toString(){
		return '<select '.$this->attributesToString().">\n".$this->optionsToString()."</select>";
	}
}

class Multipleselect_Input extends Select_Input{

	function __construct($config=null){
		parent::__construct($config);
		$this->setType('multipleselect');
		$this->setAttribute('multiple', 'multiple');
	}	
	
	function setName($name){
		if(strpos($name, '[]') === false) $name.='[]';
		parent::setName($name);
	}	
}

/**
* This class is a convienience class to handle any input implementing iInput
*/
class Inputs implements iInput{
	private $CI;
	private $input;
	var $pre_wrap = "";
	var $post_wrap = "";
	private $config;
	
	function __construct($config=null){
		if(is_array($config)){ $config = (object) $config; }
		if(!empty($config)) $this->setConfig($config);
		$this->CI =& get_instance();
	}
	
	function setConfig($config=null){
		$this->input = false;
		$this->config = false;
		if(is_array($config)){ $config = (object) $config; }
		$this->config = $config;
		$readonly = $this->CI->prefill->readonly($config->name);
		if(!$readonly) $readonly = $this->CI->prefill->readonly($config->name."[]");
		$forcefill = $this->CI->prefill->forcefilled($config->name);
		if(!$forcefill) $forcefill = $this->CI->prefill->forcefilled($config->name."[]");
		if($this->config->visibility == 'viewonly' || $this->config->visibility == 'hidden' || $readonly){	
			$type = 'Hidden_Input';
		}else{
			$type = ucfirst($config->type)."_Input";
			if(isset($config->attributes->type)) $type = ucfirst($config->attributes->type)."_Input";
		}
		$this->input = new $type($config);		
		if($readonly){
			if($this->getType() != 'radio' && $this->getType() != 'checkbox'){
				$this->setValue($readonly);
			}else{
				if(!is_array($readonly)) $readonly = array($readonly);
				$this->setSelections($readonly);
			}
			$this->config->visibility = 'viewonly';
		}
		if($forcefill){
			if($this->getType() != 'radio' && $this->getType() != 'checkbox'){
				$this->setValue($forcefill);
			}else{
				if(!is_array($forcefill)) $forcefill = array($forcefill);
				$this->setSelections($forcefill);
			}
		}
	}

	function getCopy(){
		return $this->input;
	}

	function setTag($tag){
		$this->input->setTag($tag);
	}

	function getTag(){
		return $this->input->getTag();
	}
	
	function setName($name){
		$this->input->setName($name);
	}
	
	function getName(){
		return $this->input->getName();
	}
	
	function setType($type){
		$this->input->setType($name);
	}
	
	function getType(){
		return $this->input->getType();
	}
	
	function setValue($value){
		$this->input->setValue($value);
	}
	
	function getValue(){
		return $this->input->getValue();
	}
	
	function setAttribute($name, $value){
		$this->input->setAttribute($name, $value);
	}
	
	function setAttributes($attributes){
		$this->input->setAttributes($attributes);
	}
	
	function getAttribute($name){
		return $this->input->getAttribute($name);
	}
	
	function getAttributes(){
		return $this->input->getAttributes();
	}
	
	function attributesToString(){
		return $this->input->attributesToString();
	}
	
	function ignorePost($ignore=true){
		$this->input->ignorePost($ignore);
	}

	function setReadonly($readonly=true){
		$this->input->setReadonly($readonly);
	}

	function getReadOnly(){
		$this->input->getReadonly();
	}

	function getSelected(){
		return $this->input->getSelected();
	}

	function setSelected($selected=null){
		$this->input->setSelected($selected);
	}

	function setSelections(Array $selections){
		$this->input->setSelections($selections);
	}

	function isSelected($value){
		return $this->input->isSelected();
	}

	function setOption($label, $value=null){
		$this->input->setOption($label, $value);
	}

	function setOptions($options=null){
		$this->input->setOptions($options);
	}
	
	function __toString(){
		if($this->config->visibility == 'viewonly' || $this->config->visibility == 'hidden'){
			//hack to not allow too many renderings of the value of a radio or checkbox...
			if(!$this->names) $this->names = array();
			if(in_array($this->getName(), $this->names)) return '';
			else $this->names[] = $this->getName();
		}
		if($this->config->visibility == 'viewonly'){
			$readonly = $this->CI->prefill->readonly($this->getName());
			if(!$readonly) $readonly = $this->CI->prefill->readonly($this->getName()."[]");
			if($readonly){
				if(is_array($readonly)) $readonly = implode(', ', $readonly);				
				return $this->pre_wrap.$readonly.$this->input->__toString().$this->post_wrap;
			}else{
				return $this->pre_wrap.$this->getValue().$this->input->__toString().$this->post_wrap;	
			}
		}else{
			return $this->pre_wrap.$this->input->__toString().$this->post_wrap;
		}		
	}
	
	function serialize(){
		return serialize($this->input);
	}
}
?>
