<?php
$headdata = array('title'=>'New Form', 'banner_text'=>'Create a new form.');
$this->load->view('header', $headdata);
$this->load->library('inputs');
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
echo "";
?>
<script type="text/javascript">
	$(document).ready(function(){
		var form_val = new Validation('new_form_form');
	});
</script>

<div id="form_config">
	<form name="add_form" method="POST" id="new_form_form">

		<div class="form_contain">
		
			<?php
				$this->load->view('config_form');
			?>

			<div class="form_row form_footer" id="form_footer">
				<div class="form_question">
					<div id="question_id_question_text" class="form_question_text questionName_fi2">
						Go ahead! See what happens when you submit!
					</div>
				</div>
				<div class="form_answer">
					<div class="form_element_contain">
						<input type="submit" name="submit_fi2" value="Submit">
					</div>
				</div>
			</div>

		</div>

	<?php echo "<pre>".print_r($form, true)."</pre>"; ?>
	</form>
</div>

<?php
$this->load->view('footer');	
?>