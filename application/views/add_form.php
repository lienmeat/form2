<?php
$headdata = array('title'=>'New Form', 'banner_text'=>'Create a new form.');
$this->load->view('header', $headdata);
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/jquery.eventually.js\"></script>";
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/dependencies.js\"></script>";
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
?>
<script type="text/javascript">
	$(document).ready(function(){
		var form_dep = new Dependencies('new_form_form');
		var form_val = new Validation('new_form_form');
	});
</script>

<div id="form_config">
	<form name="add_form" method="POST" id="new_form_form">

		<ul class="form_contain">
		
			<?php
				$this->load->view('config_form');
			?>

			<li class="form_row form_footer" id="form_footer">
				<div class="form_question">
					<div id="question_id_question_text" class="form_question_text questionName_fi2">						
					</div>
				</div>
				<div class="form_answer">
					<div class="form_element_contain">
						<input type="submit" name="submit_fi2" value="Submit">
					</div>
				</div>
			</li>

		</ul>	
	</form>
</div>

<?php
$this->load->view('footer');	
?>