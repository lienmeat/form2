<tr><td><label>Payment Method: </label></td><td>Credit Card</td></tr>
<tr><td><label>First Name: </label></td><td><?php echo $data->billTo_firstName; ?></td></tr>
<tr><td><label>Last Name: </label></td><td><?php echo $data->billTo_lastName; ?></td></tr>
<tr><td><label>Address: </label></td><td><?php
	echo $data->billTo_street1."<br />";
	if($data->billTo_street2){
		echo $data->billTo_street2."<br />";
	}
	echo $data->billTo_city;
	if($data->billTo_state){
		echo " ".$data->billTo_state;
	}
	if($data->billTo_postalCode){
		echo " ".$data->billTo_postalCode;
	}
	echo "<br />".$data->billTo_country;
	?>
</td></tr>
<tr><td><label>Email: </label></td><td><?php echo $data->billTo_email; ?></td></tr>
<tr><td><label>Credit Card Type: </label></td><td>
	<?php
	if($data->card_cardType == '001'){ echo "Visa"; } 
	if($data->card_cardType == '002'){ echo "Master Card"; }
	if($data->card_cardType == '003'){ echo "American Express"; }
	if($data->card_cardType == '004'){ echo "Discover"; }
	?>
</td></tr>
<tr><td><label>Card #: </label></td><td><?php echo $data->card_accountNumber; ?></td></tr>
<tr><td><label>Expiration: </label></td><td><?php echo $data->card_expirationMonth."/".$data->card_expirationYear; ?></td></tr>
