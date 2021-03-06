/**
* initialize FormEditor when dom is ready
*/
$(document).ready(function() {	
	FormEditor.__init__();	
});

/**
* FormEditor handles all the interaction
* with the client for the Edit view of a form,
* including editing form config and question config
*/
var FormEditor = function(){

}

/**
* Initialize the form editor
*/
FormEditor.__init__ = function(){
	this.form_id = form_id;
	this.initSortable();
	FormEditor.form_questions_dependencies = new Dependencies('form_questions_form');
	//not needed yet...but maybe someday.
	FormEditor.form_config_dependencies = new Dependencies('form_config_form');
	//FormEditor.question_config_dependencies = new Dependencies('question_config_form');

	FormEditor.form_questions_validator = new Validation('form_questions_form');
	FormEditor.form_config_valalidator = new Validation('form_config_form');
	FormEditor.question_config_validator = new Validation('question_config_form');
	this.initModals();
}

/**
* Init the modal dialogs for 
* editing questions and the form config
*/
FormEditor.initModals = function(){
	$("#form_config_editor").dialog(
	{	
		title: 'Form Configuration',
		autoOpen: false,
		height: 700,
		width: 1000,
		modal: true,
		buttons: {
			"Save": function(){
				FormEditor.form_config_valalidator.validateForm(FormEditor.saveFormConfig);
			}
		}
	});


	$("#question_config_editor").dialog(
	{
		title: 'Question Configuration',
		autoOpen: false,
		height: 700,
		width: 1000,
		modal: true,
		buttons: {
			"Save": function(){
				FormEditor.question_config_validator.validateForm(FormEditor.saveQuestion);
			}
		}
	});
}


/**
* Init the ability to reorder questions
*/
FormEditor.initSortable = function(){
	$('#form_questions').sortable({
		stop: function(event, ui){
			FormEditor.updateQuestionOrder();
		},
		placeholder: "sortable-question-placeholder",		
		opacity: 0.75,
	});	
}

FormEditor.updateQuestionOrder = function(){
	var order = $('#form_questions').sortable('toArray');
	doAjax('questions/reorder', {'question_ids': order}, function(){}, function(){});
}

/**
* Adds a question to the current form
* @param string below_question_id Should append new question after this one in dom
*/
FormEditor.addQuestion = function(below_question_id){
	var below_question_id = below_question_id || false;
	doAjax('questions/add/'+form_id, {'form_id': FormEditor.form_id, 'below_question_id': below_question_id}, function(resp){ FormEditor.addQuestionCallback(resp, below_question_id); }, function(){});
}

/**
* Finishes adding the question, renders edit mode
*/
FormEditor.addQuestionCallback = function(resp, below_question_id){
	FormEditor.openEditQuestion();
	FormEditor.current_question_config = resp.question.id;
	//alert('below_question_id: '+below_question_id);
	//put the edit view in the right place

	if(below_question_id){
		$('#'+below_question_id).after(resp.html.question_edit);
	}else{
		$('#form_questions').prepend(resp.html.question_edit);
	}
	
	//render question into form in correct position	
	$('#question_type_contain').html(resp.html.question_type);
	$('#question_config_contain').html(resp.html.question_config);
	FormEditor.question_config_validator.__init__();
	
	//show modal for question edit
	
}

FormEditor.editQuestion = function(question_id){
	FormEditor.current_question_config = question_id;
	//get the question config view	
 	doAjax('questions/edit/'+question_id, {}, FormEditor.editQuestionCallback, function(){});
}

/**
* Finishes editing the question after getting config, renders edit mode
*/
FormEditor.editQuestionCallback = function(resp){
	//populate question config edit div	
	$('#question_type_contain').html(resp.html.question_type);
	$('#question_config_contain').html(resp.html.question_config);
	FormEditor.question_config_validator.__init__();
	//show modal for question edit
	FormEditor.openEditQuestion();
}

