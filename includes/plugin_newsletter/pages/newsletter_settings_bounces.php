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
if(isset($_REQUEST['go'])){
    ob_end_clean();
    echo '<pre>';
    _e("Checking for bounces, please wait...");
    echo "\n\n";
    module_newsletter::check_bounces(true);
    echo "\n\n";
    _e("done.");
    echo '</pre>';

    exit;
}

$module->page_title = _l('Newsletter Bounce Checking');
print_heading('Newsletter Bounce Checking');

?>
<p><?php _e('Bounces are checked automatically using the CRON job, however if you want to check for bounces manually (ie: to see any error) please click the button below.');?></p>
<form action="" method="post">
<input type="submit" name="go" value="<?php _e('Check for bounces');?>">
</form>