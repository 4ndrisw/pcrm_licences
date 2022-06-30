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

<div class="editable" data-cid="4">Test #4</div>
<div class="editable" data-cid="5">Test #5</div>
<div class="editable" data-cid="6">Test #6</div>
<div class="editable" data-cid="7">Test #7</div>

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

<script type="text/javascript">
	// Editable
function Editable(sel, options) {
  if (!(this instanceof Editable)) return new Editable(...arguments); 
  
  const attr = (EL, obj) => Object.entries(obj).forEach(([prop, val]) => EL.setAttribute(prop, val));

  Object.assign(this, {
    onStart() {},
    onInput() {},
    onEnd() {},
    classEditing: "is-editing", // added onStart
    classModified: "is-modified", // added onEnd if content changed
  }, options || {}, {
    elements: document.querySelectorAll(sel),
    element: null, // the latest edited Element
    isModified: false, // true if onEnd the HTML content has changed
  });

  const start = (ev) => {
    this.isModified = false;
    this.element = ev.currentTarget;
    this.element.classList.add(this.classEditing);
    this.text_before = ev.currentTarget.textContent;
    this.html_before = ev.currentTarget.innerHTML;
    this.onStart.call(this.element, ev, this);
  };
  
  const input = (ev) => {
    this.text = this.element.textContent;
    this.html = this.element.innerHTML;
    this.isModified = this.html !== this.html_before;
    this.element.classList.toggle(this.classModified, this.isModified);
    this.onInput.call(this.element, ev, this);
  }

  const end = (ev) => {
    this.element.classList.remove(this.classEditing);
    this.onEnd.call(this.element, ev, this);
  }

  this.elements.forEach(el => {
    attr(el, {tabindex: 1, contenteditable: true});
    el.addEventListener("focusin", start);
    el.addEventListener("input", input);
    el.addEventListener("focusout", end);
  });

  return this;
}

// Use like:
Editable(".editable", {
  onEnd(ev, UI) { // ev=Event UI=Editable this=HTMLElement
    if (!UI.isModified) return; // No change in content. Abort here.
    const data = {
      cid: this.dataset.cid,
      text: this.textContent, // or you can also use UI.text
    }
    console.log(data); // Submit your data to server
  }
});


</script>






</body>
</html>
