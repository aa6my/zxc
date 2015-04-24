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


if(!module_config::can_i('view','Settings') || !module_faq::can_i('edit','FAQ')){
    redirect_browser(_BASE_HREF);
}

$faq_products = module_faq::get_faq_products();
$types = module_ticket::get_types();
if(class_exists('module_envato',false)){
    $all_items_rel = module_envato::get_envato_items_rel();
}

if(isset($_REQUEST['faq_product_id']) && $_REQUEST['faq_product_id']){
    $show_other_settings=false;
    $faq_product_id = (int)$_REQUEST['faq_product_id'];
    if($faq_product_id > 0){
        $faq_product = module_faq::get_faq_product($faq_product_id);
    }else{
        $faq_product = array();
    }
    if(!$faq_product){
        $faq_product = array(
            'name' => '',
            'envato_item_ids' => '',
            'default_type_id' => '',
        );
    }
    ?>


<form action="" method="post">
    <input type="hidden" name="_process" value="save_faq_product">
    <input type="hidden" name="faq_product_id" value="<?php echo $faq_product_id; ?>" />
    <table cellpadding="10" width="100%">
        <tr>
            <td valign="top">
                <h3><?php echo _l('Edit FAQ Product'); ?></h3>

                <table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_form">
                    <tbody>
                    <tr>
                        <th class="width1">
                            <?php echo _l('Product Name'); ?>
                        </th>
                        <td>
                            <input type="text" name="name"  value="<?php echo htmlspecialchars($faq_product['name']); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo _l('Default Type/Department'); ?>
                        </th>
                        <td>
                            <?php echo print_select_box($types,'default_type_id',$faq_product['default_type_id'],'',true); ?>
                        </td>
                    </tr>
                    <?php if(class_exists('module_envato',false)){ ?>
                    <tr>
                        <th>
                            <?php echo _l('Envato Item'); ?>
                        </th>
                        <td>
                            <?php
                                $linked_items = explode('|',$faq_product['envato_item_ids']);
        foreach($linked_items as $id=>$linked_item){
            if(!strlen(trim($linked_item))){
                unset($linked_items[$id]);
            }
        }
                            if(!count($linked_items)){
                                $linked_items[]='';
                            }
                            ?>

                    <div id="envato_items_holder">
                        <?php foreach($linked_items as $linked_item){
                        ?>
                        <div class="dynamic_block">

                            <?php
                            echo print_select_box($all_items_rel,'envato_item_ids[]',$linked_item);
                            ?>
                            <a href="#" class="add_addit" onclick="return seladd(this);">+</a>
                            <a href="#" class="remove_addit" onclick="return selrem(this);">-</a>
                            </div>
                        <?php } ?>
                        </div>
                            <script type="text/javascript">
                                set_add_del('envato_items_holder');
                            </script>
                        </td>
                    </tr>
                    <?php } ?>
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

	$header = array(
	    'title' => _l('FAQ Product'),
	    'type' => 'h2',
	    'main' => true,
	    'button' => array(),
	);
	if(module_faq::can_i('create','FAQ')){
	    $header['button'] = array(
	        'url' => module_faq::link_open_faq_product('new'),
	        'title' => _l('Add New Product'),
	        'type' => 'add',
	    );
	}
	print_heading($header);

    ?>



<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
    <thead>
    <tr class="title">
        <th><?php echo _l('Product Name'); ?></th>
        <th> <?php echo _l('Default Type/Department'); ?></th>
        <?php if(class_exists('module_envato',false)){ ?>
        <th> <?php echo _l('Envato Item'); ?> </th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
        <?php
        $c=0;
        foreach($faq_products as $faq_product_id => $data){
            ?>
        <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
            <td class="row_action" nowrap="">
                <?php echo module_faq::link_open_faq_product($faq_product_id,true);?>
            </td>
            <td><?php echo isset($types[$data['default_type_id']]) ? htmlspecialchars($types[$data['default_type_id']]) : '';?></td>
            <?php if(class_exists('module_envato',false)){ ?>
            <td>
                <?php
                $linked_items = explode('|',$data['envato_item_ids']);
                foreach($linked_items as $id=>$linked_item){
                    if(!strlen(trim($linked_item))){
                        unset($linked_items[$id]);
                    }
                    if(isset($all_items_rel[$linked_item])){
                        $linked_items[$id] = $all_items_rel[$linked_item];
                    }
                }
                echo implode(', ',$linked_items);

            ?>
            </td>
            <?php } ?>
        </tr>
            <?php } ?>
    </tbody>
</table>

<?php } ?>