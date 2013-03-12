//include_js.php shoves path information into the dom so we can use it in JS
function Paths(){
	this.base;
	this.basePath = function(){ return this.base; };
	this.current;
	this.uri;
  //var site_url = function(path){ return this.base+path; };
}

Paths.prototype.site_url = function(path){ return this.base+path; }

/**
* Access system path variables with paths globally!
*/
var paths = new Paths();


function doAjax(path, data, callBackSuccess, callBackFail){
  var path = path || '';
  var data = data || {};
  var callBackSuccess = callBackSuccess || defaultAjaxSuccessCallback;
  var callBackFail = callBackFail || defaultAjaxFailCallback;

  var defaultAjaxSuccessCallback = function(responce, status, error){ alert('NO CALLBACK FOR AJAX SUCCESS METHOD WAS DEFINED!'); };
  var defaultAjaxFailCallback = function(responce, status, error){ alert('Unable to talk to server via ajax!\nerror: '+error); };

  $.ajax({
    url: paths.site_url(path),
    data: data,
    dataType: 'json',
    type: 'POST',
    success: callBackSuccess,
    error: callBackFail,
  });
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

function toggleVisibility(selector){
  if($(selector).css('display') == 'none'){
    $(selector).show();
  }else{
    $(selector).hide();
  }
}