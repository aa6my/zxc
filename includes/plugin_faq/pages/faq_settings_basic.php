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


if(!module_config::can_i('view','Settings')){
    redirect_browser(_BASE_HREF);
}

print_heading('FAQ Settings');
$c = array();
$customers = module_customer::get_customers();
foreach($customers as $customer){
    $c[$customer['customer_id']] = $customer['customer_name'];
}

module_config::print_settings_form(
    array(
        array(
            'key'=>'faq_ticket_show_product_selection',
            'default'=>1,
            'type'=>'checkbox',
            'description'=>'Show product selection on ticket submit form.',
        ),
    )
);

?>

<?php

print_heading('FAQ Embed');
?>
<p>
    <?php _e('Place this in an iframe on your website, or as a link on your website, and people can view FAQ tickets.'); ?>
</p>
<p><a href="<?php echo module_faq::link_open_public(-1);?>?show_search=1&show_header=1&show_product=1" target="_blank"><?php echo module_faq::link_open_public(-1);?>?show_search=1&show_header=1&show_product=1</a></p>
