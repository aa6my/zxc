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



if(!module_config::can_i('edit','Settings')){
    redirect_browser(_BASE_HREF);
}

if(isset($_REQUEST['extra_default_id']) && $_REQUEST['extra_default_id']){
    $show_other_settings=false;
    $extra_default = module_extra::get_extra_default($_REQUEST['extra_default_id']);
    ?>


        <form action="" method="post">
            <input type="hidden" name="_process" value="save_extra_default">
            <input type="hidden" name="extra_default_id" value="<?php echo (int)$_REQUEST['extra_default_id']; ?>" />
            <table cellpadding="10" width="100%">
                <tr>
                    <td valign="top">
                        <h3><?php echo _l('Edit Extra Default Field'); ?></h3>

                        <table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form">
                            <tbody>
                                <tr>
                                    <th>
                                        <?php echo _l('Name/Label'); ?>
                                    </th>
                                    <td>
                                        <input type="text" name="extra_key"  value="<?php echo htmlspecialchars($extra_default['extra_key']); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo _l('Table'); ?>
                                    </th>
                                    <td>
                                        <?php echo htmlspecialchars($extra_default['owner_table']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo _l('Visibility'); ?>
                                    </th>
                                    <td>
                                        <?php echo print_select_box(module_extra::get_display_types(),'display_type',$extra_default['display_type'],'',false); ?>
                                        <?php _h('Default will display the extra field when opening an item (eg: opening a customer). If a user can view the customer they will be able to view the extra field information when viewing the customer. Public In Column means that this extra field will also display in the overall listing (eg: customer listing). More options coming soon (eg: private)'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo _l('Order'); ?>
                                    </th>
                                    <td>
                                        <input type="text" name="order"  value="<?php echo htmlspecialchars($extra_default['order']); ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo _l('Searchable'); ?>
                                    </th>
                                    <td>
                                        <?php echo print_select_box(get_yes_no(),'searchable',$extra_default['searchable'],'',false); ?>
                                        <?php _h('If this field is searchable it will display in the main search bar. Also quick search will return results matching this field. Note: Making every field searchable will slow down the "Quick Search".'); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="submit" name="butt_save" id="butt_save" value="<?php echo _l('Save'); ?>" class="submit_button save_button" />
                       <input type="submit" name="butt_del" id="butt_del" value="<?php echo _l('Delete'); ?>" onclick="return confirm('<?php echo _l('Really delete this record?'); ?>');" class="submit_button" />


                    </td>
                </tr>
            </table>

        </form>

    <?php
}else{
    ?>


    <h2>
        <!-- <span class="button">
            <?php echo create_link("Add New Field","add",module_extra::link_open_extra_default('new')); ?>
        </span> -->
        <?php echo _l('Extra Fields'); ?>
    </h2>
    <?php

    $extra_defaults = module_extra::get_defaults();
    $visibility_types = module_extra::get_display_types();
    ?>


    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
        <thead>
        <tr class="title">
            <th><?php echo _l('Section'); ?></th>
            <th><?php echo _l('Extra Field'); ?></th>
            <th><?php echo _l('Display Type'); ?></th>
            <th><?php echo _l('Order'); ?></th>
            <th><?php echo _l('Searchable'); ?></th>
        </tr>
        </thead>
        <tbody>
            <?php
            $c=0;
            $yn = get_yes_no();
            foreach($extra_defaults as $owner_table => $owner_table_defaults){
                foreach($owner_table_defaults as $owner_table_default){
                ?>
                <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
                    <td>
                        <?php echo htmlspecialchars($owner_table);?>
                    </td>
                    <td class="row_action" nowrap="">
                        <?php echo module_extra::link_open_extra_default($owner_table_default['extra_default_id'],true);?>
                    </td>
                    <td>
                        <?php echo isset($visibility_types[$owner_table_default['display_type']]) ? $visibility_types[$owner_table_default['display_type']] : 'N/A'; ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($owner_table_default['order']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($yn[$owner_table_default['searchable']]); ?>
                    </td>
                </tr>
            <?php }
            } ?>
        </tbody>
    </table>

<?php } ?>