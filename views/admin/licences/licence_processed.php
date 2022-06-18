<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


            <div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('licence_related'); ?>">
                <?php if(staff_can('view', 'licences') || staff_can('view_own', 'licences')) { ?>
                <div class="panel_s related_tasks-expiring">
                    <div class="panel-body padding-10">
                        <p class="padding-5"><?php echo _l('licence_related'); ?></p>
                        <hr class="hr-panel-heading-dashboard">
                        <?php if (!empty($related_tasks)) { ?>
                            <div class="table-vertical-scroll">
                                <a href="<?php echo admin_url('related_tasks'); ?>" class="mbot20 inline-block full-width"><?php echo _l('home_widget_view_all'); ?></a>
                                <table id="widget-<?php echo create_widget_id(); ?>" class="table dt-table" data-order-col="0" data-order-type="ASC">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('task'); ?></th>
                                            <th><?php echo _l('licence_number'); ?> #</th>
                                            <th><?php echo _l('licence_list_date'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php $i = 1; ?>
                                        <?php foreach ($related_tasks as $task) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo '<a href="' . admin_url("tasks/licence/" . $task["id"] . '">' . $task["name"]) . '</a>'; ?>
                                                </td>
                                                <td>
                                                   <input type="text" name="<?php echo 'tasks[task_id_' .$task["id"] .']'; ?>" value="<?php echo $task["licence_upt_number"]; ?>"> 
                                                </td>
                                                <td>
                                                    <?php echo _d($task['dateadded']); ?>
                                                </td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                                 <div class="col-md-12">
                                    <button type="button" class="btn-tr btn btn-info licence-form-submit transaction-submit">Save</button>
                                 </div>
                            </div>
                        <?php } else { ?>
                            <div class="text-center padding-5">
                                <i class="fa fa-check fa-5x" aria-hidden="true"></i>
                                <h4><?php echo _l('no_licence_processed',["7"]) ; ?> </h4>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>

