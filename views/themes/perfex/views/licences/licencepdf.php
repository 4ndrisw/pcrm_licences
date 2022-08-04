<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('licence_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . $licence_number . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . licence_status_color_pdf($status) . ');text-transform:uppercase;">' . format_licence_status($status, '', false) . '</span>';
}

// Add logo
$info_left_column .= pdf_logo_url();
// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(4);

$organization_info = '<div style="color:#424242;">';
    $organization_info .= format_organization_info();
$organization_info .= '</div>';

// Licence to
$licence_info = '<b>' . _l('licence_to') . '</b>';
$licence_info .= '<div style="color:#424242;">';
$licence_info .= format_office_info($licence->office, 'licence', 'billing', true);
$licence_info .= '</div>';

$CI = &get_instance();
$CI->load->model('licences_model');
//$licence_members = $CI->licences_model->get_licence_members($licence->id,true);

if (!empty($licence->reference_no)) {
    $licence_info .= _l('reference_no') . ': ' . $licence->reference_no . '<br />';
}

$left_info  = $swap == '1' ? $licence_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $licence_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(4);
$project = get_project($licence->project_id);

$list = explode(' ',$project->name);
$project_name = $list[0];
$project_date = _d($project->start_date);

$date = $licence->date;
$today = 'Pada hari ini, ';
$licence_declare = 'kami mengajukan permohonan penerbitan Surat Ketarangan Layak K3 untuk peralatan sebagai berikut:';
$getDayName = getDayName($date);
$getDay = getDay($date);
$getMonth = getMonth($date);
$getYear = getYear($date);

$txt = <<<EOD
$today $getDayName $getDay $getMonth $getYear, $licence_declare \r\n
EOD;

// print a block of text using Write()
$pdf->write(0, $txt, '', 0, 'J', true, 0, false, false, 0);

$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 2));

// The items table

$items  = '';
$items .= '<table style="padding:5,10,5,10" border="1" class="table table-bordered table-jobreport-items">';
$items .=    '<thead>';
$items .=        '<tr>';
$items .=            '<th width="50" align="center">No#</th>';
$items .=            '<th width="450" align="center">Items</th>';
$items .=            '<th width="200" align="center">Tags</th>';
$items .=            '<th width="100" align="center">Item/Lot</th>';
$items .=        '</tr>';
$items .=    '</thead>';
$items .=    '<tbody>';
        $i=1;
        foreach($licence->proposed_items as $item){        
            
$items .=            '<tr>';
$items .=                '<td width="50" align="right">' .$i.' </td>';
$items .=                '<td width="450">' .$item['task_name']. '</td>';
$items .=                '<td width="200">' .$item['tags_name']. '</td>';
$items .=                '<td width="100" align="center">' .$item['count']. '</td>';
$items .=            '</tr>';
            
             $i++; 
         } 
$items .=    '</tbody>';
$items .= '</table>';

pdf_multi_row($items, '', $pdf, ($dimensions['wk'] / 1) - $dimensions['lm']);

$pdf->ln(4);

$pdf->SetFont($font_name, '', $font_size);

$assigned_path = <<<EOF
        <img src="$licence->assigned_path">
    EOF;    
$assigned_info = '<div style="text-align:center;">';
    $assigned_info .= get_option('invoice_company_name') . '<br />';
    $assigned_info .= $assigned_path . '<br />';

if ($licence->assigned != 0 && get_option('show_assigned_on_licences') == 1) {
    $assigned_info .= get_staff_full_name($licence->assigned);
}
$assigned_info .= '</div>';

$acceptance_path = <<<EOF
    <img src="$licence->acceptance_path">
EOF;
$client_info = '<div style="text-align:center;">';
    $client_info .= $licence->client_company .'<br />';

if ($licence->signed != 0) {
    $client_info .= _l('licence_signed_by') . ": {$licence->acceptance_firstname} {$licence->acceptance_lastname}" . '<br />';
    $client_info .= _l('licence_signed_date') . ': ' . _dt($licence->acceptance_date_string) . '<br />';
    $client_info .= _l('licence_signed_ip') . ": {$licence->acceptance_ip}" . '<br />';

    $client_info .= $acceptance_path;
    $client_info .= '<br />';
}
$client_info .= '</div>';


$left_info  = $swap == '1' ? $client_info : $assigned_info;
$right_info = $swap == '1' ? $assigned_info : $client_info;
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
$licence_closing = 'Demikian permohonan ini kami sampaikan, atas perhatian dan kerjasamanya kami ucapkan terima kasih.';
$txt = <<<EOD
$licence_closing \r\n
EOD;

$pdf->ln(4);
// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'J', true, 0, false, false, 0);

if (!empty($licence->clientnote)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('licence_order'), 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $licence->clientnote, 0, 1, false, true, 'L', true);
}

if (!empty($licence->terms)) {
    $pdf->Ln(4);
    $pdf->SetFont($font_name, 'B', $font_size);
    $pdf->Cell(0, 0, _l('licence_terms_and_conditions') . ":", 0, 1, 'L', 0, '', 0);
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->Ln(2);
    $pdf->writeHTMLCell('', '', '', '', $licence->terms, 0, 1, false, true, 'L', true);
}

