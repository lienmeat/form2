<?php
	/**
	* Note that this only does a javascript redirect, not a php one!
	*/
	$this->load->view('header');
	if($timeout) $timeout = $timeout*1000; //convert to mili-seconds
	elseif($message) $timeout = "10000"; //default to 10 seconds if there is a message
	else $timeout = "1"; //if no message, timeout is effectively 0
	if(!$color) $color = 'black';
	if($message) echo "<h1 style=\"color: $color;\">$message</h1><p>This page will automatically redirect in ".($timeout/1000)." seconds.</p>";
	if($location && strpos($location, 'http') === FALSE) $location = site_url($location);
	elseif(empty($location)) $location = base_url().index_page();
	
	echo "<script type='text/javascript'> setTimeout('window.location=\"$location\"', $timeout); </script>";
	$this->load->view('footer');
?>
