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

// include this file to list some type of data
// supports different types of lists, everything from a major table list down to a select dropdown list

$display_type = 'table';
$allow_search = true;


switch($display_type){
	case 'table':
		
		$data_types = $module->get_data_types();
		foreach($data_types as $data_type){
			$data_type_id = $data_type['data_type_id'];
            if(isset($_REQUEST['data_type_id']) && $data_type_id != $_REQUEST['data_type_id'])continue;

            include('admin_data_list_type.php');

		}
		
		break;
	case 'select':
		
		break;
	default:
		echo 'Display type: '.$display_type.' unknown.';
		break;
}