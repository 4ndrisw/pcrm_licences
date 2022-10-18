<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// set auto page breaks
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('dejavusans');

$inspection = $suket->inspection;
$equipment = $suket->equipment[0];
$licence_item = $suket->licence_items[0];

$tanggal_inspeksi_raw = isset($inspection->date) ? _d($inspection->date) : '1970-01-01';
$tanggal_suket_raw = isset($licence_item->tanggal_suket) ? $suket->licence_items[0]->tanggal_suket : '1970-01-01';
$expired_suket_raw = isset($licence_item->expired) ? $suket->licence_items[0]->expired : '1970-01-01';
$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$inspection = $suket->inspection;
$inspection_no = format_licence_item_number($inspection->id, $suket->categories, $suket->task_id);

$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$equipment_lokasi = isset($equipment['lokasi']) ? $equipment['lokasi'] : '';
$equipment_nomor_pengesahan = isset($equipment['nomor_pengesahan']) ? $equipment['nomor_pengesahan'] : '';
$equipment_nama_pesawat = isset($equipment['nama_pesawat']) ? $equipment['nama_pesawat'] : '';
$equipment_tahun_pembuatan = isset($equipment['tahun_pembuatan']) ? $equipment['tahun_pembuatan'] : '';
$equipment_tempat_pembuatan = isset($equipment['tempat_pembuatan']) ? $equipment['tempat_pembuatan'] : '';

$equipment_merk_engine = isset($equipment['merk_engine']) ? $equipment['merk_engine'] : '';
$equipment_nomor_seri_generator = isset($equipment['nomor_seri_generator']) ? $equipment['nomor_seri_generator'] : '';
$equipment_type_model_engine = isset($equipment['type_model_engine']) ? $equipment['type_model_engine'] : '';
$equipment_pabrik_pembuat_engine = isset($equipment['pabrik_pembuat_engine']) ? $equipment['pabrik_pembuat_engine'] : '';

$equipment_merk = !empty($equipment_merk_engine) ? $equipment_merk_engine : '';
$equipment_nomor_seri = !empty($equipment_nomor_seri_generator) ? $equipment_nomor_seri_generator : '';
$equipment_type_model = !empty($equipment_type_model_engine) ? $equipment_type_model_engine : '';
$equipment_pabrik_pembuat = !empty($equipment_pabrik_pembuat_engine) ? $equipment_pabrik_pembuat_engine : '';

$equipment_merk_generator = isset($equipment['merk_generator']) ? $equipment['merk_generator'] : '';
$equipment_nomor_seri_engine = isset($equipment['nomor_seri_engine']) ? $equipment['nomor_seri_engine'] : '';
$equipment_type_model_generator = isset($equipment['type_model_generator']) ? $equipment['type_model_generator'] : '';
$equipment_pabrik_pembuat_generator =isset($equipment['pabrik_pembuat_generator']) ? $equipment['pabrik_pembuat_generator'] : '';

$equipment_merk = !empty($equipment_merk_generator) ? $equipment_merk_generator : $equipment_merk;
$equipment_nomor_seri = !empty($equipment_nomor_seri_generator) ? $equipment_nomor_seri_generator : $equipment_nomor_seri;
$equipment_type_model = !empty($equipment_type_model_generator) ? $equipment_type_model_generator : $equipment_type_model;
$equipment_pabrik_pembuat = !empty($equipment_pabrik_pembuat_generator) ? $equipment_pabrik_pembuat_generator : $equipment_pabrik_pembuat;

$equipment_nomor_unit = isset($equipment['nomor_unit']) ? $equipment['nomor_unit'] : '';
$equipment_klasifikasi = isset($equipment['klasifikasi']) ? $equipment['klasifikasi'] : '';
$equipment_kapasitas = isset($equipment['kapasitas']) ? $equipment['kapasitas'] : '';
$equipment_jumlah_silinder = isset($equipment['jumlah_silinder']) ? $equipment['jumlah_silinder'] : '';
$equipment_jenis_pemeriksaan = isset($equipment['jenis_pemeriksaan']) ? $equipment['jenis_pemeriksaan'] : '';

$office_dinas = $suket->office->dinas;
$regulasi = explode(' AND ', $equipment['regulasi']);
$equipment_regulasi = '';
$equipment_regulasi .= '<ol class="regulasi">';

$office_dinas = $suket->office->dinas;
$regulasi = explode(' AND ', $equipment['regulasi']);
$equipment_regulasi = '';
$equipment_regulasi .= '<ol class="regulasi">';

$tahun = getYear($tanggal_inspeksi_raw);
$bulan = getMonth($tanggal_inspeksi_raw);
$tanggal = getDay($tanggal_inspeksi_raw);
$tanggal_inspeksi = $tanggal.' '.$bulan.' '.$tahun;

$tahun = getYear($expired_suket_raw);
$bulan = getMonth($expired_suket_raw);
$tanggal = getDay($expired_suket_raw);
$expired_bulan = $bulan.' '.$tahun;
$expired = $tanggal.' '.$bulan.' '.$tahun;

$tahun = getYear($tanggal_suket_raw);
$bulan = getMonth($tanggal_suket_raw);
$tanggal = getDay($tanggal_suket_raw);
$tanggal_suket = $tanggal.' '.$bulan.' '.$tahun;

