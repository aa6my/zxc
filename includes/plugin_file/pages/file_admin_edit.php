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

$options = isset($_REQUEST['options']) ? unserialize(base64_decode($_REQUEST['options'])) : array();

$file_id = (int)$_REQUEST['file_id'];
$file = module_file::get_file($file_id);
if($file_id>0 && $file && $file['file_id']==$file_id){
	if(class_exists('module_security',false)){

        if(class_exists('module_job',false) && class_exists('module_customer',false)){
            // check if we can access this customer
            // OR if we can access this job.
            $customer_access = module_customer::get_customer($file['customer_id']);
            $job_access = module_job::get_job($file['job_id']);
            // hack - support non existant customer or job.
            if(
                ($customer_access && ($customer_access['customer_id'] || $customer_access['customer_id'] == $file['customer_id'])) ||
                ($job_access && $job_access['job_id'] == $file['job_id'])
            ){
                // success! we can view this file.
            }else{
                die('Failed to access file. No permissions to view this customer ID '.$file['customer_id'].' or job ID '.$job_access['job_id']);
            }
        }


        //if(!module_security::can_access_data('file',$file,$file_id)){
            //echo 'Permission denied to access data linked to customer id '.$file['customer_id'].'. Please contact your administrator.';
            //exit;
        //}

        module_security::check_page(array(
			'module' => $module->module_name,
            'feature' => 'Edit',
		));

	}
}else{
    //$file = array();
	if(class_exists('module_security',false)){
		module_security::check_page(array(
			'module' => $module->module_name,
            'feature' => 'Create',
		));
	}
	module_security::sanatise_data('file',$file);
}


if($file_id>0 && $file['file_id']==$file_id){
    $module->page_title = _l('File: %s',$file['file_name']);

    // close off any notifications here.
    $sql = "UPDATE `"._DB_PREFIX."file_notification` SET `view_time` = '".time()."' WHERE `view_time` = 0 AND `user_id` = ".module_security::get_loggedin_id()." AND file_id = ".(int)$file_id;
    query($sql);

}else{
    $module->page_title = _l('File: %s',_l('New'));
}


if(!isset($file['customer_id'])||!$file['customer_id'])$file['customer_id']=false; // helps with drop downs below.

$plupload_key = md5(time().mt_rand(1,300));

$type = (isset($file['file_url']) && $file['file_url']) ? 'remote' : 'upload';
if(isset($_REQUEST['file_type']))$type = $_REQUEST['file_type'];

if(!module_config::c('file_upload_old',0) && $type == 'upload'){
?>

<!-- Third party script for BrowserPlus runtime (Google Gears included in Gears runtime now) -->
<!-- <script type="text/javascript" src="http://bp.yahooapis.com/2.4.21/browserplus-min.js"></script> -->

<!-- Load plupload and all it's runtimes and finally the jQuery queue widget -->
<script type="text/javascript" src="<?php echo full_link('includes/plugin_file/js/plupload.full.js');?>"></script>

<script type="text/javascript">
// Custom example logic
var uploader=null;
var selected_files=[];
$(function() {
	uploader = new plupload.Uploader({
		runtimes : 'html5,flash,html4', //html5,silverlight,gears,browserplus
		browse_button : 'pickfiles',
		container : 'container',
		/*max_file_size : '10mb',*/
		url : '<?php echo module_file::link_open($file_id,false);?>&_process=plupload&plupload_key=<?php echo $plupload_key;?>',
		flash_swf_url : '<?php echo full_link('includes/plugin_file/js/plupload.flash.swf');?>'
		/*filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		],
		resize : {width : 320, height : 240, quality : 90}*/
	});

	uploader.bind('Init', function(up, params) {
		//$('#filelist').html("<div>Current runtime: " + params.runtime + "</div>");
		$('#filelist').html("");
	});

	$('#butt_save').click(function(e) {
        if(selected_files.length>0){
            uploader.start();
            e.preventDefault();
            return false;
        }
	});

	uploader.init();


	uploader.bind('FilesAdded', function(up, files) {


        /*console.debug(up.files);
         plupload.each(files, function(file) {
            if (up.files.length > 1) {
                up.removeFile(file);
            }
        });

        // remove any selected files
        var limit = 1,
            fileCount = up.files.length,
            i = 0,
            ids = $.map(up.files, function (item) { return item.id; });
        for (i = 0; i < fileCount; i++) {
            uploader.removeFile(uploader.getFile(ids[i]));
        }*/

		$.each(files, function(i, file) {
            selected_files.push({
                file_id: file.id,
                name: file.name
            });
			$('#filelist').append(
				'<div id="' + file.id + '">' +
				file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                    '<input type="hidden" name="plupload_file_name" value="'+file.name+'" />' +
			'</div>');
		});

        if(selected_files.length>1){
            for(var i = 0; i < selected_files.length-1; i++){
                if(typeof selected_files[i] == 'object' && typeof selected_files[i]['file_id'] != 'undefined'){
                    with({file_id:selected_files[i]['file_id']}){
                        // timeout required to stop exception in plupload js
                        setTimeout(function(){
                            uploader.removeFile(uploader.getFile(file_id));
                            $('#'+file_id).remove();
                        },50);
                    }
                }
            }
        }
		up.refresh(); // Reposition Flash/Silverlight
	});


	uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
        $( "#progressbar" ).progressbar({
          value: file.percent
        });
	});

	uploader.bind('Error', function(up, err) {
		$('#filelist').append("<div>Error: " + err.code +
			", Message: " + err.message +
			(err.file ? ", File: " + err.file.name : "") +
			"</div>"
		);

		up.refresh(); // Reposition Flash/Silverlight
	});

	uploader.bind('FileUploaded', function(up, file) {
		$('#' + file.id + " b").html("100% ... <?php _e('Processing, please wait');?>");
        $('#form_file_save').append('<input type="hidden" name="plupload_key" value="<?php echo $plupload_key; ?>" />');
        $('#form_file_save')[0].submit();
	});
});
</script>
<?php } ?>
	
