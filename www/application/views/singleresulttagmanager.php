<script type="text/javascript" src="<?php echo base_url()."application/views/JS/resulttagmanager.js"; ?>"></script>
<script type="text/javascript">
var resulttagintf;
$(document).ready(function() {
	resulttagintf = new ResultTagManager();
});

function addTags(){
	var result_id = '<?php echo $result->id; ?>';
	var tags = $('#addresulttagtxt').val().replace(" ", '');
	if(tags && tags.length >0){
		tags = tags.split(',');
	}
	if(tags && tags.length > 0){
		resulttagintf.addTagsToResult(tags, result_id);
		$('#addresulttagtxt').val('');
	}
} 
</script>

<div id="resulttagintf">
	<div id="resulttags">
		Tags:&nbsp;
		<?php
		if(is_array($resulttags)){
			$tagconfig = array(
				'type'=>'checkbox',
				'name'=>'resulttags[]',				
			);
			foreach($resulttags as $tag){
				$this->inputs->setConfig($tagconfig);				
				$this->inputs->setLabel($tag->tag);
				$this->inputs->setValue($tag->id);
				$this->inputs->setAttribute('class', 'resulttag');
				$this->inputs->setAttribute('data-role-tag', $tag->tag);
				$this->inputs->setSelected($tag->id);
				$this->inputs->setAttribute('onclick', "resulttagintf.toggleTagOnResult(this, '{$result->id}');");
				echo "{$this->inputs}";
			}
		}
		?>
	</div>
	<div id="resulttagsintrf_ctrl">		
		<input id="addresulttagtxt" type="text" value="" placeholder="TAG1,TAG2,TAG3"><button onclick="addTags();">Add Tags</button>
		
	</div>
</div>