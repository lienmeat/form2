<li class="form_row edit_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>">
	<?php
		if($config->type)
			$this->load->view('element/edit_'.$config->type, array('id'=>$id, 'form_id'=>$form_id, 'order'=>$order, 'config'=>$config, 'answer'=>$answer));
	?>
	<div class="clear"></div>
</li>