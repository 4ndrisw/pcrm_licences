<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$path = $CI->uri->segment(3);
$licence_id = $CI->session->userdata('licence_id');
$project_id = $CI->session->userdata('project_id');

$aColumns = [
    db_prefix() . 'tasks.name',
    db_prefix() . 'tags.name',
    'flag',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'tasks';


$join = [
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'tasks.rel_id',
    'LEFT JOIN ' . db_prefix() . 'taggables ON ' . db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'tasks.id',
    'LEFT JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id',
    'LEFT JOIN ' . db_prefix() . 'licences_related_tasks ON ' . db_prefix() . 'tasks.id = ' . db_prefix() . 'licences_related_tasks.task_id',
];

$additionalSelect = [db_prefix() . 'licences_related_tasks.licence_id', 
                     db_prefix() . 'projects.id AS project_id', 
                     db_prefix() . 'tasks.id AS task_id'];


$where  = [];
array_push($where, 'AND ' . db_prefix() . 'tasks.rel_type = "project"');
array_push($where, 'AND ' . db_prefix() . 'tasks.rel_id = "'.$project_id.'"');
//array_push($where, 'AND ' . db_prefix() . 'licences_related_tasks.licence_id = "'.$licence_id.'"');
array_push($where, 'AND ' . db_prefix() . 'licences_related_tasks.task_id IS NULL');


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == db_prefix() . 'tasks.name') {
            $_data = '<a href="' . admin_url('tasks/view/' . $aRow['task_id']) . '" target = "_blank">' . $_data . '</a>';
        }elseif ($aColumns[$i] == 'flag') {
            $_data = '<a class="btn btn-success" title = "'._l('propose_this_item').'" href="#" onclick="licence_add_proposed_item(' . $licence_id . ','. $project_id . ',' . $aRow['task_id'] . '); return false;">+</a>';
        } 
        $row[] = $_data;

    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