<form action="" method="post" enctype="multipart/form-data" id="form_file_save">
	<input type="hidden" name="_process" value="save_file" class="no_permissions" />
    <input type="hidden" name="file_id" value="<?php echo $file_id; ?>" class="no_permissions" />
    <input type="hidden" name="options" value="<?php echo base64_encode(serialize($options)); ?>" class="no_permissions" />



    <?php

    $fields = array(
    'fields' => array(
        'url' => 'Name',
    ));
    module_form::set_required(
        $fields
    );
    module_form::prevent_exit(array(
        'valid_exits' => array(
            // selectors for the valid ways to exit this form.
            '.submit_button',
        ))
    );



    hook_handle_callback('layout_column_half',1);

    $heading = array(
        'type'=>'h3',
        'title'=>'File Details',
        'button'=>array(
            'url'=>module_file::link_open($file_id).'&file_type='.($type=='upload'?'remote':'upload'),
            'title'=>($type=='upload'?'Swap to URL':'Swap to Upload'),
        )
    );

    $fieldset_data = array(
        'heading' => array(
            'type' => 'h3',
            'title' => 'File Details',
	        'button'=>array(
	            'url'=>module_file::link_open($file_id).'&file_type='.($type=='upload'?'remote':'upload'),
	            'title'=>($type=='upload'?'Swap to URL':'Swap to Upload'),
	        )
        ),
        'class' => 'tableclass tableclass_form tableclass_full',
        'elements' => array(),
        'extra_settings' => array(
            'owner_table' => 'file',
            'owner_key' => 'file_id',
            'owner_id' => $file['file_id'],
            'layout' => 'table_row',
             'allow_new' => module_file::can_i('create','Files'),
             'allow_edit' => module_file::can_i('create','Files'),
        ),
    );
    if( $type == 'upload' ){
        $fieldset_data['elements'][] = array(
            'title' => 'Upload File',
            'fields' => array(
                function() use(&$file, $file_id, &$module){
	                if(module_config::c('file_upload_old',0)){ ?>
                        <input type="file" name="file_upload">
                    <?php }else{ ?>
						<div id="progressbar"></div>
						<div id="container">
							<div id="filelist"> Error, unable to upload files. </div>
						    <input type="button" name="u" id="pickfiles" value="<?php _e('Select Files');?>">
						</div>
                    <?php } ?>
                    <?php if($file_id){ ?>
                    <a href="<?php echo $module->link('file_edit',array('_process'=>'download','file_id'=>$file['file_id']),'file',false);?>"><?php echo nl2br(htmlspecialchars($file['file_name']));?></a>
                    <?php }
                }
            ),
        );
    }else{
	    $fieldset_data['elements'][] = array(
            'title' => 'File Name',
            'field' => array(
                'type' => 'text',
	            'name' => 'file_name',
	            'value' => $file['file_name'],
            ),
        );
	    $fieldset_data['elements'][] = array(
            'title' => 'File URL',
            'fields' => array(
                function() use(&$file, $file_id){
	                if(module_file::can_i('edit','Files')){ ?>
					<input type="text" name="file_url" value="<?php echo htmlspecialchars($file['file_url']);?>">
                        <?php if($file['file_url']){ ?>
                        <a href="<?php echo htmlspecialchars($file['file_url']);?>" target="_blank"><?php _e('Open');?></a>
                        <?php } ?>
                    <?php _h('You can enter a full URL to a file here (eg: http://yoursite.com/file.txt) and that link will become available through this system.'); ?>
                    <?php }else{ ?>
                        <a href="<?php echo htmlspecialchars($file['file_url']);?>" target="_blank"><?php echo htmlspecialchars($file['file_url']);?></a>
                    <?php }
                }
            )
        );
    }
    $fieldset_data['elements'][] = array(
	    'title' => 'Status',
	    'field' => array(
	        'type' => 'select',
	        'name' => 'status',
	        'value' => $file['status'],
		    'options' => module_file::get_statuses(),
		    'allow_new' => true,
	    ),
	);
    if(class_exists('module_customer',false)){
	    $c = array();
        $res = module_customer::get_customers();
        foreach($res as $row){
            $c[$row['customer_id']] = $row['customer_name'];
        }
        if($file['customer_id'] && !isset($c[$file['customer_id']])){
            // this file is related to another job. from another customer.
            $related_customer = module_customer::get_customer($file['customer_id'],true);
            $c[$file['customer_id']] = $related_customer['customer_name'];
        }
	    $fieldset_data['elements'][] = array(
		    'title' => 'Customer',
		    'field' => array(
		        'type' => 'select',
		        'name' => 'customer_id',
		        'value' => $file['customer_id'],
			    'options' => $c,
		    ),
		);
    }
    if (class_exists('module_job',false) && class_exists('module_customer',false)){
	    $c = array();
        $res = module_job::get_jobs(array('customer_id'=>$file['customer_id']));
        foreach($res as $row){
            $c[$row['job_id']] = $row['name'];
        }
        if($file['job_id'] && !isset($c[$file['job_id']])){
            // this file is related to another job. from another customer.
            $related_job = module_job::get_job($file['job_id'],false,true);
            if($related_job && $related_job['job_id'] == $file['job_id']){
                $related_customer = module_customer::get_customer($related_job['customer_id'],true);
                if($related_customer && $related_customer['customer_id'] == $related_job['customer_id']){
                    $c[$file['job_id']] = _l('%s (from %s)',$related_job['name'],$related_customer['customer_name']);
                }else{
                    $file['job_id'] = false;
                }
            }else{
                $file['job_id'] = false;
            }
        }
	    $fieldset_data['elements'][] = array(
		    'title' => 'Job',
		    'fields' => array(
			    array(
			        'type' => 'select',
			        'name' => 'job_id',
			        'value' => $file['job_id'],
				    'options' => $c,
			    ),
			    function() use (&$file){
				    if($file['job_id']){
                        echo ' ';
                        echo '<a href="'.module_job::link_open($file['job_id'],false).'">'._l('Open Job &raquo;').'</a>';
                    }
			    }
		    ),
		);
    }
    if (class_exists('module_quote',false) && module_quote::is_plugin_enabled()){
	    $c = array();
        $res = module_quote::get_quotes(array('customer_id'=>$file['customer_id']));
        foreach($res as $row){
            $c[$row['quote_id']] = $row['name'];
        }
        if($file['quote_id'] && !isset($c[$file['quote_id']])){
            // this file is related to another quote. from another customer.
            $related_quote = module_quote::get_quote($file['quote_id'],false,true);
            if($related_quote && $related_quote['quote_id'] == $file['quote_id']){
                $related_customer = module_customer::get_customer($related_quote['customer_id'],true);
                if($related_customer && $related_customer['customer_id'] == $related_quote['customer_id']){
                    $c[$file['quote_id']] = _l('%s (from %s)',$related_quote['name'],$related_customer['customer_name']);
                }else{
                    $file['quote_id'] = false;
                }
            }else{
                $file['quote_id'] = false;
            }
        }
	    $fieldset_data['elements'][] = array(
		    'title' => 'Quote',
		    'fields' => array(
			    array(
			        'type' => 'select',
			        'name' => 'quote_id',
			        'value' => $file['quote_id'],
				    'options' => $c,
			    ),
			    function() use (&$file){
				    if($file['quote_id']){
                        echo ' ';
                        echo '<a href="'.module_quote::link_open($file['quote_id'],false).'">'._l('Open Quote &raquo;').'</a>';
                    }
			    }
		    ),
		);
    }

    $staff_members = module_user::get_staff_members();
	$staff_member_rel = array();
	foreach($staff_members as $staff_member){
	    $staff_member_rel[$staff_member['user_id']] = $staff_member['name'];
	}
	if(!isset($file['staff_ids']) || !is_array($file['staff_ids']) || !count($file['staff_ids'])){
	    $file['staff_ids']= array(false);
	    if(count($staff_member_rel) == 1){
	        $file['staff_ids'] = array(key($staff_member_rel));
	    }
	}
    $fieldset_data['elements'][] = array(
	    'title' => 'Staff',
	    'fields' => array(
		    array(
		        'type' => 'hidden',
		        'name' => 'staff_ids_save',
		        'value' => 1,
		    ),
		    '<div id="staff_ids_holder" style="float:left;">',
		    array(
                'type' => 'select',
                'name' => 'staff_ids[]',
                'options' => $staff_member_rel,
                'multiple' => 'staff_ids_holder',
                'values' => $file['staff_ids'],
            ),
		    '</div>',
		    function() use (&$file){
			    if($file['quote_id']){
                    echo ' ';
                    echo '<a href="'.module_quote::link_open($file['quote_id'],false).'">'._l('Open Quote &raquo;').'</a>';
                }
		    },
		    array(
			    'type' => 'html',
			    'value' => '',
			    'help' => 'Assign a staff member to this file. Staff members are users who have EDIT permissions on Job Tasks. Click the plus sign to add more staff members. You can apply the "Only Assigned Staff" permission in User Role settings to restrict staff members to these files.<br><br>If there are assigned staff members then those members will be the only ones to receive notifications when a change is made to the file. If no staff are assigned to this file then anyone with the "Receive Alerts" permission will receive file change/comment alerts.',
		    )
	    ),
	);
    $fieldset_data['elements'][] = array(
	    'title' => 'Approval',
	    'fields' => array(
		    function() use (&$file, $file_id){
			    if(isset($file['approved_time'])){
                    switch($file['approved_time']){
                        case -1:
                            _e('File is waiting for approval. Please <a href="%s" target="_blank">click here</a> for options.',module_file::link_public($file_id));
                            break;
                        case 0:
                            _e('File has not been sent for approval.');
                            break;
                        default:
                            _e('File was approved at %s by %s',print_date($file['approved_time'],true),htmlspecialchars($file['approved_by']));
                    }
                }
		    }
	    ),
	);
    echo module_form::generate_fieldset($fieldset_data);
    unset($fieldset_data);


    hook_handle_callback('layout_column_half',2);



    $fieldset_data = array(
        'heading' =>  array(
            'title' => _l('File Description'),
            'type' => 'h3',
        ),
        'elements' => array(
            array(
                'field' => array(
                    'type' => 'wysiwyg',
                    'name' => 'description',
                    'value' => $file['description'],
                )
            )
        )
    );
    echo module_form::generate_fieldset($fieldset_data);
    unset($fieldset_data);
    
    if(module_file::can_i('edit','Files') && (int)$file_id > 0){
        module_email::display_emails(array(
            'title' => 'File Emails',
            'search' => array(
                'file_id' => $file_id,
            )
        ));
    }

    // file comments for url files
    if((int)$file_id > 0 && isset($file['file_url']) && strlen($file['file_url']) && module_file::can_i('view','File Comments')){
	    ob_start();
	    ?>
	    <div class="tableclass" style="margin:0 0 10px 0">
            <?php if(module_file::can_i('create','File Comments')){ ?>
            <textarea name="new_comment_text" style="width:98%;" class="no_permissions"></textarea>
            <?php } ?>
            <div style="text-align:right;">
                <?php if(module_file::can_i('create','File Comments')){ ?>
                <input type="submit" name="butt_save_note" id="butt_save_note" value="<?php echo _l('Add Comment'); ?>" class="submit_button no_permissions">
                <?php } ?>
                <input type="hidden" name="delete_file_comment_id" id="delete_file_comment_id" value="0" class="no_permissions">
            </div>
        </div>
        <?php foreach(module_file::get_file_comments($file_id) as $item){
	        $note_text = forum_text($item['comment']);

	        ?>
	        <div style="border-top:1px dashed #CCCCCC; padding:3px; margin:3px 0;">
	            <?php echo $note_text; ?>
	            <div style="font-size:10px; text-align:right; color:#CCCCCC;">From <?php echo $item['create_user_id'] ? module_user::link_open($item['create_user_id'],true) : _l('Customer'); ?> on <?php echo print_date($item['date_created'],true); ?>
	                <?php if(module_file::can_i('delete','File Comments') || $item['create_user_id'] == module_security::get_loggedin_id()){ ?>
	                <a href="#" onclick="if(confirm('<?php echo _l('Really remove this comment?'); ?>')){$('#delete_file_comment_id').val('<?php echo $item['file_comment_id']; ?>'); $('#butt_save_note').click(); } return false;" style="color:#FF0000">x</a>
	                <?php } ?>
	            </div>
	        </div>
	        <?php
	    }
	    $fieldset_data = array(
	        'heading' =>  array(
	            'title' => _l('File Comments'),
	            'type' => 'h3',
	        ),
	        'elements_before' => ob_get_clean()
	    );
	    echo module_form::generate_fieldset($fieldset_data);
	    unset($fieldset_data);
    }

    hook_handle_callback('layout_column_half','end');

    $form_actions = array(
        'class' => 'action_bar action_bar_center action_bar_single',
        'elements' => array(
            array(
                'type' => 'save_button',
                'name' => 'butt_save',
                'id' => 'butt_save',
                'value' => _l('Save File'),
            ),
            array(
                'type' => 'save_button',
                'name' => 'butt_email',
                'ignore' => !module_file::can_i('edit','File Approval'),
                'value' => _l('Email For Approval'),
            ),
            array(
                'ignore' => !((int)$file_id && module_file::can_i('delete','Files')),
                'type' => 'delete_button',
                'name' => 'butt_del',
                'value' => _l('Delete'),
            ),
            array(
                'type' => 'button',
                'name' => 'cancel',
                'value' => _l('Cancel'),
                'class' => 'submit_button',
                'onclick' => "window.location.href='".module_file::link_open(false)."';",
            ),
        ),
    );
    echo module_form::generate_form_actions($form_actions);


    if((int)$file_id > 0 && isset($file['file_path']) && $file['file_path'] && is_file($file['file_path']) && module_file::can_i('view','File Comments')){   ?>
        <h2><?php echo _l('File Comments'); ?></h2>

        <div>
            <div style="width:70%;float:left;border:1px solid #EFEFEF; overflow:auto;" id="file_preview">
                <?php
                echo module_file::generate_preview($file_id,$file['file_name'],$file);
                 ?>

            </div>
            <div style="width:29%; float:right;" id="file_notes">
                <div class="tableclass" style="margin:0 0 10px 0">
                    <?php if(module_file::can_i('create','File Comments')){ ?>
                    <textarea name="new_comment_text" style="width:98%;" class="no_permissions"></textarea>
                    <?php } ?>
                    <div style="text-align:right;">
                        <?php if(module_file::can_i('create','File Comments')){ ?>
                        <input type="submit" name="butt_save_note" id="butt_save_note" value="<?php echo _l('Add Comment'); ?>" class="submit_button no_permissions">
                        <?php } ?>
                        <input type="hidden" name="delete_file_comment_id" id="delete_file_comment_id" value="0" class="no_permissions">
                    </div>
                </div>
                <?php foreach(module_file::get_file_comments($file_id) as $item){
                $note_text = forum_text($item['comment']);
                if(preg_match_all('/#(\d+)/',$note_text,$matches)){
                    //
                    foreach($matches[1] as $digit){
                        $note_text = preg_replace('/#'.$digit.'([^\d]*)/','<span node_id='.$digit.' class="pointer-ids pointer-id-'.$digit.'">#'.$digit.'</span>$1',$note_text);
                    }
                }
                ?>
                <div style="border-top:1px dashed #CCCCCC; padding:3px; margin:3px 0;">
                    <?php echo $note_text; ?>
                    <div style="font-size:10px; text-align:right; color:#CCCCCC;">From <?php echo $item['create_user_id'] ? module_user::link_open($item['create_user_id'],true) : _l('Customer'); ?> on <?php echo print_date($item['date_created'],true); ?>
                        <?php if(module_file::can_i('delete','File Comments') || $item['create_user_id'] == module_security::get_loggedin_id()){ ?>
                        <a href="#" onclick="if(confirm('<?php echo _l('Really remove this comment?'); ?>')){$('#delete_file_comment_id').val('<?php echo $item['file_comment_id']; ?>'); $('#butt_save_note').click(); } return false;" style="color:#FF0000">x</a>
                        <?php } ?>
                    </div>
                </div>
                <?php
            }
                ?>
            </div>
            <div class="clear"></div>
        </div>
        <?php } ?>


</form>
