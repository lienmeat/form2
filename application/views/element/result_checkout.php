<div class="form_question result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2">Payment/Donation Checkout</div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  result_mode <?php echo $question->config->name; ?>_fi2">
		<table><tbody>
		<tr><td><label>Payment/Donation Type: </label></td><td><?php echo $result->post->Payment_Type; ?></td></tr>
		<tr><td><label>Payment For: </label></td><td><?php echo $result->post->Payment_for; ?></td></tr>
		<tr><td><label>WWU account number to credit (7-digit AR #): </label></td><td><?php echo $result->post->Pay_to_Account_Number; ?></td></tr>
		<tr><td><label>Payment Amount: </label></td><td><?php echo $result->post->Payment_Amount; ?></td></tr>
		<tr><td colspan="2"><h4>Billing Information</h4></td></tr>		
		<?php $this->load->view("element/result_checkout_".$result->post->payment_method, array('data'=>$result->post)); ?>
		</tbody></table>
	</div>
</div>