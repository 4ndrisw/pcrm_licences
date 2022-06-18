<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel_s">
    <div class="panel-body">
     <?php if(has_permission('licences','','create')){ ?>
     <div class="_buttons">
        <a href="<?php echo admin_url('licences/create'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_licence'); ?></a>
     </div>
     <?php } ?>
     <?php if(has_permission('licences','','create')){ ?>
     <div class="_buttons">
        <a href="<?php echo admin_url('licences'); ?>" class="btn btn-primary pull-right display-block"><?php echo _l('licences'); ?></a>
     </div>
     <?php } ?>
     <div class="clearfix"></div>
     <hr class="hr-panel-heading" />
     <div class="table-responsive">
        <?php render_datatable(array(
            _l('licence_number'),
            _l('licence_company'),
            _l('licence_start_date'),
            ),'licences'); ?>
     </div>
    </div>
</div>
