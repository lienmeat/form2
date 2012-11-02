<?php
	/**
	* Note that this only does a javascript redirect, not a php one!
	*/
	$this->load->view('header');
	if($message) echo "<h1>$message</h1>";
	if($timeout) $timeout = $timeout*1000; //convert to mili-seconds
	elseif($message) $timeout = "3000"; //default to 5 seconds if there is a message
	else $timeout = "1"; //if no message, timeout is effectively 0
	if($location) $location = site_url($location);
	else $location = base_url().index_page();
	//if($not_allowed) $location = "http://wallawalla.edu";
	echo "<script type='text/javascript'> setTimeout('window.location=\"$location\"', $timeout); </script>";
	$this->load->view('footer');
?>
