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
//include("top_menu.php");




$module->page_title = _l('Finance');


$links = array();
$menu_position = 1;

array_unshift($links,array(
    "name"=>"Transactions",
    'm' => 'finance',
    'p' => 'finance',
    'default_page' => 'finance_list',
    'order' => $menu_position++,
    'menu_include_parent' => 0,
    'allow_nesting' => 0,
    'args' => array('finance_id'=>false),
));
if(module_finance::can_i('view','Finance Upcoming')){
    array_unshift($links,array(
        "name"=>"Upcoming Payments",
        'm' => 'finance',
        'p' => 'recurring',
        'order' => $menu_position++,
        'menu_include_parent' => 1,
        'allow_nesting' => 1,
        'args' => array('finance_id'=>false,'finance_recurring_id'=>false),
    ));
}

?>