<div class="form_question edit_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_question">
	<?php
		$this->load->view('question_edit_tools', array('id'=>$id, 'form_id'=>$form_id, 'order'=>$order, 'config'=>$config));
	?>		
	<div class="form_question_text <?php echo $config->name; ?>_fi2"><?php echo $config->text; ?></div>
	<div class="form_question_alt <?php echo $config->name; ?>_fi2"><?php echo $config->alt; ?></div>		
</div>	

<div class="form_answer edit_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>_answer">
	<div class="form_element_contain edit_mode <?php echo $config->name; ?>_fi2">
		<?php
		$this->inputs->setConfig($config);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$config->name.'_fi2');
		$this->inputs->setName($config->name);
		$this->inputs->setAttribute('id', $id."_input0");
		echo $this->inputs;
		?>
	</div>
</div>