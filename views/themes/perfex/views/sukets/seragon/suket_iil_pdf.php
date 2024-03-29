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

$equipment_jenis_pesawat = isset($equipment['jenis_pesawat']) ? $equipment['jenis_pesawat'] : '';
$equipment_lokasi = isset($equipment['lokasi']) ? $equipment['lokasi'] : '';
$equipment_nomor_pengesahan = isset($equipment['nomor_pengesahan']) ? $equipment['nomor_pengesahan'] : '';
$equipment_nama_pesawat = isset($equipment['nama_pesawat']) ? $equipment['nama_pesawat'] : '';
$equipment_pabrik_pembuat = isset($equipment['pabrik_pembuat']) ? $equipment['pabrik_pembuat'] : '';
$equipment_tahun_pembuatan = isset($equipment['tahun_pembuatan']) ? $equipment['tahun_pembuatan'] : '';
$equipment_tempat_pembuatan = isset($equipment['tempat_pembuatan']) ? $equipment['tempat_pembuatan'] : '';

$equipment_daya_terpasang = isset($equipment['daya_terpasang']) ? $equipment['daya_terpasang'] : '';
$equipment_daya_penerangan = isset($equipment['daya_penerangan']) ? $equipment['daya_penerangan'] : '';
$equipment_daya_terpasang = isset($equipment['daya_terpasang']) ? $equipment['daya_terpasang'] : '';
$equipment_daya_tenaga = isset($equipment['daya_tenaga']) ? $equipment['daya_tenaga'] : '';

$equipment_sumber_tenaga =isset($equipment['sumber_tenaga']) ? $equipment['sumber_tenaga'] : '';
$equipment_jenis_arus = isset($equipment['jenis_arus']) ? $equipment['jenis_arus'] : '';

$equipment_tahun_pemasangan = isset($equipment['tahun_pemasangan']) ? $equipment['tahun_pemasangan'] : '';
$equipment_instalatir = isset($equipment['instalatir']) ? $equipment['instalatir'] : '';
$equipment_jenis_tegangan = isset($equipment['jenis_tegangan']) ? $equipment['jenis_tegangan'] : '';
$equipment_jenis_pemeriksaan = isset($equipment['jenis_pemeriksaan']) ? $equipment['jenis_pemeriksaan'] : '';

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

$text = 'SURAT KETERANGAN' ."\r\n";
$text .= 'HASIL PEMERIKSAAN DAN PENGUJIAN' ."\r\n";
$text .= 'Nomor : ' . $nomor_suket;

$pdf->ln(20);
$pdf->Write(0, $text, '', 0, 'C', true, 0, false, false, 0);

$text = 'Berdasarkan hasil pemeriksaan dan pengujian yang dilakukan oleh PJK3 ';
$text .= get_option('invoice_company_name');
$text .= ' pada tanggal ' .$tanggal_inspeksi. ' terhadap ' .$equipment_nama_pesawat. ' dapat diterangkan bahwa :' . "\r\n";

$pdf->SetLeftMargin(24);
$pdf->SetRightMargin(24);
$pdf->ln(4);
$pdf->Write(0, $text, '', 0, 'J', true, 0, false, false, 0);

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
        <td style="width:310;">1. Jenis objek K3 yang diuji</td>
        <td style="width:10;">:</td>
        <td style="width:370;">Instalasi Listrik</td>
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
        <td style="width:310;">4. Lokasi Objek K3 yang Diuji</td>
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
        <td style="width:310;">1. Sumber Tenaga</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_sumber_tenaga</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">2. Tahun Pemasangan</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_tahun_pemasangan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">3. Daya Terpasang</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_daya_terpasang</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">4. Untuk Penerangan</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_daya_penerangan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">5. Untuk Tenaga</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_daya_tenaga</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">6. Jenis Arus</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_jenis_arus</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">7. Jenis pemeriksaan</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_jenis_pemeriksaan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;"></td>
        <td style="width:10;"></td>
        <td style="width:370;"></td>
    </tr>
    <tr>
        <td style="width:20;">C.</td>
        <td style="width:450;">Hasil Pemeriksaan dan Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:0;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">1. Pemeriksaan Visual</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">2. Pemeriksaan Dokumen</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">3. Pengujian</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;"></td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;">D.</td>
        <td style="width:310;">Kesimpulan</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:650;">Berdasarkan hasil pemeriksaan dan pengujian tersebut <strong>$equipment_nama_pesawat</strong></td>
        <td style="width:5;"></td>
        <td style="width:5;"></td>
    </tr>
</table>
EOD;

// Print text using writeHTMLCell()
//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
//$pdf->writeHTML($html, true, false, false, false, '');

// store old margin values
$margins = $pdf->getMargins();

// set new left margin
$pdf->SetLeftMargin(24);

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
$left_info .= "<br />";
$left_info .= 'Mengetahui,' .'<br />';
$left_info .= 'Kepala ' . $suket->office->dinas;
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= $licence_item->kepala_dinas_nama;
$left_info .= "<br />";
$left_info .= $licence_item->kepala_dinas_nip;
$left_info .= '</div>';


$right_info = '<div style="text-align:center;">';
$right_info .= "Serang, $tanggal_suket" .'<br />';
$right_info .= "<br />";
$right_info .= 'Yang Melakukan Evaluasi,' .'<br />';
$right_info .= 'Pengawas Ketenagakerjaan' .'<br />';
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= $licence_item->pengawas_nama;
$right_info .= "<br />";
$right_info .= $licence_item->pengawas_nip;
$right_info .= '</div>';


$pdf->ln(5);
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
