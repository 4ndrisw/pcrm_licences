// Init single licence
function init_licence(id) {
    load_small_table_item(id, '#licence', 'licenceid', 'licences/get_licence_data_ajax', '.table-licences');
}


// Validates licence add/edit form
function validate_licence_form(selector) {

    selector = typeof (selector) == 'undefined' ? '#licence-form' : selector;

    appValidateForm($(selector), {
        clientid: {
            required: {
                depends: function () {
                    var customerRemoved = $('select#clientid').hasClass('customer-removed');
                    return !customerRemoved;
                }
            }
        },
        proposed_date: 'required',
        office_id: 'required',
        number: {
            required: true
        }
    });

    $("body").find('input[name="number"]').rules('add', {
        remote: {
            url: admin_url + "licences/validate_licence_number",
            type: 'post',
            data: {
                number: function () {
                    return $('input[name="number"]').val();
                },
                isedit: function () {
                    return $('input[name="number"]').data('isedit');
                },
                original_number: function () {
                    return $('input[name="number"]').data('original-number');
                },
                proposed_date: function () {
                    return $('body').find('.licence input[name="proposed_date"]').val();
                },
            }
        },
        messages: {
            remote: app.lang.licence_number_exists,
        }
    });

}


// Get the preview main values
function get_licence_item_preview_values() {
    var response = {};
    response.description = $('.main textarea[name="description"]').val();
    response.long_description = $('.main textarea[name="long_description"]').val();
    response.qty = $('.main input[name="quantity"]').val();
    return response;
}

// Append the added items to the preview to the table as items
function add_licence_item_to_table(data, itemid){

  // If not custom data passed get from the preview
  data = typeof (data) == 'undefined' || data == 'undefined' ? get_licence_item_preview_values() : data;
  if (data.description === "" && data.long_description === "") {
     return;
  }

  var table_row = '';
  var item_key = lastAddedItemKey ? lastAddedItemKey += 1 : $("body").find('tbody .item').length + 1;
  lastAddedItemKey = item_key;

  table_row += '<tr class="sortable item">';

  table_row += '<td class="dragger">';

  // Check if quantity is number
  if (isNaN(data.qty)) {
     data.qty = 1;
  }

  $("body").append('<div class="dt-loader"></div>');
  var regex = /<br[^>]*>/gi;

     table_row += '<input type="hidden" class="order" name="newitems[' + item_key + '][order]">';

     table_row += '</td>';

     table_row += '<td class="bold description"><textarea name="newitems[' + item_key + '][description]" class="form-control" rows="5">' + data.description + '</textarea></td>';

     table_row += '<td><textarea name="newitems[' + item_key + '][long_description]" class="form-control item_long_description" rows="5">' + data.long_description.replace(regex, "\n") + '</textarea></td>';
   //table_row += '<td><textarea name="newitems[' + item_key + '][long_description]" class="form-control item_long_description" rows="5">' + data.long_description + '</textarea></td>';


     table_row += '<td><input type="number" min="0" onblur="calculate_total();" onchange="calculate_total();" data-quantity name="newitems[' + item_key + '][qty]" value="' + data.qty + '" class="form-control">';

     if (!data.unit || typeof (data.unit) == 'undefined') {
        data.unit = '';
     }

     table_row += '<input type="text" placeholder="' + app.lang.unit + '" name="newitems[' + item_key + '][unit]" class="form-control input-transparent text-right" value="' + data.unit + '">';

     table_row += '</td>';


     table_row += '<td><a href="#" class="btn btn-danger pull-left" onclick="delete_item(this,' + itemid + '); return false;"><i class="fa fa-trash"></i></a></td>';

     table_row += '</tr>';

     $('table.items tbody').append(table_row);

     $(document).trigger({
        type: "item-added-to-table",
        data: data,
        row: table_row
     });


     clear_item_preview_values();
     reorder_items();

     $('body').find('#items-warning').remove();
     $("body").find('.dt-loader').remove();

  return false;
}

// From licence table mark as
function licence_mark_as(status_id, licence_id) {
    var data = {};
    data.status = status_id;
    data.licenceid = licence_id;
    $.post(admin_url + 'licences/update_licence_status', data).done(function (response) {
        //table_licences.DataTable().ajax.reload(null, false);
        reload_licences_tables();
    });
}

// From licence table mark as

function licence_add_proposed_item(licence_id, project_id, task_id) {
    var data = {};
    data.licence_id = licence_id;
    data.project_id = project_id;
    data.task_id = task_id;
    $.post(admin_url + 'licences/add_proposed_item', data).done(function (response) {
        reload_licences_tables();
    });
}


// From licence table mark as
function licence_remove_proposed_item(licence_id, task_id) {
    var data = {};
    data.licence_id = licence_id;
    data.task_id = task_id;
    $.post(admin_url + 'licences/remove_proposed_item', data).done(function (response) {
        reload_licences_tables();
    });
}


// Reload all licences possible table where the table data needs to be refreshed after an action is performed on task.

function reload_licences_tables() {
    var av_licences_tables = ['.table-licences', '.table-rel-licences', '.table-licences-proposed', '.table-licences-related'];
    //var av_licences_tables = ['.licence-items-proposed'];
    $.each(av_licences_tables, function (i, selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().ajax.reload(null, false);
        }
    });
}



function licence_add_released_item(licence_id, project_id, task_id) {
    var data = {};
    data.licence_id = licence_id;
    data.project_id = project_id;
    data.task_id = task_id;
    data.released = 1;

    $.post(admin_url + 'licences/add_released_item', data).done(function (response) {
        reload_released_tables();
    });
}


// From licence table mark as
function licence_remove_released_item(licence_id, task_id) {
    var data = {};
    data.licence_id = licence_id;
    data.task_id = task_id;
    $.post(admin_url + 'licences/remove_released_item', data).done(function (response) {
        reload_released_tables();
    });
}



// Reload all licences possible table where the table data needs to be refreshed after an action is performed on task.

function reload_released_tables() {
    var av_released_tables = ['.table-licences', '.table-licences-processed', '.table-licences-released'];
    //var av_licences_tables = ['.licence-items-proposed'];
    $.each(av_released_tables, function (i, selector) {
        if ($.fn.DataTable.isDataTable(selector)) {
            $(selector).DataTable().ajax.reload(null, false);
        }
    });
}



// Init task modal and get data from server
function init_licence_task_modal(task_id) {

    requestGet('tasks/get_task_data/' + task_id).done(function (response) {
        _task_append_html(response);
        if (typeof (comment_id) != 'undefined') {
            setTimeout(function () {
                $('[data-task-comment-href-id="' + comment_id + '"]').click();
            }, 1000);
        }
    }).fail(function (data) {
        $('#task-modal').modal('hide');
        alert_float('danger', data.responseText);
    });
}