/**
* Opens the modal dialog for editing a question
*/
FormEditor.openEditQuestion = function(){
	//todo: when modal is open, bug them about leaving the page!	
	$("#question_config_editor").dialog('open');
}

/**
* Closes the modal dialog for editing a question 
*/
FormEditor.closeEditQuestion = function(){
	//todo: when modal is open, bug them about leaving the page!	
	$("#question_config_editor").dialog('close');
}

FormEditor.saveQuestion = function(do_save){
	var do_save = do_save || false;	
	var id = FormEditor.current_question_config;
	if(do_save){
		var ans = FormEditor.parseSerializedForm($('#question_config_form').serializeArray());		
		doAjax('questions/savequestion/'+id, ans, FormEditor.saveQuestionCallback, FormEditor.saveQuestionCallback);
	}
}

FormEditor.saveQuestionCallback = function(resp){
	if(resp.status == 'success'){
		var question_id = FormEditor.current_question_config;		
		FormEditor.closeEditQuestion();
	}
	$('#'+question_id).replaceWith(resp.html);
	FormEditor.form_questions_dependencies.__init__();
	FormEditor.form_questions_validator.__init__();
}

FormEditor.duplicateQuestion = function(question_id){	
	//tell server to duplicate
 	doAjax('questions/duplicate/'+question_id, {}, FormEditor.duplicateQuestionCallback, function(){});
}

FormEditor.duplicateQuestionCallback = function(resp){
	if(resp && resp.status == 'success'){
		$("#"+resp.orig_question_id).after(resp.html);
		FormEditor.updateQuestionOrder();
		FormEditor.form_questions_dependencies.__init__();
		FormEditor.form_questions_validator.__init__();
	}
}

/**
* Opens the modal dialog for editing a form (config)
* @param string form_id
*/
FormEditor.openEditForm = function(){
	//todo: when modal is open, bug them about leaving the page!
	//process for opening up a form modal 
	$("#form_config_editor").dialog('open');
}

/**
* Closes the modal dialog for editing a form config 
*/
FormEditor.closeEditForm = function(){
	//todo: when modal is open, bug them about leaving the page!	
	$("#form_config_editor").dialog('close');
}

FormEditor.saveFormConfig = function(do_save){
	var do_save = do_save || false;		
	if(do_save){
		var ans = FormEditor.parseSerializedForm($('#form_config_form').serializeArray());
		doAjax('forms/saveconfig/'+FormEditor.form_id, ans, FormEditor.saveFormConfigCallback, FormEditor.saveFormConfigCallback);
	}
}

FormEditor.saveFormConfigCallback = function(resp){
	if(resp && resp.status == 'success'){
		$('#form_title').html(resp.form.title+"&nbsp;("+resp.form.name+")");
		FormEditor.closeEditForm();		
		$('#form_config_contain').html(resp.html.form_config_form);
	}	
}

FormEditor.loadElementConfig = function(question_id, type){
	doAjax('questions/loadconfigview/'+question_id+"/"+type, {}, FormEditor.loadElementConfigCallback, function(){});
}

FormEditor.loadElementConfigCallback = function(resp){	
	$('#question_config_contain').html(resp.html.question_config);
	FormEditor.question_config_validator.__init__();
}


FormEditor.deleteQuestion = function(question_id){
	var conf = confirm('Are you sure you want to delete this question?!\nIt will not be able to be recovered!');
	if(conf){
		doAjax('questions/delete/'+question_id, {}, FormEditor.deleteQuestionCallback, function(){});
	}
}

FormEditor.deleteQuestionCallback = function(resp){
	if(resp && resp.status == 'success'){
		$('#'+resp.question_id).hide();
	}
}

/**
* Parses jquery's serializeArray() output into a data structure
* that we can post to php and have it interpreted as a traditional
* form post
* @param object form_answers output of jquery's serializeArray() on a form
* @return object Resembling a normal form's $_POST representation
*/
FormEditor.parseSerializedForm = function(form_answers){
	//moved function body to core.js as it's needed other places
	return parseSerializedForm(form_answers);
}