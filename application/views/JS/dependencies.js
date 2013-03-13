/**
* Handles dependent questions on specific form values.  Easy way to make forms have advanced behaviors
* by showing/hiding questions based on input in fields.  Hidden fields will not be validated if set up properly!
*	Class must be run/inited before validation so that event handling occurs in the right order.
*/

function Dependencies(form_id){
	this.handle = false;	
	this.form_id = form_id;	
	this.dependson = {};
	this.rules = {};
	this.createHandle();
	this.__init__();
}

/**
* Creates a handle in global scope in window object
* so we can solve scope issues easier later
*/
Dependencies.prototype.createHandle = function(){
	if(!window.dependencies){
		window.dependencies = [];
	}
	this.handle = (window.dependencies.push(this)-1);
	return this.handle;
}

/**
* Gets the global handle in the window object for this object
* So we can run event handlers easily/solve scope issues
*/
Dependencies.prototype.getHandle = function(){
	return this.handle;
}

/**
* Set up object, binding events to inputs and anything else
*/
Dependencies.prototype.__init__ = function(){
	var handle = this.getHandle();
	this.dependson = [];	
	//match inputs with validation attribute and loop

	$('#'+this.form_id+' [dependencies]').each(
		function(i){
			//parse out rules for every question/thing with dependencies
			window.dependencies[handle].parseRules(this);			
		}
	);
	this.bindInputs();

	if($('#dependhiddenquestions_'+this.form_id).toArray().length <= 0){
		$('#'+this.form_id).append('<input type="hidden" id="dependhiddenquestions_'+this.form_id+'" name="dependhiddenquestions">');
		$('#'+this.form_id).append('<input type="hidden" id="dependhiddeninputs_'+this.form_id+'" name="dependhiddeninputs">');
	}else{
		$('#dependhiddenquestions_'+this.form_id).val('');
		$('#dependhiddeninputs_'+this.form_id).val('');
	}

	this.checkAll();
}

/**
* Parses rules from "dependencies" attribute, making them objects for easier use
* and relates them to the actual fields that they depend on.
* Also persists rules in object format in the dom on the questions.
* @param element question Question/form row which has a dependencies attribute
*/
Dependencies.prototype.parseRules = function(question){
	//split rules string into individual rules.
	var raw_rules = $(question).attr('dependencies').split('&&');
	var rules = [];
	for(i in raw_rules){
		var operator = null;
		
		//get operator
		if(raw_rules[i].indexOf('>=') != -1) operator = '>=';
		else if(raw_rules[i].indexOf('>') != -1) operator = '>';
		else if(raw_rules[i].indexOf('<=') != -1) operator = '<=';
		else if(raw_rules[i].indexOf('<') != -1) operator = '<';
		else if(raw_rules[i].indexOf('!=') != -1) operator = '!=';
		else if(raw_rules[i].indexOf('=') != -1) operator = '=';

		var parts = raw_rules[i].split(operator);

		rules.push({'question_id':$(question).attr('id'), 'fieldname':parts[0], 'operator':operator, 'value':parts[1]});
		this.addDependsOn(parts[0], $(question).attr('id'));		
	}

	//assign rules to question dom so that we can always look them up later
	this.addRules($(question).attr('id'), rules);	
}

/**
* Add a dependency to check
* @param string fieldname Name of a field on which a rule depends
* @param string question_id ID of question that depends on this field
*/
Dependencies.prototype.addDependsOn = function(fieldname, question_id){
	//make new array if one doesn't exist for this fieldname
	if(!this.dependson[fieldname]) this.dependson[fieldname] = [];
	//add field if not already in array
	if(this.dependson[fieldname].indexOf(question_id) == -1){
		this.dependson[fieldname].push(question_id);
	}	
}

/**
* Binds inputs in dependson array to change/blur events
* so that we can check for dependencies when they change value
*/
Dependencies.prototype.bindInputs = function(){
	var handle = this.getHandle();
	
	for(i in this.dependson){
		var name = i;
		//make name compatible with jquery selectors if a multi-select elem
		name = name.replace('[', '\\[').replace(']', '\\]');
		$('#'+this.form_id+" [name='"+name+"']").each(
			function(elem){
				//bind event to the elem
				if($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio'){
					$(this).blur(
						function(event){
							window.dependencies[handle].checkDepend(event.target);
						}
					);
				}else{
					$(this).change(
						function(event){
							window.dependencies[handle].checkDepend(event.target);
						}
					);
				}				
			}
		);	
	}
}

/**
* Checks all dependencies
*/
Dependencies.prototype.checkAll = function(){
	for(i in this.dependson){
		for(j in this.dependson[i])
			this.testDepend(this.dependson[i][j]);
	}
}

/**
* Checks a particular element to see if anything is depending on it
* If so, will run all dependencies for those things.
*/
Dependencies.prototype.checkDepend = function(target){
	 //console.log(target);
	var name = $(target).attr('name');
	if(this.dependson[name]){
		for(i in this.dependson[name]){
			this.testDepend(this.dependson[name][i]);
		}
	}
}

