/**
* This object handles validating a form or form inputs
*/
function Validation(form_id){
	/**
	* where in the window variable is this set
	* so we can access it via events?
	*/
	this.handle = false;
	this.fullform = false;
	this.form_id = form_id;	
	this.validation_errors = [];
	this.ignore_validation = false;
	this.createHandle();
	this.__init__();
}

/**
* Set up object, binding events to inputs and submit
*/
Validation.prototype.__init__= function(){
	var handle = this.getHandle();
	//match inputs with validation attribute and loop

	$('#'+this.form_id+' [validation]').each(
		function(i){
			//bind validateInput to change event on each input that matched
			$(this).blur(
				function(event){
					window.validators[handle].validateInput(event.target);
				}
			);
		}
	);


	$('#'+this.form_id).submit(
		function(){
			//way to get around validating form when using the 
			//remote validation callback after validateForm() calls it.
			if(window.validators[handle].ignore_validation == true){				
				return true;
			}else{
				return window.validators[handle].validateForm();
			}			
		}
	);
}

Validation.prototype.createHandle = function(){
	if(!window.validators){
		window.validators = [];
	}
	this.handle = (window.validators.push(this)-1);
	return this.handle;
}

Validation.prototype.getHandle = function(){
	return this.handle;
}

Validation.prototype.disableSubmitButton = function(){	
	$('#'+this.form_id+" :submit").each(function(i, input){		
		$(input).val('...WORKING...');
		$(input).attr('disabled', 'disabled');
	});
}

Validation.prototype.enableSubmitButton = function(){
	$('#'+this.form_id+" :submit").each(function(i, input){
		$(input).val('Submit');
		$(input).attr('disabled', false);
	});	
}

Validation.prototype.validateForm = function(callback){
	var handle = this.getHandle();
	var callback = callback || function(ret){ window.validators[handle].submitForm(ret); };
	this.disableSubmitButton();
	this.fullform = true;
	var inputs = $('#'+this.form_id+' [validation]').get();	
	var remotevalidations = [];	
	var validated = true;
	for(i in inputs){
		var ret = this.validateInput(inputs[i], true);
		if(ret){
			var remotevalidations = remotevalidations.concat(ret);			
		}else{
			validated = false;			
		}
	}
	if(validated && remotevalidations.length == 0){
		this.fullform = false;
		this.enableSubmitButton();
		callback(true);
		return true;
	}else if(validated && remotevalidations.length > 0){ //we have remote validations to do
		this.doRemoteValidations(remotevalidations, true, callback);
		return false;
	}else{ //we failed a validation
		this.doRemoteValidations(remotevalidations);
		this.enableSubmitButton();
		callback(false);
		return false;
	}
}

/**
*	Validates an individual input for validity
*/
Validation.prototype.validateInput = function(input){
	//get this value as soon as we instantiate, because it's important
	//that we know the state when it started, more than at the end! 
	var fullform = this.fullform;
	//get validation string
	var validation = $(input).attr('validation') || '';
		
	//get validation objects parsed from validation string
	var validations = this.parseValidationString(validation);

	var validated = true;
	var remotevalidations = [];
	
	//loop over validation objects and validate the value
	for(i in validations){
		var res = this.checkFunction(validations[i].function, validations[i].params, input);
		if(res == 'undefined_function'){
			//we need to hit the server with the function and hope!
			var val = validations[i];
			val.value = this.getInputValue(input);
			val.input_id = $(input).attr('id');
			remotevalidations.push(val);			
		}else{
			if(res == true){
				//we don't care, go to next function
				continue;
			}else if(res == false){
				//set error msg and break out of loop
				var err = $(input).attr('validationmessage');
				if(!err){
					var err = this.getValidationError(validations[i].function);
					if(err){
						this.showError(input, err);
					}else{
						var err = "Validation message not configured for "+validations[i].function+" but it failed validation!";
						this.showError(input, err);
					}
				}else{
					this.showError(input, err);
				}
				validated = false;
				break;				
			}else{
				//was a text modification function
				//set the value of the input (NOT SUPORTED BY ANYTHING OTHER THAN <input> or <textarea>, and never will be!)
				var tag = $(input).prop('tagName');
				if(tag == "INPUT" || tag == "TEXTAREA"){
					$(input).val(res);
				}
			}
		}
	}

	if(!fullform){
		if(validated){			
			this.hideError(input); //get rid of error message if one exists
			this.doRemoteValidations(remotevalidations);
		}
	}else{ //if fullform, aggregate remote validations, and lock form from submitting
		if(validated){
			this.hideError(input);
			return remotevalidations;
		}else{
			return false;
		}
	}
}

