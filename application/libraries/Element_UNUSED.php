<?php
interface iElement{
	function setInputs(Array $inputs);
	function addInput($input);
	function getInputs();
	function setAttributes(Array $attributes);
	function setAttribute($name, $value);
	function getAttributes();
	function attributesToString();
	function setName($name);
	function getName();
	function setType($type);
	function getType();
	function setOrder($order);
	function getOrder();
	function inputsToString();
	function __toString();
}

class Base_Element implements iElement{
	//array of input objects that are in this element
	private $inputs = array();
	//attributes assigned to this Element
	private $attributes = array();
	//name of element
	private $name = "unnamed"; 
	//should get over-ridden by subclasses
	private $type = "Base_Element";
	//order element should appear in form
	private $order;
	//options for this element
	private $options = array();

	function __construct($config){
		if(is_object($config)) $config = (array) $config;
		elseif(!is_array($config)) return;
		extract($config);
		if($name) $this->setName($name);
		if($type) $this->setType($type);
		if($order) $this->setOrder($order);
		if($attributes) $this->setAttributes($attributes);
		if($inputs) $this->setInputs($inputs);		
	}

	function setInputs(Array $inputs){
		foreach($inputs as $i){
			$this->addInput($i);
		}
	}

	function addInput($input){
		if(is_array($input)){
			$this->inputs[] = new InputHandler($input);
		}elseif(is_object($input)){
			$this->inputs[] = $input;
		}
	}

	function getInputs(){
		return $this->inputs;
	}

	function setAttributes(Array $attributes){
		foreach($attributes as $a=>$v){
			$this->setAttribute($a, $v);
		}
	}

	function setAttribute($name, $value){
		$this->attributes[$name] = $value;
	}

	function getAttributes(){
		return $this->attributes;
	}
	
	function attributesToString(){
		$out = "";
		if(!empty($this->attributes)){
			foreach($this->attributes as $a=>$v){
				$out.=" $a=\"$v\"";
			}
		}
		return $out;
	}

	function setName($name){
		$this->name = $name;
	}

	function getName(){
		return $this->name;
	}

	function setType($type){
		$this->type = $type;
	}

	function getType(){
		return $this->type;
	}

	function setOrder($order){
		$this->order = $order;
	}

	function getOrder(){
		return $this->order;
	}


	function inputsToString(){
		return implode('<br />', $this->inputs);
	}

	function __toString(){
		return "<li ".$this->attributesToString()."><div class=\"question\">Base_Element: </div><div class=\"answer\">".$this->inputsToString()."</div></li>";
	}
}


class ElementHandler{
	
}
require 'Input.php';
$e = new Base_Element(array('name'=>'funstuff', 'type'=>'base', 'attributes'=>array('id'=>'funstuff'), 'inputs'=>array(new InputHandler(array('type'=>'text', 'name'=>'testing')))));

echo $e;
?>
