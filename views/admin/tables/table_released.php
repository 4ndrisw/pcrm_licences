<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$path = $CI->uri->segment(3);
$licence_id = $CI->session->userdata('licence_id');

$aColumns = [
    db_prefix() . 'tasks.name',
    db_prefix() . 'tags.name',
    'flag',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'licences_related_tasks';


$join = [
    'LEFT JOIN ' . db_prefix() . 'tasks ON ' . db_prefix() . 'tasks.id = ' . db_prefix() . 'licences_related_tasks.task_id',
    'LEFT JOIN ' . db_prefix() . 'taggables ON ' . db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'tasks.id',
    'LEFT JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id',
];

$additionalSelect = [db_prefix() . 'licences_related_tasks.id','licence_id','task_id'];


$where  = [];
array_push($where, 'AND ' . db_prefix() . 'licences_related_tasks.licence_id = "'.$licence_id.'"');
array_push($where, 'AND ' . db_prefix() . 'tasks.rel_type = "project"');


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == db_prefix() . 'tasks.name') {
            $_data = '<a href="' . admin_url('tasks/view/' . $aRow['task_id']) . '" target = "_blank">' . $_data . '</a>';
            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'flag') {
            $_data = '<a class="btn btn-danger" title = "'._l('remove_this_item').'" href="#" onclick="licence_remove_item(' . $aRow['licence_id'] . ',' . $aRow['task_id'] . '); return false;">x</a>';
        } 
        $row[] = $_data;

    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
