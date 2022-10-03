<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<div class ="table-responsive">
  <table id="<?= 'licence-'.$licence->id ?>" class="table licence table-bordered">
     <tbody>
        <tr>
           <td style="width:20%">Nama Perusahaan</td>
           <td style="width:2%">:</td>
           <td><?= get_licence_company_by_clientid($licence->clientid) ?></td>      
        </tr>
        <tr>
           <td style="width:20%">Alamat Perusahaan</td>
           <td style="width:2%">:</td>
           <td><?= get_licence_company_address($licence->id) ?></td>      
        </tr>
        <?php ?>
        <?php if(isset($equipment['nama_pesawat']) && $equipment['nama_pesawat'] !='') { ?>
           <tr>
              <td style="width:20%">Nama Pesawat</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['nama_pesawat'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['pabrik_pembuat']) && $equipment['pabrik_pembuat'] !='') { ?>
           <tr>
              <td style="width:20%">Pabrik Pembuat</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['pabrik_pembuat'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['tempat_pembuatan']) && $equipment['tempat_pembuatan'] !='') { ?>
           <tr>
              <td style="width:20%">Tempat Pembuatan</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['tempat_pembuatan'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['tahun_pembuatan']) && $equipment['tahun_pembuatan'] !='') { ?>
           <tr>
              <td style="width:20%">Tahun Pembuatan</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['tahun_pembuatan'] ?></td>
           </tr>
        <?php } ?>

        <?php if(isset($equipment['merk']) && $equipment['merk'] !='') { ?>
           <tr>
              <td style="width:20%">Merk</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['merk'] ?></td>
           </tr>
        <?php } ?>

        <?php if(isset($equipment['nomor_seri']) && $equipment['nomor_seri'] !='') { ?>
           <tr>
              <td style="width:20%">Nomor Seri</td>
              <td style="width:2%">:</td>
              <td class="editable" data-field="nomor_seri" data-jenis_pesawat="<?= $licence->equipment_type ?>" data-licence_id="<?= $licence->id ?>" data-task_id="<?= $task->id ?>"><?= $equipment['nomor_seri'] ?></td>      
           </tr>
        <?php } ?>

        <?php if(isset($equipment['nomor_unit']) && $equipment['nomor_unit'] !='') { ?>
           <tr>
              <td style="width:20%">Nomor Unit</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['nomor_unit'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['type_model']) && $equipment['type_model'] !='') { ?>
           <tr>
              <td style="width:20%">Type / Model</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['type_model'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['kapasitas']) && $equipment['kapasitas'] !='') { ?>
           <tr>
              <td style="width:20%">Kapasitas</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['kapasitas'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['daya']) && $equipment['daya'] !='') { ?>
           <tr>
              <td style="width:20%">Daya</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['daya'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['daya_penerangan']) && $equipment['daya_penerangan'] !='') { ?>
           <tr>
              <td style="width:20%">Daya Penerangan</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['daya_penerangan'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['daya_produksi']) && $equipment['daya_produksi'] !='') { ?>
           <tr>
              <td style="width:20%">Daya Produksi</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['daya_produksi'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['daya_tenaga']) && $equipment['daya_tenaga'] !='') { ?>
           <tr>
              <td style="width:20%">Daya Tenaga</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['daya_tenaga'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['daya_terpasang']) && $equipment['daya_terpasang'] !='') { ?>
           <tr>
              <td style="width:20%">Daya Terpasang</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['daya_terpasang'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['sumber_tenaga']) && $equipment['sumber_tenaga'] !='') { ?>
           <tr>
              <td style="width:20%">Sumber Tenaga</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['sumber_tenaga'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['jenis_arus']) && $equipment['jenis_arus'] !='') { ?>
           <tr>
              <td style="width:20%">Jenis Arus</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['jenis_arus'] ?></td>
           </tr>
        <?php } ?>


        <?php if(isset($equipment['tekanan_design'])) { ?>
           <tr>
              <td style="width:20%">Tekanan Design</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['tekanan_design'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['tekanan_kerja'])) { ?>
           <tr>
              <td style="width:20%">Tekanan Kerja</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['tekanan_kerja'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['tekanan_uji'])) { ?>
           <tr>
              <td style="width:20%">Tekanan Uji</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['tekanan_uji'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['bentuk'])) { ?>
           <tr>
              <td style="width:20%">Bentuk</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['bentuk'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['isi_cairan'])) { ?>
           <tr>
              <td style="width:20%">Isi Cairan</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['isi_cairan'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['digunakan_untuk'])) { ?>
           <tr>
              <td style="width:20%">Digunakan Untuk</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['digunakan_untuk'] ?></td>
           </tr>
        <?php } ?>

        <?php if(isset($equipment['jumlah_kotak_hydrant'])) { ?>
           <tr>
              <td style="width:20%">Jumlah Kotak Hydrant</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['jumlah_kotak_hydrant'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['jumlah_selang_hydrant'])) { ?>
           <tr>
              <td style="width:20%">Jumlah Nozzle</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['jumlah_selang_hydrant'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['jumlah_nozzle'])) { ?>
           <tr>
              <td style="width:20%">Jenis Pemeriksaan</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['jumlah_nozzle'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['kapasitas_air'])) { ?>
           <tr>
              <td style="width:20%">Kapasitas air</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['kapasitas_air'] ?></td>
           </tr>
        <?php } ?>
        <?php if(isset($equipment['jenis_pemeriksaan'])) { ?>
           <tr>
              <td style="width:20%">Jenis Pemeriksaan</td>
              <td style="width:2%">:</td>
              <td><?= $equipment['jenis_pemeriksaan'] ?></td>
           </tr>
        <?php } ?>


     
     </tbody>
  </table>
