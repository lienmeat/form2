<?php
  if(uri_string() == "/" or uri_string() == "")
    $links[]=anchor(site_url(), "Dashboard", array('style'=>'font-weight: bold;'));
  else
    $links[]=anchor(site_url(), "Dashboard");
/*
  if(uri_string() != "/payment" and uri_string() != "/payment/")
    $links[]=anchor("/payment", "Make a Payment");
  if(uri_string() != "/payment/donation" and uri_string() != "/payment/donation/")
    $links[]=anchor("/payment/donation", "Give a Gift");
*/    
  if($this->auth->username()) $links[]="<br /><a href='".current_url()."?action=logout'>Sign Out</a>";
?>

<div class="menu" style="text-align: right;"><?php echo implode("<br />", $links); ?></div>

<?php if(FORMIT2_DEV_VERSION) echo "<h3 style='color: red;'>FORMIT2_DEV_VERSION</h3>"; ?>
