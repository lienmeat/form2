<?php

/**
* Function automatically handles doing either a regular
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

/**
* Grabs json from a curl request (as long as the body has json at the end)
*/
function getJSONFromCurl($string){
	$matches = array();
	$res = preg_match('/{.*}$/', $string, $matches);
	if($res){
		return $matches[0];
	}else{
		return '{}';
	}
}

?>