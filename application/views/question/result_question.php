<div class="form_row result_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>">
	<div class="form_question result_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_question">		
		<div class="form_question_text <?php echo $config->name; ?>_fi2"><?php echo $config->text; ?></div>
		<div class="form_question_alt <?php echo $config->name; ?>_fi2"><?php echo $config->alt; ?></div>
		<div class="form_question_err <?php echo $config->name; ?>_fi2"></div>
	</div>	
	<div class="form_answer result_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_answer">
		<div class="form_element_contain  result_mode <?php echo $config->name; ?>_fi2">
			<?php $this->load->view('element/result_'.$config->element->type, array('id'=>$id, 'form_id'=>$form_id, 'order'=>$order 'config'=>$config)); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>