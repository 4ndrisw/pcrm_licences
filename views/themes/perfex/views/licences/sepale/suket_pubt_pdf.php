<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// set auto page breaks
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('dejavusans');

$text = 'Nomor Sertifikat : ' . $certificate_item_number;

$pdf->ln(35);
$pdf->Write(0, $text, '', 0, 'C', true, 0, false, false, 0);

$inspection = $certificate->inspection;
$inspection_date = _d($inspection->date);
$client = $certificate->client;
$client_company = $client->company;
$client_address = $client->address;

$equipment = $certificate->equipment[0];
$equipment_jenis_pesawat = $equipment['jenis_pesawat'];
$equipment_lokasi = $equipment['lokasi'];
$equipment_nama_pesawat = $equipment['nama_pesawat'];
$equipment_pabrik_pembuat = $equipment['pabrik_pembuat'];
$equipment_tahun_pembuatan = $equipment['tahun_pembuatan'];

$equipment_nomor_seri = $equipment['nomor_seri'];
$equipment_nomor_unit = $equipment['nomor_unit'];
$equipment_kapasitas =$equipment['kapasitas'];
$equipment_digunakan_untuk = $equipment['digunakan_untuk'];
$equipment_jenis_pemeriksaan = $equipment['jenis_pemeriksaan'];
$equipment_jenis_bejana = $equipment['jenis_bejana'];
$office_dinas = $certificate->office->dinas;
$regulasi = explode(' AND ', $equipment['regulasi']);
$equipment_regulasi = '';
$equipment_regulasi .= '<ol class="regulasi">'; 

foreach($regulasi as $row){
    $equipment_regulasi .= '<li style="margin-left:70;">' .$row. '</li>'; 
}
$equipment_regulasi .= '</ol>'; 

//var_dump($certificate->equipment);

// Set some content to print
$pdf->ln(2);
$html = <<<EOD
<style>
    tr > ol {
    margin-left: 76px;
    }
</style>
<table cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Pemilik</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$client_company</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Alamat</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$client_address</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Lokasi Unit</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_lokasi</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Jenis Pesawat</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_jenis_pesawat</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Tanggal Pemeriksaan</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$inspection_date</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Nama Pesawat</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_nama_pesawat</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Jenis Bejana</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_jenis_bejana</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Pabrik Pembuat</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_pabrik_pembuat</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Tahun Pembuatan</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_tahun_pembuatan</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">No Seri / No Unit</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_nomor_seri / $equipment_nomor_unit</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Kapasitas</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_kapasitas</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Digunakan untuk</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_digunakan_untuk</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Jenis Pemeriksaan</td>
        <td style="border-bottom:1px solid black; width:45;">:</td>
        <td style="border-bottom:1px solid black; width:400;">$equipment_jenis_pemeriksaan</td>
    </tr>
    <tr>
        <td style="border-bottom:1px solid black; width:200;">Referensi</td>
        <td style="border-bottom:1px solid black; width:10;">:</td>
        <td style="border-bottom:1px solid black; width:435;">$equipment_regulasi</td>
    </tr>
</table>
EOD;

// Print text using writeHTMLCell()
//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
//$pdf->writeHTML($html, true, false, false, false, '');

// store old margin values
$margins = $pdf->getMargins();

// set new left margin
$pdf->SetLeftMargin(30);

// output the HTML content
// restore the left margin
$pdf->SetLeftMargin($margins['left']);

$pdf->writeHTML($html, true, 0, true, true);
$blank_line ="\r\n";
$pdf->Write(0, $blank_line, '', 0, 'J', true, 0, false, false, 0);

$pdf->ln(2);


$text = "Setelah melakukan pemeriksaan dan pengujian " .$equipment_nama_pesawat. " di tempat dan pada tanggal tersebut di atas maka dengan ini kami menyimpulkan:" ."\r\n \r\n";
$text .= $equipment_nama_pesawat ." tersebut yang digunakan di ". $equipment_lokasi ." berada dalam keadaan baik dan memenuhi syarat K3 sehingga dapat diajukan ke Dinas Tenaga Kerja dan Transmigrasi setempat untuk mendapatkan surat keterangan peralatan tersebut." ."\r\n \r\n";
$text .= "Demikian sertifikat ini dibuat dengan sesungguhnya berdasarkan hasil pemeriksaan dan pengujian yang dilakukan sesuai ketentuan ". $office_dinas ."." ."\r\n \r\n";


$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->Write(0, $text, '', 0, 'J', true, 0, false, false, 0);

$qrcode ="";
$qrcode .=$client_company ."\r\n";
$qrcode .=$certificate_item_number . "\r\n";
$qrcode .=$equipment_nama_pesawat ."\r\n";
$qrcode .= $certificate->proposed_date;



// define barcode style// set style for barcode
$style = array(
    'border' => 0,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
$x_pos = $pdf->getX();
$y_pos = $pdf->getY();
// QRCODE,L : QR-CODE Low error correction
$pdf->write2DBarcode($qrcode, 'QRCODE,M', $x_pos+70, $y_pos+2, 40, 40, $style, 'N');


$assigned = '<div style="text-align:center;">';
$assigned .= $certificate->proposed_date;
$assigned .= '<br /><br /><br /><br /><br /><br /><br /><br />';
$assigned .= get_staff_full_name($certificate->assigned);
$assigned .= '</div>';

$pdf->MultiCell(0, 0, $assigned, 0, 'R', 0, 1, $x_pos+100, $y_pos+4, true, 0, true);
