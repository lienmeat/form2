<?php
	$menu = array(
		anchor('forms/results/'.$form->name, 'Form Results'),
		anchor('forms/manage/'.$form->name, 'Manage Form'),
		anchor('forms/view/'.$form->name, 'View Form'),
		anchor('forms/edit/'.$form->name, 'Edit Form'),
		
	);
	$this->load->view('header', array('title'=>"F2 Result ".$form->result->id, 'banner_menu'=>$menu, 'embedded'=>$embedded_form));
	//$this->load->view('JS/dependencies.js');
?>

<div class="form_result_info">
	<?php if($topmessage) echo '<h1 class="resultmessage">'.$topmessage.'</h1>'; ?>
	<div class="result_link">You may share this completed form, by using this link: <a href="<?php echo site_url('results/view/'.$form->result->id); ?>"><?php echo site_url('results/view/'.$form->result->id); ?></a></div>
	<div class="advanced_info_contain">
		<a href="javascript:void(0);" onclick="toggleVisibility('#advanced_result_info');">Verbose Result Info</a>
		<div id="advanced_result_info" class="advanced_info" style="display: none;">
			<?php
				echo "<ul>";
				$post_out = '';
				foreach($form->result as $f=>$v){
					if($f == 'post'){
						echo "<li><label><a href=\"javascript:void(0);\" onclick=\"toggleVisibility('#post_data');\">Raw Post Data:</a>&nbsp;</label><div id=\"post_data\" style=\"display: none;\"><pre>".print_r($v, true)."</pre></div></li>";
					}else{
						echo "<li><label>$f:&nbsp;</label>$v</li>";
					}				
				}
				echo "</ul>";
			?>
		</div>		
	</div>
</div>

<!-- form header common to all forms -->
<div class="form_title_contain edit_mode">
	<h2 id="form_title"><?php echo $form->title." ($form->name)"; ?></h2>
</div>

<ul class="form_contain result_mode">
<?php
$hidden = explode(',', $form->result->post->dependhiddenquestions);
if(!is_array($hidden)) $hidden = array();
//probably shouldn't have called questions questions,
//because there is other crap in forms than questions....
if($form->questions){
	foreach($form->questions as $question){
		if(!in_array($question->id, $hidden)){
			$this->load->view('question/result_question', array('question'=>$question, 'formresult'=>$form->result));
		}
	}
}
?>
</ul>



<!-- form footer -->
<div class="form_footer">
	<!-- Maybe a standard submit footer or something...idk -->
</div>
<?php
	$this->load->view('footer', array('embedded'=>$embedded_form));
?>