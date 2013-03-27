<?php
$this->load->view('header', array('title'=>'Duplicate?'));
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
?>
<script type="text/javascript">
	$(document).ready(function(){		
		var form_val = new Validation('identconfirmform');
	});	
</script>
<h1>It appears you accidentally submitted the same thing twice!</h1>
<p>If this was not an accident, and you didn't refresh the page after submitting a form, select YES, otherwise, you probably want to select NO.</p>
<form id="identconfirmform" method="POST">
	<ul class="form_contain view_mode">
	<?php
		$config =(object) array(
				'id'=>'1',
				'text'=>'Did you mean to submit the same data again?',
				'name'=>'identconfirm',
				'type'=>'radio',
				'options'=>array('Yes'=>'Yes','No'=>'No'),
				'validation'=>'required',
			);
		$this->load->view('question/view_question',(object) array('question'=>(object) array('config'=>$config)));
	?>
	</ul>
	<input type="submit" name="submitme" value="Submit">
</form>
<?php
$this->load->view('footer');
?>