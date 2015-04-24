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
if(!$invoice_safe)die('failed');
$invoice_id = (int)$_REQUEST['invoice_id'];
$invoice = module_invoice::get_invoice($invoice_id);

if(class_exists('module_company',false) && isset($invoice['company_id']) && (int)$invoice['company_id']>0){
    module_company::set_current_company_id($invoice['company_id']);
}


// template for sending emails.
// are we sending the paid one? or the dueone.
$original_template_name = $template_name = '';
$template_name = '';
if(isset($invoice['credit_note_id']) && $invoice['credit_note_id']){
    $original_template_name = $template_name = 'credit_note_email';
}else if($invoice['date_paid'] && $invoice['date_paid']!='0000-00-00'){
    $original_template_name = $template_name = 'invoice_email_paid';
}else if($invoice['overdue'] && $invoice['date_sent'] && $invoice['date_sent']!='0000-00-00'){
    $original_template_name = $template_name = 'invoice_email_overdue';
}else{
    $original_template_name = $template_name = 'invoice_email_due';
}
$template_name = isset($_REQUEST['template_name']) ? $_REQUEST['template_name'] : $template_name;
$template = module_template::get_template_by_key($template_name);

$replace = module_invoice::get_replace_fields($invoice_id,$invoice);

$replace['from_name'] = module_security::get_loggedin_name();

// generate the PDF ready for sending.
$pdf = module_invoice::generate_pdf($invoice_id);

// find available "to" recipients.
// customer contacts.
$to_select=false;
if($invoice['customer_id']){
    $customer = module_customer::get_customer($invoice['customer_id']);
    $replace['customer_name'] = $customer['customer_name'];
    $to = module_user::get_contacts(array('customer_id'=>$invoice['customer_id']));
    if($invoice['user_id']){
        $primary = module_user::get_user($invoice['user_id']);
        if($primary){
            $to_select = $primary['email'];
        }
    }else if($customer['primary_user_id']){
        $primary = module_user::get_user($customer['primary_user_id']);
        if($primary){
            $to_select = $primary['email'];
        }
    }
}else if($invoice['member_id']){
    $member = module_member::get_member($invoice['member_id']);
    $to = array($member);
    $replace['customer_name'] = $member['first_name'];
}else{
    $to = array();
}

$template->assign_values($replace);


module_email::print_compose(
    array(
        'title' => _l('Email Invoice: %s',$invoice['name']),
        'find_other_templates' => $original_template_name, // find others based on this name, eg: job_email*
        'current_template' => $template_name,
        'customer_id'=>$invoice['customer_id'],
        'company_id'=>isset($invoice['company_id']) ? $invoice['company_id'] : 0,
        'to'=>$to,
        'to_select'=>$to_select,
        'bcc'=>module_config::c('admin_email_address',''),
        'content' => $template->render('html'),
        'subject' => $template->replace_description(),
        'success_url'=>module_invoice::link_open($invoice_id),
        'success_callback'=>'module_invoice::email_sent', // ('.$invoice_id.',"'.$template_name.'","{SUBJECT}","{TO}");
        'success_callback_args'=>array(
            'invoice_id' => $invoice_id,
            'template_name' => $template_name,
        ),
        'invoice_id'=>$invoice_id,
        'cancel_url'=>module_invoice::link_open($invoice_id),
        'attachments' => array(
            array(
                'path'=>$pdf,
                'name'=>basename($pdf),
                'preview'=>module_invoice::link_generate($invoice_id,array('arguments'=>array('go'=>1,'print'=>1),'page'=>'invoice_admin','full'=>false)),
            ),
        ),
    )
);
?>