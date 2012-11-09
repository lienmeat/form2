<?php
$headdata = array('title'=>'New Form', 'banner_text'=>'Create a new form.');
$this->load->view('header', $headdata);
$this->load->library('inputs');
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
//$this->load->library('elements');
?>
<style>


</style>


<div id="form_config">
	<form name="add_form" method="POST" id="form_id">

		<div class="form_contain">
		
			<div class="form_row">
							
				<div class="form_question">
					<div class="form_question_text">blah blah b blah blah blah blah blah </div>
					<div class="form_question_alt">dddd</div>
					<div class="form_question_err"></div>
				</div>
				
				<div class="form_answer">
					<div class="form_element_contain">
						<input type="text">
					</div>
				</div>

				<div class="clear"></div>

			</div>

			<div class="form_row questionName_fi2" id="question_id">
				
				<div class="form_question questionName_fi2" id="question_id_question">
					<div id="question_id_question_text" class="form_question_text questionName_fi2">blah blah blah blah blah blah blah blah blah</div>
					<div id="question_id_question_alt" class="form_question_alt questionName_fi2">dddd</div>
					<div id="question_id_question_err" class="form_question_err questionName_fi2"></div>
				</div>
				
				<div class="form_answer questionName_fi2" id="question_id_answer">
					<div class="form_element_contain questionName_fi2" id="question_id_element_contain">
						<input id="question_id_input1" class="questionName_fi2" type="text" name="questionname" onclick="" validation="required" ><span class="questionName_fi2 required">*</span> <!-- validationmessage="Test Message" -->
					</div>
				</div>

				<div class="clear"></div>

			</div>

			<div class="form_row form_footer" id="form_footer">
				<div class="form_question">
					<div id="question_id_question_text" class="form_question_text questionName_fi2">
						Go ahead! See what happens when you submit!
					</div>
				</div>
				<div class="form_answer">
					<div class="form_element_contain">
						<input type="submit" name="submit_fi2" value="Submit" onclick="return Validation.validateForm(this, 'form_id');">
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