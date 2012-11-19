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
	this.initModals();
	this.initSortable();
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
	});

	$("#question_config_editor").dialog(
	{
		title: 'Question Configuration',
		autoOpen: false,
		height: 700,
		width: 1000,
		modal: true,
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
FormEditor.addQuestion = function(below){
	var below_question_id = below_question_id || false;
	doAjax('questions/add', {'form_id': this.form_id}, function(resp){ FormEditor.addQuestionCallback(resp, below_question_id); }, function(){});
}

/**
* Finishes adding the question, renders edit mode
*/
FormEditor.addQuestionCallback = function(resp, below_question_id){
	//render question into form in correct position
	
	//populate question config edit div

	//show modal for question edit
	FormEditor.openEditQuestion();
}

FormEditor.editQuestion = function(question_id){
	//get the question config view	
 	doAjax('questions/edit/'+question_id, {}, function(resp){ FormEditor.editQuestionCallback(resp, question_id); }, function(){});
}

/**
* Finishes editing the question after getting config, renders edit mode
*/
FormEditor.editQuestionCallback = function(resp, question_id){
	//populate question config edit div	
	$('#question_config_editor').html(resp.html.question_config);


	//show modal for question edit
	FormEditor.openEditQuestion();
}

/**
* Opens the modal dialog for editing a question
* @param string question_id
*/
FormEditor.openEditQuestion = function(){
	//todo: when modal is open, bug them about leaving the page!
	//process for opening up a question modal
	$("#question_config_editor").dialog('open');
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