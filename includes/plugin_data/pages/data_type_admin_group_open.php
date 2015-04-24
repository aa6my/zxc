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
if(!module_data::can_i('edit',_MODULE_DATA_NAME)){
	die("access denied");
}



$data_type_id = (int)$_REQUEST['data_type_id'];
$data_field_group_id = $_REQUEST['data_field_group_id'];
if($data_type_id && $data_type_id != 'new'){
	$data_type = $module->get_data_type($data_type_id);
}else{
	die("No type defined");
}
if($data_field_group_id && $data_field_group_id != 'new'){
	$data_field_group = $module->get_data_field_group($data_field_group_id);
    $data_field_group['layout'] = 'table';
}else{
	
}




?>

<form action="" method="post">
<input type="hidden" name="_process" value="save_data_field_group" />
<input type="hidden" name="_redirect" value="" />
<input type="hidden" name="data_type_id" value="<?php echo $data_type_id; ?>" />

    <?php
    $sublists = array();
    foreach( $module->get_data_types() as $sub_data_type){
        if($sub_data_type['data_type_id'] == $data_type_id)continue;
        $sublists[$sub_data_type['data_type_id']] = htmlspecialchars($sub_data_type['data_type_name']);
    }

    $header_buttons = array();
    $fieldset_data = array(
        'heading' => array(
            'main' => true,
            'type' => 'h2',
            'title' => 'Data Fieldset',
            'button' => $header_buttons,
        ),
        'class' => 'tableclass tableclass_form tableclass_full',
        'elements' => array(
            'title' => array(
                'title' => _l('Title'),
                'field' => array(
                    'type' => 'text',
                    'name' => 'title',
                    'value' => (isset($data_field_group['title']))?htmlspecialchars($data_field_group['title']):'',
                    'size' => 60
                ),
            ),
            'position' => array(
                'title' => _l('Order'),
                'field' => array(
                    'type' => 'text',
                    'name' => 'position',
                    'value' => (isset($data_field_group['position']))?htmlspecialchars($data_field_group['position']):'',
                    'size' => 5
                ),
            ),
            'sub_data_type_id' => array(
                'title' => _l('Sub List (advanced)'),
                'ignore' => !count($sublists),
                'field' => array(
                    'type' => 'select',
                    'name' => 'sub_data_type_id',
                    'value' => (isset($data_field_group['sub_data_type_id']))?htmlspecialchars($data_field_group['sub_data_type_id']):'',
                    'options' => $sublists,
                    'blank' => _l(' - default, use fields defined below - '),
                    'help' => 'If you want to display a list from another Customer Data Form here, choose it from the dropdown. If you choose a sub list then NO FIELDS WILL DISPLAY HERE, only the sub list.'
                ),
            ),
            /*'layout' => array(
                'title' => _l('Layout'),
                'field' => array(
                    'type' => 'select',
                    'name' => 'layout',
                    'blank' => false,
                    'value' => (isset($data_field_group['layout']))?htmlspecialchars($data_field_group['layout']):'',
                    'options' => array(
                        'table' => 'Table',
                        'float' => 'Floating',
                    ),
                ),
            ),*/
            /*'display_default' => array(
                'title' => _l('Title'),
                'field' => array(
                    'type' => 'display_default',
                    'name' => 'title',
                    'value' => (isset($data_field_group['display_default']))?htmlspecialchars($data_field_group['display_default']):'',
                ),
            ),*/
        ),
    );
    echo module_form::generate_fieldset($fieldset_data);
    unset($fieldset_data);
    ?>

    <input type="submit" name="butt_save" id="butt_save" value="<?php echo _l('Save Settings'); ?>" class="submit_button save_button">
    <input type="submit" name="butt_del" id="butt_del" value="<?php echo _l('Delete'); ?>" class="submit_button delete_button">
    <input type="button" name="cancel" value="<?php echo _l('Cancel'); ?>" onclick="window.location.href='<?php echo $module->link('',array(
"data_type_id"=>$data_type['data_type_id'],
)); ?>';" class="submit_button" />

</form>	

