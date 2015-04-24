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

@set_time_limit(0);
if(!module_config::can_i('view','Upgrade System')){
    echo 'upgrade denied. ssorry';
    exit;
}

module_config::register_css('config','upgrade.css',11);
module_config::register_js('config','upgrade.js',11);


// quick hack to get custom files.
$held_files = array();
if(is_file(_UCM_FOLDER.'upgrade_ignore.txt')){
    foreach(file(_UCM_FOLDER.'upgrade_ignore.txt') as $held_file){
        $held_files[]=trim($held_file);
    }
}

if(!isset($setup_upgrade_hack)){
    print_heading(array(
        'title'=>'Upgrade System',
    'type' => 'h2',
    'main' => true,
    ));
}


if(isset($_REQUEST['save_license_codes']) && isset($_REQUEST['license_codes']) && is_array($_REQUEST['license_codes'])){
    $license_codes = '';
    foreach($_REQUEST['license_codes'] as $license_code){
        $license_code= trim($license_code);
        if(strlen($license_code)>5 || $license_code == 'free')$license_codes .= $license_code.'|';
    }
    $license_codes = rtrim($license_codes,'|');
    module_config::save_config('_installation_code',$license_codes);
}
if(isset($_REQUEST['process_upgrade']) && isset($_REQUEST['via_ajax'])){
	ob_end_clean();
	header("Content-type: text/javascript");
	$result = array(
		'message' => ''
	);
	$new_system_version = module_config::current_version();
    $fail = false;
	if(isset($plugins[$_REQUEST['process_upgrade']])){
		$result['message'] .= "Processing update: <span style='text-decoration:underline;'>".$_REQUEST['process_upgrade']."</span> - Current Version: ".$plugins[$_REQUEST['process_upgrade']]->get_plugin_version().".... ";
		ob_start();
		if($version = $plugins[$_REQUEST['process_upgrade']]->install_upgrade()){
            $result['message'] .= '<span class="success_text">all good</span>';
            $new_system_version = max($version,$new_system_version);
			$plugins[$_REQUEST['process_upgrade']]->init();
            // lol typo - oh well.
            $plugins[$_REQUEST['process_upgrade']]->set_insatlled_plugin_version($version);
        }else{
            $fail = true;
            $result['message'] .= '<span class="error_text">failed</span> ';
        }
		$result['message'] .= ob_get_clean().'<br/>';
		$result['message'] .= '<br/>';
	    if($fail){
	        $result['message'] .= _('Some things failed. Please go back and try again.');
	    }else{
			$result['message'] .= '<strong>'._l('Success! Everything worked.').'</strong>';
	        module_config::set_system_version($new_system_version);
	        module_config::save_config('last_update',time());
	    }
		if(isset($_SESSION['_message']) && count($_SESSION['_message'])){
			$result['message'] .= '<br/>';
			$result['message'] .= implode('<br/>', $_SESSION['_message']);
			unset($_SESSION['_errors']);
		}
		if(isset($_SESSION['_errors']) && count($_SESSION['_errors'])){
			$result['message'] .= '<br/>';
			$result['message'] .= implode('<br/>', $_SESSION['_errors']);
			unset($_SESSION['_errors']);
		}
    }
	if(!$fail){
		$result['success'] = 1;
	}
	echo json_encode($result);
	exit;
}else if(isset($_REQUEST['install_upgrade']) && isset($_REQUEST['via_ajax'])){
	ob_end_clean();
	header("Content-type: text/javascript");
	$result = array(
		'message' => '',
		'plugin_name' => isset($_REQUEST['plugin_name']) ? $_REQUEST['plugin_name'] : '',
	);
    $errors = array();

	if(!strlen(trim(module_config::c('_installation_code')))){
        $result['message'] = 'Please enter your license code before doing an upgrade.';
    }else if(!isset($_REQUEST['doupdate']) || !is_array($_REQUEST['doupdate'])){
        $result['message'] = 'please select at least one upgrade to perform';
    }else{
		$available_updates = module_config::check_for_upgrades();
	    if($available_updates && isset($available_updates['message']) && strlen(trim($available_updates['message']))>2){
	        $result['message'] = $available_updates['message'];
	    }else{
		    $upgrade_plugins = $_REQUEST['doupdate'];
		    $completed_updates = array();
		    foreach($upgrade_plugins as $plugin_name){
			    $this_update = array(
				    'key' => $plugin_name,
			    );
		        /*$this_update = false;
		        foreach($available_updates['plugins'] as $available_update){
		            if($available_update['key'] == $plugin_name){
		                $this_update = $available_update;
		                break;
		            }
		        }*/
		        if($this_update && strlen(trim($this_update['key']))){
			        $result['message'] .= "Downloading files... <br/>\n";
			        if ( $update = module_config::download_update( $this_update['key'] ) ) {
						foreach($update['plugins'] as $available_update){
		                    if(
		                        $available_update['key'] != $this_update['key']
		                        //(!isset($available_update['linked_key']) && $available_update['key'] != $this_update['key']) ||
		                        //(isset($available_update['linked_key']) && $available_update['linked_key'] != $this_update['key'])
		                    ){
		                        // core update bug fix.
		                        continue;
		                    }
		                    // have we done this yet?
		                    if(isset($completed_updates[$available_update['key']]))continue;
		                    $completed_updates[$available_update['key']] = true;

		                    foreach($available_update['folders'] as $file){
			                    if(!is_dir(_UCM_FOLDER.$file)) {
				                    $result['message'] .= "Folder: $file <br/>\n";
				                    $result['message'] .= '<span class="small">';
				                    if ( is_dir( _UCM_FOLDER . $file ) ) {
					                    $result['message'] .= 'this folder exists, nothing will change.';
				                    } else {
					                    // check if writable
					                    if ( mkdir( _UCM_FOLDER . $file, 0777, true ) ) {
						                    $result['message'] .= 'this new folder has been <strong>created</strong>';
					                    } else {
						                    $error    = '<strong>WARNING:</strong> failed to create new folder: ' . $file;
						                    $errors[] = $error;
						                    $result['message'] .= $error;
					                    }
				                    }
				                    $result['message'] .= "</span><br/>";
			                    }
		                    }
							foreach($available_update['files'] as $file){
								$result['message'] .= "File: $file <br/>\n";
			                    $result['message'] .= '<span class="small">';
			                    if(in_array($file,$held_files)){
	                                $error = '<strong>Custom:</strong> file not upgraded: '.$file;
	                                $errors[] = $error;
                                    $result['message'] .= $error;
	                            }else if(!isset($available_update['file_contents'][$file])){
	                                $error = '<strong>WARNING:</strong> failed to get file contents from server: '.$file;
	                                $errors[] = $error;
                                    $result['message'] .= $error;
	                            }else if(!file_put_contents(_UCM_FOLDER.$file,base64_decode($available_update['file_contents'][$file]))){
	                                $error = '<strong>WARNING:</strong> failed to install the file: '.$file;
	                                $errors[] = $error;
                                    $result['message'] .= $error;
	                            }else{
                                    $result['message'] .= 'this file has been <strong>installed</strong> successfully';
	                            }
			                    $result['message'] .= "</span><br/>";
							}
		                }
		                //exit;
		            }else{
			            $error = "failed to download files ($plugin_name):";
			            $errors[] = $error;
		                $result['message'] .=  '<span class="error_text">'.$error.'</span><br/> ';
		            }
		            //$_REQUEST['run_upgrade'] = true; // so we do the DB update again down the bottom.
		        }else{
		            $error = "Failed to start update ($plugin_name):";
		            $errors[] = $error;
			        $result['message'] .= $error.'<br/>';
		        }
		    }
		}
	}

	$result['errors'] = $errors;
	if(!count($errors)) {
		$result['success'] = 1;
	}
	echo json_encode($result);
	exit;

}else if(isset($_REQUEST['install_upgrade'])){

    if(!strlen(trim(module_config::c('_installation_code')))){
        ?>
        Please enter your license code before doing an upgrade.
        <?php
        exit;
    }

    if(!isset($setup_upgrade_hack) && (!isset($_REQUEST['doupdate']) || !is_array($_REQUEST['doupdate']))){
        echo 'please select at least one upgrade to perform';
        exit;
    }
    $available_updates = module_config::check_for_upgrades();

    if(isset($setup_upgrade_hack) && $available_updates && isset($available_updates['message']) && strlen(trim($available_updates['message']))>2){
        echo $available_updates['message'];
        ?>
        <input type="button" name="go" value="<?php _e('Try Installation Again');?>" class="submit_button save_button" onclick="window.location.href='?m=setup&step=3'">
        <?php
        return;
    }

    if($available_updates && isset($available_updates['plugins'])){ ?>
        <p>When this is complete please click the "Continue Installation" button at the very bottom of the page.</p>
    <?php }

    $errors = array();

    // grab the requested plugins and process each one at a time outputting the result to the page

    if(isset($setup_upgrade_hack)){
        $upgrade_plugins = array();
        if($available_updates && isset($available_updates['plugins'])){
            foreach($available_updates['plugins'] as $available_update){
                $upgrade_plugins[$available_update['key']] = true;
            }
        }
    }else{
        $upgrade_plugins = $_REQUEST['doupdate'];
    }

    $completed_updates = array();

    foreach($upgrade_plugins as $plugin_name => $tf){

        $this_update = false;
        foreach($available_updates['plugins'] as $available_update){
            if($available_update['key'] == $plugin_name){
                $this_update = $available_update;
                break;
            }
        }
        if($this_update){
            echo "Downloading files: <span style='text-decoration:underline;'>".htmlspecialchars($this_update['description'])."</span>... ";

            if($update = module_config::download_update($this_update['key'])){
                ?>
                <span class="success_text">complete!</span>
                <a href="#" onclick="$('#update_<?php echo $this_update['key'];?>').toggle();">[view details]</a>
                <div style="font-weight: 0.9em; padding:9px; display: none;" id="update_<?php echo $this_update['key'];?>">
                <?php
                //echo '<pre>';print_r($update);exit;
                foreach($update['plugins'] as $available_update){

                    if(
                        $available_update['key'] != $this_update['key']
                        //(!isset($available_update['linked_key']) && $available_update['key'] != $this_update['key']) ||
                        //(isset($available_update['linked_key']) && $available_update['linked_key'] != $this_update['key'])
                    ){
                        // core update bug fix.
                        continue;
                    }
                    // have we done this yet?
                    if(isset($completed_updates[$available_update['key']]))continue;
                    $completed_updates[$available_update['key']] = true;

                    foreach($available_update['folders'] as $file){ ?>
                        Folder: <?php echo $file;?>
                        <span class="small">
                            <?php
                            if(is_dir(_UCM_FOLDER.$file)){
                                echo 'this folder exists, nothing will change.';
                            }else{
                                // check if writable
                                if(mkdir(_UCM_FOLDER.$file,0777,true)){
                                    echo 'this new folder has been <strong>created</strong>';
                                }else{
                                    $error = '<strong>WARNING:</strong> failed to create new folder: '.$file;
                                    $errors[] = $error;
                                    echo $error;
                                }
                            }
                            ?>
                        </span><br>
                    <?php } ?>
                    <?php foreach($available_update['files'] as $file){ ?>
                        File: <?php echo $file;?>
                        <span class="small">
                            <?php
                            if(in_array($file,$held_files)){
                                $error = '<strong>Custom:</strong> file not upgraded: '.$file;
                                $errors[] = $error;
                            }else if(!isset($available_update['file_contents'][$file])){
                                $error = '<strong>WARNING:</strong> failed to get file contents from server: '.$file;
                                $errors[] = $error;
                                echo $error;
                            }else if(!file_put_contents(_UCM_FOLDER.$file,base64_decode($available_update['file_contents'][$file]))){
                                $error = '<strong>WARNING:</strong> failed to install the file: '.$file;
                                $errors[] = $error;
                                echo $error;
                            }else{
                                echo 'this file has been <strong>installed</strong> successfully';
                            }
                        ?>
                        </span><br>
                    <?php }
                }
                ?>
                </div>
                <?php
                //exit;
            }else{
	            $error = "failed to download files ($plugin_name):";
	            $errors[] = $error;
                echo '<span class="error_text">'.$error.'</span> ';
            }
            echo '<br>';
            //$_REQUEST['run_upgrade'] = true; // so we do the DB update again down the bottom.
        }else{
            $error = "Failed to start update ($plugin_name):";
            $errors[] = $error;
            echo $error;
        }
    }

    if($errors){ ?>
	    <div class="warning">
	        <ul>
	            <?php foreach($errors as $error){ ?>
	            <li><?php echo $error;?></li>
	            <?php } ?>
	        </ul>
	    </div>

	    <form action="" method="post">
	        <input type="hidden" name="install_upgrade" value="true">
	        <?php if(!isset($setup_upgrade_hack)){
	        foreach($upgrade_plugins as $key=>$val){
	        ?>
	            <input type="hidden" name="doupdate[<?php echo htmlspecialchars($key);?>]" value="1">
	        <?php } ?>
	        <?php } ?>
	        <input type="submit" name="go" value="<?php _e('Try Installation Again');?>" class="submit_button save_button">
	    </form>

	    <?php
    }

    ?>
    Update has completed! Please click the button below to finish installation:

	<form action="" method="post">
	    <input type="hidden" name="run_upgrade" value="true">
	    <input type="submit" name="go" value="<?php _e('Complete Upgrade Process');?>" class="submit_button">
	</form>
    <?php


}else if(isset($_REQUEST['download_upgrade'])){

    if(_DEMO_MODE){
        echo 'Sorry, downloading updates as a zip file is not allowed in demo mode :)';
        echo ' If you do find a demo mode bug though, please let me know. Cheers!';
        exit;
    }

}else if(isset($_REQUEST['check_upgrade'])){

    if(!strlen(trim(module_config::c('_installation_code')))){
        ?>
        Please enter your license code before doing an upgrade.
        <?php
        exit;

    }
    // check for any available upgrades on the internet.
    $available_updates = module_config::check_for_upgrades();

    if(isset($available_updates['message'])){
        echo $available_updates['message'];
    }

    if($available_updates && isset($available_updates['plugins']) && count($available_updates['plugins'])){
        $x=0;
        $errors = array();
        ?>

        <form action="" method="post">
	        We found <?php count($available_updates['plugins']);?> available updates! <br>
	        <!-- This will upgrade your system from version <?php echo module_config::current_version(); ?> to <?php echo $available_updates['new_version']; ?> <br> -->

	        Please select which updates you would like to install: <br/>
	        <em>Recommended Updates are already ticked, if nothing is ticked then you don't need to upgrade just yet (come back later).</em>

	        <table class="tableclass tableclass_rows">
	            <thead>
	            <tr>
	                <th>Update</th>
	                <th>Installed Version</th>
	                <th>Available Version</th>
	                <th>Description</th>
	                <th>Files to update</th>
	            </tr>
	            </thead>
	            <tbody>
	            <?php foreach($available_updates['plugins'] as $available_update){

	                // update - remove any folders that already exist.
	                // reduces the count of the updates much nicer.
	                foreach($available_update['folders'] as $folder_id => $file){
	                    if(is_dir(_UCM_FOLDER.$file)){
	                        //echo 'this folder exists, nothing will change.';
	                        unset($available_update['folders'][$folder_id]);
	                    }
	                }

	                ?>
	            <tr class="<?php echo $x++%2 ? 'odd' : 'even';?>">
	                <td class="update_checkbox">
	                    <input type="checkbox" name="doupdate[<?php echo $available_update['key'];?>]" data-plugin="<?php echo $available_update['key'];?>" value="1" <?php echo isset($available_update['recommended']) && $available_update['recommended'] ? 'checked="checked"' : '';?>>
	                </td>
	                <td>
	                    <?php echo $available_update['installed_version'];?>
	                </td>
	                <td>
	                    <?php echo $available_update['available_version'];?>
	                </td>
	                <td>
	                    <?php echo htmlspecialchars($available_update['description']);?>
		                <div class="update_progress"><?php _e('Pending...');?></div>
	                </td>
	                <td>
	                    <?php echo count($available_update['files']);?> files,
	                    <?php echo count($available_update['folders']);?> folders:
	                    <a href="#" onclick="$('#update_<?php echo $available_update['key'];?>').toggle(); return false;">view</a>
	                    <div id="update_<?php echo $available_update['key'];?>" style="display:none;">
	                    <?php foreach($available_update['folders'] as $file){ ?>
	                        Folder: <?php echo $file;?><br>
	                            <span class="small">
	                                <?php if(is_dir(_UCM_FOLDER.$file)){
	                                    echo 'this folder exists, nothing will change.';
	                                }else{
	                                    // check if writable
	                                    $dir = _UCM_FOLDER.dirname($file);
	                                    if(mkdir(_UCM_FOLDER.$file)){
	                                        echo 'this new folder will be <strong>created</strong>';
	                                    }else{
	                                        $error = '<strong>WARNING:</strong> no permissions to create folder: '.$file;
	                                        $errors[] = $error;
	                                        echo $error;
	                                    }
	                                }
	                                ?>
	                            </span><br>
	                    <?php } ?>
	                    <?php foreach($available_update['files'] as $file){ ?>
	                        File: <?php echo $file;?><br>
	                            <span class="small">
	                            <?php if(is_file(_UCM_FOLDER.$file)){
	                                if(in_array($file,$held_files)){
	                                    $error = '<strong>Custom:</strong> this custom file will not be automatically upgraded: '.$file;
	                                    $errors[] = $error;
	                                }else if(is_writable(_UCM_FOLDER.$file)){
	                                    echo 'this file exists and will be <strong>upgraded</strong>.';
	                                }else{
	                                    $error = '<strong>WARNING:</strong> no permissions to update file: '.$file;
	                                    $errors[] = $error;
	                                    echo $error;
	                                }
	                            }else{
	                                // check if holding folder is writable
	                                // edit: only if parent folder exists.
	                                $dir = _UCM_FOLDER.dirname($file);
	                                if(is_dir($dir)){
	                                    if(is_writable($dir)){
	                                        echo 'this new file will be <strong>created</strong>';
	                                    }else{
	                                        $error = '<strong>WARNING:</strong> no permissions to create file: '.$file;
	                                        $errors[] = $error;
	                                        echo $error;
	                                    }
	                                }
	                            }
	                            ?>
	                            </span><br>
	                    <?php } ?>
	                    </div>

	                </td>
	            </tr>
	            <?php } ?>
	            </tbody>
	        </table>
            <?php if($errors){ ?>
            <div class="warning">
	            <ul>
	            <?php foreach($errors as $error){ ?>
	                <li><?php echo $error;?></li>
	            <?php } ?>
	            </ul>
            </div>
            <?php } ?>

            <br>
			<?php if(module_config::c('upgrade_via_ajax',1)){ ?>
	        <script type="text/javascript">
		        if(typeof ucm.upgrade != 'undefined'){
			        ucm.upgrade.upgrade_url = '<?php echo _BASE_HREF . '?m=config&p=config_upgrade';?>';
			        ucm.upgrade.lang.processing = '<?php _e('Processing, please wait...');?>';
					$(function(){
					    ucm.upgrade.init();
					});
				}else{
			        alert('Upgrade javascript failed to load. Please report this issue to support');
		        }
	        </script>

            <input type="button" name="install_upgrade" value="<?php _e('Start the Upgrade');?>" id="upgrade_start" class="submit_button save_button">
		    <?php }else{ ?>
		    <input type="submit" name="install_upgrade" value="<?php _e('Start the Upgrade');?>" id="upgrade_start" class="submit_button save_button">
		    <?php } ?>
        </form>

        <?php

    }else{
        ?>
        No upgrade available at this time.
        <br>
        <?php
        if(isset($setup_upgrade_hack)){
            ?>
            <input type="button" name="go" value="<?php _e('Continue');?>" class="submit_button" onclick="window.location.href='?m=setup&step=4';">
            <?php
        }
    }
}else if(isset($_REQUEST['run_upgrade'])){

    $new_system_version = module_config::current_version();
    $fail = false;
    $set_versions = array();
    foreach($plugins as $plugin_name => &$p){
        echo "Checking plugin: <span style='text-decoration:underline;'>$plugin_name</span> - Current Version: ".$p->get_plugin_version().".... ";
        if($version = $p->install_upgrade()){
            echo '<span class="success_text">all good</span>';
            $set_versions[$plugin_name] = $version;
            $new_system_version = max($version,$new_system_version);
        }else{
            $fail = true;
            echo '<span class="error_text">failed</span> ';
        }
        echo '<br>';
    }
    // all done?

    //if(isset($set_versions['config'])){
        // config db worked.
        foreach($plugins as $plugin_name => &$p){
            if(isset($set_versions[$plugin_name])){
                $p->init();
                // lol typo - oh well.
                $p->set_insatlled_plugin_version($set_versions[$plugin_name]);
            }
        }
    //}
    if($fail){
        print_header_message();
        echo '<br><br>';
        _e('Some things failed. Please go back and try again');
    }else{
        echo '<br><br><strong>';
        _e('Success! Everything worked.');
        echo '</strong>';
        module_config::set_system_version($new_system_version);
        module_config::save_config('last_update',time());
    }

    if(isset($setup_upgrade_hack)){
        ?>
        <input type="button" name="go" value="<?php _e('Continue');?>" class="submit_button" onclick="window.location.href='?m=setup&step=4';">
        <?php
    }

}else{

    $license_codes = explode('|',module_config::c('_installation_code',''));
    foreach($license_codes as $license_code_id => $license_code){
        if(!trim($license_code)){
            unset($license_codes[$license_code_id]);
        }
    }
    if(!count($license_codes)){
        $license_codes[]='';
    }


    hook_handle_callback('layout_column_half',1);


    if(class_exists('module_backup',false) && module_config::c('backup_time',0) < time() - 43300 && module_backup::can_i('create','Backups')){
        $form_actions = array(
	        'class' => 'action_bar action_bar_center action_bar_single',
	        'elements' => array(
	            array(
	                'type' => 'save_button',
	                'name' => 'butt_create_backup',
	                'value' => _l('Create a Backup'),
	                'onclick' => "window.location.href='".module_backup::link_open(false)."'; return false;",
	            ),
	        ),
         );
		$fieldset_data = array(
		    'heading' =>  array(
		        'title' => 'Backup',
		        'type' => 'h3',
		    ),
		    'elements' => array(
			    array(
			        'message' => _l('The last backup was made: %s', !module_config::c('backup_time',0) ? _l('Never') : print_date(module_config::c('backup_time',0),true))
			    ),
			    array(
			        'message' => _l('Would you like to create a Backup before Upgrading?')
			    ),
		    ),
			'elements_after' => module_form::generate_form_actions($form_actions),
		);
		echo module_form::generate_fieldset($fieldset_data);
		unset($fieldset_data);
    }
	 ?>

    <form action="" method="post">
        <?php
        module_form::print_form_auth();
		$form_actions = array(
	        'class' => 'action_bar action_bar_center action_bar_single',
	        'elements' => array(
	            array(
	                'type' => 'save_button',
	                'name' => 'check_upgrade',
	                'value' => _l('Check for Upgrades'),
	            ),
	            array(
	                'type' => 'submit',
	                'name' => 'run_upgrade',
	                'value' => _l('Run Manual Upgrades'),
	            ),
	        ),
	     );
		$fieldset_data = array(
		    'heading' =>  array(
		        'title' => 'Upgrade',
		        'type' => 'h3',
		    ),
		    'elements' => array(
			    array(
			        'message' => _l('Please insert your license code(s) below to receive upgrades and new features.') . ' '._hr('Your license code can be found from your CodeCanyon downloads page. <a href="%s">Click here</a> for help.','http://dtbaker.net/admin/includes/plugin_envato/images/envato-license-code.gif')
			    ),
			    array(
				    'field' => array(
	                    'type' => 'hidden',
	                    'name' => 'save_license_codes',
				        'value' => 'yes',
	                ),
			    ),
			    array(
				    'title' => _l('License Codes'),
				    'fields' => array(
	                    function() use ($license_codes){
		                    ?>
		                    <div id="license_codes_holder">
		                        <?php foreach($license_codes as $license_code){ ?>
		                        <div class="dynamic_block">

		                            <input type="text" name="license_codes[]" value="<?php echo htmlspecialchars($license_code);?>" style="width:400px; padding:5px; border:1px solid #CCC;">

		                            <a href="#" class="add_addit" onclick="return seladd(this);">+</a>
		                            <a href="#" class="remove_addit" onclick="return selrem(this);">-</a>
		                            <?php
		                            // we check if this is a confirmed licence code.
		                            // if it is we display the item name, rather than the code.
		                            if(strlen($license_code)>10){
		                                $confirmed = module_config::c('_licence_code_'.$license_code,false);
		                                if($confirmed){
		                                    $foo = explode('|',$confirmed);
		                                    ?>
		                                    <div class="dynamic_clear"><small>
		                                        <?php _e('The above license code is for <a href="%s" target="_blank">%s</a> - thanks for purchasing!',$foo[0],$foo[1]); ?>
		                                    </small></div>
		                                    <?php
		                                }
		                            }
		                            ?>
		                        </div>
		                        <?php } ?>

		                    </div>
		                    <script type="text/javascript">
		                        set_add_del('license_codes_holder');
		                    </script>
		                    <?php
	                    }
	                ),
			    ),
		        array(
			        'message' => _l('To complete a previously started/failed upgrade please click the "Run Manual Upgrades" button.')
		        )
		    ),
			'elements_after' => module_form::generate_form_actions($form_actions),
		);
        $url = module_config::c('ucm_upgrade_url','http://ultimateclientmanager.com/api/upgrade.php');
        if($url != 'http://ultimateclientmanager.com/api/upgrade.php' && $url != 'http://api.ultimateclientmanager.com/upgrade.php'){
            $fieldset_data['elements_before'] = '<div class="error_text text-center">Warning, "ucm_upgrade_url" setting is incorrect. Please contact <a href="http://ultimateclientmanager.com/support/support-ticket/">Support</a></div>';
        }
		echo module_form::generate_fieldset($fieldset_data);
		unset($fieldset_data);
	            ?>
	    </form>

	<?php
    hook_handle_callback('layout_column_half',2);

	if(!isset($setup_upgrade_hack) && module_config::c('upgrade_page_show_extra_info',1)) {
		$fieldset_data = array(
			'heading'         => array(
				'title' => 'Latest News',
				'type'  => 'h3',
			),
			'elements_before' => '<iframe src="http://ultimateclientmanager.com/api/info.php?codes=' . htmlspecialchars(module_config::c('_installation_code')) .'" frameborder="0" style="width:100%; height:600px; background: transparent" ALLOWTRANSPARENCY="true"></iframe>',
		);
		echo module_form::generate_fieldset( $fieldset_data );
		unset( $fieldset_data );
	}


    hook_handle_callback('layout_column_half','end');

}