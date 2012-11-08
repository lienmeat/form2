<div class="form_row edit_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>">
	<div class="question_edit_tools <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_question_edit_tools"></div> 
	<div class="form_question edit_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_question">		
		<div class="form_question_text <?php echo $config->name; ?>_fi2"><?php echo $config->text; ?></div>
		<div class="form_question_alt <?php echo $config->name; ?>_fi2"><?php echo $config->alt; ?></div>
		<div class="form_question_err <?php echo $config->name; ?>_fi2"></div>
	</div>	
	<div class="form_answer edit_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_answer">
		<div class="form_element_contain edit_mode <?php echo $config->name; ?>_fi2">
			<?php $this->load->view('element/edit_'.$config->element->type, array('id'=>$id, 'form_id'=>$form_id, 'order'=>$order 'config'=>$config)); ?>
		</div>
	</div>
	<div class="clear"></div>
</div>