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

require_once('includes/plugin_paymethod_stripe/stripe-php/lib/Stripe.php');

$stripe = array(
  "secret_key"      => module_config::c('payment_method_stripe_secret_key'),
  "publishable_key" => module_config::c('payment_method_stripe_publishable_key')
);

Stripe::setApiKey($stripe['secret_key']);
 ?>