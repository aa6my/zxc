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

if(!module_social::can_i('edit','Facebook','Social','social')){
    die('No access to Facebook accounts');
}

$social_facebook_id = isset($_REQUEST['social_facebook_id']) ? (int)$_REQUEST['social_facebook_id'] : 0;
$facebook = new ucm_facebook_account($social_facebook_id);

$heading = array(
    'type' => 'h3',
    'title' => 'Facebook Account',
);
?>

<form action="" method="post">
	<input type="hidden" name="_process" value="save_facebook">
	<input type="hidden" name="social_facebook_id" value="<?php echo $facebook->get('social_facebook_id');?>">

	<?php
	module_form::print_form_auth();

	$fieldset_data = array(
	    'heading' => $heading,
	    'class' => 'tableclass tableclass_form tableclass_full',
	    'elements' => array(
	        array(
	            'title' => _l('Account Name'),
	            'field' => array(
	                'type' => 'text',
		            'name' => 'facebook_name',
		            'value' => $facebook->get('facebook_name'),
		            'help' => 'Choose a name for this account. This name will be shown here in the system.',
	            ),
	        ),
	    )
	);
	// check if this is active, if not prmopt the user to re-connect.
	if($facebook->is_active()){
		$fieldset_data['elements'][] = array(
			'title' => _l('Last Checked'),
	            'fields' => array(
	                print_date($facebook->get('last_checked'),true),
	            ),
	        );
		$pages = array(
			'title' => _l('Available Pages'),
            'fields' => array(
	            '<input type="hidden" name="save_facebook_pages" value="yep">',
            ),
        );
		$data = @json_decode($facebook->get('facebook_data'),true);
		if($data && isset($data['pages']) && is_array($data['pages']) && count($data['pages']) > 0){
			$pages['fields'][] = '<strong>Choose which Facebook Pages you would like to manage:</strong><br>';
			foreach($data['pages'] as $page_id => $page_data) {
				$pages['fields'][] = '<div>';
				$pages['fields'][] = array(
					'type' => 'check',
					'name' => 'facebook_page['.$page_id.']',
					'value' => 1,
					'label' => $page_data['name'],
					'checked' => $facebook->is_page_active($page_id),
				);
				if($facebook->is_page_active($page_id)){
					$pages['fields'][] = '(<a href="' . module_social_facebook::link_open_facebook_page_refresh($social_facebook_id,$page_id,false,false).'" target="_blank">manually re-load page data</a>)';
				}
				$pages['fields'][] = '</div>';
			}
		}else{
			$pages['fields'][] = 'No Facebook Pages Found to Manage';
		}
		$fieldset_data['elements'][] = $pages;
	}else{

	}
	echo module_form::generate_fieldset($fieldset_data);

	$form_actions = array(
	    'class' => 'action_bar action_bar_center',
	    'elements' => array(),
	);
	echo module_form::generate_form_actions($form_actions);

	if(!$facebook->is_active()){
		// show a 'save' and button as normal
		$form_actions['elements'][] = array(
	        'type' => 'save_button',
	        'name' => 'butt_save_connect',
	        'value' => _l('Save & Connect to Facebook'),
	    );
	}else{
		$form_actions['elements'][] = array(
	        'type' => 'save_button',
	        'name' => 'butt_save',
	        'value' => _l('Save'),
	    );
		$form_actions['elements'][] = array(
	        'type' => 'submit',
	        'name' => 'butt_save_connect',
	        'value' => _l('Re-Connect to Facebook'),
	    );
	}
	if($facebook->get('social_facebook_id')){
		// show delete if we have an id.

		$form_actions['elements'][] = array(
			    'ignore' => !(module_social::can_i('delete','Facebook','Social','social')),
			    'type' => 'delete_button',
			    'name' => 'butt_del',
			    'value' => _l('Delete'),
			);
	}
	// always show a cancel button
	$form_actions['elements'][] = array(
	    'type' => 'button',
	    'name' => 'cancel',
	    'value' => _l('Cancel'),
	    'class' => 'submit_button',
	    'onclick' => "window.location.href='".$module->link_open(false)."';",
	);

	echo module_form::generate_form_actions($form_actions);
	?>


</form>