<?php

/**
* Stock form validation is kind of limited by the fact callbacks happen
* on the controller running, and we also cannot call individual validation
* functions on a value. Fix that by calling this to do our work in this reguard.
*/
class Validation{
	private $CI;
	private $validation_messages = array();	

	function __construct(){
		$this->CI =& get_instance();
	}
		
	/**
	* Call a specific validation function and get a result for that input
	*/
	function validateFunction($function, $value, Array $params=null){		
		$function = $this->getFunctionName($function);

		if(!$params){	
			$params = $this->getFunctionParams($function);
			array_unshift($params, $value);
		}else{
			array_unshift($params, $value);
		}
		if(function_exists($function)){
			//check for existance of a function called $function in global scope
			$ret = call_user_func_array($function, $params);
		}elseif(method_exists($this, $function)){
			//check for existance of a function in the validation lib			
			$ret = call_user_func_array(array($this, $function), $params);
		}else{
			throw new Exception("The validation method \"$function\" does not seem to be defined!");
		}
		return $ret;
	}

	function validateForm($form, $post){
		//lots to do here...
		//need dependency checking lib, or a method of telling php which fields were not showing
		//(either one, I don't care).
	}

	/** 
	* Set a validation message for a rule
	* @param string $rule Rule to set message for
	* @param string $message
	*/
	function setValidationMessage($rule, $message){
		$this->validation_messages[$rule] = $message;
	}

	/**
	* Get a validation message set for a rule
	* @param string $rule Rule to get message for
	* @return string
	*/
	function getValidationMessage($rule){
		return $this->validation_messages[$rule];
	}

	/**
	* Get a function name from the validation rule string (ex. min_length[3])
	* @param string $validation_string
	*/
	function getFunctionName($validation_string){
		$l_brkt = strpos($validation_string, '[');
		if($l_brkt !== false){
			return substr($validation_string, 0, ($l_brkt - 1));
		}else{
			return $validation_string;
		}
	}

	/**
	* Get parameters from the validation rule string (ex. length_between[3,6])
	* @param string $validation_string
	*/
	function getFunctionParams($validation_string){
		$l_brkt = strpos($validation_string, '[');
		$r_brkt = strpos($validation_string, ']');
		if($l_brkt !== false){
			$params = explode(",", substr($validation_string, $l_brkt, ($r_brkt - 1)));
		}
		if($params){
			return $params;
		}else{
			return array();
		}
	}

	#############  Below here are class-defined validation functions! ###########
	# (if you want to add functions that should not appear in this lib, add them to validation_helper.php
	# as it will be included in this lib)


	// --------------------------------------------------------------------



