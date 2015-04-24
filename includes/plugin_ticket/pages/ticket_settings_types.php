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

$ticket_types = module_ticket::get_types(false);

if(isset($_REQUEST['ticket_type_id']) && $_REQUEST['ticket_type_id']){
    $show_other_settings=false;
    $ticket_type_id = (int)$_REQUEST['ticket_type_id'];
    if($ticket_type_id > 0){
        $ticket_type = module_ticket::get_ticket_type($ticket_type_id);
    }else{
        $ticket_type = array();
    }
    if(!$ticket_type){
        $ticket_type = array(
            'name' => '',
            'public' => '1',
        );
    }
    ?>


        <form action="" method="post">
            <input type="hidden" name="_process" value="save_ticket_type">
            <input type="hidden" name="ticket_type_id" value="<?php echo $ticket_type_id; ?>" />

              <?php ob_start();
            ?>

                <table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form">
                    <tbody>
                        <tr>
                            <th class="width1">
                                <?php echo _l('Type/Department'); ?>
                            </th>
                            <td>
                                <input type="text" name="name"  value="<?php echo htmlspecialchars($ticket_type['name']); ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php echo _l('Public'); ?>
                            </th>
                            <td>
                                <?php echo print_select_box(get_yes_no(),'public',$ticket_type['public'],'',false);?>
                                <?php echo _h('If this is public this option will display in the public ticket submission form.'); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>

             <?php


    $fieldset_data = array(
        'heading' => array(
            'type' => 'h3',
            'main' => true,
            'title' => 'Edit Ticket Type',
        ),
        'elements_before' => ob_get_clean(),
    );

    echo module_form::generate_fieldset($fieldset_data);
    unset($fieldset_data);


    $form_actions = array(
        'class' => 'action_bar action_bar_center action_bar_single',
        'elements' => array(
            array(
                'type' => 'save_button',
                'name' => 'butt_save',
                'value' => _l('Save'),
            ),
            array(
                'type' => 'delete_button',
                'name' => 'butt_del',
                'value' => _l('Delete'),
                'onclick' => "return confirm('". _l('Really delete this record?')."');",
            ),
        ),
    );
    echo module_form::generate_form_actions($form_actions);

    ?>


        </form>

    <?php
}else{



    print_heading(array(
        'title' => 'Ticket Types/Departments',
        'type' => 'h2',
        'main' => true,
        'button' => array(
            'url' => module_ticket::link_open_type('new'),
            'title' => 'Add New Type',
            'type' => 'add',
        ),
    ));


    ?>

    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
        <thead>
        <tr class="title">
            <th><?php echo _l('Type/Department'); ?></th>
            <th><?php echo _l('Public'); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
            $c=0;
            foreach($ticket_types as $ticket_type_id => $name){
                $ticket_type = module_ticket::get_ticket_type($ticket_type_id);
                ?>
                <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
                    <td class="row_action" nowrap="">
                        <?php echo module_ticket::link_open_type($ticket_type_id,true);?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($ticket_type['public']); ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<?php } ?>