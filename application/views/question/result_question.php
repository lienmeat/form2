<li class="form_row result_mode <?php echo $config->name; ?>_fi2" id="<?php echo $id; ?>">
	<?php $this->load->view('element/result_'.$config->type, array('id'=>$id, 'form_id'=>$form_id, 'order'=>$order 'config'=>$config)); ?>
</li>