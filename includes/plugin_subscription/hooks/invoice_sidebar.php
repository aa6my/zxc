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
if($subscription['subscription_owner_id']){
    $subscription_owner = get_single('subscription_owner','subscription_owner_id',$subscription['subscription_owner_id']);
    if(count($subscription_owner)){

		ob_start();
	    ?>
	    <table border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form tableclass_full">
	        <tbody>
	        <tr>
	            <td>
	                <?php
	                switch($subscription_owner['owner_table']){
	                    case 'member':
	                        $member_name = module_member::link_open($subscription_owner['owner_id'],true);
	                        break;
	                    case 'website':
	                        $member_name = module_website::link_open($subscription_owner['owner_id'],true);
	                        break;
	                    case 'customer':
	                        $member_name = module_customer::link_open($subscription_owner['owner_id'],true);
	                        break;
	                }
	                $subscription_name = module_subscription::link_open($subscription['subscription_id'],true);
	                _e('This is a subscription payment for %s %s on the subscription: %s',$subscription_owner['owner_table'],$member_name,$subscription_name); ?>
	            </td>
	        </tr>
	        </tbody>
	    </table>
		<?php
		$fieldset_data = array(
		    'heading' => array(
		        'title' => _l('%s Subscription',_l(ucwords($subscription_owner['owner_table']))),
		        'type' => 'h3',
		    ),
		    'elements_before' => ob_get_clean(),
		);
		echo module_form::generate_fieldset($fieldset_data);
    }
} ?>