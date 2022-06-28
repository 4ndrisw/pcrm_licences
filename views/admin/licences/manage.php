<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                     <?php if(has_permission('licences','','create')){ ?>

                     <div class="_buttons">
                        <a href="<?php echo admin_url('licences/create'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_licence'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <?php } ?>
                    <?php render_datatable(array(
                        _l('licence_number'),
                        _l('licence_company'),
                        _l('licence_list_project'),
                        //_l('licence_projects_name'),
                        _l('licence_status'),
                        _l('licence_start_date'),
                        //_l('licence_acceptance_name'),
                        _l('licence_acceptance_date'),
                        //_l('licence_end_date'),
                        ),'licences'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" id="licence-js" src="<?= base_url() ?>modules/licences/assets/js/licences.js?"></script>
<script>
    $(function(){
        initDataTable('.table-licences', window.location.href, 'undefined', 'undefined','fnServerParams', [0, 'desc']);
    });
</script>
</body>
</html>
