<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content licence-add">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(),array('id'=>'licence-form','class'=>'_transaction_form'));
			if(isset($licence)){
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<?php $this->load->view('admin/licences/licence_template'); ?>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="/modules/licences/assets/js/licences.js"></script>
<script type="text/javascript">
	$(function(){
		validate_licence_form();
		// Project ajax search
		init_ajax_project_search_by_customer_id();
	});
</script>
</body>
</html>
