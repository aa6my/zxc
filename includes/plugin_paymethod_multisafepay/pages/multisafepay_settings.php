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

/*
define('MSP_TEST_API',     true); // seperate testaccount needed
define('MSP_ACCOUNT_ID',   '1001001');
define('MSP_SITE_ID',      '60');
define('MSP_SITE_CODE',    '123');

define('BASE_URL', ($_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER['SCRIPT_NAME']) . "/");
*/


print_heading('Multisafepay Settings');?>


<?php module_config::print_settings_form(
    array(
         array(
            'key'=>'payment_method_multisafepay_enabled',
            'default'=>0,
             'type'=>'checkbox',
             'description'=>'Enable Multisafepay Checkout',
         ),
         array(
            'key'=>'payment_method_multisafepay_account',
            'default'=>'',
             'type'=>'text',
             'description'=>'Your Multisafepay Account ID',
         ),
         array(
            'key'=>'payment_method_multisafepay_site_id',
            'default'=>'',
             'type'=>'text',
             'description'=>'Your Multisafepay Site ID',
         ),
         array(
            'key'=>'payment_method_multisafepay_side_code',
            'default'=>'',
             'type'=>'text',
             'description'=>'Your Multisafepay Site Code',
         ),
         array(
            'key'=>'payment_method_multisafepay_sandbox',
            'default'=>0,
             'type'=>'checkbox',
             'description'=>'Use Multisafepay Testing Mode (for testing payments)',
         ),
    ) //
); ?>

<?php print_heading('Multisafepay setup instructions:');?>

<p><strong>Note:</strong> Multisafepay only accepts EUR currency</p>

<p>Please signup for a Multisafepay account here: http://www.multisafepay.com - please enter your multisafepay account and site details above.</p>

<p>The notification url is:</p> <pre><?php echo full_link(_EXTERNAL_TUNNEL.'?m=paymethod_multisafepay&h=ipn&method=multisafepay&type=initial')?></pre>

<p>
    Login to test merchant account here: https://testmerchant.multisafepay.com/login <br/>
    Login to live merchant account here: https://merchant.multisafepay.com/login <br/>
    Login to test user/customer account here: https://testuser.multisafepay.com/login <br/>
    Login to live user/customer account here: https://user.multisafepay.com/login <br/>
</p>

<p>
    Sandbox/Testing credit card details are:<br/><br/>
    <strong>Visa</strong><br>
    #4111111111111111  Correct <br>
    #4012888888881881  Error reason invalid balance<br/>
    #4012888888881882  card number error  <br/>
    (this can already be handled via the card number control script)<br/>
    <strong>MasterCard</strong> <br/>
    #5105105105105100  Correct<br/>
    #5555555555554444  Error reason invalid balance<br/>
    <br/>
    Use payments less than $1 for testing.
</p>