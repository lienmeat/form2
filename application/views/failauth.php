<?php
	/**
	* Note that this only does a javascript redirect, not a php one!
	*/
	$this->load->view('header');
	if($timeout) $timeout = $timeout*1000; //convert to mili-seconds
	elseif($message) $timeout = "10000"; //default to 5 seconds if there is a message
	else $timeout = "1"; //if no message, timeout is effectively 0
	if($message) echo "<h1 style=\"color: red;\">$message</h1><p>This page will automatically redirect to the ".APP_NAME." dashboard in ".($timeout/1000)." seconds.</p>";
	
	$location = base_url().index_page();
	//if($not_allowed) $location = "http://wallawalla.edu";
	echo "<script type='text/javascript'> setTimeout('window.location=\"$location\"', $timeout); </script>";
	$this->load->view('footer');
?>
