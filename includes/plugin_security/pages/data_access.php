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

$access = true;


switch($table_name){
    case 'invoice':
    default:
        // check if current user can access this invoice.
        if($data && isset($data['customer_id']) && (int)$data['customer_id']>0){
            $valid_customer_ids = module_security::get_customer_restrictions();
            if($valid_customer_ids){
                $access = isset($valid_customer_ids[$data['customer_id']]);
                if(!$access)return false;
            }
        }
        break;
}