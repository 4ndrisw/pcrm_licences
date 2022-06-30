<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="col-md-12">
    <div class="panel_s">
        <div class="panel-body">
         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="table-responsive">
            <?php render_datatable(array(
                _l('licence_task'),
                _l('licence_equipment_name'),
                _l('licence_tag'),
                _l('licence_flag'),
                ),'licences-related'); ?>
         </div>
        </div>
    </div>
</div>



<!-- ============ MODAL ADD BARANG =============== -->
        <div class="modal fade" id="modal_add_new" tabindex="-1" role="dialog" aria-labelledby="largeModal" aria-hidden="true">
            <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                <h3 class="modal-title" id="myModalLabel">Tambah/Ubah Nama Alat</h3>
            </div>
            <?php
                echo form_open($this->uri->uri_string(),array('id'=>'licence-form','class'=>'_transaction_form form-horizontal'));
            ?>
                <div class="modal-body">
 
                    <div class="form-group">
                        <label class="control-label col-xs-3" >Nama Alat</label>
                        <div class="col-xs-8">
                            <input name="equipment_name" class="form-control" type="text" placeholder="Nama Alat..." required>
                        </div>
                    </div>
  
                </div>
 
                <div class="modal-footer">
                    <button class="btn" data-dismiss="modal" aria-hidden="true">Tutup</button>
                    <button class="btn btn-info">Simpan</button>
                </div>
            <?php echo form_close(); ?>
            </div>
            </div>
        </div>
        <!--END MODAL ADD BARANG-->
