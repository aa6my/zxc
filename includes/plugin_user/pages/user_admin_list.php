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


$search = (isset($_REQUEST['search']) && is_array($_REQUEST['search'])) ? $_REQUEST['search'] : array();
$search['customer_id'] = 0;
$users = module_user::get_users($search);

// grab a list of customer sites
$sites = array();
$user_statuses = module_user::get_statuses();
$roles = module_security::get_roles();

$heading = array(
    'title' => _l('User Administration'),
    'button' => array(),
    'main' => true,
);
if(module_user::can_i('create','Users','Config')){
    $heading['button'][] = array(
        'title' => 'Add new user',
        'type' => 'add',
        'url' => $module->link_open('new'),
    );
}

print_heading($heading);
?>


<form action="" method="post">

<?php $search_bar = array(
        'elements' => array(
            'name' => array(
                'title' => _l('Users Name:'),
                'field' => array(
                    'type' => 'text',
                    'name' => 'search[generic]',
                    'value' => isset($search['generic'])?$search['generic']:'',
                )
            ),
        )
    );
    echo module_form::search_bar($search_bar);


/** START TABLE LAYOUT **/
    $table_manager = module_theme::new_table_manager();
    $columns = array();
    $columns['name'] = array(
            'title' => 'Users Name',
            'callback' => function($user){
                echo module_user::link_open($user['user_id'],true);
            },
            'cell_class' => 'row_action',
        );
    $columns['email'] = array(
            'title' => 'Email Address',
            'callback' => function($user){
                echo htmlspecialchars($user['email']);
            }
        );
    $columns['role'] = array(
            'title' => 'Role / Permissions',
            'callback' => function($user) use ($roles){
                if($user['user_id']==1){
                    echo _l('Everything');
                }else{
                    if(isset($user['roles']) && $user['roles']){
                        foreach($user['roles'] as $role){
                            echo $roles[$role['security_role_id']]['name'];
                        }
                    }
                }
            }
        );
    $columns['can_login'] = array(
            'title' => 'Can Login',
            'callback' => function($user){
                echo module_security::can_user_login($user['user_id']) ? _l('Yes') : _l('No');
            }
        );
    $table_manager->set_columns($columns);
    $table_manager->row_callback = function($row_data){
        // load the full vendor data before displaying each row so we have access to more details
        return module_user::get_user($row_data['user_id']);
    };
    $table_manager->set_rows($users);
    if(class_exists('module_extra',false)){
        $table_manager->display_extra('user',function($user){
            module_extra::print_table_data('user',$user['user_id']);
        });
    }
    $table_manager->pagination = true;
    $table_manager->print_table();

?>
</form>