</div>
<div class ="table-responsive">
  <?php 
     $pemeriksaan_dokumen_t = '&#9744;';
     if(isset($equipment['pemeriksaan_dokumen']) && ($equipment['pemeriksaan_dokumen'] == 1)){
        $pemeriksaan_dokumen_t = '&#9745;';
     }
     $pemeriksaan_dokumen_f = '&#9744;';
     if(isset($equipment['pemeriksaan_dokumen']) && ($equipment['pemeriksaan_dokumen'] == 2)){
        $pemeriksaan_dokumen_f = '&#9745;';
     }
     $pemeriksaan_dokumen_n = '&#9744;';
     if(isset($equipment['pemeriksaan_dokumen']) && ($equipment['pemeriksaan_dokumen'] == 3)){
        $pemeriksaan_dokumen_n = '&#9745;';
     }

     $pemeriksaan_visual_t = '&#9744;';
     if(isset($equipment['pemeriksaan_visual']) && $equipment['pemeriksaan_visual'] == 1){
        $pemeriksaan_visual_t = '&#9745;';
     }
     $pemeriksaan_visual_f = '&#9744;';
     if(isset($equipment['pemeriksaan_visual']) && $equipment['pemeriksaan_visual'] == 2){
        $pemeriksaan_visual_f = '&#9745;';
     }
     $pemeriksaan_visual_n = '&#9744;';
     if(isset($equipment['pemeriksaan_visual']) && $equipment['pemeriksaan_visual'] == 3){
        $pemeriksaan_visual_n = '&#9745;';
     }

     $pemeriksaan_pengaman_t = '&#9744;';
     if(isset($equipment['pemeriksaan_pengaman']) && $equipment['pemeriksaan_pengaman'] == 1){
        $pemeriksaan_pengaman_t = '&#9745;';
     }
     $pemeriksaan_pengaman_f = '&#9744;';
     if(isset($equipment['pemeriksaan_pengaman']) && $equipment['pemeriksaan_pengaman'] == 2){
        $pemeriksaan_pengaman_f = '&#9745;';
     }
     $pemeriksaan_pengaman_n = '&#9744;';
     if(isset($equipment['pemeriksaan_pengaman']) && $equipment['pemeriksaan_pengaman'] == 3){
        $pemeriksaan_pengaman_n = '&#9745;';
     }

     $pengujian_beban_t = '&#9744;';
     if(isset($equipment['pengujian_beban']) && $equipment['pengujian_beban'] == 1){
        $pengujian_beban_t = '&#9745;';
     }
     $pengujian_beban_f = '&#9744;';
     if(isset($equipment['pengujian_beban']) && $equipment['pengujian_beban'] == 2){
        $pengujian_beban_f = '&#9745;';
     }
     $pengujian_beban_n = '&#9744;';
     if(isset($equipment['pengujian_beban']) && $equipment['pengujian_beban'] == 3){
        $pengujian_beban_n = '&#9745;';
     }

     $pengujian_penetrant_t = '&#9744;';
     if(isset($equipment['pengujian_penetrant']) && $equipment['pengujian_penetrant'] == 1){
        $pengujian_penetrant_t = '&#9745;';
     }
     $pengujian_penetrant_f = '&#9744;';
     if(isset($equipment['pengujian_penetrant']) && $equipment['pengujian_penetrant'] == 2){
        $pengujian_penetrant_f = '&#9745;';
     }
     $pengujian_penetrant_n = '&#9744;';
     if(isset($equipment['pengujian_penetrant']) && $equipment['pengujian_penetrant'] == 3){
        $pengujian_penetrant_n = '&#9745;';
     }

     $pengujian_thickness_t = '&#9744;';
     if(isset($equipment['pengujian_thickness']) && $equipment['pengujian_thickness'] == 1){
        $pengujian_thickness_t = '&#9745;';
     }
     $pengujian_thickness_f = '&#9744;';
     if(isset($equipment['pengujian_thickness']) && $equipment['pengujian_thickness'] == 2){
        $pengujian_thickness_f = '&#9745;';
     }
     $pengujian_thickness_n = '&#9744;';
     if(isset($equipment['pengujian_thickness']) && $equipment['pengujian_thickness'] == 3){
        $pengujian_thickness_n = '&#9745;';
     }

     $pengujian_grounding_t = '&#9744;';
     if(isset($equipment['pengujian_grounding']) && $equipment['pengujian_grounding'] == 1){
        $pengujian_grounding_t = '&#9745;';
     }
     $pengujian_grounding_f = '&#9744;';
     if(isset($equipment['pengujian_grounding']) && $equipment['pengujian_grounding'] == 2){
        $pengujian_grounding_f = '&#9745;';
     }
     $pengujian_grounding_n = '&#9744;';
     if(isset($equipment['pengujian_grounding']) && $equipment['pengujian_grounding'] == 3){
        $pengujian_grounding_n = '&#9745;';
     }

     $pengujian_thermal_infrared_t = '&#9744;';
     if(isset($equipment['pengujian_thermal_infrared']) && $equipment['pengujian_thermal_infrared'] == 1){
        $pengujian_thermal_infrared_t = '&#9745;';
     }
     $pengujian_thermal_infrared_f = '&#9744;';
     if(isset($equipment['pengujian_thermal_infrared']) && $equipment['pengujian_thermal_infrared'] == 2){
        $pengujian_thermal_infrared_f = '&#9745;';
     }
     $pengujian_thermal_infrared_n = '&#9744;';
     if(isset($equipment['pengujian_thermal_infrared']) && $equipment['pengujian_thermal_infrared'] == 3){
        $pengujian_thermal_infrared_n = '&#9745;';
     }

     $pengujian_kapasitas_hantar_t = '&#9744;';
     if(isset($equipment['pengujian_kapasitas_hantar']) && $equipment['pengujian_kapasitas_hantar'] == 1){
        $pengujian_kapasitas_hantar_t = '&#9745;';
     }
     $pengujian_kapasitas_hantar_f = '&#9744;';
     if(isset($equipment['pengujian_kapasitas_hantar']) && $equipment['pengujian_kapasitas_hantar'] == 2){
        $pengujian_kapasitas_hantar_f = '&#9745;';
     }
     $pengujian_kapasitas_hantar_n = '&#9744;';
     if(isset($equipment['pengujian_kapasitas_hantar']) && $equipment['pengujian_kapasitas_hantar'] == 3){
        $pengujian_kapasitas_hantar_n = '&#9745;';
     }


     $pengujian_pompa_t = '&#9744;';
     if(isset($equipment['pengujian_pompa']) && $equipment['pengujian_pompa'] == 1){
        $pengujian_pompa_t = '&#9745;';
     }
     $pengujian_pompa_f = '&#9744;';
     if(isset($equipment['pengujian_pompa']) && $equipment['pengujian_pompa'] == 2){
        $pengujian_pompa_f = '&#9745;';
     }
     $pengujian_pompa_n = '&#9744;';
     if(isset($equipment['pengujian_pompa']) && $equipment['pengujian_pompa'] == 3){
        $pengujian_pompa_n = '&#9745;';
     }

     $pengujian_tekanan_t = '&#9744;';
     if(isset($equipment['pengujian_tekanan']) && $equipment['pengujian_tekanan'] == 1){
        $pengujian_tekanan_t = '&#9745;';
     }
     $pengujian_tekanan_f = '&#9744;';
     if(isset($equipment['pengujian_tekanan']) && $equipment['pengujian_tekanan'] == 2){
        $pengujian_tekanan_f = '&#9745;';
     }
     $pengujian_tekanan_n = '&#9744;';
     if(isset($equipment['pengujian_tekanan']) && $equipment['pengujian_tekanan'] == 3){
        $pengujian_tekanan_n = '&#9745;';
     }

     $pengujian_daya_pancar_t = '&#9744;';
     if(isset($equipment['pengujian_daya_pancar']) && $equipment['pengujian_daya_pancar'] == 1){
        $pengujian_daya_pancar_t = '&#9745;';
     }
     $pengujian_daya_pancar_f = '&#9744;';
     if(isset($equipment['pengujian_daya_pancar']) && $equipment['pengujian_daya_pancar'] == 2){
        $pengujian_daya_pancar_f = '&#9745;';
     }
     $pengujian_daya_pancar_n = '&#9744;';
     if(isset($equipment['pengujian_daya_pancar']) && $equipment['pengujian_daya_pancar'] == 3){
        $pengujian_daya_pancar_n = '&#9745;';
     }

     $pengujian_operasional_t = '&#9744;';
     if(isset($equipment['pengujian_operasional']) && $equipment['pengujian_operasional'] == 1){
        $pengujian_operasional_t = '&#9745;';
     }
     $pengujian_operasional_f = '&#9744;';
     if(isset($equipment['pengujian_operasional']) && $equipment['pengujian_operasional'] == 2){
        $pengujian_operasional_f = '&#9745;';
     }
     $pengujian_operasional_n = '&#9744;';
     if(isset($equipment['pengujian_operasional']) && $equipment['pengujian_operasional'] == 3){
        $pengujian_operasional_n = '&#9745;';
     }
  ?>
  <table class="table table-bordered">
     <tbody>
         <?php if(isset($equipment['pemeriksaan_dokumen'])){ ?>
           <tr class="pemeriksaan_dokumen">
              <td width="32%">Pemeriksan Dokumen</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_dokumen_t ?></span> Lengkap</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_dokumen_f ?></span> Tidak lengkap</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_dokumen_n ?></span> Tidak ada</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pemeriksaan_visual'])){ ?>
           <tr class="pemeriksaan_visual">
              <td width="32%">Pemeriksan Visual</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_visual_t ?></span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_visual_f ?></span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_visual_n ?></span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pemeriksaan_pengaman'])){ ?>
           <tr class="pemeriksaan_pengaman">
              <td width="32%">Pemeriksan Perlengkapan Pengaman</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_pengaman_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_pengaman_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pemeriksaan_pengaman_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pengujian_beban'])){ ?>
           <tr class="pengujian_beban">
              <td width="32%">Pengujian Beban</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_beban_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_beban_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_beban_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pengujian_penetrant'])){ ?>
           <tr class="pengujian_penetrant">
              <td width="32%">Pengujian NDT (Penetrant)</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_penetrant_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_penetrant_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_penetrant_n ?> </span> Tidak dilaksanakan</td>
           </tr>         
         <?php } ?>
         <?php if(isset($equipment['pengujian_thickness'])){ ?>
           <tr class="pengujian_thickness">
              <td width="32%">Pengujian NDT (thickness)</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_thickness_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_thickness_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_thickness_n ?> </span> Tidak dilaksanakan</td>
           </tr>         
         <?php } ?>
         <?php if(isset($equipment['pengujian_grounding'])){ ?>
           <tr class="pengujian_grounding">
              <td width="32%">Pengujian Grounding</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_grounding_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_grounding_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_grounding_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pengujian_thermal_infrared'])){ ?>
           <tr class="pengujian_thermal_infrared">
              <td width="32%">Pengujian Termal Infrared</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_thermal_infrared_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_thermal_infrared_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_thermal_infrared_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pengujian_kapasitas_hantar'])){ ?>
           <tr class="pengujian_kapasitas_hantar">
              <td width="32%">Pengujian Kapasitas Hantar</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_kapasitas_hantar_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_kapasitas_hantar_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_kapasitas_hantar_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pengujian_pompa'])){ ?>
           <tr class="pengujian_pompa">
              <td width="32%">Pengujian Pompa</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_pompa_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_pompa_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_pompa_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pengujian_tekanan'])){ ?>
           <tr class="pengujian_tekanan">
              <td width="32%">Pengujian Tekanan</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_tekanan_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_tekanan_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_tekanan_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>         
         <?php if(isset($equipment['pengujian_daya_pancar'])){ ?>
           <tr class="pengujian_daya_pancar">
              <td width="32%">Pengujian Daya Pancar</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_daya_pancar_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_daya_pancar_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_daya_pancar_n ?> </span> Tidak dilaksanakan</td>
           </tr>
         <?php } ?>
         <?php if(isset($equipment['pengujian_operasional'])){ ?>
           <tr class="pengujian_operasional">
              <td width="32%">Pengujian Operasional</td>
              <td width="1%">:</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_operasional_t ?> </span> Baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_operasional_f ?> </span> Tidak baik</td>
              <td width="22%"><span style='font-size:2.5rem;'><?= $pengujian_operasional_n ?> </span> Tidak dilaksanakan</td>
           </tr>
        <?php } ?>


     </tbody>
  </table>
</div>	    