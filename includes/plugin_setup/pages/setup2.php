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

print_heading('Step #2: Database Installation');

// check for existing database tables.
// upgrade if neccessary.
// check with each plugin to get a list of SQL to install / upgrade.

//$current_db_version = _UCM_VERSION;

// check if db is installed
//$sql = "SHOW TABLES LIKE '"._DB_PREFIX."config'";
//$res = qa1($sql);
/*
if(count($res)){
    // something is installed, find out what version.
    $sql = "SELECT * FROM `"._DB_PREFIX."config` WHERE `key` = 'db_version'";
    $res = qa1($sql);
    if(count($res)){
        // found a version.
        $current_db_version = $res['val'];
    }
    $do_upgrade = true;
}else{
    $do_upgrade = false;
}*/

// start running all the hooks to install plugins.
$fail = false;
$set_versions = array();
foreach($plugins as $plugin_name => &$p){
    echo "Installing <span style='text-decoration:underline;'>$plugin_name</span> plugin version ".$p->get_plugin_version().".... ";
    if($version = $p->install_upgrade()){ //$do_upgrade,$current_db_version
        echo '<span class="success_text">success</span>';
        $set_versions[$plugin_name] = $version;
    }else{
        $fail = true;
        echo '<span class="error_text">fail</span> ';
    }
    echo '<br>';
}
// all done?

if(isset($set_versions['config'])){
    // config db worked.
    foreach($plugins as $plugin_name => &$p){
        if(isset($set_versions[$plugin_name])){
            $p->init();
            // lol typo - oh well. 
            $p->set_insatlled_plugin_version($set_versions[$plugin_name]);
        }
    }
}


if($fail){
    print_header_message();
    ?>
        <br/>
        Some things failed. Would you like to retry? <br/>
    <a href="?m=setup&amp;step=2" class="uibutton">Retry</a>
    <?php
}else{
    ?>

    <p>Database Installation Success!</p>

    <p><a href="?m=setup&amp;step=3" class="submit_button btn btn-success">Continue to Step 3 &raquo;</a></p>

    <?php
}

?>