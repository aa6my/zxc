<!-- show a list of tabs for all the different social methods, as menu hooks -->

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

$module->page_title = _l('Social');


$links = array();
if(module_social::can_i('view','Combined Comments','Social','social')){
	$links [] = array(
        "name"=>_l('Inbox'),
        'm' => 'social',
        'p' => 'social_messages',
		'args' => array(
			'combined' => 1,
			'social_twitter_id' => false,
			'social_facebook_id' => false,
		),
        'force_current_check' => true,
        //'current' => isset($_GET['combined']),
        'order' => 1, // at start
        'menu_include_parent' => 0,
        'allow_nesting' => 1,
    );

	//if(isset($_GET['combined'])){
	//	include('social_messages.php');
	//}
}