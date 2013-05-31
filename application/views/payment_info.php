<div>
	<h4>Billing Information</h4>
</div>

<?php if(in_array('creditcard', $payment_methods)) { ?>
<div id="ccpayinfo" dependencies="payment_method=credit*">
	Credit Card
</div>
<?php } ?>

<?php if(in_array('echeck', $payment_methods)) { ?>
<div id="ecpayinfo" dependencies="payment_method=echeck*">
	eCheck
</div>
<?php } ?>