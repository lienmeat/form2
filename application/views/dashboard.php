<?php
$this->load->view('header', array('title'=>'F2 Dashboard', 'banner_text'=>'Dashboard: Search and manage forms.'));
?>

<style>
#formsearchresults{
	display: none;	
}

#myforms{	
	/*display: none;*/
	margin-left: 1em;
}

#myforms_contain{
	margin-top: 2em;
}

</style>

<script type="text/javascript">
function testit(){
	
}

/**
* Searches for forms
* @param string input JQ selector of input
* @param string container JQ selector of container to put results
* 
*/
function formsearchfunc(input, container){
	var value = $(input).val();
	doAjax('forms/ajaxSearch', {'search': value}, function(resp, status, error){ formsearchdone(resp, container); }, log);	
	return false;
}


function formsearchdone(resp, container){
	var results = false;
	if(resp && resp.status && resp.status == 'success'){
		if(resp.html && resp.html.length > 10){
			$(container).html(resp.html);
			results = true;
		}
	}

	if(!results){
		$(container).html("<h4>No Results</h4>");		
	}
	$(container).show();
}

function searchCreatorOption(elem){
	$('#formsearchtxt').val($(elem).val());
	$('#formseachform').submit();
}
</script>

<div id="search_contain">
	<form id="formseachform" name="formsearch" onsubmit="return formsearchfunc('#formsearchtxt', '#formsearchresults');">		
		<input id="formsearchtxt" type="search" onchange="formsearchfunc('#formsearchtxt', '#formsearchresults');"/><input type="submit" value="Search">(search by id, name, title, or creator's username)<br />OR Select from form creators:
		<?php
			$form_creators_opt = array();
			if(is_array($form_creators)){
				foreach($form_creators as $value){
					$form_creators_opt[$value] = $value;
				}
			}

			$this->load->library('inputs');
			$form_creators_opt = array_merge(array('Select One'=>''), $form_creators_opt);
			$usernames_conf = array(
				'type'=>'select',
				'options'=>$form_creators_opt,
				'attributes'=>(object) array(
					'onchange'=>'searchCreatorOption(this);'
				),

			);
			$this->inputs->setConfig($usernames_conf);
			echo $this->inputs;
		?>
	</form>
	<div id="formsearchresults">			
	</div>
</div>

<div id="myforms_contain"><a href="javascript:void(0);" onclick="toggleView('#myforms');">My Forms</a>

	<div id="myforms">
		<?php
		echo anchor('forms/add', 'Make a Form');		
		if($myforms){
			$this->load->view('formlist', array('forms'=>$myforms));
			echo anchor('forms/add', 'Make a Form');
		}
		?>
	</div>

</div>

<?php
$this->load->view('footer');
?>