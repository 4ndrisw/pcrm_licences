<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


            <div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('licence_released'); ?>">
                <?php if(staff_can('view', 'licences') || staff_can('view_own', 'licences')) { ?>
                <div class="panel_s released_tasks-expiring">
                    <div class="panel-body padding-10">
                        <p class="padding-5"><?php echo _l('licence_released'); ?></p>
                        <hr class="hr-panel-heading-dashboard">
                        <?php if (!empty($released_tasks)) { ?>
                            <div class="table-vertical-scroll">
                                <a href="<?php echo admin_url('released_tasks'); ?>" class="mbot20 inline-block full-width"><?php echo _l('home_widget_view_all'); ?></a>
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
                                        <?php foreach ($released_tasks as $task) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo '<a href="' . admin_url("tasks/licence/" . $task["id"] . '">' . $task["name"]) . '</a>'; ?>
                                                </td>
                                                <td>
                                                   <?php echo $task["licence_upt_number"]; ?>
                                                </td>
                                                <td>
                                                    <?php echo _d($task['dateadded']); ?>
                                                </td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="text-center padding-5">
                                <i class="fa fa-check fa-5x" aria-hidden="true"></i>
                                <h4><?php echo _l('no_licence_released',["7"]) ; ?> </h4>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>