Validation.prototype.doRemoteValidations = function(validations, submit, callback){	
	var submit = submit || false;
	var validations = validations || [];
	var callback = callback || function(){};	
	var handle = this.getHandle();

	if(validations.length > 0){
		doAjax('validate/', {validations: validations}, function(resp){ window.validators[handle].remoteValidationCallback(resp, submit, callback); }, function(resp){ window.validators[handle].remoteValidationCallback(resp, submit, callback); });
		return true;
	}
	return false;
}

Validation.prototype.remoteValidationCallback = function(validations, submit, callback){
	var submit = submit || false;
	var validated = true;
	var callback = callback || function(){};	

	for(i in validations){
		if(validations[i].status == "fail"){
			var input = $('#'+validations[i].input_id).get(0);			
			this.showError(input, validations[i].error_message);
			validated = false;
		}else if(validations[i].status == "value_change"){
			$('#'+validations[i].input_id).val(validations[i].value);
		}
		//other cases are status = pass || undefined_function
		//but we really don't care to do anything about either...
	}
	this.enableSubmitButton();
	this.fullform = false;
	if(callback){
		if(validated){
			callback(true);
		}else{
			callback(false);
		}
	}

	/*
	if(submit){
		//force this off so if there is an onchange that
		//needs a remote validation, it will do it, instead of
		//returning the validations!
		this.fullform = false;
		if(!validated){			
			
		}
	}
	*/
	//this.enableSubmitButton();	
}

/**
* Turns validation string into an array of objects representing validation rules
* @param string validation Complete string in validation attribute of input
* @return object Object containing function name, and params to run on it
*/
Validation.prototype.parseValidationString = function(validation){
	//set validation string
	var validation = validation || '';
	//split string into rules
	var funcs = validation.split("|") || [];
	//set up array for us to push objects into
	var validations = [];
	//loop over rule strings
	for(i in funcs){
		//get right and left bracket positions (if any)
		var l_brkt = funcs[i].indexOf('[');
		var r_brkt = funcs[i].indexOf(']');		
		if(l_brkt > 0){
			//we have a left bracket, break function name from params
			var func = funcs[i].substr(0,l_brkt);
			var params =  funcs[i].substr((l_brkt+1), ((r_brkt - l_brkt) - 1)).split(',');
		}else{
			//no brackets, grab function name, and set empty params array
			var func = funcs[i];
			var params = [];
		}
		//push our validation object to the array
		validations.push({function: func, params: params});
	}
	return validations;
}

/**
* Checks a validation function against an input's value
* If the validation function doesn't exist client side,
* It will be put in an array of validations to run server side
* @param string func Function name to run
* @param array params Parameters to send to function
* @param HTMLELEMENT input Input element in question
* @return misc Whatever the function returns, or "undefined_function"
*/
Validation.prototype.checkFunction = function(func, params, input){
	var value = this.getInputValue(input);

	//test for globalally accessible functions
	if(typeof window[func] == 'function'){
		var res = window[func](value, params);
		if(res == false){
			var err = this.getValidationError(func);
			if(!err){
				this.setValidationError(func, 'Validation failed!');
			}
			return false;
		}else{
			return res;
		}		
	}else if(typeof this[func] == 'function'){
		//defined inside this class
		return this[func](value, params);
	}else{
		return "undefined_function";
	}	
}

