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

/*$sql = "SELECT pay.*, i.job_id, p.website_id ";
$sql .= " FROM `"._DB_PREFIX."invoice_payment` pay ";
$sql .= " LEFT JOIN `"._DB_PREFIX."invoice` i ON pay.invoice_id = i.invoice_id ";
$sql .= " LEFT JOIN `"._DB_PREFIX."job` p ON i.job_id = p.job_id ";
$sql .= " WHERE (pay.date_paid >= '$start_date' AND pay.date_paid <= '$end_date')";
$res = qa($sql);*/

// grab payments from the finanace module.
// this searches invoice payments and also the new extra transactions table.
$res = array();
if($end_date != $start_date){
    $end_date = date('Y-m-d',strtotime('-1 day',strtotime($end_date)));
}
$finance_records = module_finance::get_finances(array('date_from'=>$start_date,'date_to'=>$end_date));
foreach($finance_records as $finance_record){
    if($finance_record['credit'] > 0){
        $finance_record['amount'];
        $finance_record['date_paid'] = $finance_record['transaction_date'];
        $res[] = $finance_record;
    }
    if($finance_record['debit'] > 0){
        $finance_record['amount'];
        $finance_record['transaction_date'];
    }
}

$title = _l('Income');
$total = 0;

print_heading("$title for " . print_date($start_date) . $end_date_str);
?>

<table class="tableclass tableclass_rows tableclass_full">
    <thead>
        <tr>
            <th><?php _e('Date');?></th>
            <th><?php _e(module_config::c('project_name_single','Website'));?></th>
            <th><?php _e('Job');?></th>
            <th><?php _e('Invoice');?></th>
            <th><?php _e('Amount');?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($res as $r){
            $invoice_data = array();
            if(isset($r['invoice_id']) && $r['invoice_id'] > 0){
                $invoice_data = module_invoice::get_invoice($r['invoice_id']);
            }
            ?>
            <tr>
                <td>
                    <?php echo print_date($r['date_paid']); ?>
                </td>
                <?php if(isset($r['website_id']) || isset($r['job_id']) || isset($invoice_data['job_ids']) || isset($r['invoice_id'])){ ?>
                <td>
                    <?php
                    if(isset($r['website_id']) && $r['website_id'] > 0){
                        echo module_website::link_open($r['website_id'],true);
                    }
                    ?>
                </td>
                <td>
                    <?php if(isset($invoice_data['job_id']) && $invoice_data['job_id']){
                        echo module_job::link_open($invoice_data['job_id'],true);
                    }else if(isset($invoice_data['job_ids']) && is_array($invoice_data['job_ids'])){
                        foreach($invoice_data['job_ids'] as $job_id){
                            echo module_job::link_open($job_id,true);
                        }
                    }?>
                </td>
                <td>
                    <?php
                    if(isset($r['invoice_id']) && $r['invoice_id'] > 0){
                        echo module_invoice::link_open($r['invoice_id'],true);
                    }
                    ?>
                </td>
                <?php }else if(isset($r['finance_id'])){ ?>
                <td colspan="3">
                    <?php echo module_finance::link_open($r['finance_id'],true); ?>
                    
                    <?php echo htmlspecialchars($r['description']); ?>
                </td>
                <?php } ?>
                <td>
                    <?php
                        $total += $r['amount'];
                    echo dollar($r['amount']);
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" align="right">
                <?php _e('Total:'); ?>
            </td>
            <td>
                <span style="font-weight: bold;"><?php echo dollar($total); ?></span>
            </td>
        </tr>
    </tfoot>
</table>