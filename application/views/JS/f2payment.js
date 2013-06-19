/*
 *  Project: f2 Payments
 *  Description: Javascript to handle formit2 payments
 *  Author: Eric (who else?!)
 *	Example use:
 * 	To initialize, bind f2payment to a form (and only a form!)
 *	$('#form_id').f2payment();
 * 	The script will automatically look for the right field names, and form events, and just work.
 */

// the semi-colon before function invocation is a safety net against concatenated 
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, undefined ) {

	// undefined is used here as the undefined global variable in ECMAScript 3 is
	// mutable (ie. it can be changed by someone else). undefined isn't really being
	// passed in so we can ensure the value of it is truly undefined. In ES5, undefined
	// can no longer be modified.

	// window and document are passed through as local variables rather than globals
	// as this (slightly) quickens the resolution process and can be more efficiently
	// minified (especially when both are regularly referenced in your plugin).

	// Create the defaults once
	var pluginName = 'f2payment',
			document = window.document,
			//public methods are callable via the first argument
			//of your plugin.  Alias them any way you see fit!
			publicMethods = {
				//accessible via $.pluginName('example')
				//'example': 'exampleToAlias',
				'pay': 'pay',
				'paymentDone': 'paymentDone',
			};
			

	// The actual plugin constructor (leave it alone!)
	function Plugin( element ) {    
		
		//set the name of the plugin
		this._name = pluginName;

		//element is accessible here
		this.element = element;

		//map the public methods to the plugin
		this.publicMethods = publicMethods;

		//set the defaults
		this._defaults = $.fn[this._name].options;

		//set options to default
		this.options = this._defaults;

		//run init with stock options
		//this.init( this.options );
	}

	/**
	* Init called any time no public method is called
	*/
	Plugin.prototype.init = function ( options ) {    
		this.setOptions( options );
		// Place initialization logic here
		// You already have access to the DOM element and the options via the instance, 
		// e.g., this.element and this.options
		this.bindEvents();
	};

	/**
	* Pass an options object to change options
	*/
	Plugin.prototype.setOptions = function( options ) {
		var options = options || {};

		// jQuery has an extend method which merges the contents of two or 
		// more objects, storing the result in the first object. The first object
		// is generally empty as we don't want to alter the default options for
		// future instances of the plugin
		this.options = $.extend( {}, $.fn[this._name].options, this.options, options);
	}

	/**
	* Binds events to the form
	*/
	Plugin.prototype.bindEvents = function(){
		var elem = this.element;		
		$(elem).eventually('on', 'submit', {}, function(e){ $(elem).f2payment('pay', e); });
		//$(elem).eventually('after', 'submit', {}, function(e){ /*console.log(e);*/ });

		//probably have to handle checkout items too!

	}

	/**
	* Run a payment
	* Currently only creditcard is supported
	*/
	Plugin.prototype.pay = function(event){
		var elem = this.element;
		if(!elem.payment_complete){
			//make sure submit event stops!
			event.stopImmediatePropagation();
			console.log(event);
			var payment_details = this.getPaymentData();    
			if(payment_details){
				switch(payment_details.payment_method){
					case "creditcard":
						this.doCCPayment(payment_details);
						break;
				}
			}
		}    
	}

	/**
	* Gets only core payment fields that wwu accounting needs for every payment
	*/
	Plugin.prototype.getPaymentData = function(){
		var payment_data = {};
		var elem = this.element;
		payment_data['f2token'] = $('#' +elem.id + ' input[name="f2token"]').val();
		payment_data['Payment_Type'] = $('#' +elem.id + ' input[name="Payment_Type"]').val();
		payment_data['Payment_Amount'] = $('#' +elem.id + ' input[name="Payment_Amount"]').val();
		payment_data['Payment_for'] = $('#' + elem.id + ' input[name="Payment_for"]').val();
		payment_data['Pay_to_Account_Number'] = $('#' + elem.id + ' input[name="Pay_to_Account_Number"]').val();
		payment_data['payment_method'] = $('#' + elem.id + ' input[name="payment_method"]').attr('readonly', 'readonly').val();
		return payment_data;
	}

	/**
	* Gets all CC payment specific fields, and then runs the payment, returning results
	* @param object payment_data Data pulled from the form previously from other functions
	* @return object results of the payment operation
	*/
	Plugin.prototype.doCCPayment = function(payment_data){
		var elem = this.element;
		payment_data['billTo_firstName'] = $('#' + elem.id + ' input[name="billTo_firstName"]').val();    
		payment_data['billTo_lastName'] = $('#' + elem.id + ' input[name="billTo_lastName"]').val();
		payment_data['billTo_street1'] = $('#' + elem.id + ' input[name="billTo_street1"]').val();
		payment_data['billTo_street2'] = $('#' + elem.id + ' input[name="billTo_street2"]').val();
		payment_data['billTo_city'] = $('#' + elem.id + ' input[name="billTo_city"]').val();
		payment_data['billTo_state'] = $('#' + elem.id + ' select[name="billTo_state"]').val();
		payment_data['billTo_postalCode'] = $('#' + elem.id + ' input[name="billTo_postalCode"]').val();
		payment_data['billTo_country'] = $('#' + elem.id + ' select[name="billTo_country"]').val();
		payment_data['billTo_email'] = $('#' + elem.id + ' input[name="billTo_email"]').val();
		payment_data['card_cardType'] = $('#' + elem.id + ' select[name="card_cardType"]').val();
		payment_data['card_cvNumber'] = $('#' + elem.id + ' input[name="card_cvNumber"]').val();
		payment_data['card_accountNumber'] = $('#' + elem.id + ' input[name="card_accountNumber"]').val();
		payment_data['card_expirationMonth'] = $('#' + elem.id + ' select[name="card_expirationMonth"]').val();
		payment_data['card_expirationYear'] = $('#' + elem.id + ' select[name="card_expirationYear"]').val();
		this.doPayment(payment_data);
		return false;
	}

	/**
	* Actually hits the server with the payment data, and returns the response
	* @param object payment_data
	* @return object
	*/
	Plugin.prototype.doPayment = function(payment_data){
		var elem = this.element;
		doAjax('payments/pay', payment_data, function(resp){ $(elem).f2payment('paymentDone', resp, payment_data); });
	}

	Plugin.prototype.paymentDone = function(resp, payment_data){
		if(resp && resp.status && resp.status == 'success'){
			this.element.payment_complete = true;
			$(this.element).eventually('trigger', 'payment_success', {'payment_data': payment_data, 'processor_response': resp});			
			$(this.element).submit();  //re-submit this form
		}else{
			$(this.element).eventually('trigger', 'payment_failure', {'payment_data': payment_data, 'processor_response': resp});
			alert('This payment failed to complete!  Please look and make sure the billing information you provided is correct, and try again!');
		}
	}




	// A really lightweight plugin wrapper around the constructor, 
	// preventing against multiple instantiations,
	// while giving tons of cool functionality
	$.fn[pluginName] = function ( methodOrOptions ) {
		//we need the arguments of this call to be available after the annonymous function runs!
		var args = arguments;

		return this.each(function() {
			if( !$.data( this, 'plugin_' + pluginName ) ) {
				var plugin = new Plugin( this );
				$.data( this, 'plugin_' + pluginName, plugin );
			} else {
				var plugin = $.data( this, 'plugin_' + pluginName );
			}

			//Lets you run multi-argument methods (if they are public) on the plugin 
			//(ex: $('#thing').pluginName('example', 'flip', 'jr', {'key': 'value'}); )
			if( plugin.publicMethods[ methodOrOptions ] ){
				//1st argument is in publicMethods, run it with the rest of the arguments!
				plugin[plugin.publicMethods[ methodOrOptions ]].apply( plugin, Array.prototype.slice.call( args, 1 ) );
			} else if( typeof methodOrOptions === 'object' || ! methodOrOptions ){
				// Default to "init" if argument is an object or empty
				plugin.init( methodOrOptions );
			} else {
				//otherwise, they did something silly
				$.error( 'Method ' +  methodOrOptions + ' does not exist on '+pluginName );
			}
		});
	}

	//globally configurable options
	//which can be over-ridden
	$.fn[pluginName].options = {       
	};

}( jQuery, window ));