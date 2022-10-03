<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

    <div class="panel_s">
        <div class="panel-body">
         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="table-responsive">
            <?php render_datatable(array(
                _l('licence_task'),
                _l('suket'),
                _l('expired'),
                _l('tanggal'),
                _l('licence_flag'),
                ),'suket-proposed'); ?>
         </div>
        </div>
    </div>
