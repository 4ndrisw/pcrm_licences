<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s accounting-template licence">
   <div class="panel-body">
      <?php if(isset($licence)){ ?>
      <?php echo format_licence_status($licence->status); ?>
      <hr class="hr-panel-heading" />
      <?php } ?>
      <div class="row">
          <?php if (isset($licence_request_id) && $licence_request_id != '') {
              echo form_hidden('licence_request_id',$licence_request_id);
          }
          ?>
         <div class="col-md-6">
            <div class="f_client_id">
             <div class="form-group select-placeholder">
                <label for="clientid" class="control-label"><?php echo _l('licence_select_customer'); ?></label>
                <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search<?php if(isset($licence) && empty($licence->clientid)){echo ' customer-removed';} ?>" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
               <?php $selected = (isset($licence) ? $licence->clientid : '');
                 if($selected == ''){
                   $selected = (isset($customer_id) ? $customer_id: '');
                 }
                 if($selected != ''){
                    $rel_data = get_relation_data('customer',$selected);
                    $rel_val = get_relation_values($rel_data,'customer');
                    echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                 } ?>
                </select>
              </div>
            </div>
            <div class="form-group select-placeholder projects-wrapper<?php if((!isset($licence)) || (isset($licence) && !customer_has_projects($licence->clientid))){ echo ' hide';} ?>">
             <label for="project_id"><?php echo _l('project'); ?></label>
             <div id="project_ajax_search_wrapper">
               <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <?php
                  if(isset($licence) && $licence->project_id != 0){
                    echo '<option value="'.$licence->project_id.'" selected>'.get_project_name_by_id($licence->project_id).'</option>';
                  }
                ?>
              </select>
            </div>
           </div>

            <?php
               $next_licence_number = get_option('next_licence_number');
               $format = get_option('licence_number_format');
               
                if(isset($licence)){
                  $format = $licence->number_format;
                }

               $prefix = get_option('licence_prefix');

               if ($format == 1) {
                 $__number = $next_licence_number;
                 if(isset($licence)){
                   $__number = $licence->number;
                   $prefix = '<span id="prefix">' . $licence->prefix . '</span>';
                 }
               } else if($format == 2) {
                 if(isset($licence)){
                   $__number = $licence->number;
                   $prefix = $licence->prefix;
                   $prefix = '<span id="prefix">'. $prefix . '</span><span id="prefix_year">' . date('Y',strtotime($licence->date)).'</span>/';
                 } else {
                   $__number = $next_licence_number;
                   $prefix = $prefix.'<span id="prefix_year">'.date('Y').'</span>/';
                 }
               } else if($format == 3) {
                  if(isset($licence)){
                   $yy = date('y',strtotime($licence->date));
                   $__number = $licence->number;
                   $prefix = '<span id="prefix">'. $licence->prefix . '</span>';
                 } else {
                  $yy = date('y');
                  $__number = $next_licence_number;
                }
               } else if($format == 4) {
                  if(isset($licence)){
                   $yyyy = date('Y',strtotime($licence->date));
                   $mm = date('m',strtotime($licence->date));
                   $__number = $licence->number;
                   $prefix = '<span id="prefix">'. $licence->prefix . '</span>';
                 } else {
                  $yyyy = date('Y');
                  $mm = date('m');
                  $__number = $next_licence_number;
                }
               }
               
               $_licence_number = str_pad($__number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
               $isedit = isset($licence) ? 'true' : 'false';
               $data_original_number = isset($licence) ? $licence->number : 'false';
               ?>
            <div class="form-group">
               <label for="number"><?php echo _l('licence_add_edit_number'); ?></label>
               <div class="input-group">
                  <span class="input-group-addon">
                  <?php if(isset($licence)){ ?>
                  <a href="#" onclick="return false;" data-toggle="popover" data-container='._transaction_form' data-html="true" data-content="<label class='control-label'><?php echo _l('settings_sales_licence_prefix'); ?></label><div class='input-group'><input name='s_prefix' type='text' class='form-control' value='<?php echo $licence->prefix; ?>'></div><button type='button' onclick='save_sales_number_settings(this); return false;' data-url='<?php echo admin_url('licences/update_number_settings/'.$licence->id); ?>' class='btn btn-info btn-block mtop15'><?php echo _l('submit'); ?></button>"><i class="fa fa-cog"></i></a>
                   <?php }
                    echo $prefix;
                  ?>
                  </span>
                  <input type="text" name="number" class="form-control" value="<?php echo $_licence_number; ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>">
                  <?php if($format == 3) { ?>
                  <span class="input-group-addon">
                     <span id="prefix_year" class="format-n-yy"><?php echo $yy; ?></span>
                  </span>
                  <?php } else if($format == 4) { ?>
                   <span class="input-group-addon">
                     <span id="prefix_month" class="format-mm-yyyy"><?php echo $mm; ?></span>
                     .
                     <span id="prefix_year" class="format-mm-yyyy"><?php echo $yyyy; ?></span>
                  </span>
                  <?php } ?>
               </div>
            </div>

            <div class="row">
               <div class="col-md-12">
                      <?php
                     $selected = isset($licence->upt_id) ? $licence->upt_id : '';
                     foreach($offices as $upt){
                      if(isset($licence)){
                        if($licence->upt == $upt['id']) {
                          $selected = $upt['id'];
                        }
                      }
                     }
                     echo render_select('upt_id',$offices,array('id',array('full_name')),'licence_upt_string',$selected);
                     ?>
               </div>
               <div class="col-md-6">
                  
               </div>
            </div>
            <div class="clearfix mbot15"></div>
         </div>
         <div class="col-md-6">
            <div class="panel_s no-shadow">
               <div class="form-group">
                  <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                  <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($licence) ? prep_tags_input(get_tags_in($licence->id,'licence')) : ''); ?>" data-role="tagsinput">
               </div>
               <div class="row">
                   <div class="col-md-6">
                     <div class="form-group select-placeholder">
                        <label class="control-label"><?php echo _l('licence_status'); ?></label>
                        <select class="selectpicker display-block mbot15" name="status" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <?php foreach($licence_statuses as $status){ ?>
                           <option value="<?php echo $status; ?>" <?php if(isset($licence) && $licence->status == $status){echo 'selected';} ?>><?php echo format_licence_status($status,'',false); ?></option>
                           <?php } ?>
                        </select>
                     </div>
                  </div>

                  <div class="col-md-6">
                    <?php $value = (isset($licence) ? $licence->reference_no : ''); ?>
                    <?php echo render_input('reference_no','reference_no',$value); ?>
                  </div>
                     <div class="col-md-6">
                        <?php $value = (isset($licence) ? _d($licence->date) : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('date','licence_add_edit_date',$value); ?>
                     </div>
                     
                     <div class="col-md-6">
                            <?php
                           $selected = get_option('default_licence_assigned');
                           foreach($staff as $member){
                            if(isset($licence)){
                              if($licence->assigned == $member['staffid']) {
                                $selected = $member['staffid'];
                              }
                            }
                           }
                           echo render_select('assigned',$staff,array('staffid',array('firstname','lastname')),'licence_assigned_string',$selected);
                           ?>
                     </div>
                  
               </div>
               <?php $value = (isset($licence) ? $licence->adminnote : ''); ?>
               <?php echo render_textarea('adminnote','licence_add_edit_admin_note',$value); ?>

            </div>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-12 mtop15">
         <div class="panel-body bottom-transaction">
           <?php $value = (isset($licence) ? $licence->clientnote : get_option('predefined_clientnote_licence')); ?>
           <?php echo render_textarea('clientnote','licence_add_edit_client_note',$value,array(),array(),'mtop15'); ?>
           <?php $value = (isset($licence) ? $licence->terms : get_option('predefined_terms_licence')); ?>
           <?php echo render_textarea('terms','terms_and_conditions',$value,array(),array(),'mtop15'); ?>
         </div>
      </div>
      <div class='clearfix'></div>
      <div id="footer" class="col-md-12">
         <div class="col-md-8">
         </div>  
         <div class="col-md-2">
            <div class="bottom-tollbar">
               <div class="btn-group dropup">
                  <button type="button" class="btn-tr btn btn-info licence-form-submit transaction-submit">Save</button>
                  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-right width200">
                     <li>
                        <a href="#" class="licence-form-submit save-and-send transaction-submit"><?php echo _l('submit'); ?></a>
                     </li>
                     <li>
                        <a href="#" class="licence-form-submit save-and-send-later transaction-submit"><?php echo _l('save_and_send_later'); ?></a>
                     </li>
                  </ul>
               </div>
            </div>
         </div>
      </div>
      
   </div>

 <div class="btn-bottom-pusher"></div>


</div>
