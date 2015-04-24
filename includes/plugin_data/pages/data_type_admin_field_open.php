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
if(!module_data::can_i('edit',_MODULE_DATA_NAME)){
	die("access denied");
}



$data_type_id = (int)$_REQUEST['data_type_id'];
$data_field_group_id = (int)$_REQUEST['data_field_group_id'];
$data_field_id = $_REQUEST['data_field_id'];

if($data_type_id){
	$data_type = $module->get_data_type($data_type_id);
}else{
	die("No type defined");
}

if($data_field_group_id){
	$data_field_group = $module->get_data_field_group($data_field_group_id);
}else{
	die("No type defined");
}


if($data_field_id && $data_field_id != 'new'){
	$data_field = $module->get_data_field($data_field_id);
}else{
	$data_field = array(
		'field_type' => 'text',
	);
	if(isset($_REQUEST['order'])){
		$data_field['order'] = $_REQUEST['order'];
	}
}


print_heading(array(
    'main' => true,
    'type' => 'h2',
    'title' => 'Data Field Settings',
));
?>

<form action="" method="post">
<input type="hidden" name="_process" value="save_data_field" />
<input type="hidden" name="_redirect" value="" />
<input type="hidden" name="data_field_id" value="<?php echo $data_field_id; ?>" />
<input type="hidden" name="data_field_group_id" value="<?php echo $data_field_group_id; ?>" />
<input type="hidden" name="data_type_id" value="<?php echo $data_type_id; ?>" />
<table border="0" cellspacing="0" cellpadding="5" class="tableclass tableclass_full tableclass_form">
	<tr>
		<th class="width2">
			<?php echo _l('Title'); ?>
		</th>
		<td>
			<input type="text" 
				name="title" id="title" 
				value="<?php echo (isset($data_field['title']))?htmlspecialchars($data_field['title']):''; ?>" 
			/>
		</td>
	</tr>
	<tr>
		<th>
			<?php echo _l('Position'); ?>
		</th>
		<td>
			<input type="text" 
				name="order" id="order" 
				size="4"
				value="<?php echo (isset($data_field['order']))?htmlspecialchars($data_field['order']):''; ?>" 
			/>
			(1, 2, 3, etc.. - go to visual editor for drag &amp; drop re-arrange)
		</td>
	</tr>
	<tr>
		<th>
			<?php echo _l('Type'); ?>
		</th>
		<td>
			<input type="text" 
				name="field_type" id="field_type" 
				size="7"
				value="<?php echo (isset($data_field['field_type']))?htmlspecialchars($data_field['field_type']):''; ?>" 
			/>
			(text, textarea, checkbox, select, radio, html, encrypted, created_by, updated_by, created_date_time, updated_date_time)
		</td>
	</tr>
	<tr>
		<th>
			<?php echo _l('Type Data'); ?>
		</th>
		<td>
			<textarea
			name="field_data" id="field_data" 
			rows="3" cols="40"
			><?php echo (isset($data_field['field_data']))?htmlspecialchars($data_field['field_data']):''; ?></textarea>
            <?php _h('This area is used to store settings for this particular field element. Here are some examples of the data that can be put in here:<br><Br>For select/radio elements: <br><strong>attributes=One|Two|Three|Other</strong><br>will be used in the drop down or radio listing. \'Other\' will prompt for a text input.<br><br>For other input elements:<br><strong>width=110<br>height=23</strong><br>will be used to control the width/height of the actual input box (use visual editor to set this easier).<br><br>For all elements:<br><strong>style="clear:left;"</strong><br>add any style to the surrounding element box, eg: clear:left will place this item on a new line.'); ?>
		</td>
	</tr>
	<tr>
		<th>
			<?php echo _l('Default'); ?>
		</th>
		<td>
			<input type="text" 
				name="default" id="default" 
				size="15"
				value="<?php echo (isset($data_field['default']))?htmlspecialchars($data_field['default']):''; ?>" 
			/>
		</td>
	</tr>
	<tr>
		<th>
			<?php echo _l('Searchable'); ?>
		</th>
		<td>
			<input type="checkbox" 
			name="searchable" id="searchable" 
			value="1" 
			<?php echo (isset($data_field['searchable']) && $data_field['searchable']) ? ' checked':''; ?>>
			Yes
		</td>
	</tr>
	<tr>
		<th>
			<?php echo _l('Required'); ?>
		</th>
		<td>
			<input type="checkbox" 
			name="required" id="required" 
			value="1" 
			<?php echo (isset($data_field['required']) && $data_field['required']) ? ' checked':''; ?>>
			Yes
		</td>
	</tr>
	<!--<tr>
		<th>
			<?php /*echo _l('Reportable'); */?>
		</th>
		<td>
			<input type="checkbox" 
			name="reportable" id="reportable" 
			value="1" 
			<?php /*echo (isset($data_field['reportable']) && $data_field['reportable']) ? ' checked':''; */?>>
			Yes
		</td>
	</tr>-->
	<tr>
		<th>
			<?php echo _l('Show in main listing'); ?>
		</th>
		<td>
			<input type="checkbox" 
			name="show_list" id="show_list" 
			value="1" 
			<?php echo (isset($data_field['show_list']) && $data_field['show_list']) ? ' checked':''; ?>>
			Yes
		</td>
	</tr>
	<!--<tr>
		<th>
			<?php /*echo _l('Size'); */?>
		</th>
		<td>
			<input type="text" 
				name="width" id="width" 
				size="2"
				value="<?php /*echo (isset($data_field['width']))?htmlspecialchars($data_field['width']):''; */?>"
			/>
			x
			<input type="text" 
				name="height" id="height" 
				size="2"
				value="<?php /*echo (isset($data_field['height']))?htmlspecialchars($data_field['height']):''; */?>"
			/>
		</td>
	</tr>-->
	<!-- <tr>
		<th>
			<?php echo _l('Display Group If'); ?>
		</th>
		<td>
			<input type="text" 
				name="display_group_if" id="display_group_if" 
				size="15"
				value="<?php echo (isset($data_field['display_group_if']))?htmlspecialchars($data_field['display_group_if']):''; ?>" 
			/>
		</td>
	</tr> -->
</table>
    <p>
        <input type="submit" name="butt_save" id="butt_save" value="<?php echo _l('Save Settings'); ?>" class="submit_button save_button">
			<?php if($data_field_id && $data_field_id != 'new'){ ?>
			<input type="submit" name="butt_dell" id="butt_dell" value="<?php echo _l('Delete Field'); ?>" class="submit_button delete_button" onclick="return confirm('Really delete?');">
			<?php } ?>
			<input type="button" name="cancel" value="<?php echo _l('Cancel'); ?>" onclick="window.location.href='<?php echo $module->link("",array('data_type_id'=>$data_type_id,'data_field_group_id'=>$data_field_group_id)); ?>';" class="submit_button" />
    </p>
</form>	
