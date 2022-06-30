<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'formatted_number',
    'company',
    'proposed_date',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'licences';


$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'licences.clientid',
    //'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'licences.project_id',
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], ['id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        $path = $this->ci->uri->segment(3);
        if ($aColumns[$i] == 'formatted_number') {
            $_data = '<a href="' . admin_url('licences/'.$path.'/' . $aRow['id']) . '">' . $_data . '</a>';
            $_data .= '<div class="row-options">';
            $_data .= '<a href="' . admin_url('licences/propose/' . $aRow['id']) . '">' . _l('propose') . '</a>';
            $_data .= ' | <a href="' . admin_url('licences/update/' . $aRow['id']) . '">' . _l('edit') . '</a>';

            if (has_permission('licences', '', 'delete')) {
                $_data .= ' | <a href="' . admin_url('licences/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
            }
            $_data .= '</div>';
        } elseif ($aColumns[$i] == 'date') {
            $_data = _d($_data);
        } 
        $row[] = $_data;

    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
