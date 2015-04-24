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
if(isset($_REQUEST['customer_id'])){
    $search['customer_id'] = $_REQUEST['customer_id'];
}
if(isset($_REQUEST['job_id']) && (int)$_REQUEST['job_id']>0){
    $search['job_id'] = (int)$_REQUEST['job_id'];
    //$job = module_job::get_job($search['job_id'],false);
}
if(isset($_REQUEST['quote_id']) && (int)$_REQUEST['quote_id']>0){
    $search['quote_id'] = (int)$_REQUEST['quote_id'];
    //$job = module_job::get_job($search['job_id'],false);
}
$files = module_file::get_files($search);
$module->page_title = _l('Files');


$header = array(
    'title' => _l('Customer Files'),
    'type' => 'h2',
    'main' => true,
    'button' => array(),
);
if(module_file::can_i('create','Files')){
    $header['button'] = array(
        'url' => module_file::link_open('new'),
        'title' => _l('Add New File'),
        'type' => 'add',
    );
}
print_heading($header);

?>


<form action="" method="post">

    <?php module_form::print_form_auth();?>


<?php $search_bar = array(
    'elements' => array(
        'name' => array(
            'title' => _l('File Name / Description:'),
            'field' => array(
                'type' => 'text',
                'name' => 'search[generic]',
                'value' => isset($search['generic'])?$search['generic']:'',
            )
        ),
    )
);
if(class_exists('module_job',false)){
    $search_bar['elements']['job'] = array(
        'title' => _l('Job:'),
        'field' => array(
            'type' => 'select',
            'name' => 'search[job_id]',
            'value' => isset($search['job_id'])?$search['job_id']:'',
            'options' => module_job::get_jobs(),
            'options_array_id' => 'name',
        )
    );
}
echo module_form::search_bar($search_bar); 


$table_manager = module_theme::new_table_manager();
$columns = array();
$columns['file_name'] = array(
    'title' => 'File Name',
    'callback' => function($file){
        echo module_file::link_open($file['file_id'],true);
	    if(isset($file['file_url'])&&strlen($file['file_url'])){
            echo ' ';
            echo '<a href="'.htmlspecialchars($file['file_url']).'">'.htmlspecialchars($file['file_url']).'</a>';
        }
    },
    'cell_class' => 'row_action',
);
$columns['file_description'] = array(
    'title' => 'Description',
    'callback' => function($file){
	    echo module_security::purify_html($file['description']);
    },
);
$columns['file_status'] = array(
    'title' => 'Status',
    'callback' => function($file){
		echo nl2br(htmlspecialchars($file['status']));
    },
);
$columns['file_size'] = array(
    'title' => 'File Size',
    'callback' => function($file){
		if(file_exists($file['file_path'])){
            echo module_file::format_bytes(filesize($file['file_path']));
        }
    },
);
if(!isset($_REQUEST['customer_id'])) {
	$columns['file_customer'] = array(
		'title'    => 'Customer',
		'callback' => function ( $file ) {
			echo module_customer::link_open($file['customer_id'],true);
		},
	);
}
if (class_exists('module_job',false)){
	$columns['file_job'] = array(
		'title'    => 'Job',
		'callback' => function ( $file ) {
			echo module_job::link_open($file['job_id'],true);
		},
	);
}
if (class_exists('module_quote',false) && module_quote::is_plugin_enabled()){
	$columns['file_quote'] = array(
		'title'    => 'Quote',
		'callback' => function ( $file ) {
			echo module_quote::link_open($file['quote_id'],true);
		},
	);
}
$columns['file_date_added'] = array(
    'title' => 'Date Added',
    'callback' => function($file){
		echo _l('%s by %s',print_date($file['date_created']),module_user::link_open($file['create_user_id'],true));
    },
);

if(class_exists('module_extra',false)){
    $table_manager->display_extra('file',function($file){
        module_extra::print_table_data('file',$file['file_id']);
    });
}
$table_manager->set_columns($columns);
$table_manager->row_callback = function($row_data){
    // load the full file data before displaying each row so we have access to more details
	if(isset($row_data['file_id']) && (int)$row_data['file_id']>0){
	    // not needed in this case
	    //return module_file::get_file($row_data['file_id']);
    }
    return array();
};
$table_manager->set_rows($files);

$table_manager->pagination = true;
$table_manager->print_table();


?>
</form>