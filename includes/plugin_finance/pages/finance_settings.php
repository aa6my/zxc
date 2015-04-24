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


if(!module_config::can_i('view','Settings')){
    redirect_browser(_BASE_HREF);
}

module_config::print_settings_form(array(
    'heading' => array(
        'title' => 'Dashboard Finance Settings',
        'main' => true,
        'type' => 'h2',
    ),
    'settings' => array(
         array(
            'key'=>'dashboard_income_summary',
            'default'=>1,
             'type'=>'checkbox',
             'description'=>'Show income summary on the dashboard.',
         ),
    )
)
);
