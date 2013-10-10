<?php
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/jquery.eventually.js\"></script>";
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/dependencies.js\"></script>";
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/validation.js\"></script>";
echo "<script type=\"text/javascript\" src=\"".base_url()."/application/views/JS/tiny_mce/jquery.tinymce.js\"></script>";
$this->load->view('JS/jquery-ui');
?>
<script>
$(document).tinymce({
	script_url : '<?php echo base_url();?>/application/views/JS/tiny_mce/tiny_mce.js',
	mode : "none",
	theme : "advanced",			
	plugins : "autosave"
});
</script>