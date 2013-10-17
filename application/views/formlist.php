<?php
if($forms){
	$thead = "<tr><th>Title</th><th>Actions</th><th>Published?</th><th>ID</th><th>Created</th></tr>";
	$lastform = null;
	$drawer = "";
	echo "<ul class=\"formslist\">";	
	foreach ($forms as $form) {
		if($form->name != $lastform){
			if($lastform){
				//print last item, it's done...
				echo $li.$drawer."</tbody></table></div></li>";
			}
			$lastform = $form->name;
			$li = "
			<li>
				<div class=\"formlistheading\" onclick=\"toggleFLDrawer(this);\"><a><div class=\"formlistheading_details\">{$form->name}</div><div class=\"formlistheading_details\">{$form->title}</div><div class=\"formlistheading_details\">{$form->creator}</div></a></div>
			";
			$drawer = "<div id=\"drawer_{$form->name}\" class=\"formdetailsdrawer\"><table class=\"formlisttable\"><tbody>$thead";			
		}

		//add contents to drawer
		if($form->published and $form->published != "0000-00-00 00:00:00"){
			$published = $form->published;			
		}else{
			$published = "";
		}
		$drawer.="<tr><td>$form->title</td><td>".anchor('forms/viewid/'.$form->id, 'View').' '.anchor('forms/edit/'.$form->id, 'Edit').' '.anchor('forms/results/'.$form->name, 'Results').' '.anchor('forms/manage/'.$form->name, 'Manage')."</td><td>$published</td><td>$form->id</td><td>$form->created</td></tr>";
	}
	echo $li.$drawer."</tbody></table></div></li>";	
	echo "</ul>";	
}
?>