/**
* Gets the values of all the inputs similarly named (name="whatever") values as an array (because check boxes and multi select!)
* @param HTMLinput|select|textarea Input to work with
*/
Validation.prototype.getInputValue = function(input){
	var tag = $(input).prop('tagName');
	var type = $(input).attr('type');
	var name = $(input).attr('name');
		
	//handle cases where we need to get the value in any way besides just using .val() on the input itself
	if(type && (type == 'checkbox' || type == 'radio')){
		name = name.replace('[', '\\[').replace(']', '\\]');
		var value = $('[name='+name+']:checked').val();
	}else{
		var value = $(input).val();
	}
	return value;
}

/**
* Set a validation error for a validation function
* @param string func Function that was called
* @param string err Error message to register with function
*/
Validation.prototype.setValidationError = function(func, err){
	this.validation_errors[func] = err;
}

/**
* Get a validation error message string by validation function name
* @param string func Function that was called
* @return string|false
*/
Validation.prototype.getValidationError = function(func){
	var msg = this.validation_errors[func];
	if(msg) return msg;
	else return false;
}

/**
* Gets an element's size and position
*/
Validation.prototype.getElementSizeAndPosition = function(elem){
	var props = {size: {}};


	props.size.width = $(elem).width();
	props.size.height = $(elem).height();
	props.position = $(elem).position();	
	
	//tl
	props.position.topLeftX = props.position.left;
	props.position.topLeftY = props.position.top;
	//tr
	props.position.topRightX = props.position.right;
	props.position.topRightY = props.position.top;

	//bl
	props.position.bottomLeftX = props.position.left;
	props.position.bottomLeftY = props.position.top + props.size.height;
	//br
	props.position.bottomRightX = props.position.right;
	props.position.bottomRightY = props.position.top + props.size.height;
	return props;	
}

Validation.prototype.showError = function(input, err){
	var notif = this.getNotif(input);
	$(notif).html(err);
	$(notif).removeClass('hide');
	$(notif).addClass('show');	
}

Validation.prototype.hideError = function(input){
	var notif = this.getNotif(input);
	$(notif).removeClass('show');
	$(notif).addClass('hide');
	
}

Validation.prototype.getNotif = function(input){
	//var input_id = $(input).attr('id');
	var input_name = $(input).attr('name').replace('[', '--').replace(']', '--');
	var notif_id = input_name+'_err';
	var notif = $('#'+notif_id).get(0);
	if(!notif){
		var props = this.getElementSizeAndPosition(input);
		var notif = jQuery('<div />').appendTo($(input).parent());
		$(notif).addClass('err_notif hide');
		$(notif).attr('id', notif_id);		
		$(notif).css('top', props.position.bottomLeftY+6);
		$(notif).css('left', props.position.bottomLeftX);		
	}
	return notif;
}


/**
* Gets the class "questionName_fi2" from an input
* So we can query the dom for the input's parent's parts (error container)
* @param HTMLELEMENT elem Element
* @return string|false
*/
Validation.prototype.getQuestionClassFromElem = function(elem){
	var classes = $(elem).attr('class').split(' ');
	var q_class = false;
	for(i in classes){
		if(classes[i].indexOf('_fi2') >= 0){
			q_class = classes[i];
			break;
		}
	}
	return q_class;
}

Validation.prototype.submitForm = function(submit){
	if(submit == true){		
		this.ignore_validation = true;
		$('#'+this.form_id).submit();
		console.log("should have submitted: "+submit+" form_id: "+this.form_id);		
	}
}

/*********************** Validation functions go below this line *******************/

Validation.prototype.min_length = function(value, params){
	if(value.length < params[0]){
		this.setValidationError('min_length', 'Must be at least '+params[0]+' in length!');
		return false;
	}
	return true;
}

function trim(value, params){
	return value.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	//return false;
}