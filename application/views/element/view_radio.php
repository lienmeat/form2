<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
		<?php
		if($question->config->options->dataprovider){
			$question->config->options = $this->dataprovider->run($question->config->options->dataprovider->method);
		}

		$check_input_count = 1;
		echo "
		<table>
			<tbody>";
		if(!$question->config->options) $question->config->options = array();
		if(!$question->config->columns){
			$question->config->columns = 2;
		}
		foreach($question->config->options as $label=>$value){
			$mod =  $check_input_count % ($question->config->columns);
			$input = array(
				'name'=>$question->config->name,
				'type'=>'radio',
				'value'=>$value,
				'label'=>$label,
				'selected'=>$question->config->selected,
			);		

			$this->inputs->setConfig($input);

			$this->inputs->setAttribute('validation', $question->config->validation);
			$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');			
			$this->inputs->setAttribute('id', $question->id."_input".$check_input_count);
			$this->inputs->setAttribute('validation', $question->config->validation);

			if(($check_input_count-1) % $question->config->columns == 0){
				echo "<tr>";
			}
			echo "<td>".$this->inputs."</td>\n";
			if($mod == 0){
				echo "</tr>\n";
			}
			$check_input_count++;
		}
		if(($check_input_count-1) % $question->config->columns){
			echo "</tr>";
		}		
		echo "</tbody>
		</table>";
		unset($radio_input_count);		
		?>
	</div>
</div>