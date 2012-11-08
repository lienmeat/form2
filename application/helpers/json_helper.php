<?php

/**
* Funciton automatically handles doing either a regular
* ajax response, or a jsonp response
*/
function jsonResponse($data){
	$out = json_encode($data);

	//our jsonp callback method is set, do jsonp
	if($_GET['jsonp123']){
		$out=$_GET['jsonp123']."(".$out.");";
	}

	echo $out;
}

?>