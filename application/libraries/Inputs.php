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
	//sets the type of element (needed, because some types need to append [])
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
	function setAttributes(Array $attributes);
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
	function setReadonly($flag=true);
	function getReadonly();
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
		$this->setAttribute('name', $name);
	}

	/**
	* Get value of input
	* @return string
	*/
	function getValue(){
		return $this->getAttribute('name');
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
		return "<".$this->getTag()." ".$this->attributesToString().">".."</".$this->getTag().">";
	}
	
	function ignorePost($ignore=true){
		if($ignore===false) $this->ignore_post = false;
		else $this->ignore_post = true;
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
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('radio');
	}
	
	function __toString(){
		$post = $this->getPost();
		if($post == $this->getAttribute('value') && !$this->ignore_post) $this->setAttribute('checked', 'checked');
		return "<input ".$this->attributesToString().">";
	}
}

class Checkbox_Input extends Input_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('checkbox');
	}
	
	//because checkboxes are always multiselect...force them to be
	function setName($name){
		if(strpos($name, '[]') === false) $name.='[]';
		parent::setName($name);
	}
	
	function __toString(){
		$post = $this->getPost();
		if(!is_array($post)) $post = array();
		if(in_array($this->getAttribute('value'), $post) && !$this->ignore_post) $this->setAttribute('checked', 'checked');
		return "<input ".$this->attributesToString().">";
	}
}

class Textarea_Input extends Base_Input{
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('textarea');
	}
	
	function __toString(){
		$post = $this->getPost();
		if($post !== false && !$this->ignore_post) $this->setValue($post);
		return '<textarea '.$this->attributesToString().'>'.$this->getValue().'</textarea>';
	}
}

class Select_Input extends Base_Input{
	private $options = array();
	private $selected = array();
	
	function __construct($config=null){
		parent::__construct($config);
		$this->setType('select');
		if(!empty($config['options'])) $this->setOptions($config['options']);
		if(!empty($config['selected'])) $this->setSelected($config['selected']);
		$post = $this->getPost();
		if($post !== false && !$this->ignore_post) $this->setSelected($post);
	}

	function setOption($label, $value=null){
		$this->options[$label] = $value;
	}

	function setOptions(Array $options=null){
		$this->options = array();
		if($options){
			foreach($options as $l=>$v){
				$this->setOption($l, $v);
			}
		}
	}

	function setSelected($selected=null){
		if(!is_array($selected)) $selected = array($selected); 
		if($selected) $this->selected = $selected;
		else $this->selected = array();
	}

	function optionsToString(){
		$out = '';
		foreach($this->options as $l=>$v){
			if(in_array($l, $this->selected) or in_array($v, $this->selected)) $sel = ' selected="selected"';
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

class MultipleSelect_Input extends Select_Input{

	function __construct($config=null){
		parent::__construct($config);
		$this->setAttribute('multiple', 'multiple');
	}
	
	function setName($name){
		if(strpos($name, '[]') === false) $name.='[]';
		parent::setName($name);
	}	
}
/*
//move to dependencies.php file

class InputDependency{
	//input name that this dependency checks against
	var $dependent_on;
	//what operator it uses (=/==, !=, <, <=, >, >=)
	var $operator;
	//value to check for
	var $value;
	//what input this belongs to (by input name)
	var $belongs_to;

	function __construct($dependent_on, $operator, $value, $belongs_to){
		$this->dependent_on = $dependent_on;
		$this->operator = $operator;
		$this->value = $value;
		$this->belongs_to = $belongs_to;
	}

	function __toString(){
		return $this->dependent_on.$this->operator.$this->value;
	}
}

class InputValidation{
	var $rule;
	var $params = array();

	function __construct($rule, Array $params=null){
		$this->rule = $rule;
		$this->params = $params;
	}

	function __toString(){
		$out = $this->rule;
		if(!empty($this->params)) $out.="[".implode(",", $this->params)."]";
		return $out;
	}
}
*/

/**
* This class is a convienience class to handle any input implementing iInput
*/
class Inputs implements iInput{
	private $input;
	var $pre_wrap = "";
	var $post_wrap = "";
	function __construct($config=null){
		if(!empty($config)) $this->setConfig($config);
	}
	
	function setConfig($config=null){
	  if(is_object($config)){
	    $config = (array) $config;
	  }
	  if(is_array($config)){
	    if($config['config'] && !is_array($config['config']) && !is_object($config['config'])){
	      //we have ourselves a raw database record
	      $this->pre_wrap = $config['pre_wrap'];
	      $this->post_wrap = $config['post_wrap'];
	      $this->id = $config['id'];
	      $config = (array) json_decode($config['config']);
	      
	    }
	  }else{
	    if($config) $config = (array) json_decode($config);
	  }
		$type = ucfirst($config['type'])."_Input";
		if(isset($config['attributes']['type'])) $type = ucfirst($config['attributes']['type'])."_Input";
		$this->input = new $type($config);
	}

	function getCopy(){
		return $this->input;
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
		$this->input->getValue();
	}
	
	function setAttribute($name, $value){
		$this->input->setVaue($name, $value);
	}
	
	function setAttributes(Array $attributes){
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
	
	function __toString(){
		return $this->pre_wrap.$this->input->__toString().$this->post_wrap;
	}
	
	function serialize(){
		return serialize($this->input);
	}
}
?>
