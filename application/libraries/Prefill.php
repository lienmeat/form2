<?php
/**
* This library is unusual.
* It handles persisting, and deleting persisted forcefilled and/or readonly input values.
* If you send the forms/view method get urls, they will be persisted via this library.
* This same library is used in the inputs library to over-ride default values or set inputs
* as readonly, rather than them being editable.
* It's done in a library so that I don't have to change things in more than one
* place if I decide to change where/how things are persisted or something else.
*/

class Prefill{
	/**
	* Set a field to be readonly
	* @param string $field Name of input to fill
	* @param string $value Value to fill it with
	*/	
	public function setReadonly($field, $value){
		$_SESSION['f2']['prefill']['readonly'][$field] = $value;
	}

	/**
	* Set a field to be filled with a value, but not readonly
	* @param string $field Name of input to fill
	* @param string $value Value to fill it with
	*/
	public function setForcefilled($field, $value){
		$_SESSION['f2']['prefill']['forcefilled'][$field] = $value;
	}

	/**
	* Sets an array to be the contents, rather than just appending stuff
	* @param array $data
	*/
	public function setReadonlys(Array $data){
		if(!empty($data)){
			$_SESSION['f2']['prefill']['readonly'] = $data;
		}
	}

	/**
	* Sets an array to be the contents, rather than just appending stuff
	* @param array $data
	*/
	public function setForcefilleds(Array $data){
		if(!empty($data)){
			$_SESSION['f2']['prefill']['forcefilled'] = $data;
		}
	}

	/**
	* Get the value of a forcefilled record by field
	* @param string $field
	* @param string|false
	*/
	public function forcefilled($field){
		if($_SESSION['f2']['prefill']['forcefilled'][$field]){
			return $_SESSION['f2']['prefill']['forcefilled'][$field];
		}else{
			return false;
		}
	}

	/**
	* Get the value of a readonly record by field
	* @param string $field
	* @param string|false
	*/
	public function readonly($field){
		if($_SESSION['f2']['prefill']['readonly'][$field]){
			return $_SESSION['f2']['prefill']['readonly'][$field];
		}else{
			return false;
		}
	}

	/**
	*	Get all readonly fields
	*/
	public function getReadonlys(){
		return $_SESSION['f2']['prefill']['readonly'];
	}

	/**
	*	Get all forcefilled fields
	*/
	public function getForcefilleds(){
		return $_SESSION['f2']['prefill']['forcefilled'];
	}

	/**
	* Unregister all fields that are readonly
	*/
	public function clearReadonlys(){
		$_SESSION['f2']['prefill']['readonly'] = array();
	}

	/**
	* Unregister all fields that are forcefilled
	*/
	public function clearForcefilleds(){
		$_SESSION['f2']['prefill']['forcefilled'] = array();
	}	
}
?>