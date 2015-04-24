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
if(!module_change_request::can_i('delete','Change Requests'))die('no perms');
$change_request_id = (int)$_REQUEST['change_request_id'];
$change_request = module_change_request::get_change_request($change_request_id);
if(!$change_request['website_id'])die('no linked website');
$website_data = module_website::get_website($change_request['website_id']);

if(module_form::confirm_delete('change_request_id',"Really delete Change Request?",module_website::link_open($change_request['website_id']))){
    module_change_request::delete_change_request($_REQUEST['change_request_id']);
    set_message("Change request deleted successfully");
    redirect_browser(module_website::link_open($change_request['website_id']));
}