<p>&nbsp;</p>
<?php
if($data_field_group_id && $data_field_group_id != 'new'){

    if(isset($data_field_group['sub_data_type_id']) && $data_field_group['sub_data_type_id']){

    }else{
        $data_fields = $module->get_data_fields($data_field_group_id);
        $next_order = 0;
        foreach($data_fields as $data_field){
            $next_order = max($data_field['order'],$next_order);
        }
        $next_order++;

        $header_buttons = array();
        $header_buttons[] = array(
            'url' => $module->link("",array('data_type_id'=>$data_type_id,'data_field_group_id'=>$data_field_group_id,'data_field_id'=>'new','order'=>$next_order)),
            'title' => 'Create New',
            'type' => 'add',
        );
        if(count($data_fields)){
            if(isset($_REQUEST['visual_editor'])){
                $header_buttons[] = array(
                    'url' => $module->link("",array('data_type_id'=>$data_type_id,'data_field_group_id'=>$data_field_group_id,'visual_editor'=>false)),
                    'title' => 'Switch to Standard Editor',
                );
            }else{
                $header_buttons[] = array(
                    'url' => $module->link("",array('data_type_id'=>$data_type_id,'data_field_group_id'=>$data_field_group_id,'visual_editor'=>'1')),
                    'title' => 'Switch to Visual Editor',
                );
            }
        }

        print_heading(array(
            'type' => 'h2',
            'title' => 'Data Fields',
            'main' => true,
            'button' => $header_buttons,
            'help' => 'These are the input fields that will make up this data group. Switch to visual editor for easy drag and drop of elements and their sizes.',
        ));

        if(isset($_REQUEST['visual_editor'])){
            $mode = 'admin';
            $display_field_group_heading = false;
            $admin_edit_what = (isset($_REQUEST['admin_edit_what'])) ? $_REQUEST['admin_edit_what'] : 'position';
            ?>
            <script type="text/javascript">
            $(function() {
                <?php
                switch($admin_edit_what){
                    case 'input':
                        ?>
                        $(".data_group_fields textarea").resizable({
                            handles: 's, e',
                            minHeight: 22,
                            cancel: '.ui-state-disabled',
                            stop: function(event, ui) {
                                var data_field_id = ui.element.parents('<?php echo $data_field_group['layout'] == 'table' ? 'tr' : 'li'; ?>').first().attr('rel');
                                $.post("<?php echo full_link("?m=data&_process=save_ajax&type=input&data_field_id=");?>"+data_field_id, ui.size);
                            }
                        });
                        $(".data_group_fields input:text").resizable({
                            handles: 'e',
                            cancel: '.ui-state-disabled',
                            stop: function(event, ui) {
                                var data_field_id = ui.element.parents('<?php echo $data_field_group['layout'] == 'table' ? 'tr' : 'li'; ?>').first().attr('rel');
                                $.post("<?php echo full_link("?m=data&_process=save_ajax&type=input&data_field_id=");?>"+data_field_id, ui.size);
                            }
                        });

                        $('.ui-resizable-handle').hover(
                            function(){
                                $(this).css('backgroundColor','#4f59d7');
                            },
                            function(){
                                $(this).css('backgroundColor','');
                            }
                        );
                        <?php
                        break;
                    case 'boundary':
                        ?>
                        $("ul.data_group_fields li").resizable({
                            //grid:[2,26],
                            minHeight: <?php echo _MIN_INPUT_HEIGHT;?>,
                            cancel: '.ui-state-disabled',
                            stop: function(event, ui) {
                                var data_field_id = ui.element.attr('rel');
                                $.post("<?php echo full_link("?m=data&_process=save_ajax&type=boundary&data_field_id=");?>"+data_field_id, ui.size);
                            }
                        });
                        <?php
                        break;
                    case 'position':
                    default:
                        ?>
                        $("ul.data_group_fields").sortable({
                            cancel: '.ui-state-disabled',
                            update: function(event, ui) {
                                var holder = $(ui.item[0]).parent();
                                var order = holder.sortable('serialize');
                                $.post("<?php echo full_link("?m=data&_process=save_ajax&type=position&data_field_group_id=".(int)$data_field_group['data_field_group_id']);?>", order);
                            }
                        }).disableSelection();
                $("table.data_group_fields tbody").each(function(){
                    var t = this;
                    (function(){
                        const $srt = $(t);
                        console.debug($srt);
                        $srt.sortable({
                            cancel: '.ui-state-disabled',
                            helper: function(e, ui) {
                                ui.children().each(function() {
                                    $srt.css('width',$srt.width());
                                });
                                ui.css('width','auto');
                                return ui;
                            },
                            update: function(event, ui) {
                                var order = $srt.sortable('serialize');
                                $.post("<?php echo full_link("?m=data&_process=save_ajax&type=position&data_field_group_id=".(int)$data_field_group['data_field_group_id']);?>", order);
                            }
                        }).disableSelection();
                    })();
                });

                        <?php
                        break;
                }
                ?>

            });
            </script>
            <style type="text/css">
            li.data_field{
                border:1px dashed #CCC !important;
            }
            </style>
                <form action="" method="post">
                <p>Choose what you would like to administer below:
                    <select name="admin_edit_what" onchange="this.form.submit();">
                    <option value="position"<?php echo ($admin_edit_what=='position')?' selected':'';?>>Position of elements (drag &amp; drop elements around the form)</option>
                    <option value="input"<?php echo ($admin_edit_what=='input')?' selected':'';?>>Size of input boxes (click &amp; drag the border of input boxes)</option>
                    <?php if($data_field_group['layout'] != 'table'){ ?>
                    <option value="boundary"<?php echo ($admin_edit_what=='boundary')?' selected':'';?>>Size of boundry boxes (click &amp; drag the border of dotted outlines)</option>
                <?php } ?>
                    </select>
                    <!-- <input type="submit" name="go" value="Go" class="submit_button save_button"> -->
                    </p>
                </form>

            <?php
            include('render_group.php');
        }else{
            ?>

            <table class="tableclass tableclass_rows tableclass_full">
                <thead>
                <tr class="title">
                    <th style="max-width: 15px">#</th>
                    <th><?php echo _l('Field Name'); ?></th>
                    <th><?php echo _l('Field Type'); ?></th>
                    <th><?php echo _l('Required'); ?></th>
                    <th><?php echo _l('Default'); ?></th>
                    <th><?php echo _l('Searchable'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(!count($data_fields)){
                    ?>
                    <tr>
                        <td colspan="6" align="center">
                            <a href="<?php echo $module->link("",array('data_type_id'=>$data_type_id,'data_field_group_id'=>$data_field_group_id,'data_field_id'=>'new','order'=>$next_order));?>" class="uibutton">
                                <?php if(isset($button['type']) && $button['type'] == 'add'){ ?> <img src="<?php echo _BASE_HREF;?>images/add.png" width="10" height="10" alt="add" border="0" /> <?php } ?>
                                <span><?php echo _l('Create New Field');?></span>
                            </a>
                        </td>
                    </tr>
                    <?php
                }else{
                $c=0;
                $yn = get_yes_no();
                foreach($data_fields as $data){
                    $data_field = $module->get_data_field($data['data_field_id']);
                    ?>
                    <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
                        <td><?php echo htmlspecialchars($data_field['order']);?></td>
                        <td class="row_action"><a href="<?php echo $module->link('',array(
                            "data_type_id"=>$data_type['data_type_id'],
                            "data_field_group_id"=>$data_field['data_field_group_id'],
                            "data_field_id"=>$data_field['data_field_id'],
                            ));?>"><?php echo $data_field['title'] ? htmlspecialchars($data_field['title']) : 'Not Sure';?></a></td>
                        <td><?php echo htmlspecialchars($data_field['field_type']);?></td>
                        <td><?php echo $yn[$data_field['required']];?></td>
                        <td><?php echo htmlspecialchars($data_field['default']);?></td>
                        <td><?php echo $yn[$data_field['searchable']];?></td>
                    </tr>
                <?php }
                }
                ?>
                </tbody>
            </table>
            <?php
        }
    }
}
?>