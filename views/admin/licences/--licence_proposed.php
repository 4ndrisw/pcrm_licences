<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>


            <div class="widget dataTables_wrapper form-inline dt-bootstrap no-footer" id="widget-proposed" data-name="<?php echo _l('licence_proposed'); ?>">
                <?php if(staff_can('view', 'licences') || staff_can('view_own', 'licences')) { ?>
                <div class="panel_s proposed_tasks-expiring">
                    <div class="panel-body padding-10">
                        <p class="padding-5"><?php echo _l('licence_proposed'); ?></p>
                        <hr class="hr-panel-heading-dashboard">
                        <?php if (!empty($proposed_tasks)) { ?>
                            <div class="table-vertical-scroll">
                                <a href="<?php echo admin_url('proposed_tasks'); ?>" class="mbot20 inline-block full-width"><?php echo _l('home_widget_view_all'); ?></a>
                                <table id="widget-<?php echo create_widget_id(); ?>" class="table dt-table licence-items-proposed" data-order-col="0" data-order-type="DESC">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('select'); ?></th>
                                            <th><?php echo _l('licence_number'); ?> #</th>
                                            <th><?php echo _l('licence_list_date'); ?></th>
                                            <th><?php echo 'X'; ?> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       <?php $i = 1; ?>
                                        <?php foreach ($proposed_tasks as $task) { ?>
                                            <tr>
                                                <td>
                                                   <?= $i ?> 
                                                </td>
                                                <td>
                                                    <?php echo '<a href="' . admin_url("tasks/licence/" . $task["id"] . '">' . $task["name"]) . '</a>'; ?>
                                                </td>
                                                <td>
                                                    <?php echo _d($task['dateadded']); ?>
                                                </td>
                                                <td>
                                                    <?php echo ' <a href="#" class="_delete" onclick="licence_remove_item(' . $licence->id . ',' . $task['id'] . '); return false;">x</a>  '; ?>
                                                </td>
                                            </tr>
                                        <?php $i++; } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="text-center padding-5">
                                <i class="fa fa-check fa-5x" aria-hidden="true"></i>
                                <h4><?php echo _l('no_licence_proposed',["7"]) ; ?> </h4>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>

