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

$equipment_merk = isset($equipment['merk']) ? $equipment['merk'] : '';
$equipment_pabrik_pembuat = isset($equipment['pabrik_pembuat']) ? $equipment['pabrik_pembuat'] : '';
$equipment_jumlah_nozzle = isset($equipment['jumlah_nozzle']) ? $equipment['jumlah_nozzle'] : '';
$equipment_tempat_pembuatan = isset($equipment['tempat_pembuatan']) ? $equipment['tempat_pembuatan'] : '';
$equipment_tahun_pembuatan =isset($equipment['tahun_pembuatan']) ? $equipment['tahun_pembuatan'] : '';

$equipment_type_model = isset($equipment['type_model']) ? $equipment['type_model'] : '';
$equipment_kapasitas = isset($equipment['kapasitas']) ? $equipment['kapasitas'] : '';
$equipment_tekanan_uji = isset($equipment['tekanan_uji']) ? $equipment['tekanan_uji'] : '';
$equipment_kecepatan_angkat = isset($equipment['kecepatan_angkat']) ? $equipment['kecepatan_angkat'] : '';
$equipment_tinggi_angkat = isset($equipment['tinggi_angkat']) ? $equipment['tinggi_angkat'] : '';
$equipment_nomor_seri = isset($equipment['nomor_seri']) ? $equipment['nomor_seri'] : '';
$equipment_digunakan_untuk = isset($equipment['digunakan_untuk']) ? $equipment['digunakan_untuk'] : '';
$equipment_jenis_pemeriksaan = isset($equipment['jenis_pemeriksaan']) ? $equipment['jenis_pemeriksaan'] : '';
$equipment_kelengkapan_pengaman = isset($equipment['kelengkapan_pengaman']) ? $equipment['kelengkapan_pengaman'] : '';

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

$text = '<div style="text-align:justify;">';
$text .= 'Berdasarkan laporan hasil pemeriksaan dan pengujian yang telah dilakukan oleh ';
$text .= $ahli_k3 .' dari ' . get_option('invoice_company_name') .', ';
$text .= 'Nomor <strong>'.$inspection_no.'</strong> pada tanggal ' .$tanggal_inspeksi.', ';
$text .= 'terhadap Pesawat Angkat Angkut jenis <strong>'. $equipment_nama_pesawat .'</strong> (laporan terlampir) diterangkan bahwa: ' ."\r\n";
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
        <td style="width:310;">1. Jenis Objek K3 yang diuji</td>
        <td style="width:10;">:</td>
        <td style="width:370;">Pesawat Angkat dan Angkut</td>
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
        <td style="width:310;">4. Nomor Pengesahan</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_nomor_pengesahan</td>
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
        <td style="width:310;">1. Jenis Pesawat Angkat dan Angkut</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_nama_pesawat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">2. Merk / Pabrik Pembuat</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_merk / $equipment_pabrik_pembuat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">3. Tempat / Tahun Pembuatan</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_tempat_pembuatan / $equipment_tahun_pembuatan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">4. Nomor Seri / Type / Model</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_nomor_seri / $equipment_type_model</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">5. Kapasitas Angkut</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_kapasitas</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">6. Tinggi Angkat</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_tinggi_angkat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">7. Kecepatan Angkat</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_kecepatan_angkat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">8. Digunakan untuk</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_digunakan_untuk</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">9. Kelengkapan Pengaman</td>
        <td style="width:10;">:</td>
        <td style="width:370;">$equipment_kelengkapan_pengaman</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:310;">9. Jenis pemeriksaan</td>
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
$text .= 'Demikian surat keterangan ini dibuat dengan sebenarnya agar dapat digunakan sebagaimana mestinya dan berlaku sepanjang objek pengujian tidak dilakukan perubahan dan / atau sampai dilakukan pengujian selanjutnya paling lambat bulan ';
$text .= '<strong>'. $expired_bulan .'</strong>.';
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
$right_info .= 'Pemeriksa' .'<br />';
$right_info .= 'Pengawas Ketenagakerjaan K3' .'<br />';
$right_info .= 'Spesialis PAA' .'<br />';
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

$pdf->ln(4);
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
