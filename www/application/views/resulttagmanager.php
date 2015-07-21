<?php
if(!empty($resulttags)){ ?>
<script type="text/javascript" src="<?php echo base_url()."application/views/JS/resulttagmanager.js"; ?>"></script>
<script type="text/javascript">
var resulttagintf = new ResultTagManager();
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
				echo "<div class=\"input_multiple\">{$this->inputs}</div>";
			}
		}
		?>
	</div>
	<div id="resulttagsintrf_ctrl">
		<?php
			echo "Show results with 
			<button onclick=\"resulttagintf.filterResults('ANY', resulttagintf.getSelectedTags());\">ANY</button>&nbsp;
			<button onclick=\"resulttagintf.filterResults('ALL', resulttagintf.getSelectedTags());\">ALL</button>&nbsp;
			<button onclick=\"resulttagintf.filterResults('NONE', resulttagintf.getSelectedTags());\">NO</button> checked tag(s).&nbsp;";

			echo "&nbsp;<button onclick=\"resulttagintf.addTagsToResults(resulttagintf.getSelectedTagIDs(), getCheckedResultIds());\">Add checked tags to checked results</button>"
		?>
	</div>
</div>
<?php } ?>