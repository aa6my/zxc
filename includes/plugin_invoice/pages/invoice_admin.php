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

$invoice_safe = true;

if(isset($_REQUEST['print'])){
    include(module_theme::include_ucm("includes/plugin_invoice/pages/invoice_admin_print.php"));
        //include('invoice_admin_print.php');
}else if(isset($_REQUEST['invoice_id'])){

    if(isset($_REQUEST['email'])){
        include(module_theme::include_ucm("includes/plugin_invoice/pages/invoice_admin_email.php"));
        //include('invoice_admin_email.php');
    }else{
        /*if(module_security::getlevel() > 1){
            include('invoice_customer_view.php');
        }else{*/
            include(module_theme::include_ucm("includes/plugin_invoice/pages/invoice_admin_edit.php"));
            //include("invoice_admin_edit.php");
        /*}*/
    }

}else{

    include(module_theme::include_ucm("includes/plugin_invoice/pages/invoice_admin_list.php"));
	//include("invoice_admin_list.php");
	
} 

