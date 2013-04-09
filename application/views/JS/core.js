//include_js.php shoves path information into the dom so we can use it in JS
function Paths(){
	this.base;
	this.basePath = function(){ return this.base; };
	this.current;
	this.uri;
  //var site_url = function(path){ return this.base+path; };
}
/**
* Get a CI style path using php inserted path data
*/
Paths.prototype.site_url = function(path){ return this.base+path; }

/**
* Access system path variables with paths globally!
*/
var paths = new Paths();

/**
* Run an ajax request
* @param string path CI style path, completed by paths object (forms/view/1)
* @param object data Object to send as post to path
* @param function callBackSuccess callback function to run on success
* @param function callBackFail callback function to run on fail
*/
function doAjax(path, data, callBackSuccess, callBackFail){
  var path = path || '';
  var data = data || {};

  var defaultAjaxSuccessCallback = function(responce, status, error){ alert('NO CALLBACK FOR AJAX SUCCESS METHOD WAS DEFINED!'); };
  var defaultAjaxFailCallback = function(responce, status, error){ alert('Unable to talk to server via ajax!\nerror: '+error); };

  var callBackSuccess = callBackSuccess || defaultAjaxSuccessCallback;
  var callBackFail = callBackFail || defaultAjaxFailCallback;

  $.ajax({
    url: paths.site_url(path),
    data: data,
    dataType: 'json',
    type: 'POST',
    success: function(responce, status, error){ ajaxDone(responce, status, error, callBackSuccess); },
    error: function(responce, status, error){ ajaxDone(responce, status, error, callBackFail); },
  });
}


function ajaxDone(responce, status, error, callback){  
  if(responce && responce.loggedout){
    notifyLoggedOut();
  }else{
    callback(responce);
  }
}

function notifyLoggedOut(){
  alert('You have been logged out it seems!');
}

/**
* Is input an integer?
*/
function is_int(input){
	return typeof(input)=='number'&&parseInt(input)==input;
}

/**
* When a submit button is clicked, change value of button and disable it (only click once!)
* @param object button Button element (use onclick="submitClicked(this, form_id);")
* @param string form_id The id (#) of the form the button belongs to
*/
function submitClicked(button, form_id){
  var button = button || false;
  var form_id = form_id || false;
  if(button){
    button.value = "...WORKING...";
    button.disabled = true;
  }
  if(form_id){
    $('#'+form_id).submit();
  }
}


function testAjax(){
  doAjax('forms/add', {something: 'silly'}, function(resp){ alert(resp); });
}

/**
* Alternately hide or show selected elements
* @param string selector Jquery selector
*/
function toggleVisibility(selector){
  if($(selector).css('display') == 'none'){
    $(selector).show();
  }else{
    $(selector).hide();
  }
}

/**
* Alias of toggleVisibility
*/
function toggleView(selector){  
  toggleVisibility(selector);
}

/**
* Get/show help for f2help row
* @param integer id ID of help to show
*/
function f2Help(id){
  if($('#f2help_contain_'+id).hasClass('open')){    
    $('#f2help_contain_'+id).removeClass('open');
  }else{    
    var html = $('#f2help_'+id).html();    
    if(html.length == 0){
      doAjax('helps/getHelpTXT/'+id, {}, f2HelpDone);
    }else{
      $('#f2help_contain_'+id).addClass('open');
    }
  }
}

/**
* Ajax callback method for f2Help if needed
*/
function f2HelpDone(resp){
  if(resp && resp.help){
    $('#f2help_'+resp.help.id).html(resp.help.help);
    $('#f2help_contain_'+resp.help.id).addClass('open');
  }
}

/**
* Parses jquery's serializeArray() output into a data structure
* that we can post to php and have it interpreted as a traditional
* form post
* @param object form_answers output of jquery's serializeArray() on a form
* @return object Resembling a normal form's $_POST representation
*/
function parseSerializedForm(form_answers){
  var form_answers = form_answers || {};
  var ans = {}; 
  for(i in form_answers){
    if(form_answers[i].name.indexOf('[]') >= 0){
      form_answers[i].name = form_answers[i].name.replace('[]', '');
      if(!ans[form_answers[i].name]){
        ans[form_answers[i].name] = [];
      }
      ans[form_answers[i].name].push(form_answers[i].value);
    }else{
      ans[form_answers[i].name] = form_answers[i].value;
    }
  }
  return ans;
}


/**
* Save a form as a draft
* @param string form_id
*/
function saveDraft(form_id){
  var formdata = parseSerializedForm($('#'+form_id).serializeArray());
  doAjax('forms/saveDraft/'+form_id, {formdata: formdata}, saveDraftDone);
}

/**
* Callback to announce link to draft
*/
function saveDraftDone(resp){
  if(resp && resp.url){
    alert("You can continue filling out this form at a later date by going to the following address:\n"+resp.url);
  }
}

function openRTE(textarea_id, clicked_elem){
  if(clicked_elem){
    $(clicked_elem).attr('onclick', "closeRTE('"+textarea_id+"', this);");
  }
  tinyMCE.execCommand("mceAddControl", true, textarea_id);
}

function closeRTE(textarea_id, clicked_elem){
  if(clicked_elem){
    $(clicked_elem).attr('onclick', "openRTE('"+textarea_id+"', this);");
  }
  tinyMCE.execCommand("mceRemoveControl", true, textarea_id);
}

function getCheckedResultIds(){
  var checked = $('.formresult_chk:checked').toArray();
  var ids = [];
  for(var i=0 in checked){
    ids.push($(checked[i]).val());
  }
  return ids;
}

