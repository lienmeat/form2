<?php
$menu = array(
	anchor('admin/forms', 'Form Admin'),
	anchor('admin/roles', 'Role Admin'),		
);
$this->load->view('header', array('title'=>$form->name, 'banner_menu'=>$menu));	

echo anchor('admin/forms', 'Form Admin')."<br />";
echo anchor('admin/roles', 'Role Admin');

$this->load->view('footer');
?>