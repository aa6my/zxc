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
if(!module_file::can_i('edit','File Approval'))die('no perms');
$file_id = (int)$_REQUEST['file_id'];
$file = module_file::get_file($file_id);


module_template::init_template('file_approval_email','Dear {CUSTOMER_NAME},<br>
<br>
This email is regarding your file <strong>{FILE_NAME}</strong>{if:JOB_LINK} related to the job <a href="{JOB_LINK}">{JOB_NAME}</a>{endif:JOB_LINK}.<br><br>
Please view this file and comments online by <a href="{FILE_URL}">clicking here</a>.<br><br>
Thank you,<br><br>
{FROM_NAME}
','File for Approval: {FILE_NAME}',array(
                                       'CUSTOMER_NAME' => 'Customers Name',
                                       'FILE_NAME' => 'File Name',
                                       'JOB_NAME' => 'Job Name',
                                       'FROM_NAME' => 'Your name',
                                       'FILE_URL' => 'Link to file for customer',
                                       ));



// template for sending emails.
// are we sending the paid one? or the dueone.
//$template_name = 'file_email';
$template_name = isset($_REQUEST['template_name']) ? $_REQUEST['template_name'] : 'file_approval_email';
$template = module_template::get_template_by_key($template_name);
$file['from_name'] = module_security::get_loggedin_name();
$file['file_url'] = module_file::link_public($file_id);
if(class_exists('module_job',false) && $file['job_id']){
    $job_data = module_job::get_job($file['job_id'],false);
    $file['job_name'] =  htmlspecialchars($job_data['name']);
    $file['job_link'] = module_job::link_public($file['job_id']);
}else{
    $file['job_name'] =  _l('N/A');
    $file['job_link'] = '';
}

// find available "to" recipients.
// customer contacts.
$to_select=false;
if($file['customer_id']){
    $customer = module_customer::get_customer($file['customer_id']);
    $file['customer_name'] = $customer['customer_name'];
    $to = module_user::get_contacts(array('customer_id'=>$file['customer_id']));
    if($customer['primary_user_id']){
        $primary = module_user::get_user($customer['primary_user_id']);
        if($primary){
            $to_select = $primary['email'];
        }
    }
}else{
    $to = array();
}

if(class_exists('module_extra',false) && module_extra::is_plugin_enabled()){
    $all_extra_fields = module_extra::get_defaults('file');
    foreach($all_extra_fields as $e){
        $file[$e['key']] = _l('N/A');
    }
    // and find the ones with values:
    $extras = module_extra::get_extras(array('owner_table'=>'file','owner_id'=>$file_id));
    foreach($extras as $e){
        $file[$e['extra_key']] = $e['extra'];
    }
}
$template->assign_values($file);

ob_start();
module_email::print_compose(
    array(
        'title' => _l('Email File: %s',$file['file_name']),
        'find_other_templates' => 'file_approval_email', // find others based on this name, eg: file_email*
        'current_template' => $template_name,
        'customer_id'=>$file['customer_id'],
        'job_id'=>$file['job_id'],
        'file_id'=>$file['file_id'],
        'debug_message' => 'Sending file as email',

        'to'=>$to,
        'to_select'=>$to_select,
        'bcc'=>module_config::c('admin_email_address',''),
        'content' => $template->render('html'),
        'subject' => $template->replace_description(),
        'success_url'=>module_file::link_open($file_id),
        'success_callback'=>'module_file::email_sent',
        'success_callback_args'=>array('file_id'=>$file_id),
        'cancel_url'=>module_file::link_open($file_id),
    )
);

