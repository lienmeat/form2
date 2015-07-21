<?php
$menu_items = array(
	anchor('forms/manage/'.$forms[0]->name, 'Manage Form'),
	anchor('forms/view/'.$forms[0]->name, 'View Form'),
	anchor('forms/edit/'.$forms[0]->name, 'Edit Form'),
);

$this->load->view('header', array(
	'title'=>$forms[0]->name,
	'banner_text'=>"Submissions for {$forms[0]->title} ({$forms[0]->name})",
	'banner_menu'=>$menu_items,
));

echo "<style>
.disp_table {
	display: table;
	width: 100%;
}

.disp_table_row {
	display: table-row;	
}

.disp_table_row:nth-child(even) {
	background: rgba(0,0,0,0.20);
}

.disp_table_row:nth-child(odd) {
	background: rgba(255,255,255,0.20);
}

.disp_table_cell {
	display: table-cell;
	padding-left: 5px;
}

#result_manage_contain {
	padding: 0.4em;
	border: 1px dotted rgba(0, 0, 0, 0.50);
	background: rgba(0, 0, 0, 0.10);
}

#result_manage_contain div.result_manage_section {
	border-bottom: 1px solid rgba(0, 0, 0, 0.50);
}

#result_manage_contain div.result_manage_section:last-child {
	border-bottom: none;
}
</style>";

echo "
<script>
function toggleResultTimestamps(button_elem){
	if($(button_elem).attr('data-role-status') == 'off'){
		$(button_elem).attr('data-role-status', 'on');
		$('.formresult_timestamp').show();
	}else{
		$(button_elem).attr('data-role-status', 'off');
		$('.formresult_timestamp').hide();
	}
}

function toggleResultSelections(checkbox_elem){
	if($(checkbox_elem).prop('checked')){		
		$('.formresult_chk:visible').prop('checked', true);
	}else{
		$('.formresult_chk:visible').prop('checked', false);
	}	
}

$(document).ready(function(){
	$('.formresult_chk').on('click', function(event){
		if(!$(event.target).prop('checked')){
			$('.formresultselectionstoggle').prop('checked', false);
		}else{
			var frs = $('.formresult_chk').get();
			var check = true;
			for(var i=0 in frs){
				if(!$(frs[i]).prop('checked')){
					check = false;
				}
			}			
			if(check){
				$('.formresultselectionstoggle').prop('checked', true);
			}else{
				$('.formresultselectionstoggle').prop('checked', false);				
			}
		}
	});
});
</script>
";

//gui for result management
echo '
<div id="result_manage_contain">
	<div id="result_tagmanage_contain" class="result_manage_section">'
		.$this->load->view('resulttagmanager', array('form'=>$forms[0]->name, 'resulttags'=>$resulttags), true).
	'</div>
	<div id="" class="result_manage_section">
		<input class="formresultselectionstoggle" type="checkbox" onclick="toggleResultSelections(this);">Select/Unselect All Results&nbsp;&nbsp;<button onclick="toggleResultTimestamps(this);" data-role-status="off">Show/Hide Time Stamps</button>
	</div>
</div>';

echo '<div class="">';
$count = 0;
foreach($formresults as $fr){
	$tags = '';
	$tagids = array();
	if($fr->resulttags){		
		foreach ($fr->resulttags as $tag) {
			$tagids[] = $tag->id;
		}
		$tags = implode(',', $tagids);
	}
	if($count%5 == 0){
		/*
		if($count !== 0) echo "</div>";
		echo '<div class="">';
		*/
	}
	if($fr->submitter) $res_name = $fr->submitter;
	else $res_name = $fr->timestamp;
	$title =
	'title="Submitted: '.$fr->timestamp.'"';	
	echo '<div id="formresult_'.$fr->id.'" class="formresult" data-role-tags="'.$tags.'">
	<div><input class="formresult_chk" type="checkbox" value="'.$fr->id.'">'.anchor('results/view/'.$fr->id,$res_name, $title).'</div>
	<div class="formresult_timestamp hide">'.$fr->timestamp.'</div>
	</div>';
	$count++;
}
if($count%5) echo '</div>';
echo '</div>';

$this->load->view('footer');
?>