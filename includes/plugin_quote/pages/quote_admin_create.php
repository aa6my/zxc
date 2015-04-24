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

if(!$quote_safe)die('denied');


$quote_id = (int)$_REQUEST['quote_id'];
$quote = module_quote::get_quote($quote_id);
$staff_members = module_user::get_staff_members();
$staff_member_rel = array();
foreach($staff_members as $staff_member){
    $staff_member_rel[$staff_member['user_id']] = $staff_member['name'];
}

$c = array();
$customers = module_customer::get_customers();
foreach($customers as $customer){
    $c[$customer['customer_id']] = $customer['customer_name'];
}
if(count($c)==1){
    $quote['customer_id']=key($c);
}


// check permissions.
if(class_exists('module_security',false)){
    module_security::check_page(array(
        'category' => 'Quote',
        'page_name' => 'Quotes',
        'module' => 'quote',
        'feature' => 'create',
    ));
}

$quote_tasks = array(); //module_quote::get_tasks($quote_id);
?>

<script type="text/javascript">
    var completed_tasks_hidden = false; // set with session variable / cookie
    var editing_quote_task_id = false;
    function show_completed_tasks(){

    }
    function hide_completed_tasks(){

    }

    function setamount(a,quote_task_id){
		var amount = 0;
	    if(a.match(/:/)){
		    var bits = a.split(':');
		    var hours = bits[0].length > 0 ? parseInt(bits[0]) : 0;
		    var minutes = 0;
		    if(typeof bits[1] != 'undefined' && bits[1].length > 0){
			    if(bits[1].length == 1){
				    // it's a 0 or a 123456789
				    if(bits[1] == "0"){
					    minutes = 0;
				    }else{
					    minutes = parseInt(bits[1] + "0");
				    }
			    }else{
				    minutes = parseInt(bits[1]);
			    }
		    }
		    if(hours > 0 || minutes > 0){
			    amount = <?php echo $quote['hourly_rate'];?> * hours;
			    amount += <?php echo $quote['hourly_rate'];?> * (minutes / 60);
	        }
	    }else{
		    var bits = a.split('<?php echo module_config::c('currency_decimal_separator','.');?>');
		    var number = bits[0].length > 0 ? parseInt(bits[0]) : 0;
		    number += typeof bits[1] != 'undefined' && parseInt(bits[1]) > 0 ? parseFloat("." + bits[1]) : 0;
		    amount = <?php echo $quote['hourly_rate'];?> * number;
	    }
	    $('#'+quote_task_id+'taskamount').val(amount);
	    $('#'+quote_task_id+'complete_hour').val(a);
    }

    function canceledittask(){
        if(editing_quote_task_id){
            $('#task_edit_'+editing_quote_task_id).html(loading_task_html);
            editing_quote_task_id = false;
        }
        $('.task_edit').hide();
        $('.task_preview').show();
    }
    var last_quote_name = '';
    function setnewquotetask(){
        var quote_name = $('#quote_name').val();
        var current_new_task = $('#task_desc_new').val();
        if(current_new_task == '' || current_new_task == last_quote_name){
            $('#task_desc_new').val(quote_name);
            last_quote_name = quote_name;
        }
    }
    $(function(){
        $('.task_toggle_long_description').click(function(event){
            event.preventDefault();
            $(this).parent().find('.task_long_description').slideToggle('fast',function(){
                if($('textarea.edit_task_long_description').length>0){
                    $('textarea.edit_task_long_description')[0].focus();
                }
            });
            return false;
        });
        <?php if(module_config::c('quote_create_task_as_name',0)){ ?>
        $('#quote_name').keyup(setnewquotetask).change(setnewquotetask);
        <?php } ?>
    });
</script>

