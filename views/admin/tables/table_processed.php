<?php

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
$sTable       = db_prefix() . 'licence_items';


$join = [
    'LEFT JOIN ' . db_prefix() . 'licences ON ' . db_prefix() . 'licences.id = ' . db_prefix() . 'licence_items.licence_id',
    'LEFT JOIN ' . db_prefix() . 'tasks ON ' . db_prefix() . 'tasks.id = ' . db_prefix() . 'licence_items.task_id',
    'LEFT JOIN ' . db_prefix() . 'taggables ON ' . db_prefix() . 'taggables.rel_id = ' . db_prefix() . 'tasks.id',
    'LEFT JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id',
];

$additionalSelect = [db_prefix() . 'licence_items.id','licence_id','task_id'];


$where  = [];
array_push($where, 'AND ' . db_prefix() . 'licence_items.licence_id = "'.$licence_id.'"');
array_push($where, 'AND ' . db_prefix() . 'licence_items.released IS NULL');
array_push($where, 'AND ' . db_prefix() . 'tasks.rel_type = "project"');
array_push($where, 'AND ' . db_prefix() . 'licences.status = "2"');


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == db_prefix() . 'tasks.name') {
            $_data = '<a href="' . admin_url('licences/licence_proposed/' . $aRow['licence_id'] . '/' . $aRow['task_id']) . '">' . $_data . '</a>';
        }elseif ($aColumns[$i] == 'flag') {
            $_data = '<a class="btn btn-success" title = "'._l('release_this_item').'" href="#" onclick="licence_add_released_item(' . $licence_id . ','. $project_id . ',' . $aRow['task_id'] . '); return false;">+</a>';
        } 
        $row[] = $_data;

    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
