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
$module->page_title = _l('Preview');
//print_heading('Newsletter Editor');

$newsletter_id = isset($_REQUEST['newsletter_id']) ? (int)$_REQUEST['newsletter_id'] : false;
if(!$newsletter_id){
    redirect_browser(module_newsletter::link_list(false));
}
//$newsletter = module_newsletter::get_newsletter($newsletter_id);

if(isset($_REQUEST['show'])){
    // render the newsletter and display it on screen with nothing else.

    echo module_newsletter::render($newsletter_id,false,false,'preview');
    exit;
}

?>


<table width="100%" cellpadding="5">
    <tbody>
    <tr>
        <td width="50%" valign="top">

            <?php
            print_heading(array(
                  'type' => 'h2',
                  'title' => 'Preview Newsletter',
                  'button' => array(
                      'url' => module_newsletter::link_open($newsletter_id),
                      'title' => 'Return to Editor',
                  ),
              ));
            ?>

<iframe src="<?php echo module_newsletter::link_preview($newsletter_id);?>&show=true" frameborder="0" style="width:100%; height:700px; border:0;" background="transparent"></iframe>


    </td></tr></tbody></table>