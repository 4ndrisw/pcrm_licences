<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// set auto page breaks
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('dejavusans');

$inspection = $suket->inspection;
$tanggal_inspeksi_raw = isset($inspection->date) ? _d($inspection->date) : '1970-01-01';
$tanggal_suket_raw = isset($suket->licence_items[0]['tanggal_suket']) ? $suket->licence_items[0]['tanggal_suket'] : '1970-01-01';
$expired_suket_raw = isset($suket->licence_items[0]['expired']) ? $suket->licence_items[0]['expired'] : '1970-01-01';
$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$inspection = $suket->inspection;
$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$equipment = $suket->equipment[0];
$equipment_jenis_pesawat = isset($equipment['jenis_pesawat']) ? $equipment['jenis_pesawat'] : '';
$equipment_lokasi = isset($equipment['lokasi']) ? $equipment['lokasi'] : '';
$equipment_nama_pesawat = isset($equipment['nama_pesawat']) ? $equipment['nama_pesawat'] : '';
$equipment_pabrik_pembuat = $equipment['pabrik_pembuat'];
$equipment_tahun_pembuatan = $equipment['tahun_pembuatan'];
$equipment_tempat_pembuatan = $equipment['tempat_pembuatan'];

$equipment_jumlah_selang_hydrant = isset($equipment['jumlah_selang_hydrant']) ? $equipment['jumlah_selang_hydrant'] : '';
$equipment_jumlah_kotak_hydrant = isset($equipment['jumlah_kotak_hydrant']) ? $equipment['jumlah_kotak_hydrant'] : '';
$equipment_jumlah_nozzle = isset($equipment['jumlah_nozzle']) ? $equipment['jumlah_nozzle'] : '';
$equipment_jumlah_jokey_pump = isset($equipment['jumlah_jokey_pump']) ? $equipment['jumlah_jokey_pump'] : '';
$equipment_jumlah_elektrik_pump =isset($equipment['jumlah_elektrik_pump']) ? $equipment['jumlah_elektrik_pump'] : '';

$equipment_tekanan_pompa = isset($equipment['tekanan_pompa']) ? $equipment['tekanan_pompa'] : '';
$equipment_tekanan_kerja = isset($equipment['tekanan_kerja']) ? $equipment['tekanan_kerja'] : '';
$equipment_tekanan_uji = isset($equipment['tekanan_uji']) ? $equipment['tekanan_uji'] : '';
$equipment_kapasitas_air = isset($equipment['kapasitas_air']) ? $equipment['kapasitas_air'] : '';
$equipment_penggerak_utama = isset($equipment['penggerak_utama']) ? $equipment['penggerak_utama'] : '';
$equipment_jumlah_diesel_pump = isset($equipment['jumlah_diesel_pump']) ? $equipment['jumlah_diesel_pump'] : '';
$equipment_jenis_pemeriksaan = $equipment['jenis_pemeriksaan'];

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
$bulan_suket = $bulan.' '.$tahun;
$expired = $tanggal.' '.$bulan.' '.$tahun;

$tahun = getYear($tanggal_suket_raw);
$bulan = getMonth($tanggal_suket_raw);
$tanggal = getDay($tanggal_suket_raw);
$tanggal_suket = $tanggal.' '.$bulan.' '.$tahun;

$nomor_suket = $suket->licence_items[0]['nomor_suket'];

$text = 'SURAT KETERANGAN' ."\r\n";
$text .= 'HASIL PEMERIKSAAN DAN PENGUJIAN' ."\r\n";
$text .= 'Nomor : ' . $nomor_suket;

$pdf->ln(20);
$pdf->Write(0, $text, '', 0, 'C', true, 0, false, false, 0);

$text = 'Berdasarkan hasil pemeriksaan dan pengujian yang dilakukan oleh ';
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
        <td style="width:280;">Data Umum Objek Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Jenis objek K3 yang diuji</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_nama_pesawat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Nama Perusahaan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$client_company</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">3. Alamat Perusahaan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$client_address</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">4. Lokasi objek yang diuji</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_lokasi</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;"></td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;">B.</td>
        <td style="width:280;">Data Teknis Objek Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Merk / Model</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_jumlah_selang_hydrant / $equipment_jumlah_kotak_hydrant</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Pabrik Pembuat</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_pabrik_pembuat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">3. Tempat / Tahun Pembuatan</td>
        <td style="width:10;">:</td>
        <td style="width:420;"> $equipment_tempat_pembuatan / $equipment_tahun_pembuatan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">4. No. Serie / No. Unit</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_jumlah_nozzle / $equipment_jumlah_jokey_pump</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">4. Kapasitas Angkat</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_jumlah_elektrik_pump</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">5. Jenis pemeriksaan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_jenis_pemeriksaan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;"></td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;">C.</td>
        <td style="width:450;">Hasil Pemeriksaan dan Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:0;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Pemeriksaan Dokumen</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Pengujian NDT</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Pengujian Beban</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;"></td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;">D.</td>
        <td style="width:280;">Kesimpulan</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:650;">Berdasarkan hasil peneriksaan dan pengujian tersebut $equipment_nama_pesawat</td>
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


$text = 'MEMENUHI' ."\r\n";
$text .= 'PERSYARATAN KESELAMATAN DAN KESEHATAN KERJA' ."\r\n";

$pdf->Write(0, $text, '', 0, 'C', true, 0, false, false, 0);

$pdf->ln(5);

$text = "Demikian surat keterangan ini dibuat dengan sebenarnya agar dapat digunakan sebagaimana mestinya dan berlaku sepanjang objek pengujian tidak dilakukan perubahan dan / atau sampai dilakukan pengujian selanjutnya paling lambat tanggal ";
$text .= $expired .".\r\n \r\n";

$pdf->SetLeftMargin(24);
$pdf->SetRightMargin(24);
$pdf->Write(0, $text, '', 0, 'J', true, 0, false, false, 0);


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
$left_info .= $suket->office->kepala_dinas_nama;
$left_info .= "<br />";
$left_info .= $suket->office->kepala_dinas_nip;
$left_info .= '</div>';


$right_info = '<div style="text-align:center;">';
$right_info .= "Serang, $tanggal_suket" .'<br />';
$right_info .= 'Yang Melakukan Evaluasi,' .'<br />';
$right_info .= 'Pengawas Ketenagakerjaan' .'<br />';
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= $suket->office->pengawas_pubt_nama;
$right_info .= "<br />";
$right_info .= $suket->office->pengawas_pubt_nip;
$right_info .= '</div>';



pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);
