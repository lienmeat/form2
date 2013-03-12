<?php
  if(uri_string() == "/" or uri_string() == "")
    $links[]=anchor(site_url(), "Dashboard", array('style'=>'font-weight: bold;'));
  else
    $links[]=anchor(site_url(), "Dashboard");
?>

<div class="menu" style="text-align: right;"><?php echo implode("<br />", $links); ?></div>

<?php if(ENVIRONMENT == 'development') echo "<h3 style='color: red;'>".APP_NAME."_DEV_VERSION</h3>"; ?>
