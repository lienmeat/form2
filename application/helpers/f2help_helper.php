<?php
/**
* inserts code to make help work
*/
function f2Help($help_id){
	$CI =& get_instance();
	return $CI->load->view('f2help', array('id'=>$help_id), true);
}
?>