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
</style>";
//echo "<pre>".print_r($forms, true).print_r($formresults, true)."</pre>";

echo '<div class="disp_table">';
$count = 0;
foreach($formresults as $fr){
	if($count%5 == 0){
		if($count !== 0) echo "</div>";
		echo '<div class="disp_table_row">';
	}
	if($fr->submitter) $res_name = $fr->submitter;
	else $res_name = $fr->timestamp;
	$title =
	'title="Submitted: '.$fr->timestamp.'"';	
	echo '<div class="disp_table_cell"><input class="formresult_chk" type="checkbox" value="'.$fr->id.'">'.anchor('results/view/'.$fr->id,$res_name, $title).'</div>';
	$count++;
}
if($count%5) echo '</div>';
echo '</div>';

$this->load->view('footer');
?>