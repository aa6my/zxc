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
//$ticket_id > 0 &&
if(module_config::c('ticket_allow_extra_data',1)){
    $extras = module_ticket::get_ticket_extras_keys($ticket['ticket_account_id']);
    if(count($extras)){

        ob_start();
        ?>
        <table class="tableclass tableclass_form tableclass_full">
            <tbody>
            <?php foreach ($extras as $extra){ ?>
                <tr>
                    <th class="width1" style="white-space: nowrap;">
                        <?php echo htmlspecialchars($extra['key']);?>
                    </th>
                    <td>
                        <?php
                        // we do a hook in here with the encryption plugin.
                        // if some of these fields are marked for encryption we display a nice lock
                        // and some stars instead of the value.
                        // if there isn't a value then we don't display any stars, just a lock symbol

                        // we also have to show an "unlock" button so the admin can unlock the value.

                        if((!isset($extras_editable) || $extras_editable) ){
                            module_form::generate_form_element(array(
                                'type' => $extra['type'],
                                'name' => 'ticket_extra['.$extra['ticket_data_key_id'].']',
                                'value' => isset($ticket['extra_data'][$extra['ticket_data_key_id']]) ? $ticket['extra_data'][$extra['ticket_data_key_id']]['value'] : '',
                                'options' => isset($extra['options']) && $extra['options'] ? unserialize($extra['options']) : array(),
                                'class' => 'no_permissions',
                                    // encryption is available on this field
                                'encrypt' => (class_exists('module_encrypt',false) && isset($extra['encrypt_key_id']) && $extra['encrypt_key_id']),
                                'page_name' => 'ticket_extras', // this is also set within ticket.php in public saving.
                                'id' => 'ticket_extras_'.$extra['ticket_data_key_id'], // this is also set within ticket.php in public saving.
                            ));
                            if(preg_match('#(https?://[^\s]*)$#',isset($ticket['extra_data'][$extra['ticket_data_key_id']]) ? $ticket['extra_data'][$extra['ticket_data_key_id']]['value'] : '',$matches)){
                                ?> <a href="<?php echo htmlspecialchars($matches[1]);?>" target="_blank"><?php _e('Open');?> &raquo;</a> <?php
                            }else if(preg_match('#(www\.[^\s]*)$#',isset($ticket['extra_data'][$extra['ticket_data_key_id']]) ? $ticket['extra_data'][$extra['ticket_data_key_id']]['value'] : '',$matches)){
                                ?> <a href="http://<?php echo htmlspecialchars($matches[1]);?>" target="_blank"><?php _e('Open');?> &raquo;</a> <?php
                            }
                        }else{
                            if(class_exists('module_encrypt',false) && isset($extra['encrypt_key_id']) && $extra['encrypt_key_id']){
                                echo '********';
                            }else{
                                echo h(isset($ticket['extra_data'][$extra['ticket_data_key_id']]) ? $ticket['extra_data'][$extra['ticket_data_key_id']]['value'] : '');
                            }
                        }?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php
    $fieldset_data = array(
        'heading' => array(
            'title' => _l('Extra Information'),
            'type' => 'h3',
        ),
        'elements_before' => ob_get_clean(),
    );
    echo module_form::generate_fieldset($fieldset_data);
    unset($fieldset_data);

    }
}