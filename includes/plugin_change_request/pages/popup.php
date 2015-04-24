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
/*
?>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Change Request</title>
    <link rel="stylesheet" href="<?php echo full_link('/includes/plugin_change_request/css/change_request.css');?>">
</head>
<body>
<?php */ ?>
<div id="change_request_popup">
    <?php if($change_history[1]>0){ ?>
    <div class="change_request_remain">
        <strong><?php echo max(0,$change_history[1] - $change_history[0]);?></strong> of <?php echo $change_history[1];?> <?php _e('changes remaining');?>
    </div>
    <?php } ?>
    <h1><?php _e('Website Change Request');?></h1>
    <?php if($step>=1){ ?>
    <ol id="change_request_steps">
        <li class="<?php echo $step==1 ? 'current' : '';?>"><span><?php _e('Step');?> <span>1</span></span></li>
        <li class="<?php echo $step==2 ? 'current' : '';?>"><span><?php _e('Step');?> <span>2</span></span></li>
        <li class="<?php echo $step==3 ? 'current' : '';?>" style="margin:0"><span><?php _e('Step');?> <span>3</span></span></li>
    </ol>
    <?php } ?>
    <?php
    include('popup_content_'.$step.'.php');
    ?>
</div>
    <?php /*
</body>
</html> */?>