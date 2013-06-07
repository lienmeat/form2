<?php
$this->load->view('header');
?>

<script type="text/javascript" src="<?php echo base_url(); ?>application/views/JS/jquery.eventually.js"></script>
<script type="text/javascript">

$(document).ready(function(){

	$('#testbut').eventually('on', 'click', {}, 
		function(event){ 
			alert(event); 
		}
	);
	
	$('#testbut').eventually('before', 'click', {'testing_before': 'ok'},
		function(event){
			alert('beforebuttonclick');
			//event.stopImmediatePropagation();
			//event.stopPropagation();
			//return false;
		}
	);	

	$('#testbut').eventually('after', 'click', {'testing_after': 'ok'}, 
		function(event){
			alert('afterbuttonclick');
			var res = $(window).eventually('trigger', 'BOOM', {'id': 'dynomite', 'triggered_by': event});
			if(res){
				alert('success');
			}else{
				alert('fail');
			}
		}
	);	

	$(window).eventually('before', 'BOOM', {'boom': 'ok'}, 
		function(event){ 
			alert('beforeBOOM fired');
			//event.stopImmediatePropagation();
			return false;
		}
	);
		
	$(window).eventually('after', 'BOOM', {'boom': 'ok'}, 
		function(event){ 
			alert('afterBOOM fired');
		}
	);
});
</script>
<div id="testdiv1" class="testclass"></div>
<div id="testdiv2" class="testclass"></div>
<button id="testbut">test</button>

<?php
$this->load->view('footer');
?>