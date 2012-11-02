//include_js.php shoves path information into the dom so we can use it in JS
function Paths(){
	this.base;
	this.basePath = function(){ return this.base; };
	this.current;
	this.uri;
}

/**
* Access system path variables with paths globally!
*/
var paths = new Paths();

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
