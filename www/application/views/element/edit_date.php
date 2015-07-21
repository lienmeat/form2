<div class="form_question edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">
	<?php
		$this->load->view('question_edit_tools', array('question'=>$question));
	?>		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>	

<div class="form_answer edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain edit_mode <?php echo $question->config->name; ?>_fi2">
		<?php
		$this->load->view('JS/jquery-ui.php');
		$question->config->type = 'text';
		$this->inputs->setConfig($question->config);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('validation', $question->config->validation);
		$this->inputs->setAttribute('id', $question->id."_input0");
		$this->inputs->setAttribute('placeholder', 'yyyy-mm-dd');
		echo $this->inputs;

		?>
		<script>
		$(function() {
			var dp_options = {
				//easiest date format to parse out
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true,
				<?php
				//print out mindate setting if it exists
				if($question->config->mindate) {
					//absolute date is set
					echo "\"minDate\": \"{$question->config->mindate}\",\n";
				}elseif( isset($question->config->mindays) || isset($question->config->minmonths) || isset($question->config->minyears) ){
					//relative date is set
					$minstr = "";
					if($question->config->mindays > 0){
						$minstr.="+".$question->config->mindays."d ";
					}elseif($question->config->mindays < 0){
						$minstr.=$question->config->mindays."d ";
					}
					if($question->config->minmonths > 0){
						$minstr.="+".$question->config->minmonths."m ";
					}elseif($question->config->minmonths < 0){
						$minstr.=$question->config->minmonths."m ";
					}
					if($question->config->minyears > 0){
						$minstr.="+".$question->config->minyears."y ";
					}elseif($question->config->minyears < 0){
						$minstr.=$question->config->minyears."y ";
					}
					echo "\"minDate\": \"$minstr\",\n";
				}?>
				<?php
				//print out maxdate setting if it exists
				if($question->config->maxdate) {
					//absolute date is set
					echo "\"maxDate\": \"{$question->config->maxdate}\",";
				}elseif( isset($question->config->maxdays) || isset($question->config->maxmonths) || isset($question->config->maxyears) ){
					//relative date is set
					$maxstr = "";
					if($question->config->maxdays > 0){
						$maxstr.="+".$question->config->maxdays."d ";
					}elseif($question->config->maxdays < 0){
						$maxstr.=$question->config->maxdays."d ";
					}
					if($question->config->maxmonths > 0){
						$maxstr.="+".$question->config->maxmonths."m ";
					}elseif($question->config->maxmonths < 0){
						$maxstr.=$question->config->maxmonths."m ";
					}
					if($question->config->maxyears > 0){
						$maxstr.="+".$question->config->maxyears."y ";
					}elseif($question->config->maxyears < 0){
						$maxstr.=$question->config->maxyears."y ";
					}
					echo "\"maxDate\": \"$maxstr\",\n";
				}?>
				//function to tell datepicker what days to show, not show
				beforeShowDay: function(date){
					var m = date.getMonth()+1, d = date.getDate(), y = date.getFullYear();
    				if(d < 10) { 
						d = '0'+d;
					}
					if(m < 10) {
						m = '0'+m;
					}
					var cmp_date = y + '-' + m + '-' + d;

					//days that are disabled from being picked on calendar by specific date
					var disabledDays = <?php echo json_encode($question->config->disabled_days); ?>;
					if($.inArray(cmp_date, disabledDays) != -1){
						return [false];
					}					

					return [true];
				}				 			
			};
		    $('#<?php echo $question->id."_input0"; ?>').datepicker(dp_options);
		});	
		</script>
	</div>
</div>