<form action="" method="post" id="quote_form" class="quote_form_new">
    <input type="hidden" name="_process" value="save_quote" />
    <input type="hidden" name="quote_id" value="<?php echo $quote_id; ?>" />
    <input type="hidden" name="customer_id" value="<?php echo $quote['customer_id']; ?>" />


    <?php

    $fields = array(
    'fields' => array(
        'name' => 'Name',
    ));
    module_form::set_required(
        $fields
    );
    //module_form::set_default_field('task_desc_new');
    module_form::set_default_field('quote_name');
    module_form::prevent_exit(array(
        'valid_exits' => array(
            // selectors for the valid ways to exit this form.
            '.submit_button',
            '.save_task',
            '.delete',
            '.task_defaults',
        ))
    );


    hook_handle_callback('layout_column_half',1,'35');


    /**** QUOTE DETAILS ****/
    ob_start();

    ?>

    <table border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form tableclass_full">
        <tbody>
            <tr>
                <th class="width1">
                    <?php echo _l('Quote Title'); ?>
                </th>
                <td>
                    <input type="text" name="name" id="quote_name" value="<?php echo htmlspecialchars($quote['name']); ?>" />
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo _l('Type'); ?>
                </th>
                <td>
                    <?php echo print_select_box(module_quote::get_types(),'type',$quote['type'],'',true,false,true); ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo _l('Hourly Rate'); ?>
                </th>
                <td>
                    <?php echo currency('<input type="text" name="hourly_rate" class="currency" value="'.$quote['hourly_rate'].'">');?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo _l('Status'); ?>
                </th>
                <td>
                    <?php echo print_select_box(module_quote::get_statuses(),'status',$quote['status'],'',true,false,true); ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo _l('Create Date'); ?>
                </th>
                <td>
                    <input type="text" name="date_create" class="date_field" value="<?php echo print_date($quote['date_create']);?>">
                    <?php _h('This is the date the Quote was created.');?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo _l('Approved Date'); ?>
                </th>
                <td>
                    <input type="text" name="date_approved" class="date_field" value="<?php echo print_date($quote['date_approved']);?>">
                    <?php _h('This is the date the Quote was accepted by the client. This date is automatically set if the client clicks "Approve"');?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php echo _l('Approved By'); ?>
                </th>
                <td>
                    <input type="text" name="approved_by" id="approved_by" value="<?php echo htmlspecialchars($quote['approved_by']);?>">
                </td>
            </tr>
            <?php if(module_config::c('quote_allow_staff_assignment',1)){ ?>
            <tr>
                <th>
                    <?php _e('Staff Member');?>
                </th>
                <td>
                    <?php
                    echo print_select_box($staff_member_rel,'user_id',$quote['user_id']);
                    _h('Assign a staff member to this quote. You can also assign individual tasks to different staff members.');
                    ?>
                </td>
            </tr>
            <?php } ?>
            <tr>
                <th>
                    <?php echo _l('Tax'); ?>
                </th>
                <td>
                   <?php // we turn on 'incrementing' if any of the taxes have this option enabled.
                    $incrementing = false;
                    if(!isset($quote['taxes']) || !count($quote['taxes'])){
                        $quote['taxes'][] = array(); // at least have 1?
                    }
                    foreach($quote['taxes'] as $tax){
                        if(isset($tax['increment']) && $tax['increment']){
                            $incrementing = true;
                        }
                    } ?>
                    <span class="quote_tax_increment">
                        <input type="checkbox" name="tax_increment_checkbox" id="tax_increment_checkbox" value="1" <?php echo $incrementing ? ' checked' : '';?>> <?php _e('incremental');?>
                    </span>
                    <div id="quote_tax_holder">
                    <?php
                    foreach($quote['taxes'] as $id=>$tax){  ?>
                        <div class="dynamic_block">
                            <input type="hidden" name="tax_ids[]" class="dynamic_clear" value="<?php echo isset($tax['quote_tax_id']) ? (int)$tax['quote_tax_id'] : 0;?>">
                            <input type="text" name="tax_names[]" class="dynamic_clear" value="<?php echo isset($tax['name']) ? htmlspecialchars($tax['name']) : '';?>" style="width:30px;">
                            @
                            <input type="text" name="tax_percents[]" class="dynamic_clear" value="<?php echo isset($tax['percent']) ? htmlspecialchars(number_out($tax['percent'], module_config::c('tax_trim_decimal', 1), module_config::c('tax_decimal_places',module_config::c('currency_decimal_places',2)))) : '';?>" style="width:35px;">%
                            <a href="#" class="add_addit" onclick="seladd(this); ucm.quote.update_quote_tax(); return false;">+</a>
                            <a href="#" class="remove_addit" onclick="selrem(this); ucm.quote.update_quote_tax(); return false;">-</a>
                        </div>
                    <?php } ?>
                    </div>
                    <script type="text/javascript">
                        set_add_del('quote_tax_holder');
                    </script>

                </td>
            </tr>

            <tr>
                <th>
                    <?php echo _l('Currency'); ?>
                </th>
                <td>
                    <?php echo print_select_box(get_multiple('currency','','currency_id'),'currency_id',$quote['currency_id'],'',false,'code'); ?>
                </td>
            </tr>

        </tbody>
        <?php

        if(class_exists('module_extra',false) && module_extra::is_plugin_enabled()){
             module_extra::display_extras(array(
                'owner_table' => 'quote',
                'owner_key' => 'quote_id',
                'owner_id' => false, //$quote['quote_id'],
                'layout' => 'table_row',
                     'allow_new' => module_quote::can_i('create','Quotes'),
                     'allow_edit' => module_quote::can_i('create','Quotes'),
                )
            );
        }
        ?>
    </table>
    <?php
     $fieldset_data = array(
        'heading' => array(
            'title' => _l('Quote Details'),
            'type' => 'h3',
        ),
        'elements_before' => ob_get_clean(),
    );
    echo module_form::generate_fieldset($fieldset_data);
    unset($fieldset_data);

    if(module_config::c('quote_enable_description',1)){

        if(!module_quote::can_i('edit','Quotes') && !$quote['description']){
            // no description, no ability to edit description, don't show anything.
        }else{
            // can edit description
            $fieldset_data = array(
                'heading' => array(
                    'title' => _l('Quote Description'),
                    'type' => 'h3',
                ),
                'class' => 'tableclass tableclass_form tableclass_full',

            );
            if(module_quote::can_i('edit','Quotes')){
                $fieldset_data['elements'] = array(
                    array(
                        'field' => array(
                            'type' => 'wysiwyg',
                            'name' => 'description',
                            'value' => $quote['description'],
                        ),
                    )
                );
            }else{
                $fieldset_data['elements'] = array(
                    array(
                        'fields' => array(
                            module_security::purify_html($quote['description']),
                        ),
                    )
                );
            }
            echo module_form::generate_fieldset($fieldset_data);
            unset($fieldset_data);
        }
    }

    /**** ADVANCED ***/

    if(module_quote::can_i('view','Quote Advanced')){
        ob_start();
        ?>
        <table border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form tableclass_full">
            <tbody>
                <?php if(class_exists('module_website',false) && module_website::is_plugin_enabled()){ ?>
                <tr>
                    <th class="width1">
                        <?php echo _l('Assign '.module_config::c('project_name_single','Website')); ?>
                    </th>
                    <td>
                        <?php
                        $c = array();
                        // change between websites within this customer?
                        // or websites all together?
                        $res = module_website::get_websites(array('customer_id'=>(isset($_REQUEST['customer_id'])?(int)$_REQUEST['customer_id']:false)));
                        //$res = module_website::get_websites();
                        while($row = array_shift($res)){
                            $c[$row['website_id']] = $row['name'];
                        }
                        echo print_select_box($c,'website_id',$quote['website_id']);
                        ?>
                        <?php if($quote['website_id'] && module_website::can_i('view','Websites')){ ?>
                            <a href="<?php echo module_website::link_open($quote['website_id'],false);?>"><?php _e('Open');?></a>
                        <?php } ?>
                        <?php _h('This will be the '.module_config::c('project_name_single','Website').' this quote is assigned to - and therefor the customer. Every quote should have a'.module_config::c('project_name_single','Website').' assigned. Clicking the open link will take you to the '.module_config::c('project_name_single','Website'));?>
                    </td>
                </tr>
                <?php }else if(!class_exists('module_website',false) && module_config::c('show_ucm_ads',1)){ ?>
                <tr>
                    <th>
                        <?php echo module_config::c('project_name_single','Website'); ?>
                    </th>
                    <td>
                        (website option available in <a href="http://codecanyon.net/item/ultimate-client-manager-pro-edition/2621629?ref=dtbaker" target="_blank">UCM Pro Edition</a>)
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th>
                        <?php echo _l('Assign Customer'); ?>
                    </th>
                    <td>
                        <?php
                        $c = array();
                        $customers = module_customer::get_customers();
                        foreach($customers as $customer){
                            $c[$customer['customer_id']] = $customer['customer_name'];
                        }
                        echo print_select_box($c,'customer_id',$quote['customer_id']);
                        ?>
                        <?php if($quote['customer_id'] && module_customer::can_i('view','Customers')){ ?>
                        <a href="<?php echo module_customer::link_open($quote['customer_id'],false);?>"><?php _e('Open');?></a>
                        <?php } ?>
                    </td>
                </tr>
	            <tr>
	                <th>
	                    <?php _e('Discount Amount'); ?>
	                </th>
	                <td>
	                    <?php  echo (!module_security::is_page_editable()) ?
	                        '<span class="currency">'.dollar($quote['discount_amount'],true,$quote['currency_id']).'</span>' :
	                        currency('<input type="text" name="discount_amount" value="'.number_out($quote['discount_amount']).'" class="currency">') ?>
	                    <?php  _h('Here you can apply a before tax discount to this quote. You can name this anything, eg: DISCOUNT, CREDIT, REFUND, etc..'); ?>
	                </td>
	            </tr>
	            <tr>
	                <th>
	                    <?php _e('Discount Name'); ?>
	                </th>
	                <td>
	                    <?php  echo (!module_security::is_page_editable()) ?
	                        htmlspecialchars(_l($quote['discount_description'])) :
	                        '<input type="text" name="discount_description" value="'.htmlspecialchars(_l($quote['discount_description'])).'" style="width:80px;">' ?>
	                </td>
	            </tr>
	            <tr>
	                <th>
	                    <?php _e('Discount Type'); ?>
	                </th>
	                <td>
	                    <?php echo print_select_box(array('0'=>_l('Before Tax'),1=>_l('After Tax')),'discount_type',$quote['discount_type']);?>
	                </td>
	            </tr>
                <tr>
                    <th>
                        <?php _e('Task Type');?>
                    </th>
                    <td>
                        <?php
                        echo print_select_box(module_quote::get_task_types(),'default_task_type',isset($quote['default_task_type'])?$quote['default_task_type']:0,'',false);?>
                        <?php _h('The default is hourly rate + amount. This will show the "Hours" column along with an "Amount" column. Inputing a number of hours will auto complete the price based on the quote hourly rate. <br>Quantity and Amount will allow you to input a Quantity (eg: 2) and an Amount (eg: $100) and the final price will be $200 (Quantity x Amount). The last option "Amount Only" will just have the amount column for manual input of price. Change the advanced setting "default_task_type" between 0, 1 and 2 to change the default here.'); ?>

                    </td>
                </tr>
                <?php
                if(class_exists('module_extra',false) && module_extra::is_plugin_enabled()){
                $quote_default_tasks = module_quote::get_default_tasks();
                    if(module_config::c('quote_enable_default_tasks',1) && count($quote_default_tasks)){


                    ?>
                    <tr>
                        <th>
                            <?php _e('Task Defaults'); ?>
                        </th>
                        <td>
                            <?php
                            echo print_select_box($quote_default_tasks,'default_task_list_id','','',true,'');
                            ?>
                            <input type="button" name="i" id="insert_saved" value="<?php _e('Insert');?>" class="small_button task_defaults">
                            <input type="hidden" name="default_tasks_action" id="default_tasks_action" value="0">
                            <script type="text/javascript">
                                $(function(){
                                    $('#insert_saved').click(function(){
                                        // set a flag and submit our form.
                                        $('#default_tasks_action').val('insert_default');
                                        $('#quote_form')[0].submit();
                                    });
                                });
                            </script>
                            <?php _h('Here you can insert a previously saved set of default tasks.'); ?>
                        </td>
                    </tr>
                    <?php }
                    } ?>
            </tbody>
        </table>
        <?php

        $fieldset_data = array(
            'heading' => array(
                'title' => _l('Advanced'),
                'type' => 'h3',
            ),
            'elements_before' => ob_get_clean(),
        );
        echo module_form::generate_fieldset($fieldset_data);
        unset($fieldset_data);
    }
$form_actions = array(
        'class' => 'action_bar action_bar_left',
        'elements' => array(
            array(
                'type' => 'save_button',
                'name' => 'butt_save',
                'value' => _l('Save Quote'),
            ),
            array(
                'type' => 'button',
                'name' => 'cancel',
                'value' => _l('Cancel'),
                'class' => 'submit_button',
                'onclick' => "window.location.href='".module_quote::link_open(false)."';",
            ),
        ),
    );
    echo module_form::generate_form_actions($form_actions);


    hook_handle_callback('layout_column_half',2,'65');

    if(module_quote::can_i('edit','Quote Tasks')||module_quote::can_i('view','Quote Tasks')){

        $header = array(
            'title_final' => _l('Quote Tasks %s',''),
            'button' => array(),
            'type' => 'h3',
        );

        ob_start();
        ?>

        <table border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows tableclass_full">
            <thead>
            <tr>
                <?php if(module_config::c('quote_show_task_numbers',1)){ ?>
                <th width="10">#</th>
                <?php } ?>
                <th class="task_column task_width"><?php _e('Description');?></th>
                <th width="10" class="task_type_label">
                <?php if($quote['default_task_type']==_TASK_TYPE_AMOUNT_ONLY){
                    }else if($quote['default_task_type']==_TASK_TYPE_QTY_AMOUNT){
                    _e(module_config::c('task_qty_name',_l('Qty')));
                    }else if($quote['default_task_type']==_TASK_TYPE_HOURS_AMOUNT){
                    _e(module_config::c('task_hours_name',_l('Hours')));
                    } ?>
                </th>
                <th width="72"><?php _e('Amount');?></th>
                <?php if(module_config::c('quote_allow_staff_assignment',1)){ ?>
                <th width="78"><?php _e('Staff');?></th>
                <?php } ?>
                <th width="60"> </th>
            </tr>
            </thead>
            <?php
                if(module_security::is_page_editable() && module_quote::can_i('create','Quote Tasks')){ ?>
            <tbody>
            <tr>
                <?php if(module_config::c('quote_show_task_numbers',1)){ ?>
                    <td valign="top">&nbsp;</td>
                <?php } ?>
                <td valign="top">
                    <input type="text" name="quote_task[new][description]" id="task_desc_new" class="edit_task_description" value=""><?php
                    if(class_exists('module_product',false)){
                        module_product::print_quote_task_dropdown('new');
                    } ?><a href="#" class="task_toggle_long_description ui-icon ui-icon-plus">&raquo;</a>
                    <div class="task_long_description">
                        <textarea name="quote_task[new][long_description]" id="task_long_desc_new" class="edit_task_long_description"></textarea>
                    </div>
                </td>
                <td valign="top">
                    <input type="text" name="quote_task[new][hours]" value="" size="3" style="width:25px;" onchange="setamount(this.value,'new');" onkeyup="setamount(this.value,'new');" id="task_hours_new">
                </td>
                <td valign="top" nowrap="">
                    <?php echo currency('<input type="text" name="quote_task[new][amount]" value="" id="newtaskamount" class="currency">');?>
                </td>
                <?php if(module_config::c('quote_allow_staff_assignment',1)){ ?>
                    <td valign="top">
                        <?php echo print_select_box($staff_member_rel,'quote_task[new][user_id]',
                            isset($staff_member_rel[module_security::get_loggedin_id()]) ? module_security::get_loggedin_id() : false, 'quote_task_staff_list', ''); ?>
                    </td>
                <?php } ?>
                <td align="center" valign="top">
                    <input type="submit" name="save" value="<?php _e('New Task');?>" class="save_task small_button">
                </td>
            </tr>
            </tbody>
            <?php } ?>
            <?php
            $c=0;
            $task_number = 0;
            foreach($quote_tasks as $quote_task_id => $task_data){
                $task_number++;
                if(module_security::is_page_editable() && module_quote::can_i('edit','Quote Tasks')){ ?>
                    <tbody id="task_edit_<?php echo $quote_task_id;?>" style="display:none;" class="task_edit"></tbody>
                <?php  } else {
                    $task_editable = false;
                }
                echo module_quote::generate_task_preview($quote_id,$quote,$quote_task_id,$task_data,$task_number);
            } ?>
            </table>

            <?php
        $fieldset_data = array(
            'heading' => $header,
            'elements_before' => ob_get_clean(),
        );
        echo module_form::generate_fieldset($fieldset_data);
        unset($fieldset_data);

    }  // end can i view quote tasks


hook_handle_callback('layout_column_half','end');

     ?>

</form>