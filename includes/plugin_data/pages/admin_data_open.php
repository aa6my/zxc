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

// when a particular data entry is opened, this page is displayed.
// this reads the layout of the page structure from the database (configured through the drag/drop settings) and displays the info.

// we could pass rendering this type of layout off to one of the /layout/ 

// this file could be included multiple times from within itself, maybe... so we do some if(!isset()) checking first..

 
$view_revision_id = (isset($_REQUEST['revision_id'])) ? $_REQUEST['revision_id'] : false;


if(!isset($data_record_id) || !$data_record_id){
	$data_record_id = $_REQUEST['data_record_id'];
	if($data_record_id && $data_record_id != 'new'){
		$data_record = $module->get_data_record($data_record_id);
		$data_type_id = $data_record['data_type_id']; // so we dont have to pass it in all the time.
		$data_record_revision_id = $data_record['last_revision_id'];
		if($view_revision_id){
			$data_record_revision_id = $view_revision_id;
		}
		$data_items = $module->get_data_items($data_record_id,$data_record_revision_id);
		//$data_notes = $module->get_notes($data_record_id);
		$create_user = module_user::get_user($data_record['create_user_id']);
		/*if($data_record['create_user_id']){
			// hack to get the name of the login from a custom login box.
			$user_login = module_user::get_user($data_record['create_user_id']);
			if($user_login['name']){
				$create_user['name'] .= ' (' . htmlspecialchars($user_login['name']).')';
			}
		}*/
		$update_user = module_user::get_user($data_record['update_user_id']);
		/*if($data_record['update_user_id']){
			// hack to get the name of the login from a custom login box.
			$user_login = module_user::get_user($data_record['update_user_id']);
			if($user_login['name']){
				$update_user['name'] .= ' (' . htmlspecialchars($user_login['name']).')';
			}
		}*/
		$revision_count = 1; // get list of history.
		$data_record_revisions = $module->get_data_record_revisions($data_record_id);
		$revision_count = count($data_record_revisions);
		
		/*if(getlevel()!="administrator" && $data_record['create_user_id'] != $_SESSION['_user_id']){
			// dodgy security check. but works.
			echo 'Sorry, you do not have permission to access this record';
			exit;
		}*/

		// record that a record was accessed 
		$module->record_access($data_record_id);
		
	}else{
		// for printing otu the summary:
		$data_record['status'] = 'New';
		$data_record['data_record_id'] = $module->next_record_id();
		$data_record['create_ip_address'] = $_SERVER['REMOTE_ADDR'];
		$data_record['update_ip_address'] = '';
		$data_record['create_user_id'] = module_security::get_loggedin_id();
		$data_record['date_created'] = time();
		$data_record['date_updated'] = false;
		$data_record['parent_data_record_id'] = isset($_REQUEST['parent_data_record_id']) ? (int)$_REQUEST['parent_data_record_id'] : 0;
		$update_user = $create_user = module_user::get_user($data_record['create_user_id']);
		$data_record_revisions = array();
		$data_record_revision_id = false;
		$revision_count = 0;
		$data_items = array();
	}
}



if(!isset($data_type_id) || !$data_type_id){
	$data_type_id = isset($_REQUEST['data_type_id']) ? (int)$_REQUEST['data_type_id'] : false;
}
if(!isset($data_type) || !$data_type){
	if($data_type_id){
		$data_type = $module->get_data_type($data_type_id);
	}else{
		die('No data type, please try again');
	}
}

if(!$module->can_i('view',$data_type['data_type_name'])){
    die('no permissions');
}


if(!isset($data_field_groups) || !$data_field_groups){
	$data_field_groups = $module->get_data_field_groups($data_type_id);
}

$rendered_field_groups = array();

// starting work on form error handling:
$GLOBALS['form_id'] = 'data_form';


$mode = (isset($_REQUEST['mode']) && $_REQUEST['mode']) ? $_REQUEST['mode'] : 'view'; // edit revisions
$show_incident_menu = true; 

if(isset($_SESSION['admin_mode']) && $_SESSION['admin_mode'] && !isset($_REQUEST['mode'])){
	$mode = 'admin';
}

