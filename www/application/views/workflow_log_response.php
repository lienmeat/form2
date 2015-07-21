<div class="workflow_log_response">
	<?php $resp = json_decode($log->response); ?>
	<span>Response: <strong><?php echo $resp->decision; ?></strong><br />
	Comments: <strong><?php echo $resp->comments; ?></strong></span><br />
	<span>by <?php echo $log->username; ?> at <?php echo $log->timestamp; ?></span>
</div>