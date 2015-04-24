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

$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : array();
$subscriptions = module_subscription::get_subscriptions($search);

$pagination = process_pagination($subscriptions);

$heading = array(
    'title' => 'Subscriptions',
    'type' => 'h2',
        'main' => true,
);
if(module_subscription::can_i('create','Subscriptions')){
    $heading['button'] = array(
        'title' => "Create New Subscription",
        'type' => 'add',
        'url' => module_subscription::link_open('new'),
    );
}
print_heading($heading);

?>




<form action="" method="post">

<?php echo $pagination['summary'];?>
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
	<thead>
	<tr class="title">
		<th><?php echo _l('Subscription Name'); ?></th>
		<th><?php echo _l('Repeat Every'); ?></th>
		<th><?php echo _l('Amount'); ?></th>
		<th><?php echo _l('Member Count'); ?></th>
		<th><?php echo _l('Customer Count'); ?></th>
		<th><?php echo _l('Website Count'); ?></th>
		<th><?php echo _l('Automatically Renew'); ?></th>
		<th><?php echo _l('Automatically Email'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
	$c=0;
	foreach($pagination['rows'] as $subscription){ ?>
        <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
            <td class="row_action">
	            <?php echo module_subscription::link_open($subscription['subscription_id'],true,$subscription); ?>
            </td>
            <td>
				<?php
                if(!$subscription['days']&&!$subscription['months']&&!$subscription['years']){
                    echo _l('Once off');
                }else{
                    $bits = array();
                    if($subscription['days']>0){
                        $bits[] = _l('%s days',$subscription['days']);
                    }
                    if($subscription['months']>0){
                        $bits[] = _l('%s months',$subscription['months']);
                    }
                    if($subscription['years']>0){
                        $bits[] = _l('%s years',$subscription['years']);
                    }
                    echo _l('Every %s',implode(', ',$bits));
                }
				?>
            </td>
            <td>
				<?php
                echo dollar($subscription['amount'],true,$subscription['currency_id']);
				?>
            </td>
            <td> <?php echo htmlspecialchars($subscription['member_count']); ?> </td>
            <td> <?php echo htmlspecialchars($subscription['customer_count']); ?> </td>
            <td> <?php echo htmlspecialchars($subscription['website_count']); ?> </td>
            <td><?php echo $subscription['automatic_renew'] ? _l('Yes') : _l('No');?></td>
            <td><?php echo $subscription['automatic_email'] ? _l('Yes') : _l('No');?></td>
        </tr>
	<?php } ?>
  </tbody>
</table>
<?php echo $pagination['links'];?>
</form>

<?php


module_config::print_settings_form(
    array(
        'title' => 'Subscription Options',
        'settings' => array(
            array(
                'key' => 'subscription_invoice_due_date',
                'default' => 0,
                'type' => 'text',
                'description' => 'Invoice Due Date Days',
                'help' => 'How many days after the invoice is created should the due date be set?',
            )
        )
    )
);

?>