<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php

$this->db->select(array('tbllicences.formatted_number','tbllicences.id AS licence_id'));
//$this->db->select(array('tblstaff.firstname','tblstaff.lastname'));
$this->db->select(array('tbltasks.name'));
$this->db->select(array('tbllicence_items.released AS released'));


$this->db->from('licence_items'); 

$this->db->join('tbltasks', 'tbltasks.id = tbllicence_items.task_id', 'left');
//$this->db->join('tblprojects', 'tbltasks.rel_id = tblprojects.id', 'left');
//$this->db->join('tbltask_assigned', 'tbltask_assigned.taskid = tbltasks.id', 'left');
//$this->db->join('tbllicence_items', 'tbllicence_items.task_id = tbltasks.id', 'left');
$this->db->join('tbllicences', 'tbllicences.id = tbllicence_items.licence_id', 'left');

//$this->db->join('tblstaff', 'tbltask_assigned.staffid = tblstaff.staffid', 'left');

//$this->db->where('tblprojects.status =','4');
$this->db->where('tbltasks.status','5');
//$this->db->where('tblprojects.estimated_hours', NULL);
$this->db->where('tbllicence_items.released', NULL);

//echo $this->db->get_compiled_select(db_prefix() . 'licence_items');

$query = $this->db->get();
$widget_data = $query->result();
if(count($widget_data) == 0){
  return;
}

?>


<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('completed_tasks_no_licence'); ?>">
  <div class="">
    <div class="panel_s">
      <div class="panel-body">
        <div class="widget-dragger"></div>

        <h4 class="pull-left mtop5"><?= _l('completed_tasks_no_licence'); ?></h4>
        <div class="clearfix"></div>
        <div class="row mtop5">
          <hr class="hr-panel-heading-dashboard">
        </div>
        <div class="table-responsive">
        <table id="widget-<?php echo create_widget_id(); ?>" class="table dt-table" data-order-col="1" data-order-type="desc">
            <thead>
                <th>No</th>
                <th>Licence</th>
                <th>Task</th>
            </thead>
            <tbody>
              <?php $i=1;?>
              <?php if (count($widget_data) > 0) { ?>
                <?php foreach ($widget_data as $widget_row) { ?>
                  <tr>
                      <td><?= $i ?></td>
                      <td><a href="<?= admin_url('licences/release/' . $widget_row->licence_id) ?>"><?= $widget_row->formatted_number ?></a></td>
                      <td><?=  $widget_row->name ?></td>
                  </tr>
                  <?php $i++; ?>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="7"><?= _l('not_found') ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>