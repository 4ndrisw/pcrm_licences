<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// set auto page breaks
$pdf->SetAutoPageBreak(true, 5);

$inspection = $suket->inspection;
$inspection_date = _d($inspection->date);
$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$equipment = $suket->equipment[0];

$equipment_jenis_pesawat = isset($equipment['jenis_pesawat']) ? $equipment['jenis_pesawat'] : 'CEK DATA INSPEKSI';

//var_dump($suket->equipment);

$pdf->SetFont('dejavusans');

$filePath = strtolower($equipment_jenis_pesawat) .'_pdf.php';
$text = 'File suket_';
$text .= $filePath;
$text .= ' Tidak ada';

$pdf->ln(35);
$pdf->Write(0, $text, '', 0, 'C', true, 0, false, false, 0);
