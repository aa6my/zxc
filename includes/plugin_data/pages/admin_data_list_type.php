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

if(!$module->can_i('view',$data_type['data_type_name'])){
                return;
            }


            $header_buttons = array();
            if(module_data::can_i('edit',_MODULE_DATA_NAME)){
                $header_buttons[] = array(
                    'url' => $module->link_open_data_type($data_type['data_type_id']),
                    'title' => 'Settings',
                );
            }
            if($module->can_i('create',$data_type['data_type_name'])){ // todo: perms for each data type
                $header_buttons[] = array(
                    'url' => $module->link('',array(
                        'data_type_id'=>$data_type['data_type_id'],
                        'data_record_id'=>'new',
                        'mode'=>'edit',
                        'parent_data_record_id' => isset($parent_data_record_id) ? $parent_data_record_id : false,
                    )),
                    'title' => "Create New ".htmlspecialchars($data_type['data_type_name']),
                );
            }

			if($allow_search){
                $header_buttons[] = array(
                    'url' => $module->link("",array(
                        'search_form'=>1,
                        'data_type_id'=>$data_type_id,
                    )),
                    'title' => "Search",
                );
            }

            if(_DEMO_MODE){
                ?> <div style="padding:20px; text-align: center">This is a demo of the new Custom Data Forms feature. <?php if(module_data::can_i('edit',_MODULE_DATA_NAME)){
               ?> Please feel free to change the <a href="<?php echo $module->link_open_data_type($data_type['data_type_id']);?>">Settings</a> for this Custom Data Form. <?php
            } ?>More details are <a href="http://ultimateclientmanager.com/support/documentation-wiki/custom-data-forms/" target="_blank">located here in the Documentation</a>. </div> <?php
            }

            print_heading(array(
                'main' => isset($parent_data_record_id) && $parent_data_record_id ? false : true,
                'type' => 'h2',
                'title' => htmlspecialchars($data_type['data_type_name']),
                'button' => $header_buttons,
            ));

			$search = array();
            foreach($module->get_data_link_keys() as $key){
                if(isset($_REQUEST[$key])){
                    $search[$key] = $_REQUEST[$key];
                }
            }
			if($allow_search){
				$search = (isset($_REQUEST['search']) && is_array($_REQUEST['search'])) ? $_REQUEST['search'] : $search;
			}
			$search['data_type_id'] = $data_type_id;
            if(isset($parent_data_record_id) && $parent_data_record_id){
                $search['parent_data_record_id'] = $parent_data_record_id;
            }
			// we have to limit the data types to only those created by current user if they are not administration
		    $datas = $module->get_datas($search);
			?>
				<table width="100%" border="0" cellspacing="0" cellpadding="2" class="tableclass tableclass_rows">
					<thead>
						<tr class="title">
                            <?php if(isset($_REQUEST['view_all'])){ ?>
                            <th><?php _e('Parent');?></th>
                            <?php }
                            /*<th><?php echo _l('ID Number'); ?></th>
					    	<th><?php echo _l('Status'); ?></th>
					    	<th><?php echo _l('Date'); ?></th>
					    	<?php */
					    	// find what fields to show in the list
					    	$list_fields = array();
					    	$data_field_groups = $module->get_data_field_groups($data_type_id);
					    	foreach($data_field_groups as $data_field_group){
					    		$data_fields = $module->get_data_fields($data_field_group['data_field_group_id']);
					    		foreach($data_fields as $data_field){
					    			if($data_field['show_list']){
					    				$list_fields[$data_field['data_field_id']] = $data_field;
					    			}
					    		}
					    	}
					    	//todo - uasort by 'order' field.
					    	foreach($list_fields as $list_field){
					    		?>
					    		<th><?php echo $list_field['title'];?></th>
					    		<?php
					    	}
					    	?>
					    </tr>
				    </thead>
				    <tbody>
				    <?php /*if($allow_search){ ?>
				    <tr class="search">
				    	<td><input type="text" name="search[data_title]" value="<?php echo isset($search['data_title'])?htmlspecialchars($search['data_title']):''; ?>" /></td>
				    	<td></td>
				        <td><?php echo print_select_box(get_col_vals("user","user_id","real_name"),"search[user_id]",(isset($search['user_id'])?$search['user_id']:'')); ?></td>
				        <td><?php echo print_select_box($module->get_data_statuses(),"search[status]",isset($search['data_status'])?$search['data_status']:''); ?></td>
				        <td></td>
				        <td nowrap="nowrap">
					        <?php echo create_link("Reset","reset",$module->link()); ?>
				        	<?php echo create_link("Search","submit"); ?>
				        </td>
				    </tr>
				    <?php
				    } */
				    $c=0;
					foreach($datas as $data){
						//$data = $module->get_data_record($data['data_record_id']);

						$list_data_items = $module->get_data_items($data['data_record_id']);
						?>
				        <tr class="<?php echo ($c++%2)?"odd":"even"; ?>">
				            <?php if(isset($_REQUEST['view_all'])){ ?>
                            <td><?php
                                foreach($module->get_data_link_keys() as $key){
                                    if(isset($data[$key]) && (int)$data[$key] > 0){
                                        switch($key){
                                            case 'customer_id':
                                                echo module_customer::link_open($data[$key],true);
                                                break;
                                            case 'job_id':
                                                echo module_job::link_open($data[$key],true);
                                                break;
                                            case 'invoice_id':
                                                echo module_invoice::link_open($data[$key],true);
                                                break;
                                        }
                                    }
                                }
                                ?></td>
                            <?php }/*<td><a href="<?php echo $module->link('',array("data_record_id"=>$data['data_record_id'],"customer_id"=>isset($_REQUEST['customer_id']) ? $_REQUEST['customer_id'] : false)); ?>"><?php echo $module->format_record_id($data['data_type_id'],$data['data_record_id']); ?></a></td>
				            <td><?php echo $data['status']; ?></td>
				            <td><?php echo print_date($data['date_updated']); ?></td>
				            <?php */
                            $first = true;
				            foreach($list_fields as $list_field){
                                $settings = @unserialize($list_data_items[$list_field['data_field_id']]['data_field_settings']);
					            if(!isset($settings['field_type']))$settings['field_type'] = isset($list_field['field_type'])  ? $list_field['field_type'] : false;
								if(isset($list_data_items[$list_field['data_field_id']])){
									$value = $list_data_items[$list_field['data_field_id']]['data_text'];
								}else{
                                    switch($settings['field_type']){
                                        default:
									        $value = _l('N/A');
                                            break;
                                    }
								}
					            switch($settings['field_type']){
						            case 'encrypted':
							            $value = '*******';
							            break;
						            case 'wysiwyg':
		                                $value = module_security::purify_html($value);
							            break;
                                    case 'created_date_time':
                                        $value =  isset($data['date_created']) && $data['date_created'] != '0000-00-00 00:00:00' ? print_date($data['date_created'],true) : _l('N/A');
                                        break;
                                    case 'created_date':
                                        $value =  isset($data['date_created']) && $data['date_created'] != '0000-00-00 00:00:00' ? print_date($data['date_created'],false) : _l('N/A');
                                        break;
                                    case 'created_time':
                                        $value =  isset($data['date_created']) && $data['date_created'] != '0000-00-00 00:00:00' ? date(module_config::c('time_format','g:ia'),strtotime($data['date_created'])) : _l('N/A');
                                        break;
                                    case 'updated_date_time':
                                        $value = isset($data['date_updated']) && $data['date_updated'] != '0000-00-00 00:00:00' ? print_date($data['date_updated'],true) : (isset($data['date_created']) && $data['date_created'] != '0000-00-00 00:00:00' ? print_date($data['date_created'],true) : _l('N/A'));
                                        break;
                                    case 'updated_date':
                                        $value = isset($data['date_updated']) && $data['date_updated'] != '0000-00-00 00:00:00' ? print_date($data['date_updated'],false) : (isset($data['date_created']) && $data['date_created'] != '0000-00-00 00:00:00' ? print_date($data['date_created'],false) : _l('N/A'));
                                        break;
                                    case 'updated_time':
                                        $value = isset($data['date_updated']) && $data['date_updated'] != '0000-00-00 00:00:00' ? date(module_config::c('time_format','g:ia'),strtotime($data['date_updated'])) : (isset($data['date_created']) && $data['date_created'] != '0000-00-00 00:00:00' ? date(module_config::c('time_format','g:ia'),strtotime($data['date_created'])) : _l('N/A'));
                                        break;
                                    case 'created_by':
                                        $value = isset($data['create_user_id']) && (int)$data['create_user_id'] > 0 ? module_user::link_open($data['create_user_id'],true) : _l('N/A');
                                        break;
                                    case 'updated_by':
                                        $value = isset($data['update_user_id']) && (int)$data['update_user_id'] > 0 ? module_user::link_open($data['update_user_id'],true) : (isset($data['create_user_id']) && (int)$data['create_user_id'] > 0 ? module_user::link_open($data['create_user_id'],true) : _l('N/A'));
                                        break;
						            case 'select':
										// todo - do this for the other field types as well..
										$settings['value'] = $value;
		                                $value = $module->get_form_element($settings,true,$data);
							            break;
						            default:
		                                $test = @unserialize($value);
		                                if(is_array($test) && count($test)){
		                                    $foo = array();
		                                    foreach($test as $key=>$val){
		                                        if($val){
		                                            $foo[] = $val == 1 ? $key : $val;
		                                        }
		                                    }
		                                    $value = implode(', ',$foo);
		                                }
		                                $value = htmlspecialchars($value);
							            break;
					            }
                                //$value = isset($settings['field_type']) && $settings['field_type'] == 'encrypted' ? '*******' : (isset($list_data_items[$list_field['data_field_id']]) ? ($list_data_items[$list_field['data_field_id']]['data_text']) : _l('N/A'));
                                if($first){
                                    ?>
                                    <td class="row_action"><a href="<?php echo $module->link('',array("data_record_id"=>$data['data_record_id'],"data_type_id"=>$data['data_type_id'])); ?>"><?php echo $value;?></a></td>
                                    <?php
                                }else{
                                    ?>
                                    <td><?php
                                        // todo: if(isset($list_data_items[$list_field['data_field_id']])) unserialize and check for array.
                                        echo $value;?></td>
                                    <?php
                                }
					    	}
					    	?>
				        </tr>
			        <?php } ?>
			        </tbody>
				</table>
