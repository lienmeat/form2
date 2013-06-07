<?php if(in_array('creditcard', $payment_methods)) { ?>
<div id="ccpayinfo" dependencies="payment_method=credit*">
	<div>
		<h4>Credit Card Billing Information</h4>
	</div>
	<table>
	<tr>
	<td class="question">
		<label for="billTo_firstName">First Name: </label>
	</td>
	<td class="answer">
		<input type="text" name="billTo_firstName" value="" maxlength="60" validation="required">
	</td>
</tr>

<tr>
	<td class="question">
		<label for="billTo_lastName">Last Name: </label>
	</td>
	<td class="answer">
		<input type="text" name="billTo_lastName" value="" maxlength="60" validation="required">
	</td>
</tr>
<h4>IMPORTANT: 90% of failed credit card transactions are due to an incorrect address.<br />The following address must be on file with your credit card company.</h4>
<tr>
	<td class="question">    
		<label>Address: </label>
	</td>
	<td class="answer">
		<input type="text" name="billTo_street1" value="" maxlength="60" placeholder="Street1" validation="required"><br />
		<input type="text" name="billTo_street2" value="" maxlength="60" placeholder="Street2" ><br />
		<input type="text" name="billTo_city" value="" maxlength="50" placeholder="City" validation="required"><br />
		<?php 
			$states_options = $this->dataprovider->stateprovOptions();
			$states_options = array_merge(array('State/Province'=>'', 'None of these'=>''), $states_options);
			$conf = (object) array(
				'type'=>'select',
				'name'=>'billTo_state',
				'options'=>(object) $states_options,				
			);
			$this->inputs->setConfig($conf);			
			echo $this->inputs."(Required only for US & CA)<br />";
		?>
		<input type="text" name="billTo_postalCode" value="" maxlength="10" size="10" placeholder="Zip/Postal Code">(Required only for US & CA)<br />
		<?php 
			$countries_options = $this->dataprovider->countryOptions();
			$countries_options = array_merge(array('Country'=>''), $countries_options);
			$conf = (object) array(
				'type'=>'select',
				'name'=>'billTo_country',
				'options'=>(object) $countries_options,
				'selected'=>array('US'),
			);
			$this->inputs->setConfig($conf);
			$this->inputs->setAttribute('validation', 'required');
			echo $this->inputs."<br />";
		?>		
	</td>
</tr>

<tr>
	<td class="question">
		<label for="billTo_email">Email: </label>
	</td>
	<td class="answer">
		<div><input type="text" name="billTo_email" validation="required|valid_email"></div>
	</td>
</tr>

<tr>
	<td class="question">
		<label for="card_cardType">Card Type: </label>
	</td>
	<td class="answer">
		<div><select name="card_cardType" validation="required"><option value="">Select One</option><option value="001">Visa</option><option value="002">MasterCard</option><option value="004">Discover</option></select></div>
	</td>
</tr>

<tr>
	<td class="question">
		<label for="card_accountNumber">Credit Card Number: </label>
	</td>
	<td class="answer">
		<div><input type="text" id="card_accountNumber" name="card_accountNumber" value="" size="16" maxlength='16' validation="required|integer|min_length[15]|max_length[16]"><span style="font-size: 8pt;">(numbers only!)</span></div>
	</td>
</tr>

<tr>
	<td class="question">    
		<label for="card_cvNumber">Security Code: <a href="javascript:void(0);" onclick="window.open('/payment/cvv2_popup.html','','width=550,height=390,')">(what is this?)</a></label>
	</td>
	<td class="answer">
		<div><input type="text" id="card_cvNumber" name="card_cvNumber" value="" maxlength="4" size="4" validation="required|integer|min_length[3]|max_length[4]"><span style="font-size: 8pt;">(numbers only!)</span></div>
	</td>
</tr>

<tr>
	<td class="question">
		<label>Expiration: </label>
	</td>
	<td class="answer">

		<?php
			$month_opts = (object) array('MM'=>'',
			'January (01)'=>'01',
			'February (02)'=>'02',
			'March (03)'=>'03',
			'April (04)'=>'04',
			'May (05)'=>'05',
			'June (06)'=>'06',
			'July (07)'=>'07',
			'August (08)'=>'08',
			'September (09)'=>'09',
			'October (10)'=>'10',
			'November (11)'=>'11',
			'December (12)'=>'12',
			);
			$conf = (object) array(
				'type'=>'select',
				'name'=>'card_expirationMonth',
				'attributes'=>(object) array('validation'=>'required'),					
				'options'=>(object) $month_opts,
			);
			$this->inputs->setConfig($conf);
			echo "<span>{$this->inputs}</span>";
		
			$years_opts = array('YYYY'=>'');
			$year = date('Y');
			$end = ($year + 10);
			for($i = $year; $i<=$end; $i++){
				$years_opts[$i] = $i;
			}
			$conf = (object) array(
				'type'=>'select',
				'name'=>'card_expirationYear',
				'attributes'=>(object) array('validation'=>'required'),					
				'options'=>(object) $years_opts,
			);
			$this->inputs->setConfig($conf);
			echo "<span>{$this->inputs}</span>";
		?>
			
	</td>
</tr>
</table>
</div>
<?php } ?>

<?php if(in_array('echeck', $payment_methods)) { ?>
<div id="ecpayinfo" dependencies="payment_method=echeck*">
	<div>
		<h4>e-Check Billing Information</h4>
	</div>
	eCheck
</div>
<?php } ?>