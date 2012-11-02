<?php
require_once('templates.php');
/* input:       $aSiteHeader=Assoc Array, as follows:
 *              $sTitle;           //page title. String.
 *              $stylesheet;       //url location of stylesheet. String.
 *              $sidebar;          //url location of background grapic. "none" for no sidebar. String.
 *              $image_title;      //image-based title located in top-left corner. "blank" will place blank title. no value will place default section title. String.
 *              $random_image;     //1=random image: 2=static image :3=no image at all. Integer.
 *              $random_image_dir; //the directory where random images are stored. only image for random use should be here. gif,jgp,png are supported. String.
 *              $nav_tool;         //a directory-based dynamic navigation system. Boolean. (not yet implemented)
 *              $custom_layout;    //gives full width of page (600 pixels) for layout. Boolean.
 *              $sidebar_info;     //if $custom_layout is FALSE, this can be assigned information to be displayed in the sidebar of the page. String.
 *              $secure;           //if $secure is set true, all content on page will be loaded securely and all links will reference secure pages. Boolean.
 *              $sHeadCode         //extra data passed just before </head>http://www.
 */

//header config
$aSiteHeader = array(
'sTitle'=>$title,
'secure'=>'true', //we MIGHT need to make this variable somehow! (cause formit does this)
'bannerText'=>'FormIt2',
'bannerRight'=>$this->load->view('menu', array(),true),
);

acadHeader($aSiteHeader); //WWU site header init

//load local header requirements
$this->load->view('include_css'); //load up core.css
$this->load->view('include_js'); //load up core.js (or other core js libs)
if($banner_text) $this->load->view('top_banner', array('banner_text'=>$banner_text));
?>
