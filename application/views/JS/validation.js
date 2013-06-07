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
	//this.ignore_validation = false;
	this.is_validated = false;
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
			var validation = $(this).attr('validation');
			if(validation && validation.indexOf('required') >= 0){
				$(this).addClass('required');
			}

			//bind validateInput to change event on each input that matched
			if($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio'){
				$(this).blur(
					function(event){
						window.validators[handle].validateInput(event.target);
					}
				);
			}else{
				$(this).change(
					function(event){
						window.validators[handle].validateInput(event.target);						
					}
				);
			}
			//todo: attach some indicator to question that it is required
			//console.log(this);
		}
	);


	$('#'+this.form_id).eventually('before','submit', {}, 
		function(event){
			//way to get around validating form when using the 
			//remote validation callback after validateForm() calls it.
			if(window.validators[handle].is_validated === true){
				//console.log(window.validators);				
				return true;
			}else{
				var res = window.validators[handle].validateForm();
				if(res){					
					$('#'+this.form_id).eventually('trigger','validation_success', {});
					return true;
				}else{					
					$('#'+this.form_id).eventually('trigger','validation_fail', {});
					return false;
				}
				return res;
			}
		}
	);

	/*$('#'+this.form_id).submit(
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
	*/
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
		this.is_validated = true;
		callback(true);
		return true;
	}else if(validated && remotevalidations.length > 0){ //we have remote validations to do
		this.doRemoteValidations(remotevalidations, true, callback);
		this.is_validated = true; //so far we are, remote validations could change this!
		return false;
	}else{ //we failed a validation
		this.is_validated = false;
		this.doRemoteValidations(remotevalidations);
		this.enableSubmitButton();
		callback(false);
		alert('The form is not filled out correctly!  Please look at the form for error bubbles and correct the problems.');
		return false;
	}
}

/**
*	Validates an individual input for validity
*/
Validation.prototype.validateInput = function(input){
	//any time this is triggered, the form obviously is NOT validated completely (because a change happened)
	this.is_validated = false;
	var name = $(input).attr('name');
	var hidden_fields = $('#dependhiddeninputs_'+this.form_id).val()
	if(hidden_fields && hidden_fields.length > 0) hidden_fields = hidden_fields.split(',');
	else hidden_fields = '';
	if(hidden_fields.indexOf(name) !== -1){
		this.hideError(input);
		return [];
	}

	//get this value as soon as we instantiate, because it's important
	//that we know the state when it started, more than at the end!
	var fullform = this.fullform;
	//get validation string
	var validation = $(input).attr('validation') || '';
	
	//figures out if input is even required
	var is_required;
	if(validation.indexOf('required') !== -1) is_required = true;
	else is_required = false;
		
	//get validation objects parsed from validation string
	var validations = this.parseValidationString(validation);

	var validated = true;
	var remotevalidations = [];

	//determines whether validation functions actually should be run
	//to fix case where there are validation functions but field has
	//no input and isn't required
	var do_validation = true;

	if(is_required){
		do_validation = true;
	}else{
		var iv = this.getInputValue(input);
		if(iv && iv.length > 0){
			do_validation = true;
		}else{
			do_validation = false;
		}
	}

	if(do_validation){
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
	var vals = [];
	for(i in validations){
		if(validations[i].function && validations[i].function.length > 0){
			vals.push(validations[i]);
		}
	}

	if(validations.length > 0){
		doAjax('validate/', {validations: vals}, function(resp){ window.validators[handle].remoteValidationCallback(resp, submit, callback); }, function(resp){ window.validators[handle].remoteValidationCallback(resp, submit, callback); });
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
			$('#'+this.form_id).eventually('trigger', 'validation_success', {});
			callback(true);
		}else{
			this.is_validated = false;
			$('#'+this.form_id).eventually('trigger', 'validation_fail', {});
			alert('The form is not filled out correctly!  Please look at the form for error bubbles and correct the problems.');
			callback(false);
		}
	}	
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
	if(!func || func.length < 1) return true;
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
		name = name.replace(/\[/g, '\\[').replace(/\]/g, '\\]');		
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
	var input_name = $(input).attr('name').replace(/\[/g, '--').replace(/\]/g, '--');
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
		//this.ignore_validation = true;
		$('#'+this.form_id).submit();
		//console.log("should have submitted: "+submit+" form_id: "+this.form_id);
	}
}

/*********************** Validation functions go below this line *******************/

Validation.prototype.required = function(value){
	if(value && value.length > 0){
		return true;
	}else{
		this.setValidationError('required', 'This field is required!');
		return false;
	}
}

Validation.prototype.regex_match = function(value, params){
	var ret = true;
	
	if(params){		
		if(params.length > 1){
			var regex = new RegExp(params[0], params[1]);
		}else{
			var regex = new RegExp(params[0]);
		}
	}

	var matches = value.match(regex);
	if( !matches || matches.length < 1) {
		ret = false;
		this.setValidationError('regex_match', "This field must match the regular expression: "+params[0]+"!");
	}		
	return ret;
}

