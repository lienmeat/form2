<div class="form_question view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer view_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  view_mode <?php echo $question->config->name; ?>_fi2">
		<table><tbody>
		<?php	
		//this is a pretty complex element...
		//full name (optional)

		//address line1 (Street address, P.O. box, company name, c/o)
		//address line2 (Apartment, suite, unit, building, floor, etc.)

		//City
		//State/Province/Region
		//ZIP/Postal Code
		//Country (DD)

		if($question->config->fullname == 'Y'){
			$fullname = array(
				'name'=>$question->config->name.'_fullname',
				'type'=>'text',				
			);
			$this->inputs->setConfig($fullname);
			$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
			$this->inputs->setAttribute('id', $question->id."_input_fullname");
			if($question->config->validation == 'required'){
				$this->inputs->setAttribute('validation', 'required|min_length[3]');
			}else{
				$this->inputs->setAttribute('validation', 'min_length[3]');
			}
			echo "<tr><td style=\"text-align: right;\"><label>Full Name: </label></td><td>".$this->inputs."</td></tr>";
		}


		$addr1 = array(
			'name'=>$question->config->name.'_addr1',
			'type'=>'text',		
		);
		$this->inputs->setConfig($addr1);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input_addr1");
		if($question->config->validation == 'required'){
			$this->inputs->setAttribute('validation', 'required|min_length[3]');
		}else{
			$this->inputs->setAttribute('validation', 'min_length[3]');
		}
		echo "<tr><td style=\"text-align: right;\"><label>Address Line 1: </label></td><td>".$this->inputs."<br /><span style=\"font-size:8pt;\">(Street address, P.O. box, company name, c/o)</span></td></tr>";

		$addr2 = array(
			'name'=>$question->config->name.'_addr2',
			'type'=>'text'			
		);
		$this->inputs->setConfig($addr2);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input_addr2");
		echo "<tr><td style=\"text-align: right;\"><label>Address Line 2: </label></td><td>".$this->inputs."<br /><span style=\"font-size:8pt;\">(Apartment, suite, unit, building, floor, etc.)</span></td></tr>";

		$city = array(
			'name'=>$question->config->name.'_city',
			'type'=>'text',
		);
		$this->inputs->setConfig($city);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input_city");
		if($question->config->validation == 'required'){
				$this->inputs->setAttribute('validation', 'required');
		}
		echo "<tr><td style=\"text-align: right;\"><label>City: </label></td><td>".$this->inputs."</td></tr>";

		$stateprov = array(
			'name'=>$question->config->name.'_stateprov',
			'type'=>'text'
		);
		$this->inputs->setConfig($stateprov);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input_stateprov");
		echo "<tr><td style=\"text-align: right;\"><label>State/Province/Region: </label></td><td>".$this->inputs."</td></tr>";

		$zip = array(
			'name'=>$question->config->name.'_zip',
			'type'=>'text'
		);
		$this->inputs->setConfig($zip);
		$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
		$this->inputs->setAttribute('id', $question->id."_input_zip");
		echo "<tr><td style=\"text-align: right;\"><label>Zip/Postal Code: </label></td><td>".$this->inputs."</td></tr>";

		if($question->config->countries == 'Y'){
			$country = array(
				'name'=>$question->config->name.'_country',
				'type'=>'select',
				'options'=>array_merge(array('Select a Country'=>''), $this->dataprovider->run('countryOptions')),
				'selected'=>array('US'),
			);
			$this->inputs->setConfig($country);
			$this->inputs->setAttribute('class', $this->inputs->getAttribute('class')." ".$question->config->name.'_fi2');
			$this->inputs->setAttribute('id', $question->id."_input_country");
			if($question->config->validation == 'required'){
				$this->inputs->setAttribute('validation', 'required');
			}
			echo "<tr><td style=\"text-align: right;\"><label>Country: </label></td><td>".$this->inputs."</td></tr>";
		}
		?>
		</tbody></table>
	</div>
</div>