$nomor_suket = $licence_item->nomor_suket;
$ahli_k3 = $inspection->assigned_item;

$text = '<div style="text-align:center;"><strong>';
$text .= 'SURAT KETERANGAN' . '<br>';
$text .= '<span style="text-decoration: underline;">HASIL PEMERIKSAAN DAN PENGUJIAN' .'</span><br>';
$text .= 'Nomor : ' . $nomor_suket;
$text .= '</strong></div>';

$pdf->ln(20);
$pdf->writeHTML($text, true, 0, true, true);

$text = '<div style="text-align:justify;">';
$text .= 'Berdasarkan hasil pemeriksaan dan pengujian yang dilakukan oleh PJK3 ';
$text .= get_option('invoice_company_name');
$text .= ' pada tanggal ' .$tanggal_inspeksi. ' terhadap ' .$equipment_nama_pesawat. ' dapat diterangkan bahwa :' . "\r\n";
$text .= '</div>';

$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->ln(4);

$pdf->writeHTML($text, true, 0, true, true);

foreach($regulasi as $row){
    $equipment_regulasi .= '<li style="margin-left:70;">' .$row. '</li>';
}
$equipment_regulasi .= '</ol>';

//var_dump($office_short_name);

$html = <<<EOD
<style>
    tr > ol {
    margin-left: 76px;
    }
</style>
<table cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td style="width:20;">A.</td>
        <td style="width:310;">Data Umum Objek Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:370;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">1. Jenis objek K3 yang Diuji</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_nama_pesawat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">2. Nama Perusahaan / Pemilik</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$client_company</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">3. Alamat Perusahaan</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$client_address</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">2. Lokasi Objek yang Diuji</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_lokasi</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;"></td>
        <td style="width:10;"></td>
        <td style="width:370;"></td>
    </tr>
    <tr>
        <td style="width:20;">B.</td>
        <td style="width:310;">Data Teknis Objek Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:370;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">1. Merk / Type</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_merk / $equipment_type_model</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">2. Pabrik Pembuat</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_pabrik_pembuat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">3. Tempat / Tahun</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_tempat_pembuatan / $equipment_tahun_pembuatan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">4. Nomor Seri / Nomor Unit</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_nomor_seri / $equipment_nomor_unit</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">5. Kapasitas Daya</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_kapasitas</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">6. Jumlah Silinder</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_jumlah_silinder</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">7. Klasifikasi</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_klasifikasi</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">8. Jenis pemeriksaan</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_jenis_pemeriksaan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;"></td>
        <td style="width:10;"></td>
        <td style="width:370;"></td>
    </tr>
</table>
EOD;

// Print text using writeHTMLCell()
//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
//$pdf->writeHTML($html, true, false, false, false, '');

// store old margin values
$margins = $pdf->getMargins();

// set new left margin
$pdf->SetLeftMargin(20);

// output the HTML content
// restore the left margin
$pdf->SetLeftMargin($margins['left']);

$pdf->ln(2);
$pdf->writeHTML($html, true, 0, true, true);
$blank_line ="\r\n";

$pdf->Write(0, $blank_line, '', 0, 'J', true, 0, false, false, 0);


$text = '<div style="text-align:center;"><strong>';
$text .= 'MEMENUHI' ."<br />";
$text .= 'PERSYARATAN KESELAMATAN DAN KESEHATAN KERJA';
$text .= '</strong></div>';
$pdf->writeHTML($text, true, 0, true, true);

$pdf->ln(5);

$text = '<div style="text-align:justify;">';
$text .= 'Demikian surat keterangan ini dibuat dengan sebenarnya agar dapat digunakan sebagaimana mestinya dan berlaku sepanjang objek pengujian tidak dilakukan perubahan dan / atau sampai dilakukan pengujian selanjutnya paling lambat tanggal ';
$text .= '<strong>'. $expired .'</strong>.';
$text .= '</div>';
$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->writeHTML($text, true, 0, true, true);


$left_info = '<div style="text-align:center;">';
$left_info .= "<br /><br />";
$left_info .= 'Mengetahui,' .'<br />';
$left_info .= 'Kepala ' . $suket->office->dinas;
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= '<span style="text-decoration: underline;"><strong>' . $licence_item->kepala_dinas_nama . '</strong></span>';
$left_info .= "<br />";
$left_info .= '<strong>' . $licence_item->kepala_dinas_nip .'</strong>';
$left_info .= '</div>';


$right_info = '<div style="text-align:center;">';
$right_info .= "Serang, $tanggal_suket" .'<br /><br />';
$right_info .= 'Yang Melakukan Evaluasi,' .'<br />';
$right_info .= 'Pengawas Ketenagakerjaan' .'<br />';
$right_info .= "Spesialis PTP";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= '<span style="text-decoration: underline;"><strong>' . $licence_item->pengawas_nama . '</strong></span>';
$right_info .= "<br />";
$right_info .= '<strong>' . $licence_item->pengawas_nip . '</strong>';
$right_info .= '</div>';

$pdf->ln(2);
//pdf_multi_row_html($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);


$html = <<<EOD

<table cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td style="text-align:center;">$left_info</td>
        <td style="text-align:center;">$right_info</td>
    </tr>
</table>
EOD;

$pdf->writeHTML($html, true, 0, true, true);
