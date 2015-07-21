<div class="form_question edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">	
	<?php
		$this->load->view('question_edit_tools', array('question'=>$question));
	?>	
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer edit_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  edit_mode <?php echo $question->config->name; ?>_fi2">
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
			$split = explode("#", $value);
			if(count($split) > 1){
				$writein = $split[1];
				$value = $split[0];
			}else{
				$writein = false;
			}

			$mod =  $check_input_count % ($question->config->columns);

			if($writein !== false){
				$input = array(
					'name'=>$question->config->name."[]",
					'type'=>'text',										
				);				
				$this->inputs->setConfig($input);
				$this->inputs->setAttribute('validation', $question->config->validation);
				if($writein) {
					$this->inputs->setAttribute('size', $writein);
				}
				$hash_idx = strpos($label, '#');
				if($hash_idx !== false){
					$wi_label = substr($label, 0, $hash_idx)."&nbsp;";
				}else{
					$wi_label = $label;
				}

			}else{
				$wi_label = '';
				//no write in defined, render normal checkbox
				$input = array(
					'name'=>$question->config->name,
					'type'=>'checkbox',
					'value'=>$value,
					'label'=>$label,
					'selected'=>$question->config->selected,
				);

				$this->inputs->setConfig($input);
				
				$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');			
				$this->inputs->setAttribute('id', $question->id."_input".$check_input_count);
				$this->inputs->setAttribute('validation', $question->config->validation);
			}
			if(($check_input_count-1) % $question->config->columns == 0){
				echo "<tr>";
			}
			echo "<td>".$wi_label.$this->inputs."</td>\n";
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
		unset($check_input_count);
		?>
	</div>
</div>