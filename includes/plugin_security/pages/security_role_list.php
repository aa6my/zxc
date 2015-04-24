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

if(!module_config::can_i('view','Settings') || !module_security::can_i('view','Security Roles','Security')){
    redirect_browser(_BASE_HREF);
}
$search = (isset($_REQUEST['search']) && is_array($_REQUEST['search'])) ? $_REQUEST['search'] : array();
$roles = $module->get_roles($search);

$header = array(
    'type' => 'h2',
    'title' => _l('Security Roles'),
    'main' => true,
    'button' => array(
        'title' => 'Add New Role',
        'type' => 'add',
        'url' => module_security::link_open_role('new'),
    )
);
print_heading($header);
?>


<form action="" method="post">


<?php

/** START TABLE LAYOUT **/
    $table_manager = module_theme::new_table_manager();
    $columns = array();
    $columns['name'] = array(
            'title' => 'Name',
            'callback' => function($role) use (&$module){
                echo $module->link_open_role($role['security_role_id'],true);
            },
            'cell_class' => 'row_action',
        );
    $table_manager->set_columns($columns);
    $table_manager->set_rows($roles);
    $table_manager->pagination = true;
    $table_manager->print_table();

?>
</form>