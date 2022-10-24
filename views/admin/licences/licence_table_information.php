<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
   $inspection_data = inspection_data($licence->inspection, $task_id);
   $licence_data = licence_data($licence, $task_id);
   $equipment = reset($licence->inspection->equipment);
 ?>
    <div class="panel_s">
        <div class="panel-body">
        <hr class="hr-panel-heading" />

          <h3> 1. Data Inspeksi : <?= isset($inspection_data['nama_pesawat']) ? $inspection_data['nama_pesawat'] : '' ?></h3>
           <div class="col-md-12">
              <div class ="table-responsive">
                 <table class="table inspection-data table-bordered">
                    <tbody>
                       <?php                          
                          foreach ($inspection_data as $key => $value) {
                             echo '<tr>';
                             echo '<td>';
                             echo '${' . $key .'}';
                             echo '</td>';

                             echo '<td>';
                             echo $value;
                             echo '</td>';
                             echo '</tr>';
                          }
                       ?>
                    </tbody> 
                 </table>
              </div>
           </div>
          <h3> 2. Data Lisensi</h3>
          <?php
              $disabled = ''; 
              $file = isset($equipment['jenis_pesawat']) ? strtolower($equipment['jenis_pesawat']).'.docx' : 'undefined.docx';
              $file = str_replace(' ', '_', $file);
              $dir = isset($licence_data['upt']) ? strtolower($licence_data['upt']) : 'undefined';
              $dir = str_replace(' ', '_', $dir);
              $template = FCPATH .'modules/'. LICENCES_MODULE_NAME . '/assets/resources/'. $dir .'/suket_'. $file;
              $label = 'download';
              if (!file_exists($template)) {
                 $disabled = 'disabled';
                 $label = 'template_not_available';
              }
          ?>
           <div class="col-md-12">
             <a class="btn btn-sm btn-danger <?= $disabled ?>"  href="<?php echo admin_url() . 'licences/suket_to_doc/'.$licence->id.'/'.$task_id; ?>">
                <?php echo _l($label); ?>
             </a>
             <?php echo '../'.$dir.'/suket_'.$file; ?>
          </div>
          <div class="clearfix"></div>
           <div class="col-md-8">
              <div class ="table-responsive">
                 <table class="table licence-data table-bordered">
                    <tbody>
                       <?php
                          
                          foreach ($licence_data as $key => $value) {
                             echo '<tr>';
                             echo '<td>';
                             echo '${' . $key .'}';
                             echo '</td>';

                             echo '<td>';
                             echo $value;
                             echo '</td>';
                             echo '</tr>';
                          }
                       ?>
                    </tbody> 
                 </table>
              </div>
           </div>
           <div class="col-md-4">
               <div class="qrcode text-center">
                   <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_licence_upload_path('licence').$licence->id.'/certificate-'.$licence->item_number.'.png')); ?>" class="img-responsive center-block licence-assigned" alt="licence-<?= $licence->id ?>">
               </div>
           </div>
    </div>
</div>
