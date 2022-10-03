<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// set auto page breaks
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('dejavusans');

$inspection = $suket->inspection;
$tanggal_inspeksi_raw = _d($inspection->date);
$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$inspection = $suket->inspection;
$inspection_no = format_inspection_item_number($inspection->id, $suket->task_id);
$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;
$tanggal_suket_raw = $suket->licence_items[0]['tanggal_suket'];
$equipment = $suket->equipment[0];
$equipment_jenis_pesawat = $equipment['jenis_pesawat'];
$equipment_lokasi = $equipment['lokasi'];
$equipment_nama_pesawat = $equipment['nama_pesawat'];
$equipment_pabrik_pembuat = $equipment['pabrik_pembuat'];
$equipment_tahun_pembuatan = $equipment['tahun_pembuatan'];

$equipment_nomor_seri = $equipment['nomor_seri'];
$equipment_nomor_unit = $equipment['nomor_unit'];
$equipment_kapasitas =$equipment['kapasitas'];
$equipment_isi_cairan =$equipment['isi_cairan'];
$equipment_digunakan_untuk = $equipment['digunakan_untuk'];
$equipment_jenis_pemeriksaan = $equipment['jenis_pemeriksaan'];
$equipment_jenis_bejana = $equipment['jenis_bejana'];
$office_dinas = $suket->office->dinas;
$regulasi = explode(' AND ', $equipment['regulasi']);
$equipment_regulasi = '';
$equipment_regulasi .= '<ol class="regulasi">'; 

$tahun = getYear($tanggal_inspeksi_raw);
$bulan = getMonth($tanggal_inspeksi_raw);
$tanggal = getDay($tanggal_inspeksi_raw);
$tanggal_inspeksi = $tanggal.' '.$bulan.' '.$tahun;

$tahun = getYear($tanggal_suket_raw);
$bulan = getMonth($tanggal_suket_raw);
$tanggal = getDay($tanggal_suket_raw);
$tanggal_suket = $tanggal.' '.$bulan.' '.$tahun;

$text = 'SURAT KETERANGAN' ."\r\n";
$text .= 'HASIL PEMERIKSAAN DAN PENGUJIAN' ."\r\n";
$text .= 'Nomor : ';// . $suket_item_number;

$pdf->ln(25);
$pdf->Write(0, $text, '', 0, 'C', true, 0, false, false, 0);

$text = 'Berdasarkan laporan hasil pemeriksaan dan pengujian yang telah dilakukan oleh ';
$text .= 'Ahli K3 PUBT ' . get_option('invoice_company_name') ;
$text .= ' nomor ' .$inspection_no;
$text .= ' pada tanggal ' .$tanggal_inspeksi. ' terhadap Pesawat Uap dan Bejana Tekan ' .$equipment_nama_pesawat. ' (laporan terlampir) diterangkan bahwa :' . "\r\n";

$pdf->SetLeftMargin(24);
$pdf->SetRightMargin(24);
$pdf->ln(5);
$pdf->Write(0, $text, '', 0, 'J', true, 0, false, false, 0);

foreach($regulasi as $row){
    $equipment_regulasi .= '<li style="margin-left:70;">' .$row. '</li>'; 
}
$equipment_regulasi .= '</ol>'; 

$html = <<<EOD
<style>
    tr > ol {
    margin-left: 76px;
    }
</style>
<table cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td style="width:20;">I.</td>
        <td style="width:280;">DATA UMUM OBYEK PENGUJIAN</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Jenis objek K3 yang diuji</td>
        <td style="width:10;">:</td>
        <td style="width:420;">Pesawat Uap dan Bejana Tekan</td>
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
        <td style="width:280;">DATA TEKNIS OBYEK PENGUJIAN</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Jenis Pesawat Uap dan Bejana Tekan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_nama_pesawat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Pabrik pembuat</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_pabrik_pembuat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Tempat / tahun pembuatan</td>
        <td style="width:10;">:</td>
        <td style="width:420;"> / $equipment_tahun_pembuatan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">3. No. serie / No. unit</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_nomor_seri / $equipment_nomor_unit</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">4. Isi cairan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_isi_cairan</td>
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

$text = "Demikian surat keterangan ini dibuat dengan sebenarnya agar dapat digunakan sebagaimana mestinya dan berlaku sepanjang objek pengujian tidak dilakukan perubahan dan / atau sampai dilakukan pengujian selanjutnya sesuai dengan ketentuan peraturan perundang-undangan yang berlaku dan dilakukan pemeriksaan dan pengujian ulang paling lambat bulan ";
$text .= $equipment_nama_pesawat ."\r\n \r\n";

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
$right_info .= 'Pemeriksa,' .'<br />';
$right_info .= 'Pengawas Ketenagakerjaan K3' .'<br />';
$right_info .= "Spesialis PUBT<br />";
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
