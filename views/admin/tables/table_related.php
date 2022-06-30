<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$path = $CI->uri->segment(3);
$licence_id = $CI->session->userdata('licence_id');
$project_id = $CI->session->userdata('project_id');

$aColumns = [
    db_prefix() . 'tasks.name',
    'equipment_name',
    db_prefix() . 'tags.name',
    'flag',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'licence_items';


$join = [
    'RIGHT JOIN ' . db_prefix() . 'tasks ON ' . db_prefix() . 'licence_items.task_id = ' . db_prefix() . 'tasks.id',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'tasks.rel_id',
    'LEFT JOIN ' . db_prefix() . 'taggables ON ' . db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'tasks.id',
    'LEFT JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id',
];

$additionalSelect = [db_prefix() . 'licence_items.id','licence_id',db_prefix() . 'tasks.id as task_id'];


$where  = [];
array_push($where, 'AND ' . db_prefix() . 'projects.id = "'.$project_id.'"');
array_push($where, 'AND ' . db_prefix() . 'tasks.rel_type = "project"');
array_push($where, 'AND ' . db_prefix() . 'licence_items.id IS NULL');


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == db_prefix() . 'tasks.name') {
            $_data = '<a href="' . admin_url('tasks/view/' . $aRow['task_id']) . '" target = "_blank">' . $_data . '</a>';
            $_data .= '<a href="#" onclick="edit_task_inline_description(this,457); return false;" class="pull-left mright5 mleft5 font-medium-xs"><i class="fa fa-pencil-square-o"></i></a>';

        }elseif ($aColumns[$i] == 'equipment_name') {
            $_data = '<div data-cid="'.$aRow['task_id'].'">aaaa-'.$aRow['task_id'].'</div>';
            $_data .= '<div class="pull-right"><a class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal_add_new"> Add New</a></div>';

        }elseif ($aColumns[$i] == 'flag') {
            $_data = '<a class="btn btn-success" title = "'._l('propose_this_item').'" href="#" onclick="licence_add_proposed_item(' . $licence_id . ','. $project_id . ',' . $aRow['task_id'] . '); return false;">+</a>';
        } 
        $row[] = $_data;

    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
