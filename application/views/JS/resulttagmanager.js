function ResultTagManager(){

	if(!window.resulttagmanagers){
		window.resulttagmanagers = [];
	}
	this.handle = (window.resulttagmanagers.push(this) - 1);
}

ResultTagManager.prototype.getHandle = function(){
	return this.handle;
}

/**
* Show/hide results based on selected tags
* @param string how What method of filtering to do on results giving what tags (ANY, ALL, NONE)
* @param array tags Tag objects to filter by
*/
ResultTagManager.prototype.filterResults = function(how, tags){
	var how = how || 'ANY';
	var tags = tags || [];
	var results = this.getResultsOnPage() || [];	

	switch(how){
		case "ANY":			
			for(var i=0 in results){
				var show = false;
				var tmptags = $(results[i]).attr('data-role-tags').split(',') || [];
				for(var j=0 in tags){
					if(tmptags.indexOf($(tags[j]).val()) >= 0){
						show = true;
						break;
					}
				}
				if(show){
					this.showResult(results[i]);
				}else{
					this.hideResult(results[i]);
				}
			}			
			break;

		case "ALL":
			for(var i=0 in results){
				var show = true;
				var tmptags = $(results[i]).attr('data-role-tags').split(',') || [];
				for(var j=0 in tags){
					if(tmptags.indexOf($(tags[j]).val()) < 0){
						show = false;
						break;
					}
				}
				if(show){
					this.showResult(results[i]);
				}else{
					this.hideResult(results[i]);
				}
			}
			break;

		case "NONE":
			for(var i=0 in results){
				var show = true;
				var tmptags = $(results[i]).attr('data-role-tags').split(',') || [];
				for(var j=0 in tags){
					if(tmptags.indexOf($(tags[j]).val()) >= 0){
						show = false;
						break;
					}
				}
				if(show){
					this.showResult(results[i]);
				}else{
					this.hideResult(results[i]);
				}
			}
			break;
	}
}

/**
* Grabs all the results (elements) on the page
* @return array Elements/divs of results
*/
ResultTagManager.prototype.getResultsOnPage = function(){
	return $('.formresult').get();
}

/**
* Grabs all the selected resulttags on the page
* @return array ResultTag checkbox elements
*/
ResultTagManager.prototype.getSelectedTags = function(){
	return $('.resulttag:checked').get();
}

ResultTagManager.prototype.getSelectedTagIDs = function(){
	var tagschecks = [];
	tagchecks = this.getSelectedTags();
	var ids = [];
	if(tagchecks){
		for(var i=0 in tagchecks){
			ids.push($(tagchecks[i]).val());
		}
	}
	return ids;
}

/**
* Shows a result given it's element
*/
ResultTagManager.prototype.showResult = function(resultelem){
	//$(resultelem).removeClass('hide');
	$(resultelem).show();
}

/**
* Hides a result, given it's element
*/
ResultTagManager.prototype.hideResult = function(resultelem){
	//$(resultelem).removeClass('show');
	$(resultelem).hide();	
}

ResultTagManager.prototype.addTagsToResults = function(tag_ids, result_ids){
	doAjax('resulttags/addResultTagsToResults', {'resulttags': tag_ids, 'results': result_ids}, function(){ window.location.reload(); });
}

ResultTagManager.prototype.addTagsToResult = function(tags, result_id){
	var handle = this.getHandle();	
	doAjax('resulttags/addResultTagsToResult', {'resulttags': tags, 'result': result_id}, function(resp){ window.resulttagmanagers[handle].addTagsDone(resp); });
}


/**
* Toggle a tag's existence on a result depending on whether a tag's checkbox is checked or not
* @param domelement checktox_elem The checkbox's dom element
* @param string result_id The result to bind/unbind to/from
*/
ResultTagManager.prototype.toggleTagOnResult = function(checkbox_elem, result_id){
	var tag = ""
	if($(checkbox_elem).prop('checked')){
		this.addTagToResult($(checkbox_elem).attr('data-role-tag'), result_id);
	}else{
		this.removeTagFromResult($(checkbox_elem).val(), result_id);
	}
}


/**
* Add a tag, existing or not, to a result
* @param string tag Tag to add
* @param string result_id Result to add it to
*/
ResultTagManager.prototype.addTagToResult = function(tag, result_id){
	var handle = this.getHandle();
	doAjax('resulttags/addResultTagToResultByTag', {'resulttagname': tag, 'result': result_id}, function(resp){ window.resulttagmanagers[handle].addTagDone(resp); });
}

ResultTagManager.prototype.addTagDone = function(resp){
	if(resp.status && resp.status == 'success'){
		this.renderTagOnResult(resp.tag, resp.result);
	}
}

ResultTagManager.prototype.addTagsDone = function(resp){
	if(resp.status && resp.status == 'success'){
		for(var i = 0 in resp.tags){
			this.renderTagOnResult(resp.tags[i], resp.result);
		}
	}
}

ResultTagManager.prototype.removeTagFromResult = function(tag_id, result_id){
	var handle = this.getHandle();
	doAjax('resulttags/removeResultTagFromResult', {'resulttag_id': tag_id, 'result': result_id}, function(resp){ window.resulttagmanagers[handle].removeTagDone(resp); });
}

ResultTagManager.prototype.removeTagDone = function(resp){	
	if(resp.status && resp.status == 'success'){
		//$().append();
	}
}

ResultTagManager.prototype.renderTagOnResult = function(tag, result_id){
	var handle = this.getHandle();
	var elem = $('.resulttag[value='+tag.id+']').get();
	if(elem.length <= 0){
		var html = '<div class="input_multiple"><input name="resulttags[]" type="checkbox" value="'+tag.id+'" class="resulttag" data-role-tag="'+tag.tag+'" onclick="window.resulttagmanagers['+handle+'].toggleTagOnResult(this, \''+result_id+'\');" checked="checked">'+tag.tag+'</div>';
		$('#resulttags').append(html);
	}
}