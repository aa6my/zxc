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

// hack to add a "group" option to the pagination results.
if(class_exists('module_group',false) && module_product::can_i('edit','Products')){
    module_group::enable_pagination_hook(
        // what fields do we pass to the group module from this customers?
        array(
            'bulk_actions'=>array(
                'delete'=>array(
                    'label'=>'Delete selected products',
                    'type'=>'delete',
                    'callback'=>'module_product::bulk_handle_delete',
                ),
            ),
        )
    );
}

$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : array();
$products = module_product::get_products($search);

$pagination = process_pagination($products);

?>

<h2>
    <?php if(module_product::can_i('create','Products')){ ?>
	<span class="button">
		<?php echo create_link("Create New Product","add",module_product::link_open('new')); ?>
	</span>
        
        <?php 
    if(class_exists('module_import_export',false)){
        $link = module_import_export::import_link(
            array(
                'callback'=>'module_product::handle_import',
                'name'=>'Products',
                'return_url'=>$_SERVER['REQUEST_URI'],
                'fields'=>array(
                    'Product ID' => 'product_id',
                    'Product Name' => 'name',
                    'Product Category' => 'category_name',
                    'Hours/Qty' => 'quantity',
                    'Amount' => 'amount',
                    'Description' => 'description',
                ),
            )
        );
        ?>
        <span class="button">
            <?php echo create_link("Import Products","add",$link); ?>
        </span>
        <?php
    } ?>
    <?php } ?>
    <span class="title">
		<?php echo _l('Job/Invoice Products'); ?>
	</span>
</h2>


<form action="" method="post">

<?php echo $pagination['summary'];?>
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
	<thead>
	<tr class="title">
		<th><?php echo _l('Product Name'); ?></th>
		<th><?php echo _l('Category Name'); ?></th>
		<th><?php echo _l('Hours/Quantity'); ?></th>
		<th><?php echo _l('Amount'); ?></th>
        <?php if(module_product::can_i('edit','Products')){ ?>
        <th width="5"></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php
	$c=0;
	foreach($pagination['rows'] as $product){ ?>
        <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
            <td class="row_action">
	            <?php echo module_product::link_open($product['product_id'],true,$product); ?>
            </td>
            <td>
				<?php
                echo htmlspecialchars($product['product_category_name']);
				?>
            </td>
            <td>
				<?php
                echo $product['quantity'];
				?>
            </td>
            <td>
				<?php
                echo dollar($product['amount']); //,true,$product['currency_id']
				?>
            </td>
            <?php if(module_product::can_i('edit','Products')){ ?>
            <td>
                <input type="checkbox" name="bulk_operation[<?php echo $product['product_id'];?>]" value="yes">
            </td>
            <?php } ?>
        </tr>
	<?php } ?>
  </tbody>
</table>
<?php echo $pagination['links'];?>
</form>