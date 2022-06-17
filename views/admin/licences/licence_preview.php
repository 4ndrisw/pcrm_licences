<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-6 no-padding">
				<?php 
			        if ($this->input->is_ajax_request()) {
			            $this->app->get_table_data(module_views_path('licences', 'admin/tables/table'));
			        }
					$this->load->view('admin/licences/licence_small_table'); 
				?>
			</div>
			<div class="col-md-6 no-padding licence-preview">
				<?php $this->load->view('admin/licences/licence_preview_template'); ?>
			</div>
		</div>
	</div>
</div>
<?php init_tail(); ?>

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

</body>
</html>
