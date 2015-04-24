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



class module_db extends module_base{

    public static function can_i($actions,$name=false,$category=false,$module=false){
        if(!$module)$module=__CLASS__;
        return parent::can_i($actions,$name,$category,$module);
    }
	public static function get_class() {
        return __CLASS__;
    }
	public function init(){
		$this->module_name = "db";
		$this->module_position = 0;

        $this->version = 2.1;
        //2.1 - 2013-07-18 - initial release
	}

    public static function update_insert($pkey,$pid,$table,$data=false,$do_replace=false){

        if($data===false){
            $data = $_REQUEST;
        }
        $fields = get_fields($table,array("date_created","date_updated")); //
        if(isset($fields['system_id']) && defined('_SYSTEM_ID')){
            $data['system_id'] = _SYSTEM_ID;
        }
        if(isset($fields['date_created'])){
            unset($fields['date_created']);
        }

        $now_string = mysql_real_escape_string(date('Y-m-d H:i:s'));
        if($do_replace || !is_numeric($pid) || !$pid){
            $pid = 'new';
            if($do_replace){
                $sql = "REPLACE INTO ";
            }else{
                $sql = "INSERT INTO ";
            }
            $sql .= "`"._DB_PREFIX."$table` SET date_created = '$now_string', ";
            if(isset($fields['create_user_id']) && isset($_SESSION['_user_id']) && $_SESSION['_user_id']){
                $sql .= "`create_user_id` = '".(int)$_SESSION['_user_id']."', ";
                unset($fields['create_user_id']);
            }
            if(isset($fields['create_ip_address'])){
                $sql .= "`create_ip_address` = '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."', ";
                unset($fields['create_ip_address']);
            }
            // check there's a valid site id
            if(isset($fields['site_id']) && (!isset($data['site_id']) || !$data['site_id']) && isset($_SESSION['_site_id'])){
                $data['site_id'] = $_SESSION['_site_id'];
            }
            $where = "";
            //module_security::sanatise_data($table,$data);
            // todo - sanatise data here before we go through teh loop.
            // if sanatisation fails or data access fails then we stop the update/insert.
            if(!$data){
                // dont do this becuase $email->new_email() fails.
               // return false;
            }
        }else{
            // TODO - security hook here, check if we can access this data.
            /*$security_dummy=array();
            if(!module_security::can_access_data($table,$security_dummy,$pid)){
                echo 'Security warning - unable to save data';
                exit;
                return false;
            }*/
            $updated = false;
            if(isset($data['date_updated'])){
                $updated = "'".mysql_real_escape_string(input_date($data['date_updated'],true))."'";
            }
            if(!$updated){
                $updated = "'$now_string'";
            }
            $sql = "UPDATE `"._DB_PREFIX."$table` SET date_updated = $updated,";
            if(isset($fields['update_user_id']) && isset($_SESSION['_user_id']) && $_SESSION['_user_id']){
                $sql .= "`update_user_id` = '".(int)$_SESSION['_user_id']."', ";
                unset($fields['update_user_id']);
            }
            if(isset($fields['update_ip_address'])){
                $sql .= "`update_ip_address` = '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."', ";
                unset($fields['update_ip_address']);
            }
            $where = " WHERE `$pkey` = '".mysql_real_escape_string($pid)."'";
            if(isset($fields['system_id']) && defined('_SYSTEM_ID')){
                $where .= " AND system_id = '"._SYSTEM_ID."'";
            }
        }

        //print_r($fields);exit;
        //print_r($data);exit;

        if(!$do_replace && isset($data[$pkey])){
            unset($data[$pkey]);
        }

        foreach($fields as $field){
            if(!isset($data[$field['name']]) || $data[$field['name']] === false){
                continue;
            }

            // special format for date fields.
            if($field['type']=='date'){
                $data[$field['name']] = input_date($data[$field['name']]);
            }
            // special format for int / double fields.
            if(($field['type']=='decimal'||$field['type']=='double') && function_exists('number_in')){
                $data[$field['name']] = number_in($data[$field['name']]);
            }

            if(is_array($data[$field['name']]))
                $val = serialize($data[$field['name']]);
            else
                $val = $data[$field['name']];
            $sql .= " `".$field['name']."` = '".mysql_real_escape_string($val)."', ";
        }
        $sql = rtrim($sql,', ');
        $sql .= $where;
        query($sql);
        if($pid == "new"){
            $pid = mysql_insert_id();
        }
        return $pid;
    }

}

/* placeholder module to contain various functions used through out the system */
@include_once 'includes/database.php'; // so we don't re-create old functions.


if(!function_exists('update_insert')){
    function update_insert($pkey,$pid,$table,$data=false,$do_replace=false){
        return module_db::update_insert($pkey,$pid,$table,$data,$do_replace);
    }
}