<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// set auto page breaks
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('dejavusans');

$text = '<div style="text-align:center;"><strong>';
$text .= 'Nomor Sertifikat : ' . $certificate_item_number;
$text .= '</strong></div>';

$pdf->setFontSize('10');
$pdf->ln(45);
$pdf->writeHTML($text, true, 0, true, true);

$pdf->setFontSize('9');


$inspection = $certificate->inspection;
$inspection_date = _d($inspection->date);
$client = $certificate->client;
$client_company = $client->company;
$client_address = $client->address;

$equipment = $certificate->equipment[0];
$equipment_jenis_peralatan = isset($equipment['jenis_peralatan']) ? $equipment['jenis_peralatan'] : 'CEK DATA INSPEKSI';
$equipment_lokasi = isset($equipment['lokasi']) ? $equipment['lokasi'] : 'CEK DATA INSPEKSI';
$equipment_nama_pesawat = isset($equipment['nama_pesawat']) ? $equipment['nama_pesawat'] : 'CEK DATA INSPEKSI';
$equipment_tinggi_tiang_penerima = isset($equipment['tinggi_tiang_penerima']) ? $equipment['tinggi_tiang_penerima'] : 'CEK DATA INSPEKSI';
$equipment_merk = isset($equipment['merk']) ? $equipment['merk'] : 'CEK DATA INSPEKSI';

$equipment_type_model = isset($equipment['type_model']) ? $equipment['type_model'] : 'CEK DATA INSPEKSI';
$equipment_daya_produksi = isset($equipment['daya_produksi']) ? $equipment['daya_produksi'] : 'CEK DATA INSPEKSI';
$equipment_daya_tenaga = isset($equipment['daya_tenaga']) ? $equipment['daya_tenaga'] : 'CEK DATA INSPEKSI';
$equipment_daya_terpasang = isset($equipment['daya_terpasang']) ? $equipment['daya_terpasang'] : 'CEK DATA INSPEKSI';
$equipment_sumber_tenaga = isset($equipment['sumber_tenaga']) ? $equipment['sumber_tenaga'] : 'CEK DATA INSPEKSI';
$equipment_jenis_pemeriksaan = isset($equipment['jenis_pemeriksaan']) ? $equipment['jenis_pemeriksaan'] : 'CEK DATA INSPEKSI';
$equipment_jenis_peralatan = isset($equipment['jenis_peralatan']) ? $equipment['jenis_peralatan'] : 'CEK DATA INSPEKSI';
$equipment_tinggi_bangunan = isset($equipment['tinggi_bangunan']) ? $equipment['tinggi_bangunan'] : 'CEK DATA INSPEKSI';
$equipment_penghantar = isset($equipment['penghantar']) ? $equipment['penghantar'] : 'CEK DATA INSPEKSI';
$equipment_tahun_pemasangan = isset($equipment['tahun_pemasangan']) ? $equipment['tahun_pemasangan'] : 'CEK DATA INSPEKSI';
$equipment_instalatir = isset($equipment['instalatir']) ? $equipment['instalatir'] : 'CEK DATA INSPEKSI';

$office_dinas = $certificate->office->dinas;

$default_regulation = get_option('predefined_regulation_of_'.$certificate->categories);
$equipment_regulasi = !empty($certificate->inspection->equipment['regulasi']) ? $certificate->inspections->equipment['regulasi'] : $default_regulation;

if (!empty($equipment_regulasi)) {
    $regulasi = explode(' -- ', $equipment_regulasi);
    $equipment_regulasi = '';
    $equipment_regulasi .= '<ol class="regulasi">'; 

    foreach($regulasi as $row){
        $equipment_regulasi .= '<li style="margin-left:70;">' .$row. '</li>'; 
    }
    $equipment_regulasi .= '</ol>';
}
$tanggal_inspeksi_raw = isset($inspection->date) ? _d($inspection->date) : '1970-01-01';
$tahun = getYear($tanggal_inspeksi_raw);
$bulan = getMonth($tanggal_inspeksi_raw);
$tanggal = getDay($tanggal_inspeksi_raw);
$tanggal_inspeksi = $tanggal.' '.$bulan.' '.$tahun;

$proposed_date_raw = isset($certificate->proposed_date) ? _d($certificate->proposed_date) : '1970-01-01';
$tahun = getYear($tanggal_inspeksi_raw);
$bulan = getMonth($tanggal_inspeksi_raw);
$tanggal = getDay($tanggal_inspeksi_raw);
$proposed_date = $tanggal.' '.$bulan.' '.$tahun;

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
        <td style="width:200;">Pemilik</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$client_company</td>
    </tr>
    <tr>
        <td style="width:200;">Alamat</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$client_address</td>
    </tr>
    <tr>
        <td style="width:200;">Lokasi Pemeriksaan</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_lokasi</td>
    </tr>
    <tr>
        <td style="width:200;">Tanggal Pemeriksaan</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$tanggal_inspeksi</td>
    </tr>
    <tr>
        <td style="width:200;">Nama Pesawat</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_nama_pesawat</td>
    </tr>
    <tr>
        <td style="width:200;">Jenis Penyalur Petir</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_jenis_peralatan</td>
    </tr>
    <tr>
        <td style="width:200;">Bangunan yang dilindungi</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_lokasi</td>
    </tr>
    <tr>
        <td style="width:200;">Tinggi Bangunan</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_tinggi_bangunan</td>
    </tr>
    <tr>
        <td style="width:200;">Jenis Pengantar</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_penghantar</td>
    </tr>
    <tr>
        <td style="width:200;">Tahun Pemasangan</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_tahun_pemasangan</td>
    </tr>
    <tr>
        <td style="width:200;">Instalatir</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_instalatir</td>
    </tr>
    <tr>
        <td style="width:200;">Tinggi Tiang Penerima</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_tinggi_tiang_penerima</td>
    </tr>
    <tr>
        <td style="width:200;">Merk</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_merk</td>
    </tr>
    <tr>
        <td style="width:200;">Type / Model</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_type_model</td>
    </tr>
    <tr>
        <td style="width:200;">Jenis Pemeriksaan</td>
        <td style="width:45;">:</td>
        <td style="width:400;">$equipment_jenis_pemeriksaan</td>
    </tr>
    <tr>
        <td style="width:200;">Referensi</td>
        <td style="width:10;">:</td>
        <td style="width:435;">$equipment_regulasi</td>
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
$pdf->write2DBarcode($qrcode, 'QRCODE,M', $x_pos+70, $y_pos+8, 40, 40, $style, 'N');


$assigned = '<div style="text-align:center;">';
$assigned .= get_option('licence_certificate_assign_city') .', '. $proposed_date .'<br />';
$assigned .= '<strong>' . strtoupper(get_option('invoice_company_name'));
$assigned .= '<br /><br /><br /><br /><br /><br /><br /><br /><br />';
$assigned .= '<span style="text-decoration: underline;">' . strtoupper(get_staff_full_name($certificate->assigned)) .'</span><br />';
$assigned .= strtoupper(get_option('licence_certificate_assign_position'));
$assigned .= '</strong></div>';


$pdf->MultiCell(0, 0, $assigned, 0, 'R', 0, 1, $x_pos+100, $y_pos+4, true, 0, true);
