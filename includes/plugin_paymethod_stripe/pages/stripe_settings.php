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


if(!module_config::can_i('edit','Settings')){
    redirect_browser(_BASE_HREF);
}

print_heading('Stripe Settings');?>


<?php module_config::print_settings_form(
    array(
         array(
            'key'=>'payment_method_stripe_enabled',
            'default'=>1,
             'type'=>'checkbox',
             'description'=>'Enable Stripe Checkout',
         ),
         array(
            'key'=>'payment_method_stripe_label',
            'default'=>'Stripe',
             'type'=>'text',
             'description'=>'Payment Method Label',
	         'help' => 'This will display on invoices as the name of this payment method.'
         ),
         array(
            'key'=>'payment_method_stripe_secret_key',
            'default'=>'',
             'type'=>'text',
             'description'=>'Your Stripe Secret Key (Test or Live)',
         ),
         array(
            'key'=>'payment_method_stripe_publishable_key',
            'default'=>'',
             'type'=>'text',
             'description'=>'Your Stripe Publishable Key (Test or Live)',
         ),
         array(
            'key'=>'payment_method_stripe_subscriptions',
            'default'=>0,
             'type'=>'checkbox',
             'description'=>'Enable Stripe Subscriptions (set web hook below!)',
         ),
         array(
            'key'=>'payment_method_stripe_currency',
            'default'=>'',
             'type'=>'text',
             'description'=>'Which Currencies To Support',
             'help'=>'A comma separated list of currencies to support, eg: AUD,USD Leave this blank to support all currencies. If an invoice is in an unsupported currency then this payment method will not display.',
         ),
         array(
            'key'=>'payment_method_stripe_limit_type',
            'default'=>'above',
             'type'=>'select',
	         'options' => array(
		         'above'=>_l('Greater Than...'),
		         'below'=>_l('Less Than...'),
	         ),
             'description'=>'Only show when invoice value is ...',
             'help'=>'Only show the stripe option if the dollar value is greater than or less than the below value.',
         ),
         array(
            'key'=>'payment_method_stripe_limit_value',
            'default'=>'0',
             'type'=>'text',
             'description'=>'... this amount',
             'help'=>'What value to restrict stripe payments to',
         ),
         array(
            'key'=>'payment_method_stripe_charge_percent',
            'default'=>0,
             'type'=>'text',
             'description'=>'Additional Charge (as %)',
             'help' => 'Example: 1.5 do not enter %% sign',
         ),
         array(
            'key'=>'payment_method_stripe_charge_amount',
            'default'=>0,
             'type'=>'text',
             'description'=>'Additional Charge (as $)',
             'help' => 'Example: 1.5 do not enter $ sign',
         ),
         array(
            'key'=>'payment_method_stripe_charge_description',
            'default'=>'Stripe Fee',
             'type'=>'text',
             'description'=>'Additional Charge (Description)',
             'help' => 'This will show on the Invoice when paying via stripe',
         ),
    )
); ?>

<?php print_heading('Stripe setup instructions:');?>

<p>Stripe only supports payments in USD and CAD </p>
<p>Please signup for a Strip account here: http://www.stripe.com - please enter your stripe API Keys above.</p>
<p>If you are using the TEST api keys then you can use the credit card number 4242424242424242 with any valid expiry date of CVC</p>
<?php print_heading('Stripe subscriptions:');?>
<p><strong>WebHook:</strong> if you are planning to offer subscriptions via stripe (enabled above) please set the webhook address (in Stripe settings) to: <?php echo full_link(_EXTERNAL_TUNNEL.'?m=paymethod_stripe&h=event_ipn&method=stripe');?></p>
<p>Also please go to Settings > Update and click the Manual Update button, just to be sure!</p>
<p>As always, set the TEST API keys above first and do a test subscription payment to ensure it all works correctly.</p>