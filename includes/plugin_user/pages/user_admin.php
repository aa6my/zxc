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



if(isset($_REQUEST['user_id'])){

    $user_id = (int)$_REQUEST['user_id'];

    if(class_exists('module_security',false)){
        if($user_id > 0){
            $user = module_user::get_user($user_id);

            if(!$user){
                die('Permission denied to view this user');
            }
            $user_id = (int)$user['user_id'];
        }
        if($user_id > 0){
            module_security::check_page(array(
                 'category' => 'Config',
                 'page_name' => 'Users',
                'module' => 'user',
                'feature' => 'edit',
            ));
        }else{
            module_security::check_page(array(
                 'category' => 'Config',
                 'page_name' => 'Users',
                'module' => 'user',
                'feature' => 'create',
            ));
        }
    }

    $user_safe = true;
    include(module_theme::include_ucm("includes/plugin_user/pages/user_admin_edit.php"));
	//include("user_admin_edit.php");

}else{

    if(class_exists('module_security',false)){
        module_security::check_page(array(
             'category' => 'Config',
             'page_name' => 'Users',
            'module' => 'user',
            'feature' => 'view',
        ));
    }

    include(module_theme::include_ucm("includes/plugin_user/pages/user_admin_list.php"));
	//include("user_admin_list.php");
	
} 

