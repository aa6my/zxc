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


if(isset($_REQUEST['product_category_id']) && $_REQUEST['product_category_id'] != ''){
    $product_category_id = (int)$_REQUEST['product_category_id'];
    $product_category = module_product::get_product_category($product_category_id);
    include('product_admin_category_edit.php');
}else{
	include('product_admin_category_list.php');
}
