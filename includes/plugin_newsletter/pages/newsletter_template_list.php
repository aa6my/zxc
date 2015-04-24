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

$search = (isset($_REQUEST['search']) && is_array($_REQUEST['search'])) ? $_REQUEST['search'] : array();

$newsletter_templates = module_newsletter::get_templates($search);


$header = array(
    'title' => _l('Newsletter Templates'),
    'type' => 'h2',
    'main' => true,
    'button' => array(),
);
$header['button'] = array(
    'url' => module_newsletter::link_open_template('new'),
    'title' => _l('Add New Template'),
    'type' => 'add',
);
print_heading($header);


$table_manager = module_theme::new_table_manager();
$columns = array();
$columns['newsletter_template_name'] = array(
    'title' => 'Template Name',
    'callback' => function($newsletter_template){
		echo module_newsletter::link_open_template($newsletter_template['newsletter_template_id'],true);
    },
    'cell_class' => 'row_action',
);
$columns['newsletter_template_action'] = array(
    'title' => 'Action',
    'callback' => function($newsletter_template){
		?> <a href="<?php echo module_newsletter::link_open_template($newsletter_template['newsletter_template_id']);?>">Edit</a> <?php
    },
);
$table_manager->set_columns($columns);
$table_manager->set_rows($newsletter_templates);
$table_manager->pagination = false;
$table_manager->print_table();

?>

