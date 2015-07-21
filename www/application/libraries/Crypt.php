<?php

require_once('EZ_Crypt.php');

class Crypt extends EZ_Crypt{
  /**
  * global salt used for generating keys
  */
	private $salt = '4^/(b]}=7/S6r_5]Oy~Gb3E!u"h#1|Nrd3894@)%GS';
	
	/* overloads the EZ_Crypt::setIV function
	*  making setting the IV automatic
	*/
	function setIV(){
		parent::setIV("3(&21aY".$this->salt, 0);
	}
	
	/* overloads the EZ_Crypt::setKey function
	*  making setting the encryption key automatic
	*/
	function setKey($salt){
		if(!empty($salt)){
		  $this->private_salt = $salt;
		  $key = $this->private_salt.$this->salt;
		}else{
		  return false;
		}
		parent::setKey($key, 0);
	}
	
	/* overloads the EZ_Crypt::encrypt function
	*/
	function encrypt($salt, $data){
	  if(empty($salt)) return false;
		$this->setKey($salt);
		$this->setIV();
		if(empty($this->key) || empty($this->iv) || empty($data)) return;
		$result = parent::encrypt($data);
		$result->private_salt = $this->private_salt;
		return $result->data;
	}

	/* overloads the EZ_Crypt::decrypt function
	*/	
	function decrypt($salt, $data){
	  if(empty($salt)) return false;
		$this->setKey($salt);
		$this->setIV();
		if(empty($this->key) || empty($this->iv) || empty($data)) return;
		$result = parent::decrypt($data);
		$result->private_salt = $this->private_salt;
		return $result->data;		
	}
	
	function makeSalt(){
	  return substr($this->makeKey(), '0', '32');
	}
}
?>