	/**
	 * Required
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function required($str)
	{
		if ( ! is_array($str))
		{
			$ret = (trim($str) == '') ? FALSE : TRUE;
		}
		else
		{
			$ret = ( ! empty($str));
		}
		if($ret) return $ret;
		else{
			$this->setValidationMessage('required', 'This field is required!');
			return $ret;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Performs a Regular Expression match test.
	 *
	 * @access	public
	 * @param	string
	 * @param	regex
	 * @return	bool
	 */
	public function regex_match($str, $regex)
	{
		$ret = TRUE;
		if ( ! preg_match($regex, $str))
		{
			$ret = FALSE;
			$this->setValidationMessage('regex_match', "This field must match the regular expression: $regex!");
		}		
		return $ret;
	}

	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	
	public function matches($str, $field)
	{
		if ( ! isset($_POST[$field]))
		{	
			$this->setValidationMessage('matches', '');
			return FALSE;
		}

		$field = $_POST[$field];

		$ret = ($str !== $field) ? FALSE : TRUE;
		if($ret) return $ret;
		else{
			$this->setValidationMessage('matches', "This field must match the one named $field!");
			return $ret;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Match one field to another
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */
	/*
	public function is_unique($str, $field)
	{
		list($table, $field)=explode('.', $field);
		$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
		
		return $query->num_rows() === 0;
  }
  */

	// --------------------------------------------------------------------

	/**
	 * Minimum Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function min_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) < $val) ? FALSE : TRUE;
		}

		return (strlen($str) < $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Max Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function max_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) > $val) ? FALSE : TRUE;
		}

		return (strlen($str) > $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Exact Length
	 *
	 * @access	public
	 * @param	string
	 * @param	value
	 * @return	bool
	 */
	public function exact_length($str, $val)
	{
		if (preg_match("/[^0-9]/", $val))
		{
			return FALSE;
		}

		if (function_exists('mb_strlen'))
		{
			return (mb_strlen($str) != $val) ? FALSE : TRUE;
		}

		return (strlen($str) != $val) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Email
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_email($str)
	{
		return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Emails
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_emails($str)
	{
		if (strpos($str, ',') === FALSE)
		{
			return $this->valid_email(trim($str));
		}

		foreach (explode(',', $str) as $email)
		{
			if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE)
			{
				return FALSE;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate IP Address
	 *
	 * @access	public
	 * @param	string
	 * @param	string "ipv4" or "ipv6" to validate a specific ip format
	 * @return	string
	 */
	/*
	public function valid_ip($ip, $which = '')
	{
		return $this->CI->input->valid_ip($ip, $which);
	}
	*/

	// --------------------------------------------------------------------

	/**
	 * Alpha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha($str)
	{
		return ( ! preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_numeric($str)
	{
		return ( ! preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alpha-numeric with underscores and dashes
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function alpha_dash($str)
	{
		return ( ! preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function numeric($str)
	{
		return (bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

	}

	// --------------------------------------------------------------------

	/**
	 * Is Numeric
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_numeric($str)
	{
		return ( ! is_numeric($str)) ? FALSE : TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Integer
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function integer($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Decimal number
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function decimal($str)
	{
		return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Greather than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function greater_than($str, $min)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str > $min;
	}

	// --------------------------------------------------------------------

	/**
	 * Less than
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function less_than($str, $max)
	{
		if ( ! is_numeric($str))
		{
			return FALSE;
		}
		return $str < $max;
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number  (0,1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural($str)
	{
		return (bool) preg_match( '/^[0-9]+$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Is a Natural number, but not a zero  (1,2,3, etc.)
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function is_natural_no_zero($str)
	{
		if ( ! preg_match( '/^[0-9]+$/', $str))
		{
			return FALSE;
		}

		if ($str == 0)
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Valid Base64
	 *
	 * Tests a string for characters outside of the Base64 alphabet
	 * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	public function valid_base64($str)
	{
		return (bool) ! preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Prep data for form
	 *
	 * This function allows HTML to be safely shown in a form.
	 * Special characters are converted.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	/*
	public function prep_for_form($data = '')
	{
		if (is_array($data))
		{
			foreach ($data as $key => $val)
			{
				$data[$key] = $this->prep_for_form($val);
			}

			return $data;
		}

		if ($this->_safe_form_data == FALSE OR $data === '')
		{
			return $data;
		}

		return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
	}
	*/

	// --------------------------------------------------------------------

	/**
	 * Prep URL
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function prep_url($str = '')
	{
		if ($str == 'http://' OR $str == '')
		{
			return '';
		}

		if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
		{
			$str = 'http://'.$str;
		}

		return $str;
	}

	// --------------------------------------------------------------------

	/**
	 * Strip Image Tags
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	/*
	public function strip_image_tags($str)
	{
		return $this->CI->input->strip_image_tags($str);
	}
	*/

	// --------------------------------------------------------------------

	/**
	 * XSS Clean
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	/*
	public function xss_clean($str)
	{
		return $this->CI->security->xss_clean($str);
	}
	*/

	// --------------------------------------------------------------------

	/**
	 * Convert PHP tags to entities
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function encode_php_tags($str)
	{
		return str_replace(array('<?php', '<?PHP', '<?', '?>'),  array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
	}

	/**
	* Formats a form name and strips out any bad chars
	*/
	public function formnameformat($str){
		return preg_replace('/[^a-z0-9\-]/i','',trim($str));
	}

	/**
	* Requires input to have a unique form name
	*/
	public function newformname($str){
		$this->CI->load->model('form');
		
		if(!empty($str)){
			$res = $this->CI->form->nameExists($str);
		}else{
			$res = true;
		}
		if($res){
			$this->setValidationMessage('newformname', "The form name is required but must not be in use by another form! Please use a different name!");
			return false;
		}else{
			return true;
		}
	}	
}

?>