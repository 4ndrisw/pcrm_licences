<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();
$path = $CI->uri->segment(3);
$licence_id = $CI->session->userdata('licence_id');

$aColumns = [
    'licence_id',
    db_prefix() . 'tasks.name',
    'flag',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'licences_related_tasks';


$join = [
    'LEFT JOIN ' . db_prefix() . 'tasks ON ' . db_prefix() . 'tasks.id = ' . db_prefix() . 'licences_related_tasks.task_id',
    //'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'licences.project_id',
];

$additionalSelect = [db_prefix() . 'licences_related_tasks.id','task_id'];


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

        if ($aColumns[$i] == 'licence_id') {
            $_data = '<a href="' . admin_url('licences/'.$licence_id.'/' . $aRow['id']) . '">' . $_data . '</a>';
            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . admin_url('licences/propose/' . $aRow['id']) . '">' . _l('propose') . '</a>';
            $_data .= ' | <a href="' . admin_url('licences/update/' . $aRow['id']) . '">' . _l('edit') . '</a>';

            if (has_permission('licences', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('licences/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'flag') {
            $_data = '<a href="#" onclick="licence_remove_item(' . $aRow['licence_id'] . ',' . $aRow['task_id'] . '); return false;">x</a>';
        } 
        $row[] = $_data;

    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
