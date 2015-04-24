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

$job_safe = true; // stop including files directly.
if(!module_job::can_i('view','Jobs')){
    echo 'permission denied';
    return;
}

if(isset($_REQUEST['job_id'])){

    if(isset($_REQUEST['email_staff'])){
        include(module_theme::include_ucm("includes/plugin_job/pages/job_admin_email_staff.php"));

    }else if(isset($_REQUEST['email'])){
        include(module_theme::include_ucm("includes/plugin_job/pages/job_admin_email.php"));

    }else if((int)$_REQUEST['job_id'] > 0){
        include(module_theme::include_ucm("includes/plugin_job/pages/job_admin_edit.php"));
        //include("job_admin_edit.php");
    }else{
        include(module_theme::include_ucm("includes/plugin_job/pages/job_admin_create.php"));
        //include("job_admin_create.php");
    }

}else{

    include(module_theme::include_ucm("includes/plugin_job/pages/job_admin_list.php"));
	//include("job_admin_list.php");
	
} 

