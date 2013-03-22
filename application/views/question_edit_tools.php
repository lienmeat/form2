<div class="question_edit_tools <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question_edit_tools">
	<span class="icon"><img src="<?php echo base_url(); ?>application/views/IMG/edit-short.gif" onclick="FormEditor.editQuestion('<?php echo $question->id; ?>');"/></span>
	<span class="icon"><img src="<?php echo base_url(); ?>application/views/IMG/drop.gif" onclick="FormEditor.deleteQuestion('<?php echo $question->id; ?>');"/></span>
	<span class="icon"><img src="<?php echo base_url(); ?>application/views/IMG/arrow-insert.gif" onclick="FormEditor.addQuestion('<?php echo $question->id; ?>');"/></span>
	<?php if($question->config->name) echo "<span>".$question->config->name."</span>"; ?>
</div>