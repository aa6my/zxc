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

$data_types = $module->get_data_types($db);

?>
<h2><?php echo _l('Select Type'); ?></h2>

<?php foreach($data_types as $data_type){
	?>
	
	<a class="uibutton" href="<?php echo $module->link('',array('data_type_id'=>$data_type['data_type_id'],'data_record_id'=>'new','mode'=>'edit'));?>"><?php echo $data_type['data_type_name'];?></a>
	
	<?php
}
?>