Validation.prototype.matches = function(value, field_name){
	var ret = false;

	var field = $("[name="+field_name+"]").get();
	field = field[0];
	var field_value = this.getInputValue(field);

	if( value != field_value ){
		ret = false;
	}else{
		ret = true;	
	}		
	if(ret) return ret;
	else{
		this.setValidationError('matches', "This field must match the one named "+field_name+"!");
		return ret;
	}
}

Validation.prototype.equals = function(value, toequal){
	var ret = false;

	if( value != toequal ){
		ret = false;
	}else{
		ret = true;
	}		
	if(ret) return ret;
	else{
		this.setValidationError('equals', "This field must contain "+toequal+"!");
		return ret;
	}
}

Validation.prototype.min_length = function(value, params){
	if(value.length < params[0]){
		this.setValidationError('min_length', 'Must be at least '+params[0]+' in length!');
		return false;
	}
	return true;
}

Validation.prototype.max_length = function(value, params){
	if(value.length > params[0]){
		this.setValidationError('max_length', 'Must less than '+params[0]+' in length!');
		return false;
	}
	return true;
}

Validation.prototype.exact_length = function(value, params){
	if(value.length == params[0]){
		this.setValidationError('exact_length', 'Must be at exactly '+params[0]+' in length!');
		return false;
	}
	return true;
}


Validation.prototype.valid_email = function(value){
	var ret = false;

	var matches = value.match(/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i);
	if(!matches || matches.length < 1){
		ret = false;
		this.setValidationError('valid_email', "This email address does not appear to be valid!");
	}else{
		ret = true;
	}
	return ret;
}

/**
 * Alpha
 */
Validation.prototype.alpha = function(value) {
	var ret = false;
	var matches = value.match(/^([a-z])+$/i);
	if(matches && matches.length > 0){
		ret = true;
	}

	if(!ret){
		this.setValidationError('alpha', "This field can only contain letters (a-z)!");
	}
	return ret;
}

/**
 * Alpha-numeric
 */
Validation.prototype.alpha_numeric = function(value) {
	var ret = false;
	var matches = value.match(/^([a-z0-9])+$/i);
	if(matches && matches > 0){
		ret = true;
	}
	if(!ret){
		this.setValidationError('alpha_numeric', "This field can only contain numbers and letters!");
	}
	return ret;
}

/**
 * Alpha-numeric with underscores and dashes
 */
Validation.prototype.alpha_dash = function(value) {
	ret = false;
	var matches = value.match(/^([-a-z0-9_-])+$/i);
	
	if(matches && matches.length > 0){
		ret = true;
	}

	if(!ret){
		this.setValidationError('alpha_dash', "This field can only contain numbers, letters, underscore, and hyphen!");			
	}
	return ret;
}

/**
 * Numeric
 */
Validation.prototype.numeric = function(value) {
	var ret = false;
	var matches = value.match(/^[\-+]?[0-9]*\.?[0-9]+$/);
	if(matches && matches.length > 0){
		ret = true;
	}
	if(!ret){
		this.setValidationError('numeric', "This field contain a numeric value!");			
	}
	return ret;
}

/**
 * Integer
 */
Validation.prototype.integer = function(value) {
	var ret = false;
	var matches = value.match(/^[\-+]?[0-9]+$/);
	if(matches && matches.length > 0){
		ret = true;
	}
	if(!ret){
		this.setValidationError('integer', "This field can only contain integers! (0, 1, 2...)");			
	}
	return ret;
}

/**
 * Decimal number
 */
Validation.prototype.decimal = function(value) {
	var ret = false;

	var matches = value.match(/^[\-+]?[0-9]+\.[0-9]+$/);
	if(matches && matches.length > 0){
		ret = true;
	}
	if(!ret){
		this.setValidationError('decimal', "This field must contain a decimal number!");			
	}
	return ret;
}

/**
 * Greather than
 */
Validation.prototype.greater_than = function(value, min) {
	var ret = false;
	
	ret = (value > min);	

	if(!ret) {
		this.setValidationError('greater_than', "This field must contain a value greater than \""+min+"\"!");
	}
	return ret;
}

/**
 * Less than
 */
Validation.prototype.less_than = function(value, max) {
	var ret = false;
	ret = (value < max);
	
	if(!ret){
		this.setValidationError('less_than', "This field must contain a value less than \""+max+"\"!");
	}
	return ret;
}

/**
 * Is a Natural number  (0,1,2,3, etc.)
 */
Validation.prototype.is_natural = function(value) {
	var ret = false;
	var matches = value.match(/^[0-9]+$/);
	if(matches && matches.length > 0){
		ret = true;
	}
	if(!ret){
		this.setValidationError('is_natural', "This field must contain a natural number (ex: 0, 1, 2...)!");
	}
	return ret;
}

/**
 * Is a Natural number, but not a zero  (1,2,3, etc.)
 */
Validation.prototype.is_natural_no_zero = function(value) {
	var ret = false;
	var matches = value.match(/^[0-9]+$/);
	if(matches && matches.length > 0) {
		ret = true;
	}
	if (value == 0) {
		ret = false;
	}

	if(!ret) {
		this.setValidationError('is_natural_no_zero', "This field must contain a natural counting number (ex: 1, 2, 3...)!");		
	}
	return ret;
}


function trim(value, params){
	return value.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	//return false;
}