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

$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : array();
$product_categories = module_product::get_product_categories($search);

$pagination = process_pagination($product_categories);

?>

<h2>
    <?php if(module_product::can_i('create','Products')){ ?>
	<span class="button">
		<?php echo create_link("Create New Category","add",module_product::link_open_category('new')); ?>
	</span>
        
        <?php 
    if(false && class_exists('module_import_export',false)){
        $link = module_import_export::import_link(
            array(
                'callback'=>'module_product::handle_import_category',
                'name'=>'product_categories',
                'return_url'=>$_SERVER['REQUEST_URI'],
                'fields'=>array(
                    'Category ID' => 'product_category_id',
                    'Product Name' => 'product_category_name',
                ),
            )
        );
        ?>
        <span class="button">
            <?php echo create_link("Import Product Categories","add",$link); ?>
        </span>
        <?php
    } ?>
    <?php } ?>
    <span class="title">
		<?php echo _l('Job/Invoice Product Categories'); ?>
	</span>
</h2>


<form action="" method="post">

<?php echo $pagination['summary'];?>
<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
	<thead>
	<tr class="title">
		<th><?php echo _l('Category Name'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
	$c=0;
	foreach($pagination['rows'] as $product){ ?>
        <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
            <td class="row_action">
	            <?php echo module_product::link_open_category($product['product_category_id'],true,$product); ?>
            </td>
        </tr>
	<?php } ?>
  </tbody>
</table>
<?php echo $pagination['links'];?>
</form>