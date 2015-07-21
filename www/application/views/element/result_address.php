<div class="form_question result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_question">		
	<div class="form_question_text <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->text; ?></div>
	<div class="form_question_alt <?php echo $question->config->name; ?>_fi2"><?php echo $question->config->alt; ?></div>		
</div>

<div class="form_answer result_mode <?php echo $question->config->name; ?>_fi2" id="<?php echo $question->id; ?>_answer">
	<div class="form_element_contain  result_mode <?php echo $question->config->name; ?>_fi2">
		<table>
			<tbody>
		<?php	
		//this is a pretty complex element...
		//full name (optional)

		//address line1 (Street address, P.O. box, company name, c/o)
		//address line2 (Apartment, suite, unit, building, floor, etc.)

		//City
		//State/Province/Region
		//ZIP/Postal Code
		//Country (DD)
		$name = $question->config->name;

		if($question->config->fullname == 'Y'){
			$tmpname = $name."_fullname";
			echo "<tr><td style=\"text-align: right;\"><label>Full Name: </label></td><td>".$formresult->post->$tmpname."</td></tr>";
		}

		$tmpname = $name."_addr1";
		echo "<tr><td style=\"text-align: right;\"><label>Address Line 1: </label></td><td>".$formresult->post->$tmpname."</td></tr>";

		$tmpname = $name."_addr2";
		echo "<tr><td style=\"text-align: right;\"><label>Address Line 2: </label></td><td>".$formresult->post->$tmpname."</td></tr>";

		$tmpname = $name."_city";
		echo "<tr><td style=\"text-align: right;\"><label>City: </label></td><td>".$formresult->post->$tmpname."</td></tr>";

		$tmpname = $name."_stateprov";
		echo "<tr><td style=\"text-align: right;\"><label>State/Province/Region: </label></td><td>".$formresult->post->$tmpname."</td></tr>";

		$tmpname = $name."_zip";
		echo "<tr><td style=\"text-align: right;\"><label>Zip/Postal Code: </label></td><td>".$formresult->post->$tmpname."</td></tr>";

		if($question->config->countries == 'Y'){
			$tmpname = $name."_country";
			echo "<tr><td style=\"text-align: right;\"><label>Country: </label></td><td>".$formresult->post->$tmpname."</td></tr>";
		}
		?>
			</tbody>
		</table>
	</div>
</div>