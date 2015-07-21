<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2">Payment/Donation Checkout</div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
	<script type="text/javascript" src="<?php echo base_url(); ?>application/views/JS/f2payment.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#<?php echo $form->id; ?>").f2payment();
		});
	</script>
	
		<?php

			$conf = (object) array(
				'type'=>'hidden',
				'name'=>'Payment_Type',
				'value'=>$question->config->Payment_Type,
			);
			$this->inputs->setConfig($conf);
			echo $this->inputs;
			
			$conf = (object) array(
				'type'=>'hidden',
				'name'=>'Payment_for',
				'value'=>$question->config->Payment_for,
			);
			$this->inputs->setConfig($conf);
			echo $this->inputs;
			
			$conf = (object) array(
				'type'=>'hidden',
				'name'=>'Pay_to_Account_Number',
				'value'=>$question->config->Pay_to_Account_Number,
			);
			$this->inputs->setConfig($conf);
			echo $this->inputs;

			$conf = (object) array(
				'type'=>'text',
				'name'=>'Payment_Amount',
				'value'=>$question->config->Payment_Amount,
			);
			$this->inputs->setConfig($conf);
			if($question->config->amount_editable == 'no'){
				$this->inputs->setAttribute('readonly', 'readonly');
				//$this->inputs->setAttribute('disabled', 'disabled');
			}
			$this->inputs->setAttribute('validation', 'required|decimal|greater_than[1.00]');			
			echo "<div><label for=\"Payment_Amount\">Amount:</label>".$this->inputs."</div>";

			if(count($question->config->payment_methods) > 1){
				$radios = '';
				foreach($question->config->payment_methods as $m){
					$label = '';
					switch($m){
						case 'creditcard':
							$label = "Credit Card";
							break;
						case 'echeck':
							$label = "e-Check";
							break;						
					}
						
					$conf = (object) array(
						'type'=>'radio',
						'name'=>'payment_method',
						'value'=>$m,
						'label'=>$label,
					);
					$this->inputs->setConfig($conf);
					$this->inputs->setAttribute('validation', 'required');
					$radios.="<div class=\"input_multiple\">".$this->inputs."</div>";
				}
				echo "<div><label>I will pay via:</label>".$radios."</div>";
			}else{
				$conf = (object) array(
					'type'=>'hidden',
					'name'=>'payment_method',
					'value'=>$question->config->payment_methods[0],						
				);
				$this->inputs->setConfig($conf);
				echo $this->inputs;
			}

			$this->load->view('payment_info', array('payment_methods'=>$question->config->payment_methods));
		?>
	</div>
</div>