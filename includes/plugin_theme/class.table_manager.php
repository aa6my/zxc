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

/**
 * Class ucm_table_manager
 *
 * This controls the table data layout on pages within the UCM system.
 * If you want to change this file please copy it to a new folder: custom/includes/plugin_theme/class.table_manager.php
 * this way it wont get overwritten during system updates.
 */


class ucm_table_manager{

    public $pagination = false;
    public $pagination_per_page = 20;
    public $pagination_hide_single_page = false;
    public $row_callback = false;
    public $inline_table = false; // adminlte theme uses this to decide what to wrap around the table output.
    public $columns = array();
    public function set_columns($columns){
        $this->columns = $columns;
    }
    public $table_class = 'tableclass tableclass_rows tableclass_full';
    public $table_id = 'table';
	public $blank_message = '';
    public $row_id = false;
    public $row_class = '';
    public $row_attributes = array();
    public $rows = array();
    public function set_rows($rows){
        $this->rows = $rows;
    }
    public $footer_rows = array();
    public function set_footer_rows($rows){
        $this->footer_rows = $rows;
    }
    public $extra_fields = array();
    public function display_extra($extra_type,$extra_callback){
        $this->extra_fields[$extra_type] = array(
            'type' => $extra_type,
            'callback' => $extra_callback
        );
    }
    public $subscription_fields = array();
    public function display_subscription($subscription_type,$subscription_callback){
        $this->subscription_fields[$subscription_type] = array(
            'type' => $subscription_type,
            'callback' => $subscription_callback
        );
    }
	public function process_data(){
		if($this->pagination) {
			$this->rows = process_pagination( $this->rows, $this->pagination_per_page, 0, $this->table_id );
		}
		if(!is_array($this->rows) || !isset($this->rows['rows'])){
            $row_data = array();
            if(is_resource($this->rows)){
                while($row = mysql_fetch_assoc($this->rows)){
                    $row_data[] = $row;
                }
            }else if(is_array($this->rows)){
                $row_data = $this->rows;
            }
            $this->rows = array(
                'rows' => $row_data,
            );
            unset($row_data);
        }
	}
    public function print_table(){
	    $this->process_data();
        if($this->pagination && isset($this->rows['summary']) && (!$this->pagination_hide_single_page || ($this->pagination_hide_single_page && $this->rows['page_numbers'] > 1))){
            echo $this->rows['summary'];
        }
	    $colspan = 0;
        ?>

        <table class="<?php echo $this->table_class;?>"<?php echo $this->table_id ? ' id="'.$this->table_id.'"' : '';?>>
            <thead>
            <tr class="title">
                <?php foreach($this->columns as $column_id => $column_data){
                    $title = is_array($column_data) ? $column_data['title'] : $column_data;
	                $colspan++;
                    ?>
                    <th id="<?php echo $column_id;?>"><?php echo _l($title); ?></th>
                <?php }
                if(class_exists('module_extra',false) && count($this->extra_fields)){
                    foreach($this->extra_fields as $extra_field){
                        $colspan += module_extra::print_table_header($extra_field['type']);
                    }
                }
                if(class_exists('module_subscription',false) && count($this->subscription_fields)){
                    foreach($this->subscription_fields as $extra_field){
                        module_subscription::print_table_header($extra_field['type']);
	                    $colspan++;
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $c=0;
            if(!count($this->rows['rows'])){
	            if($this->blank_message){
		            ?>
	                <tr>
		                <td colspan="<?php echo $colspan;?>" class="blank_message" style="text-align: center"><?php _e($this->blank_message);?></td>
	                </tr>
	                <?php
	            }
            }else{
	            foreach($this->rows['rows'] as $row){
	                $this->print_row($row);
	            }
            }
            ?>
            </tbody>
            <?php if(count($this->footer_rows)){ ?>
            <tfoot class="summary">
            <?php
            foreach($this->footer_rows as $row){
                $this->print_footer_row($row);
            }
            ?>
            </tfoot>
            <?php } ?>
        </table>
        <?php
        if($this->pagination && isset($this->rows['links']) && (!$this->pagination_hide_single_page || ($this->pagination_hide_single_page && $this->rows['page_numbers'] > 1))){
            echo $this->rows['links'];
        }
    }

    public function print_row($row){
        static $c=0;
        if(_DEBUG_MODE)module_debug::log(array('title'=>'row'));
        if(is_closure($this->row_callback)){
            /** @var Closure $row_callback */
            $row_callback = $this->row_callback;
	        $result = $row_callback($row, $this);
	        if($result === false)return false;
	        $row = array_merge($row,$result);
        }
	    // options for this row output...
	    $row_class = $this->row_class . ' '. (($c++%2)?"odd":"even");
        ?>
        <tr<?php echo $this->row_id?' id="'.$this->row_id.'"':''?> class="<?php echo $row_class; ?>"<?php
        foreach($this->row_attributes as $key=>$val){
	        echo ' '.$key.'="'.htmlspecialchars($val).'"';
        }
        ?>>
            <?php foreach($this->columns as $column_id => $column_data){
                ?>
                <td<?php
                    if(isset($row[$column_id]) && is_array($row[$column_id]) && isset($row[$column_id]['cell_class'])){
                        echo ' class="'.$row[$column_id]['cell_class'].'"';
                    }else if(isset($column_data['cell_class'])){
                        echo ' class="'.$column_data['cell_class'].'"';
                    }
                    if(isset($row[$column_id]) && is_array($row[$column_id]) && isset($row[$column_id]['cell_colspan'])){
                        echo ' colspan="'.$row[$column_id]['cell_colspan'].'"';
                    }else if(isset($column_data['cell_colspan'])){
                        echo ' colspan="'.$column_data['cell_colspan'].'"';
                    }
                ?>>
                    <?php
                    if(is_array($column_data) && isset($column_data['callback']) && is_closure($column_data['callback'])){
                        $column_data['callback']($row);
                    }else if(isset($row[$column_id])){
                        echo $row[$column_id];
                    }else{
                        _e('N/A');
                    }
                    ?>
                </td>
                <?php
            }
            if(class_exists('module_extra',false) && count($this->extra_fields)){
                foreach($this->extra_fields as $extra_field){
                    if(is_closure($extra_field['callback'])){
                        $extra_field['callback']($row);
                    }
                }
            }
            if(class_exists('module_subscription',false) && count($this->subscription_fields)){
                foreach($this->subscription_fields as $subscription_field){
                    if(is_closure($subscription_field['callback'])){
                        $subscription_field['callback']($row);
                    }
                }
            }
            ?>
        </tr>
        <?php
	    return true;
    }


    public function print_footer_row($row){
        if(_DEBUG_MODE)module_debug::log(array('title'=>'footer row'));
        ?>
        <tr class="">
            <?php foreach($this->columns as $column_id => $column_data){
                if(isset($row[$column_id])){
                    ?>
                    <td<?php
                        if(isset($row[$column_id]) && is_array($row[$column_id]) && isset($row[$column_id]['cell_class'])){
                            echo ' class="'.$row[$column_id]['cell_class'].'"';
                        }else if(isset($column_data['cell_class'])){
                            echo ' class="'.$column_data['cell_class'].'"';
                        }
                        if(isset($row[$column_id]) && is_array($row[$column_id]) && isset($row[$column_id]['cell_colspan'])){
                            echo ' colspan="'.$row[$column_id]['cell_colspan'].'"';
                        }else if(isset($column_data['cell_colspan'])){
                            echo ' colspan="'.$column_data['cell_colspan'].'"';
                        }
                    ?>>
                        <?php
                        if(isset($row[$column_id]['data'])){
                            echo $row[$column_id]['data'];
                        }else{
                            _e('N/A');
                        }
                        ?>
                    </td>
                    <?php
                }
            }
            ?>
        </tr>
        <?php
    }
}