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


if(!module_finance::can_i('view','Finance Upcoming')){
    redirect_browser(_BASE_HREF);
}

if(isset($_REQUEST['finance_recurring_id']) && $_REQUEST['finance_recurring_id'] && isset($_REQUEST['record_new'])){
    include(module_theme::include_ucm(dirname(__FILE__).'/finance_edit.php'));
}else if(isset($_REQUEST['finance_recurring_id']) && $_REQUEST['finance_recurring_id']){
    //include("recurring_edit.php");
    include(module_theme::include_ucm(dirname(__FILE__).'/recurring_edit.php'));
}else{
    //include("recurring_list.php");
    include(module_theme::include_ucm(dirname(__FILE__).'/recurring_list.php'));
}