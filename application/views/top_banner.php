<?php
	if(!$banner_menu) $banner_menu = array();
	$default_menu = array(anchor('/', 'Dashboard'));
	if($this->authorization->is('superadmin'))	$default_menu[]=anchor('admin', 'Admin Dashboard');
	$banner_menu = array_merge($default_menu, $banner_menu);
	if(is_array($banner_menu)){
		$banner_menu_html = '<div id="top_banner_menu_contain" onmouseover="$(\'.banner_menu\').show();" onmouseout="$(\'.banner_menu\').hide();">
		<div id="menu_indicator">Menu</div>
		<ul class="banner_menu">';
		foreach($banner_menu as $i){
			$banner_menu_html.="<li>$i</li>";
		}
		$banner_menu_html.='</ul></div>';
	}
	if(empty($banner_text)) $banner_text = '&nbsp;';
	echo "<div class=\"top_banner\">$banner_text$banner_menu_html</div>";
?>