if(isset($_REQUEST['print'])){
	$show_incident_menu = false;
	ob_start();
}

?>

<div id="incident_nav_wrap">
	<?php if($show_incident_menu){ ?>
	<ul id="incident_nav">
		<?php if($data_record_id && $data_record_id != 'new'){ ?>
            <?php if($data_record['parent_data_record_id']){
                $parent_data_record = $module->get_data_record($data_record['parent_data_record_id']);
                ?>
            <li class="">
                <a href="<?php echo $module->link('',array(
                    'data_type_id' => $parent_data_record['data_type_id'],
                    'data_record_id' => $data_record['parent_data_record_id'],
                    'mode' => 'view',
                ));?>">&laquo; Return</a>
            </li>
            <?php } ?>
            <li class="<?php echo ($mode=='view')?'link_current':'';?>">
                <a href="<?php echo $module->link('',array(
                    'data_type_id' => $data_type_id,
                    'data_record_id' => $data_record_id,
                    'mode' => 'view',
                ));?>">View</a>
            </li>
            <?php if($module->can_i('edit',$data_type['data_type_name'])){ ?>
            <li class="<?php echo ($mode=='edit')?'link_current':'';?>">
                <a href="<?php echo $module->link('',array(
                    'data_type_id' => $data_type_id,
                    'data_record_id' => $data_record_id,
                    'mode' => 'edit',
                ));?>">Edit</a>
            </li>
            <?php } ?>
            <li class="<?php echo ($mode=='revisions')?'link_current':'';?>">
                <a href="<?php echo $module->link('',array(
                    'data_type_id' => $data_type_id,
                    'data_record_id' => $data_record_id,
                    'mode' => 'revisions',
                ));?>">Revisions</a>
            </li>
            <li>
                <a href="<?php echo $_SERVER['REQUEST_URI'].'&print=true';?>" onclick="window.open(this.href,'pop','width=900,height=700,scrollbars=1'); return false;">Print</a>
            </li>
            <?php if($module->can_i('edit',_MODULE_DATA_NAME)){ ?>
            <li class="<?php echo ($mode=='admin')?'link_current':'';?>">
                <a href="<?php echo module_data::link_open_data_type($data_type_id);?>">Settings</a>
            </li>
            <?php } ?>
		<?php }else{ /*?>
            <li class="<?php echo ($mode=='edit')?'link_current':'';?>">
                <a href="<?php echo $module->link('',array(
                    'data_type_id' => $data_type_id,
                    'data_record_id' => $data_record_id,
                    'mode' => 'edit',
                ));?>">Create</a>
            </li>
		<?php */ } ?>
	</ul>
	<?php } ?>
	<div id="incident_content">
	
		<div id="revisions" style="<?php if($mode!='revisions'){ ?>display:none;<?php } ?>">
			<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
				<thead>
				<tr class="title">
			    	<th><?php echo _l('Revision #'); ?></th>
			        <th><?php echo _l('Date'); ?></th>
			        <!-- <th><?php echo _l('Status'); ?></th> -->
			        <th><?php echo _l('User'); ?></th>
			        <th><?php echo _l('What Fields Were Changed'); ?></th>
			    </tr>
			    </thead>
			    <tbody>
			    <?php 
			    $x=1;
			    $c=1;
			    $current_revision = array();
			    $last_revision_id = false;
			    $next_revision_id = false;
			    $previous_revision_id = false;
			    $temp_revision_id = -1;
			    $custom_highlight_fields = array();
			    foreach($data_record_revisions as $data_record_revision){
					$user = module_user::get_user($data_record_revision['create_user_id']);
					if($previous_revision_id && !$next_revision_id){
						$next_revision_id = $data_record_revision['data_record_revision_id'];
					}
					if($data_record_revision['data_record_revision_id'] == $view_revision_id){
						$current_revision = $data_record_revision;
						$current_revision['number'] = $x;
						$previous_revision_id = $temp_revision_id;
					}
					$temp_revision_id = $data_record_revision['data_record_revision_id'];
					?>
			        <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
			            <td class="row_action"><a href="<?php echo $module->link('',array("data_type_id"=>$data_type_id,"data_record_id"=>$data_record_id,"revision_id"=>$data_record_revision['data_record_revision_id']));?>">#<?php echo $x++;?></a></td>
			            <td><?php echo print_date($data_record_revision['date_created'],true); ?></td>
			            <!-- <td><?php echo $data_record_revision['status'];?></td> -->
			            <td><?php echo $user['name'];?> (<?php echo $data_record_revision['create_ip_address'];?>)</td>
			            <td>
			            	<?php if($x==2){
			            		echo 'Initial Version';
			            	}else{
			            		// find out changed fields.
			            		$sql = "SELECT * FROM `"._DB_PREFIX."data_store` WHERE data_record_revision_id = '".$data_record_revision['data_record_revision_id']."' AND data_record_id = '".$data_record_id."'";
			            		$res = qa($sql);
			            		if(!count($res)){
			            			echo 'no changes';
			            		}
			            		foreach($res as $field){
			            			$field_data = @unserialize($field['data_field_settings']);
			            			echo isset($field_data['title']) ? $field_data['title'].',' : '';
			            		}
			            	}
			            	?>
			            </td>
			        </tr>
			      <?php } ?>
			      </tbody>
			</table>
		</div>
		<?php
		switch($mode){
			case 'view':
				// view the most recent revision, or the specified revision.
				if(!$view_revision_id){
					end($data_record_revisions);
					$current_revision = current($data_record_revisions);
					$current_revision['number'] = count($data_record_revisions);
					$view_revision_id = $current_revision['data_record_revision_id'];
					$current_revision = array(); // delete this if you want to display view revisions at the top.
				}
				if($current_revision && $view_revision_id){
					// user wants a custom revision, we pull out the custom $data_field_groups
					// and we tell the form layout to use the serialized cached field layout information
					$data_field_groups = unserialize($current_revision['field_group_cache']);
					// we dont always read from cache, because then any ui changes wouldn't be reflected in older reports (if we want to change older reports)
				}
				//if(isset($_REQUEST['notify']) && $_REQUEST['notify']){
				/*if($data_record_id && $data_record_id != 'new'){
					// process any post data.
					$notes = (isset($_REQUEST['notes'])) ? $_REQUEST['notes'] . "\n\n" : 'Status Change.';
					$extra_notes = '';
					if(isset($_REQUEST['send_email']) && is_array($_REQUEST['send_email'])){
						// send this report email to all selected users.
						// record who it was sent to in the notes section.
						if(count($_REQUEST['send_email'])){
							$extra_notes = ' Sent email to: ';
							require_once("includes/phpmailer/class.phpmailer.php");
							$mail = new PHPMailer();
						    $mail->SetLanguage("en", 'phpmailer/language/');
						    if(_MAIL_SMTP){
							    $mail->IsSMTP(); 
							    // turn on SMTP authentication 
							    $mail->SMTPAuth = _MAIL_SMTP_AUTH;     
							    $mail->Host     = _MAIL_SMTP_HOST; 
							    if(_MAIL_SMTP_AUTH){
								    $mail->Username = _MAIL_SMTP_USER;
								    $mail->Password = _MAIL_SMTP_PASS;
							    }
						    }
						    $mail->From     = $_SESSION['_user_email'];
					        $mail->FromName = $_SESSION['_user_name'];
						    $mail->Subject  = "Incident Report: ".$module->format_record_id($data_type_id,$data_record_id);
							if(isset($_REQUEST['status']) && $_REQUEST['status']=='Rejected'){
						    	$mail->Subject  = "Urgent Attention Required by: ".$create_user['name'].". Regarding Incident #".$module->format_record_id($data_type_id,$data_record_id);
							}
						    // turn on HTML emails:
					        $mail->isHTML(true);
					        ob_start();
					        include("includes/plugin_data/layouts/".basename($_REQUEST['email_template']).".html");
						    $mail->Body    = ob_get_clean();
						    foreach($_REQUEST['send_email'] as $user_id => $tf){
						    	$user = $plugins['user']->get_user($user_id);
						    	$mail->AddAddress($user['email']);
						    	$extra_notes .= $user['email'].' ';
						    }
						    if(!$mail->Send()){
						    	echo "Failed to send: " . $mail->ErrorInfo;
						        exit;
						    }
						}
					}
					if(isset($_REQUEST['status']) && $_REQUEST['status']){
						// we update the new status of the main record:
						$data = array();
						$data['data_record_id'] = $data_record_id;
						$data['status'] = $_REQUEST['status'];
						$data_record_id = update_insert('data_record_id',$data_record_id,'data_record',$data);
						if(!$data_record_id){
							echo(_l('Unable to save data record'));
							exit;
						}
						// we copy the cache from the previous revision, 
						$previous_revision = get_single('data_record_revision','data_record_revision_id',$view_revision_id);
						$data['notes'] = $notes.$extra_notes;
						$data['field_cache'] = $previous_revision['field_cache'];
						$data['field_group_cache'] = $previous_revision['field_group_cache'];
						$data_record_revision_id = update_insert('data_record_revision_id','new','data_record_revision',$data);
						if(!$data_record_revision_id){
							echo(_l('Unable to save data record revision sorry'));
							exit;
						}
						update_insert('data_record_id',$data_record_id,'data_record',array('last_revision_id'=>$data_record_revision_id));
						header("Location: ".$_SERVER['REQUEST_URI']);
						exit;
					}
					// check if the status has been set to anything worth processing later.
					switch(strtolower($data_record['status'])){
						case 'new':
							?>
							<h2>Thank You</h2>
							<form action="" method="post">
							<input type="hidden" name="send_email[<?php echo _ADMIN_USER_ID;?>]" value="true"> <!-- send email to admin, duplicate with other id's for who to receive -->
							<input type="hidden" name="email_template" value="email2">
							<input type="hidden" name="status" value="Pending Approval">
							<p>Thank you for submitting your incident report. Please review the information below and confirm it is all correct.</p>
							<p>Once you are happy with the information below, click the button to have the incident report sent to administration.</p>
							<p>If you need to make any changes to the below information, click the <strong>Edit</strong> button.</p>
							<input type="submit" name="submit_report" value="Submit Report" class="submit_button">
							</form>
							<br />
							<?php
							break;
						case 'rejected':
							$rejected_revision = get_single('data_record_revision','data_record_revision_id',$view_revision_id);
							?>
							<h2>Incident Report Rejected</h2>
							<form action="" method="post">
							<input type="hidden" name="send_email[<?php echo _ADMIN_USER_ID;?>]" value="true"> <!-- send email to admin, duplicate with other id's for who to receive -->
							<input type="hidden" name="email_template" value="email2">
							<input type="hidden" name="status" value="Pending Approval">
							<p>Thank you for submitting your incident report. Unfortunately we need more information before it can be accepted.</p>
							<pre><?php echo htmlspecialchars($rejected_revision['notes']);?></pre>
							<p>To make these changes, click the <strong>Edit</strong> button above.</p>
							<input type="submit" name="submit_report" value="Submit Report Again" class="submit_button">
							</form>
							<br />
							<?php
							break;
						case 'pending approval':
							?>
							<h2>Thank You</h2>
							<p>This incident has been submitted for review/approval. <a href="index.php">Click here</a> to continue.</p>
							<?php if(getlevel()=="administrator"){ ?>
							<table>
								<tr>
									<td valign="top">
										<form action="" method="post">
										<input type="hidden" name="status" value="Approved">
										<!-- should we send an email to the author thanking them here?? -->
										<input type="submit" name="submit_report" value="Approve Report" class="submit_button">
										</form>
									</td>
									<td valign="top">
										<form action="" id="reject_form_submit" method="post">
										<input type="hidden" name="status" value="Rejected">
										<input type="hidden" name="email_template" value="email3">
										<!-- send email back to original author -->
										<input type="hidden" name="notes" id="reject_notes_submit" value="">
										<input type="hidden" name="send_email[<?php echo $data_record['create_user_id'];?>]" value="yes"> 
										<input type="button" name="submit_report"  value="Reject Report" class="" id="reject_button" style="color:#FF0000">
										</form>
										
										
										<div id="reject-form" title="Reject Incident">
											<p>Please enter the reason this report is rejected. This will be sent to the incident author.</p>
											<form>
											<textarea name="reject_notes" id="reject_notes" class="form_field" style="width:300px;height:100px;"></textarea>
											</form>
										</div>
										<script type="text/javascript">
										$(function() {
											$("#reject-form").dialog({
												autoOpen: false,
												height: 300,
												width: 350,
												modal: true,
												buttons: {
													'Reject Report': function() {
														$('#reject_notes_submit').val($("#reject_notes").val());
														$(this).dialog('close');
														$('#reject_form_submit')[0].submit();
													},
													'Cancel': function() {
														$(this).dialog('close');
													}
												}
											});
											
											$('#reject_button')
												.button()
												.click(function() {
													$('#reject-form').dialog('open');
												});
										});
										</script>


									</td>
								</tr>
							</table>
							<?php } ?>
							<br />
							<?php
							break;
						case 'approved':
							?>
							<h2>Incident Approved - choose who to notify via email:</h2>
							<form action="" method="post">
							<input type="hidden" name="status" value="Complete">
							<input type="hidden" name="email_template" value="email1">
							<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
								<thead>
								<tr class="title">
							    	<th><?php echo _l('Select'); ?></th>
							    	<th><?php echo _l('Name'); ?></th>
							        <th><?php echo _l('Email'); ?></th>
							    </tr>
							    </thead>
							    <tbody>
							    <?php 
							    $c=0;
							    $users = $plugins['user']->get_users();
							    foreach($users as $user){ 
									$user = $plugins['user']->get_user($user['user_id']);
									?>
							        <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
							            <td>
							            	<input type="checkbox" name="send_email[<?php echo $user['user_id'];?>]" value="true">
							            </td>
							            <td><?php echo $user['name'];?></td>
							            <td><?php echo $user['email'];?></td>
							        </tr>
							      <?php } ?>
							      </tbody>
							</table>
                                <p>Optional Comment (this will be passed along with the email to the above selected people):</p>
                                <textarea rows="4" cols="60" name="notes"></textarea>
							<input type="submit" name="send_nofity" value="Send Emails / File Report" class="submit_button">
							</form>
							<br/>
							<?php
							break;
						case 'complete':
						case 'completed':
							?>
							<h2>Incident Filed</h2>
							<p>Incident has been completed and approved by administration.</p>
							<p>Click the button below to email this incident to another administrator.</p>
                                <form action="" method="post">
                                    <input type="hidden" name="status" value="Approved">
                                    <input type="submit" name="Email Incident to another Administrator" class="submit_button">
                                </form>
							<?php
							break;
					}
				}*/
				// continue onto admin/edit
            /*
			case 'admin':
				if($mode == 'admin'){
					$admin_edit_what = (isset($_REQUEST['admin_edit_what'])) ? $_REQUEST['admin_edit_what'] : 'position';
					?>
					<script type="text/javascript">
					$(function() {
						if(!/Firefox[\/\s](\d+\.\d+)/.test( navigator.userAgent )){
							alert('Please use firefox to see the admin area correctly');
						}
						<?php
						switch($admin_edit_what){
							case 'input':
								?>
								$("ul.data_group_fields li textarea").resizable({
									handles: 's, e',
									minHeight: 22,
									cancel: '.ui-state-disabled',
									stop: function(event, ui) {
										var data_field_id = ui.element.parents('li').attr('rel');
										$.post("index.php?m=data_settings&_process=save_ajax&type=input&data_field_id="+data_field_id, ui.size);
									}
								});
								$("ul.data_group_fields li input:text").resizable({
									handles: 'e',
									cancel: '.ui-state-disabled',
									stop: function(event, ui) {
										var data_field_id = ui.element.parents('li').attr('rel');
										$.post("index.php?m=data_settings&_process=save_ajax&type=input&data_field_id="+data_field_id, ui.size);
									}
								});

								$('.ui-resizable-handle').hover(
									function(){
										$(this).css('backgroundColor','#4f59d7');
									},
									function(){
										$(this).css('backgroundColor','');
									}
								);
								<?php
								break;
							case 'boundary':
								?>
								$("ul.data_group_fields li").resizable({
									//grid:[2,26],
									minHeight: <?php echo _MIN_INPUT_HEIGHT;?>,
									cancel: '.ui-state-disabled',
									stop: function(event, ui) {
										var data_field_id = ui.element.attr('rel');
										$.post("index.php?m=data_settings&_process=save_ajax&type=boundary&data_field_id="+data_field_id, ui.size);
									}
								});
								<?php
								break;
							case 'position':
							default:
								?>
								$("ul.data_group_fields").sortable({
									cancel: '.ui-state-disabled',
									update: function(event, ui) {
										var holder = $(ui.item[0]).parent();
										var data_field_group_id = holder.attr('rel');
										var order = holder.sortable('serialize');
			      						$.post("index.php?m=data_settings&_process=save_ajax&type=position&data_field_group_id="+data_field_group_id, order);
									}
								});
								$("ul.data_group_fields").disableSelection();
								<?php
								break;
						}
						?>

					});
					</script>
					<style type="text/css">
					li.data_field{
						border:1px dashed #CCC !important;
					}
					</style>
					<h2 style="color:#FF0000;">Administration Mode</h2>
					<p>You are currently in Administration mode. Please <a href="index.php?m=data&_process=admin_leave">click here</a> to leave administration mode.</p>
					<p>
						<form action="" method="post">
						Choose what you would like to administer below:
							<select name="admin_edit_what" onchange="this.form.submit();">
							<option value="position"<?php echo ($admin_edit_what=='position')?' selected':'';?>>Position of elements</option>
							<option value="input"<?php echo ($admin_edit_what=='input')?' selected':'';?>>Size of input boxes</option>
							<option value="boundary"<?php echo ($admin_edit_what=='boundary')?' selected':'';?>>Size of boundry boxes</option>
							</select>
							<input type="submit" name="go" value="Go">
						</form>
					</p>
					<br/>

					<?php
				}
				// continue onto edit.
            */
			case 'edit':
				// edit the latest revision.

				
				if($view_revision_id && $current_revision){


                    print_heading(array(
                        'type' => 'h2',
                        'title' => 'Viewing Revision: #' . $current_revision['number'].' - '. print_date($current_revision['date_created']),
                    ));

					?>
					<!--
					<a href="<?php echo $module->link('',array("data_type_id"=>$data_type_id,"data_record_id"=>$data_record_id));?>">&laquo; Cancel and return to editor</a> <br>
					-->
					
					<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
					<thead>
					<tr class="title">
				    	<th><?php echo _l('Revisions'); ?></th>
				        <th><?php echo _l('Date'); ?></th>
				        <th><?php echo _l('User'); ?></th>
				        <th><?php echo _l('What Changed'); ?></th>
				    </tr>
				    </thead>
				    <tbody>
						<tr class="odd">
							<td valign="top">
								<?php if($previous_revision_id>0){ ?>
									<?php echo create_link("&laquo; Previous","link",$module->link('',array("data_type_id"=>$data_type_id,"data_record_id"=>$data_record_id,"revision_id"=>$previous_revision_id))); ?>
								<?php } ?>
								
								<?php if($next_revision_id){ 
									echo create_link("Next &raquo;","link",$module->link('',array("data_type_id"=>$data_type_id,"data_record_id"=>$data_record_id,"revision_id"=>$next_revision_id))); 
								} ?>
							</td>
							<td><?php echo print_date($current_revision['date_created'],true); ?></td>
				            <td><?php echo $user['name'];?> (<?php echo $current_revision['create_ip_address'];?>)</td>
				            <td>
				            	<?php if($current_revision['number']==1){
				            		echo 'Initial Version';
				            	}else{
				            		// find out changed fields.
				            		$sql = "SELECT * FROM `"._DB_PREFIX."data_store` WHERE data_record_revision_id = '".$current_revision['data_record_revision_id']."' AND data_record_id = '".$data_record_id."'";
				            		$res = qa($sql);
				            		if(!count($res)){
				            			echo 'no changes';
				            		}
				            		foreach($res as $field){
				            			//if($current_revision['data_record_revision_id'] == $view_revision_id){
				            				$custom_highlight_fields[$field['data_field_id']]=true;
				            			//}
				            			$field_data = unserialize($field['data_field_settings']);
				            			echo $field_data['title'].',';
				            		}
				            	}
				            	?>
				            </td>
						</tr>
						</tbody>
					</table>
					
					<?php
				}

                $module->page_title = htmlspecialchars($data_type['data_type_name']);

                print_heading(array(
                    'main' => true,
                    'type' => 'h2',
                    'title' => htmlspecialchars($data_type['data_type_name']),
                ));
				?>
					

				<form action="" method="post" class="validate" enctype="multipart/form-data">
				<?php if(!$view_revision_id){ ?>
				<input type="hidden" name="form_id" value="<?php echo $GLOBALS['form_id'];?>">
				<input type="hidden" name="_process" value="save_data_record" />
				<input type="hidden" name="_redirect" value="<?php echo $module->link("",array("saved"=>true,"data_type_id"=>$data_type_id,"data_record_id"=>$data_record_id)); ?>" />
				<input type="hidden" name="data_record_id" value="<?php echo $data_record_id; ?>" />
				<input type="hidden" name="parent_data_record_id" value="<?php echo (int)$data_record['parent_data_record_id']; ?>" />
				<input type="hidden" name="data_type_id" value="<?php echo $data_type_id; ?>" />
				<input type="hidden" name="data_save_hash" value="<?php echo $module->save_hash($data_record_id,$data_type_id); ?>" />
                    <?php foreach($module->get_data_link_keys() as $key){
                        if(isset($_REQUEST[$key])){
                            ?>
                            <input type="hidden" name="<?php echo $key;?>" value="<?php echo (int)$_REQUEST[$key];?>">
                            <?php
                        }
                    }
                }
                if(!$data_type['parent_data_type_id']){
                    if(isset($_REQUEST['print'])){ ?>

                        <div class="data_summary">
                            <table>
                                <tbody>
                                    <tr>
                                        <th>Record ID:</th>
                                        <td>
                                            <?php echo $module->format_record_id($data_type_id,$data_record['data_record_id']);?>
                                        </td>
                                        <th>Created:</th>
                                        <td>
                                            <?php echo print_date($data_record['date_created'],true);?>
                                        </td>
                                        <th>Status:</th>
                                        <td>
                                            <?php echo $data_record['status'];?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    <?php }else{ ?>
                    <div class="data_summary" style="display:none">
                        <table>
                            <tbody>
                                <tr>
                                    <th>Record ID:</th>
                                    <td>
                                        <?php echo $module->format_record_id($data_type_id,$data_record['data_record_id']);?>
                                    </td>
                                    <th>Create:</th>
                                    <td>
                                        <?php echo print_date($data_record['date_created'],true);?>
                                    </td>
                                    <th>Update:</th>
                                    <td>
                                        <?php echo print_date($data_record['date_updated'],true);?>
                                    </td>
                                    <th>Status:</th>
                                    <td>
                                        <?php echo $data_record['status'];?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Revisions:</th>
                                    <td>
                                        <?php echo $revision_count;?>
                                        <?php /*if($revision_count){ ?>
                                        <a href="#" onclick="$('#revisions').toggle(); return false;">(view)</a>
                                        <?php }*/ ?>
                                    </td>
                                    <th>Create:</th>
                                    <td>
                                        <?php echo $create_user['name'];?>
                                        <?php echo $data_record['create_ip_address'];?>
                                    </td>
                                    <th>Update:</th>
                                    <td>
                                        <?php echo $update_user['name'];?>
                                        <?php echo $data_record['update_ip_address'];?>
                                    </td>
                                    <?php if($data_record_revision_id){ ?>
                                    <th>Notes:</th>
                                    <td>
                                        <?php
                                        $last_revision = get_single('data_record_revision','data_record_revision_id',$data_record_revision_id);
                                        echo nl2br(htmlspecialchars($last_revision['notes']));
                                        ?>
                                    </td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
				<?php }
                }

				
				// time to format the fields onto the page.
				// fields goes into groups.
				
				foreach($data_field_groups as $data_field_group){
					$data_field_group_id = $data_field_group['data_field_group_id'];
					include('render_group.php');
				}
                if(!$view_revision_id){ ?>

                    <p>
                    <?php /*if($revision_count){ ?>
                    <tr>
                        <td align="right" valign="top">
                            Please <strong>briefly</strong> explain <br />the changes you made above:
                        </td>
                        <td valign="top" align="left">
                            <!-- same layout as data field so that js required field check works here too -->
                            <div class="data_field data_field_text" id="data_field_1">
                                <span class=""></span>
                                <span class="">
                                    <textarea name="notes" id="notes" class="form_field_required form_field<?php if(isset($_SESSION['_form_highlight'][$GLOBALS['form_id']]) && isset($_SESSION['_form_highlight'][$GLOBALS['form_id']]['notes'])) echo ' form_field_highlight';?>"><?php echo (isset($_REQUEST['notes'])) ? htmlspecialchars($_REQUEST['notes']) : '';?></textarea>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <?php }*/ ?>
                    <!--<tr>
                        <td align="right" valign="top">
                            Incident Status:
                        </td><td valign="top" align="left">
                            <?php /*echo print_select_box($module->get_data_statuses(),'status',$data_record['status'],'',false,'',true); */?>
                        </td>
                    </tr>-->


                            <input type="submit" name="butt_save" id="butt_save" value="<?php echo _l('Save Information'); ?>" class="submit_button save_button">
                        <?php if((int)$data_record_id > 0 && $module->can_i('delete',$data_type['data_type_name'])){ ?>
                            <input type="submit" name="butt_del" id="butt_del" value="<?php echo _l('Delete'); ?>" class="submit_button delete_button">
                        <?php } ?>
                            <input type="button" name="cancel" value="<?php echo _l('Cancel'); ?>" onclick="window.location.href='<?php
                            if($data_record['parent_data_record_id']){
                                echo $module->link('',array("data_record_id"=>$data_record['parent_data_record_id']));
                            }else{
                                echo $module->link('',array('data_type_id'=>$data_type_id)); }
                            ?>';" class="submit_button" />
</p>
				<?php } ?>
				
				</form>
				
				<hr class="clear">
				<?php
				break;
		}
		?>	
	</div>
</div>

<?php
$_SESSION['_form_highlight'][$GLOBALS['form_id']] = array();

if(isset($_REQUEST['print'])){
	$content = ob_get_clean();
	?>
	<html>
	<head>
		<title>Print</title>
		<link rel="stylesheet" href="css/styles.css" type="text/css" />
		<link type="text/css" href="css/ui-lightness/jquery-ui-1.8.1.custom.css" rel="stylesheet" />	
		<script type="text/javascript" src="js/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.1.custom.min.js"></script>

		<style type="text/css">
		body{
		background-color:#FFFFFF !important;
		}
		.data_field_view{
		background-color:#FFFFFF !important;
		border:1px solid #EFEFEF;
		}
		th,td{
		font-size:12px;
		}
		.hidden{
		display:none;
		}
		</style>
	</head>
	<body>
		<input type="button" name="print" value="Click here to print" onclick="$(this).hide(); window.print(); ">
		<?php echo $content;?>
	</body>
	</html>
	<?php
	exit;
}
?>


<script type="text/javascript">
<?php if($show_incident_menu){ ?> 
// hacky tabs classes:
$('#incident_nav_wrap').addClass('ui-tabs ui-corner-all'); //ui-widget-content ui-widget 
$('#incident_nav').addClass('ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all');
$('#incident_nav li').addClass('ui-state-default ui-corner-top');
$('#incident_nav li.link_current').addClass('ui-tabs-selected ui-state-active');
<?php } ?> 
$('#incident_content').addClass('ui-tabs-panel ui-corner-bottom'); //ui-widget-content 

</script>