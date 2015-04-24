<?php 
/** 
  * Copyright: dtbaker 2012
  * Licence: Please check CodeCanyon.net for licence details. 
  * More licence clarification available here:  http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ 
  * Deploy: 6525 ae190c43422d2b2a7c6934e00e8b8686
  * Envato: 82b7b3b1-2e56-457f-aac2-ff4d410e1e54
  * Package Date: 2014-10-02 15:05:08 
  * IP Address: 203.125.187.204
  */
global $plugins;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?></title>

    <!-- <link rel="stylesheet" href="<?php echo _BASE_HREF;?>css/mobile.css" /> -->
    <link rel="stylesheet" href="<?php echo _BASE_HREF;?>css/styles.css" />
    <link rel="stylesheet" href="<?php echo _BASE_HREF;?>includes/plugin_mobile/js/jquery.mobile-1.2.0.min.css" />
    <link rel="stylesheet" href="<?php echo _BASE_HREF;?>includes/plugin_mobile/js/jquery.mobile.structure-1.2.0.min.css" />
    <link rel="stylesheet" href="<?php echo _BASE_HREF;?>includes/plugin_mobile/js/jquery.mobile.theme-1.2.0.min.css" />
    <!-- <link rel="stylesheet" href="<?php echo _BASE_HREF;?>js/jquerymobile/jquery.ui.datepicker.mobile.css" /> -->
        
    <?php module_config::print_css();?>


    <script src="<?php echo _BASE_HREF;?>includes/plugin_mobile/js/jquery-1.8.2.min.js"></script>
<?php if(module_config::c('mobile_content_scroll',1) && module_security::is_logged_in()){ ?>
    <script src="<?php echo _BASE_HREF;?>includes/plugin_mobile/js/iscroll.js"></script>
        <?php } ?>
    <script>
        //reset type=date inputs to text
      $( document ).bind( "mobileinit", function(){
          $.extend(  $.mobile , {
              ajaxEnabled : false,
              ajaxFormsEnabled : false,
              ajaxLinksEnabled : false
          });
      });
    </script>
    <script src="<?php echo _BASE_HREF;?>includes/plugin_mobile/js/jquery.mobile-1.2.0.min.js"></script>
    <!-- <script src="<?php echo _BASE_HREF;?>js/jquerymobile/jQuery.ui.datepicker.js"></script>
    <script src="<?php echo _BASE_HREF;?>js/jquerymobile/jquery.ui.datepicker.mobile.js"></script>
    <script type="text/javascript" src="<?php echo _BASE_HREF;?>js/javascript.js?ver=2"></script> -->
        <script type="text/javascript" src="<?php echo _BASE_HREF;?>js/javascript.js?ver=<?php echo _SCRIPT_VERSION;?>"></script>
    <script>
//        $(document).live("mobileinit", function(){
//            $.mobile.ajaxEnabled = false;
//            $.mobile.ajaxLinksEnabled = false;
//            $.mobile.ajaxFormsEnabled = false;
//        });
    </script>

    <?php module_config::print_js();?>
</head>

<body>

<div data-role="page">


    <div id="message_popdown" data-role="header">
        <?php if(print_header_message()){
            /*?>
            <script type="text/javascript">
                $('#message_popdown').click(function(){
                    $(this).slideUp();
                });
            </script>
            <?php*/
        } ?>
    </div>

<?php if($page == 'pages/home.php'){
    // display full navigation options.
    ?>



    <div data-role="header" data-position="inline">
        <h1><?php echo module_config::s('header_title','UCM');?></h1>
        <?php if (module_security::getcred()){ ?>
        <a href="<?php echo _BASE_HREF;?>index.php?_logout=true" data-icon="arrow-r" class="ui-btn-right"><?php _e('Logout');?></a>
        <?php } ?>
    </div>

    <div data-role="content" style="overflow: hidden">
        <div id="mobile_content">
            <div>
    <?php
    $menu_include_parent=false;
    include("design_menu.php");
    ?>


<?php }else{
    // display header with home page and page title  ?>
    <div data-role="header" data-position="inline">
        <?php if(module_security::getcred()){ ?>
        <a href="<?php echo _BASE_HREF;?>" data-icon="back"><?php _e('Home');?></a>
        <?php } ?>
        <h1 id="mobile_page_title"><?php echo $page_title;?></h1>
        <!-- mobile page link -->
    </div>
    <div data-role="content">
        <div id="mobile_content">
            <div>
        
<?php } ?>



