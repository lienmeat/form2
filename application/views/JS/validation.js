$(document).ready(function(){ Validation.__init__(); });

/**
* This object handles validating a form or form inputs
*/
var Validation = function(){
}


Validation.validation_errors = [];

/**
* Set up object, binding events to inputs and submit
*/
Validation.__init__= function(){
	//match inputs with validation attribute and loop
	$('[validation]').each(
		function(i){
			//bind validateInput to change event on each input that matched
			$(this).change(
				function(event){
					Validation.validateInput(event.target);
				}
			);
		}
	);
	//bind to submit of form todo: standardize on form id="" value
}

/**
*	Validates an individual input for validity
*/
Validation.validateInput = function(input, fullform){

	//get validation string
	var validation = $(input).attr('validation') || '';
		
	//get validation objects parsed from validation string
	var validations = Validation.parseValidationString(validation);

	var validated = true;
	var remotevalidations = [];
	
	//loop over validation objects and validate the value
	for(i in validations){		
		var res = Validation.checkFunction(validations[i].function, validations[i].params, input);
		if(res == 'undefined_function'){
			//we need to hit the server with the function and hope!
			var val = validations[i];
			val.value = Validation.getInputValue(input);
			val.input_name = $(input).attr('name').replace('[','').replace(']','');
			remotevalidations.push(val);
		}else{
			if(res == true){
				//we don't care, go to next function
				continue;
			}else if(res == false){
				//set error msg and break out of loop
				var err = $(input).attr('validationmessage');
				if(!err){
					var err = Validation.getValidationError(validations[i].function);
					if(err){
						Validation.setErrorMessage(input, err);
					}else{
						var err = "Validation message not configured for "+validations[i].function+" but it failed validation!";
						Validation.setErrorMessage(input, err);
					}
				}else{
					Validation.setErrorMessage(input, err);
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
			Validation.setErrorMessage(input, ''); //get rid of error message if one exists
			Validation.doRemoteValidations(remotevalidations);
		}else{
			remotevalidations = [];
		}
	}else{ //if fullform, aggregate remote validations, and lock form from submitting
		if(validated){
			Validation.setErrorMessage(input, '');
			return remotevalidations;
		}else{
			return false;
		}
	}
}

Validation.doRemoteValidations = function(validations){
	var validations = validations || [];
	if(validations.length > 0){
		doAjax('validation/validate', {validations: validations}, Validation.remoteValidationCallback, Validation.remoteValidationCallback);
		return true;
	}
	return false;
}

Validation.remoteValidationCallback = function(resp){
	alert(resp);
}

/**
* Turns validation string into an array of objects representing validation rules
* @param string validation Complete string in validation attribute of input
* @return object Object containing function name, and params to run on it
*/
Validation.parseValidationString = function(validation){
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
Validation.checkFunction = function(func, params, input){
	var value = Validation.getInputValue(input);

	//test for globalally accessible functions
	if(typeof window[func] == 'function'){
		var res = window[func](value, params);
		if(res == false){
			var err = Validation.getValidationError(func);
			if(!err){
				Validation.setValidationError(func, 'Validation failed!');
			}
			return false;
		}else{
			return res;
		}		
	}else if(typeof Validation[func] == 'function'){
		//defined inside this class
		return Validation[func](value, params);
	}else{
		return "undefined_function";
	}	
}

/**
* Gets the values of all the inputs similarly named (name="whatever") values as an array (because check boxes and multi select!)
* @param HTMLinput|select|textarea Input to work with
*/
Validation.getInputValue = function(input){
	var tag = $(input).prop('tagName');
	var name = $(input).attr('name');
	
	//handle cases where we need to get the value in any way besides just using .val() on the input itself
	if(tag == 'CHECKBOX' || tag == 'RADIO'){
		var value = $('[name='+name+']:checked').val();
	}else{
		var value = $(input).val();
	}
	return value;
}

/**
* Set a validation error for a validation function
* @param string func Function that was called
* @param string message Error message to register with function
*/
Validation.setValidationError = function(func, message){
	Validation.validation_errors[func] = message;
}

/**
* Get a validation error message string by validation function name
* @param string func Function that was called
* @return string|false
*/
Validation.getValidationError = function(func){
	var msg = Validation.validation_errors[func];
	if(msg) return msg;
	else return false;
}

/**
* Set a validation error message on a question (show error to user)
* @param HTMLELEMENT input Input element validation was called on
* @param string message Message to set
*/
Validation.setErrorMessage = function(input, message){
	//figure out how schema for all form rows, and how to set an error
	var err_element = Validation.getQuestionErrorElement(input);
	$(err_element).html(message);
}

/**
* Gets the class "questionName_fi2" from an input
* So we can query the dom for the input's parent's parts (error container)
* @param HTMLELEMENT elem Element
* @return string|false
*/
Validation.getQuestionClassFromElem = function(elem){
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

/**
* Attempts to retrieve the form_question_error of the current question via classes assigned
* to an element belonging to that question.
* @param HTMLELMENT elem Element (probably an input)
* @return HTMLELEMENT
*/
Validation.getQuestionErrorElement = function(elem){
	//form_question_err
	var q_class = Validation.getQuestionClassFromElem(elem);
	return $('.'+q_class+".form_question_err").get(0);
}

/*********************** Validation functions go below this line *******************/

Validation.min_length = function(value, params){
	if(value.length < params[0]){
		Validation.setValidationError('min_length', 'Must be at least '+params[0]+' in length!');
		return false;
	}
	return true;
}

function trim(value, params){
	return value.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	//return false;
}