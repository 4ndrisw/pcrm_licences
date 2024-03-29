<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6 no-padding licence-small-table">
				<?php 
					$this->load->view('admin/licences/licence_small_table'); 
				?>
			</div>
			<div class="col-md-6 no-padding licence-preview-template">
				<?php $this->load->view('admin/licences/licence_preview_template'); ?>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 no-padding licence-table-related">
				<?php $this->load->view('admin/licences/licence_table_related'); ?>
			</div>
		</div>
	</div>
</div>





<?php init_tail(); ?>
<script type="text/javascript" id="licence-js" src="<?= base_url() ?>modules/licences/assets/js/licences.js?"></script>

<script>
   init_items_sortable(true);
   init_btn_with_tooltips();
   init_datepicker();
   init_selectpicker();
   init_form_reminder();
   init_tabs_scrollable();
   <?php if($send_later) { ?>
      licence_licence_send(<?php echo $licence->id; ?>);
   <?php } ?>
</script>

<script>
    $(function(){
        initDataTable('.table-licences', window.location.href, 'undefined', 'undefined','fnServerParams', [0, 'desc']);
    });
</script>
<script>
    $(function(){
        initDataTable('.table-licences-proposed', admin_url+'licences/table_proposed', 'undefined', 'undefined','fnServerParams', [0, 'desc']);
    });
</script>
<script>
    $(function(){
        initDataTable('.table-licences-related', admin_url+'licences/table_related', 'undefined', 'undefined','fnServerParams', [0, 'desc']);
    });
</script>


</body>
</html>
