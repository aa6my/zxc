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


if(!module_invoice::can_i('edit','Invoice Settings','Config')){
    redirect_browser(_BASE_HREF);
}
ob_start();
    echo module_template::link_open_popup('external_invoice').
     module_template::link_open_popup('credit_note_external').
     module_template::link_open_popup('invoice_payment_receipt').
     module_template::link_open_popup('invoice_print').
     module_template::link_open_popup('invoice_print_basic').
     module_template::link_open_popup('invoice_task_list').
     module_template::link_open_popup('credit_note_pdf').
     module_template::link_open_popup('invoice_email_due').
     module_template::link_open_popup('invoice_email_overdue').
     module_template::link_open_popup('invoice_email_paid').
     module_template::link_open_popup('credit_note_email');
$templates = ob_get_clean();

$settings = array(
     array(
        'key'=>'overdue_email_auto',
        'default'=>'0',
         'type'=>'checkbox',
         'description'=>'Automatic Overdue Emails',
         'help'=>'If this is ticked then by default newly created invoices will be sent automatic overdue notices. This can be disabled/enabled per invoice. See the "Auto Overdue Email" option near "Due Date".',
     ),
     array(
        'key'=>'invoice_automatic_receipt',
        'default'=>'1',
         'type'=>'checkbox',
         'description'=>'Automatic Send Invoice Receipt',
         'help'=>'Automatically send the invoice receipt to the customer once the invoice is marked as paid. If this is disabled you will have to go into the invoice and manually send it after payment is received.',
     ),
     array(
        'key'=>'invoice_template_print_default',
        'default'=>'invoice_print',
         'type'=>'text',
         'description'=>'Default PDF invoice template',
         'help'=>'Used for invoice PDF. You can overwrite in the Advanced settings of each invoice.',
     ),
     array(
        'key'=>'overdue_email_auto_days',
        'default'=>'3',
         'type'=>'text',
         'description'=>'Automically send after',
         'help'=>'How many days after the invoice is overdue is the automated email sent (set to 0 will send on the date the invoice is due)',
     ),
     array(
        'key'=>'overdue_email_auto_days_repeat',
        'default'=>'7',
         'type'=>'text',
         'description'=>'Automically re-send every',
         'help'=>'How many days after the last automatic overdue reminder is the overdue reminder re-sent automatically (set to 0 to disable this option)',
     ),
     array(
        'key'=>'invoice_automatic_after_time',
        'default'=>'7',
         'type'=>'text',
         'description'=>'Hour of day to perform automatic operations',
         'help'=>'Enter the hour of day (eg: 7 for 7am, 14 for 2pm) to perform automatic actions - such as renewing invoices, subscriptions, overdue notices, etc...',
     ),
     array(
         'type'=>'html',
         'description'=>'Templates',
         'html' => $templates,
     ),
);


module_config::print_settings_form(
    array(
        'heading' => array(
            'title' => 'Invoice Settings',
            'type' => 'h2',
            'main' => true,
        ),
        'settings' => $settings,
    )
);

// find any blank invoices.
$sql = "SELECT * FROM `"._DB_PREFIX."invoice` WHERE customer_id IS NULL AND `name` = '' AND `status` = '' AND `date_create` = '0000-00-00' AND `date_sent` = '0000-00-00' AND `date_paid` = '0000-00-00' AND `date_due` = '0000-00-00' AND c_total_amount = 0 ";
$invoices = qa($sql);
$blank_invoices = array();
foreach($invoices as $invoice){
	$items = module_invoice::get_invoice_items($invoice['invoice_id']);
	if(empty($items)){
		$blank_invoices[] = $invoice;
	}
}
if(count($blank_invoices) && isset($_POST['remove_duplicates']) && $_POST['remove_duplicates'] == 'yes'){
	foreach($blank_invoices as $id => $blank_invoice){
		module_invoice::delete_invoice($blank_invoice['invoice_id']);
		unset($blank_invoices[$id]);
	}
}
if(count($blank_invoices)){
	?>
	<h2>Blank invoices found</h2>
	We found the following <?php echo count($blank_invoices);?> blank invoices that were created from a recent "Subscription" bug:
	<ul>
		<?php foreach($blank_invoices as $blank_invoice){ ?>
		<li><?php echo module_invoice::link_open($blank_invoice['invoice_id'],true);?> created on <?php echo print_date($blank_invoice['date_created']);?></li>
		<?php } ?>
	</ul>
	You can remove all these invoices manually, or click the button below to remove them automatically.
	<form action="" method="post">
		<input type="hidden" name="remove_duplicates" value="yes">
		<input type="submit" value="Remove these <?php echo count($blank_invoices);?> invoices">
	</form>
	<?php
}