/**
* Add dependency rules for question to list of rules
* @param string question_id ID of a question element
* @param string rules Rules belonging to 
*/
Dependencies.prototype.addRules = function(question_id, rules){	
	this.rules[question_id] = rules;
}

/**
* Get dependency rules for question
* @param string question_id ID of a question element
*/
Dependencies.prototype.getRules = function(question_id){
	if(this.rules[question_id]) return this.rules[question_id];
	else return [];
}

/** 
* Test the dependencies of a question
* @param string question_id ID of question element
*/
Dependencies.prototype.testDepend = function(question_id){
	var rules = this.getRules(question_id);
	var pass = true;
	for(var i=0 in rules){
		if(this.testRule(rules[i])){
			//console.log("rule passed: ");
			//console.log(rules[i]);
			continue;
		}else{
			//console.log("rule failed: ");
			//console.log(rules[i]);
			pass = false;
			break;
		}
		//console.log(this.getInputValue(rules[i].fieldname));
	}
	if(pass){
		this.passDepend(question_id);
	}else{
		this.failDepend(question_id);
	}
}

/**
* Test one specific rule
* @param object rule Rule object to test
* @return bool True if rule passes, False otherwise
*/
Dependencies.prototype.testRule = function(rule){
	var value = this.getInputValue(rule.fieldname);
	var pass = true;
	var val = rule.value.replace("*", ".*");
	switch(rule.operator){
		case "!=":
			//test !=			
			var res = value.match(val);
			if(res) pass = false;
			break;
		case "=":
			//test =
			var res = value.match(val);
			if(!res || res.length <= 0) pass = false;
			break;
		case "<=":
			//test <=
			pass = (value <= val);
			break;
		case "<":
			//test <
			pass = (value < val);
			break;
		case ">=":
			//test >=
			pass = (value >= val);
			break;
		case ">":
			//test >
			pass = (value > val);
			break;
	}
	return pass;
}

/**
* Grabs input value/s as a comma-separated string given an input's name
* @param string name Input element name attribute
* @return string Values as comma-separated string if more than one value
*/
Dependencies.prototype.getInputValue = function(name){
	name = name.replace('[', '\\[').replace(']', '\\]');
	var inputs = $("#"+this.form_id+" [name='"+name+"']").toArray();
	var type = $(inputs[0]).attr('type');
	var values = [];
	if(type == 'checkbox' || type == 'radio'){
		inputs = $("#"+this.form_id+" [name='"+name+"']:checked").toArray();
	}	
	for(var i=0 in inputs){
		values.push($(inputs[i]).val());
	}
	return values.join();
}

Dependencies.prototype.passDepend = function(question_id){
	$('#'+question_id).removeClass('dependhidden').addClass('dependvisible');
	var children = this.getChildInputs(question_id);
	var hidden_questions = $('#dependhiddenquestions_'+this.form_id).val().replace(question_id+',', '').replace(question_id, '');
	$('#dependhiddenquestions_'+this.form_id).val(hidden_questions);
	var hidden_inputs = $('#dependhiddeninputs_'+this.form_id).val().replace(children+',', '').replace(children, '');
	$('#dependhiddeninputs_'+this.form_id).val(hidden_inputs);	
}

Dependencies.prototype.failDepend = function(question_id){
	$('#'+question_id).removeClass('dependvisible').addClass('dependhidden');
	var children = this.getChildInputs(question_id);
	
	var hidden_questions = $('#dependhiddenquestions_'+this.form_id).val().replace(question_id+',', '').replace(question_id, '');
	if(hidden_questions.indexOf(',') !== -1){
		hidden_questions = hidden_questions.split(',');
	}	
	if(hidden_questions.length){
		hidden_questions.push(question_id);
		hidden_questions = hidden_questions.join();
	}else{
		hidden_questions = question_id;
	}
	$('#dependhiddenquestions_'+this.form_id).val(hidden_questions);

	var hidden_inputs = $('#dependhiddeninputs_'+this.form_id).val().replace(children+',', '').replace(children, '');
	if(hidden_inputs.indexOf(',') !== -1){
		hidden_inputs = hidden_inputs.split(',');
	}
	if(hidden_inputs.length){
		hidden_inputs.push(children);
		hidden_inputs = hidden_inputs.join();
	}else{
		hidden_inputs = children;
	}
	$('#dependhiddeninputs_'+this.form_id).val(hidden_inputs);
}

/**
* Gets the names of all the inputs in a question
* @param string question_id
*/
Dependencies.prototype.getChildInputs = function(question_id){
	var names = [];
	var inputs = $('#'+question_id+' input').toArray();
	for(i in inputs){
		var name = $(inputs[i]).attr('name');
		if(names.indexOf(name) === -1){
			names.push(name);
		}
	}
	var inputs = $('#'+question_id+' textarea').toArray();
	for(i in inputs){
		var name = $(inputs[i]).attr('name');
		if(names.indexOf(name) === -1){
			names.push(name);
		}
	}
	var inputs = $('#'+question_id+' select').toArray();
	for(i in inputs){
		var name = $(inputs[i]).attr('name');
		if(names.indexOf(name) === -1){
			names.push(name);
		}
	}
	return names.join();	
}