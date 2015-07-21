<html>
	<body>
	<form method="POST" action="">
		<ul>
	<?php

	$mail_path = '/var/log/mail/';
	if(isset($_POST['del'])) {
		foreach($_POST['del'] as $file) {
			//delete file from mail log
			unlink($mail_path.$file);
		}
	}

	$dir = new DirectoryIterator($mail_path);
	$mails = [];
	foreach ($dir as $fileinfo) {
	    if (!$fileinfo->isDot()) {
	    	$mails[$fileinfo->getFileName()] = file_get_contents($fileinfo->getPathname());
	    }
	}
	ksort($mails);
	foreach($mails as $file=>$m) {
		echo "<li><input type=\"checkbox\" name=\"del[]\" value=\"".$file."\" />".$file.":<pre>".$m."</pre></li>";
	}

	?>
		</ul>
		<input type="submit" name="delete" value="Delete Selected Mail" />
	</form>
</body>
</html>