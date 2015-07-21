<?php
$menu = array(
);
$this->load->view('header', array('title'=>$form->name, 'banner_menu'=>$menu));	
?>
<script>
var lasthelp = '';
function addHelp(){
	var searchterms = $("#searchterms").val();
	var help = $("#help").val();
	if(help != lasthelp){
		lasthelp = help;
		doAjax('helps/add', {searchterms: searchterms, help: help}, addHelpDone);
	}
}

function addHelpDone(resp){
	$("#searchterms").val('');
	$("#help").val('');
}

</script>
<div>
	Search Terms: <textarea id="searchterms" name="searchterms"></textarea><br />
	Help Text: <textarea id="help" name="help"></textarea><br />
	<button onclick="addHelp();">Add</button>
</div>
<?php
$this->load->view('footer');
?>