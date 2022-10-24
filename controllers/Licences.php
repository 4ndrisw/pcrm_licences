<?php

use app\services\licences\LicencesPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Licences extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('licences_model');
        $this->load->model('clients_model');
        $this->load->model('projects_model');
    }

    /* Get all licences in case user go on index page */
    public function index($id = '')
    {
        if (!has_permission('licences', '', 'view')) {
            access_denied('licences');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/table'));
        }

        $data['licenceid']            = $id;
        $data['title']                 = _l('licences_tracking');
        $this->load->view('admin/licences/manage', $data);
    }


    /* Add new licence or update existing */
    public function release($id)
    {

        $licence = $this->licences_model->get($id);

        if (!$licence || !user_can_view_licence($id)) {
            blank_page(_l('licence_not_found'));
        }

        $data['licence'] = $licence;
        $data['edit']     = false;
        $title            = _l('preview_licence');

        if ($this->input->post()) {

            $licence_data = $this->input->post();
            if(!empty($licence_data['tasks'])){
                $tasks_data = $licence_data['tasks'];
                $this->licences_model->update_licence_data($id, $licence->project_id, $tasks_data);
            }

        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;

        $licence->date = _d($licence->proposed_date);

        if ($licence->project_id !== null) {
            $this->load->model('projects_model');
            $licence->project_data = $this->projects_model->get($licence->project_id);
        }

        //$data = licence_mail_preview_data($template_name, $licence->clientid);

        //$data['licence_members'] = $this->licences_model->get_licence_members($id,true);

        //$data['licence_items']    = $this->licences_model->get_licence_item($id);

        $data['activity']          = $this->licences_model->get_licence_activity($id);
        $data['licence']          = $licence;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();

        //$data['related_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id);
        //$data['released_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id, true, true);

        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'licence']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/small_table'));
            //$this->app->get_table_data(module_views_path('licences', 'admin/tables/table_proposed'));
        }

        $this->session->set_userdata('licence_id', $licence->id);
        $this->session->set_userdata('project_id', $licence->project_id);

        $this->load->view('admin/licences/licence_release_preview', $data);
    }

    /* Add new licence or update existing */
    public function release_item($id, $task_id)
    {

        $licence = $this->licences_model->get($id);

        if (!$licence || !user_can_view_licence($id)) {
            blank_page(_l('licence_not_found'));
        }

        $data['licence'] = $licence;
        $data['edit']     = false;
        $title            = _l('preview_licence');

        if ($this->input->post()) {

            $licence_data = $this->input->post();
            if(!empty($licence_data['tasks'])){
                $tasks_data = $licence_data['tasks'];
                $this->licences_model->update_licence_data($id, $licence->project_id, $tasks_data);
            }

        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;

        $licence->date = _d($licence->proposed_date);
        
        if ($licence->project_id !== null) {
            $this->load->model('projects_model');
            $licence->project_data = $this->projects_model->get($licence->project_id);
        }
        

        //$data = licence_mail_preview_data($template_name, $licence->clientid);

        //$data['licence_members'] = $this->licences_model->get_licence_members($id,true);

        //$data['licence_items']    = $this->licences_model->get_licence_item($id);

        $data['activity']          = $this->licences_model->get_licence_activity($id);
        $data['licence']          = $licence;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();

        //$data['related_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id);
        //$data['released_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id, true, true);

        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'licence']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }
        $inspection_id = $this->licences_model->get_inspection_id($id, $task_id);
        $licence->inspection_id = $inspection_id;
        $inspections_model = 'Inspections_model';
        $model_path = FCPATH . 'modules/'. INSPECTIONS_MODULE_NAME .'/models/' . $inspections_model .'.php';

        include_once($model_path);
        $this->load->model($inspections_model);
        $_inspection = $this->{$inspections_model}->get($inspection_id);
        $inspection = (object)$_inspection[0];
        $data['task_id'] = $task_id;
        $tags = get_tags_in($task_id,'task');
        //$data['jenis_pesawat'] = $tags[0];

        $equipment_type = ucfirst(strtolower(str_replace(' ', '_', $tags[0])));
        
        $tag_id = $this->licences_model->get_available_tags($task_id);
        $licence->categories = get_option('tag_id_'.$tag_id['0']['tag_id']);
                
        $licence->item_number = format_licence_item_number($id, $licence->categories, $task_id);
        $licence_items = $this->licences_model->get_licence_items($licence->id, $task_id);
        $licence->licence_items = $licence_items[0];
        $equipment_model = $equipment_type .'_model';
        $model_path = FCPATH . 'modules/'. INSPECTIONS_MODULE_NAME .'/models/' . $equipment_model .'.php';
        

        if (!file_exists($model_path)) {
            set_alert('danger', _l('file_not_found ;', $equipment_model));
            log_activity('File '. $equipment_model . ' not_found');
            redirect(admin_url('licences/release/'.$id));
        }

        include_once($model_path);
        $this->load->model($equipment_model);

        $_equipment = $this->{$equipment_model}->get('', ['rel_id' => $inspection_id->id, 'task_id' =>$task_id]);
        $equipment = (object)$_equipment;
        $inspection->equipment = $equipment;
        $inspection->client = $licence->client;
        
        $licence->inspection = (object)$inspection;
        $licence->equipment = $equipment;
        $tag_id = get_available_tags($task_id);

        $inspection->categories = get_option('tag_id_'.$tag_id['0']['tag_id']);
        $qrcode = licence_generate_qrcode($licence);
/*        
        echo '<pre>';
        var_dump($inspection->billing_street);
        var_dump($equipment);
        echo '</pre>';
        die();
*/

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/small_table'));
            //$this->app->get_table_data(module_views_path('licences', 'admin/tables/table_proposed'));
        }

        $this->session->set_userdata('licence_id', $licence->id);
        $this->session->set_userdata('project_id', $licence->project_id);

        $this->load->view('admin/licences/licence_release_item_preview', $data);
    }

    /* Add new propose or update existing */
    public function propose($id='')
    {

        $licence = $this->licences_model->get($id);

        if (!$licence || !user_can_view_licence($id)) {
            blank_page(_l('licence_not_found'));
        }

        $data['licence'] = $licence;
        $data['edit']     = false;
        $title            = _l('preview_licence');

        if($id ==''){
            $this->load->view('admin/licences/manage', $data);
           return;
        }

        if ($this->input->post()) {

            $licence_data = $this->input->post();
            if(!empty($licence_data['tasks'])){
                $tasks_data = $licence_data['tasks'];
                unset($tasks_data['licence_id_'.$id]);
                unset($tasks_data['project_id_'.$licence->project_id]);
                $this->licences_model->licence_add_proposed_item($id, $licence->project_id, $tasks_data);
            }
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;

        $licence->proposed_date       = _d($licence->proposed_date);

        if ($licence->project_id !== null) {
            $this->load->model('projects_model');
            $licence->project_data = $this->projects_model->get($licence->project_id);
        }

        //$data = licence_mail_preview_data($template_name, $licence->clientid);

        //$data['licence_members'] = $this->licences_model->get_licence_members($id,true);

        //$data['licence_items']    = $this->licences_model->get_licence_item($id);

        $data['activity']          = $this->licences_model->get_licence_activity($id);
        $data['licence']          = $licence;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();

        //$data['related_tasks'] = $this->licences_model->get_related_tasks('', $licence->project_data->id);
        //$data['proposed_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id, true);

        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'licence']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/small_table'));
            //$this->app->get_table_data(module_views_path('licences', 'admin/tables/table_proposed'));
        }

        $this->session->set_userdata('licence_id', $licence->id);
        $this->session->set_userdata('project_id', $licence->project_id);

        $this->load->view('admin/licences/licence_preview', $data);
    }

    /* Add new licence */
    public function create()
    {
        if ($this->input->post()) {

            $licence_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($licence_data['save_and_send_later'])) {
                unset($licence_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if (!has_permission('licences', '', 'create')) {
                access_denied('licences');
            }

            $next_licence_number = get_option('next_licence_number');
            $_format = get_option('licence_number_format');
            $_prefix = get_option('licence_prefix');

            $prefix  = isset($licence->prefix) ? $licence->prefix : $_prefix;
            $format  = isset($licence->number_format) ? $licence->number_format : $_format;
            $number  = isset($licence->number) ? $licence->number : $next_licence_number;

            $date = date('Y-m-d');

            $licence_data['formatted_number'] = licence_number_format($number, $format, $prefix, $date);

            $id = $this->licences_model->add($licence_data);

            if ($id) {
                set_alert('success', _l('added_successfully', _l('licence')));

                $redUrl = admin_url('licences/propose/' . $id);

                if ($save_and_send_later) {
                    $this->session->set_userdata('send_later', true);
                    // die(redirect($redUrl));
                }

                redirect(
                    !$this->set_licence_pipeline_autoload($id) ? $redUrl : admin_url('licences/release/')
                );
            }
        }
        $title = _l('create_new_licence');

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
        /*
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        */

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['offices']           = get_available_office();
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;

        $this->load->view('admin/licences/licence_create', $data);
    }

    /* Add new licence */
    public function import($client='',$project='')
    {

        $data['clientid'] = $this->uri->segment(4);
        $data['project_id'] = $this->uri->segment(5);

        $project = $this->projects_model->get($data['project_id']);

        $data['project_data'] = false;
        $data['task'] = false;

        if(isset($project->id)){
            $data['project_data'] = $project;
            $data['client_data'] = $project->client_data;
            $task = $this->projects_model->get_tasks($project->id);
            $data['task_data'] = $task;
        }

        if ($this->input->post()) {

            $licence_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($licence_data['save_and_send_later'])) {
                unset($licence_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if (!has_permission('licences', '', 'create')) {
                access_denied('licences');
            }

            $next_licence_number = get_option('next_licence_number');
            $_format = get_option('licence_number_format');
            $_prefix = get_option('licence_prefix');

            $prefix  = isset($licence->prefix) ? $licence->prefix : $_prefix;
            $format  = isset($licence->number_format) ? $licence->number_format : $_format;
            $number  = isset($licence->number) ? $licence->number : $next_licence_number;

            $date = date('Y-m-d');

            $licence_data['formatted_number'] = licence_number_format($number, $format, $prefix, $date);

            $id = $this->licences_model->add($licence_data);

            if ($id) {
                set_alert('success', _l('added_successfully', _l('licence')));

                $redUrl = admin_url('licences/release/' . $id);

                if ($save_and_send_later) {
                    $this->session->set_userdata('send_later', true);
                    // die(redirect($redUrl));
                }

                //redirect(
                //    !$this->set_licence_pipeline_autoload($id) ? $redUrl : admin_url('licences/release/')
                //);
            }
        }


        $title = _l('create_new_licence');

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
        /*
        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->items_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        */

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;

        $this->load->view('admin/licences/licence_import', $data);
    }

    /* update licence */
    public function update($id)
    {
        if ($this->input->post()) {
            $licence_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($licence_data['save_and_send_later'])) {
                unset($licence_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if (!has_permission('licences', '', 'edit')) {
                access_denied('licences');
            }

            $next_licence_number = get_option('next_licence_number');
            $format = get_option('licence_number_format');
            $_prefix = get_option('licence_prefix');

            $number_settings = $this->get_number_settings($id);

            $prefix = isset($number_settings->prefix) ? $number_settings->prefix : $_prefix;

            $number  = isset($licence_data['number']) ? $licence_data['number'] : $next_licence_number;

            $date = date('Y-m-d');

            $licence_data['formatted_number'] = licence_number_format($number, $format, $prefix, $date);

            $success = $this->licences_model->update($licence_data, $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('licence')));
            }

            if ($this->set_licence_pipeline_autoload($id)) {
                redirect(admin_url('licences/'));
            } else {
                redirect(admin_url('licences/release/' . $id));
            }
        }

            $licence = $this->licences_model->get($id);

            if (!$licence || !user_can_view_licence($id)) {
                blank_page(_l('licence_not_found'));
            }

            $data['licence'] = $licence;
            $data['edit']     = true;
            $title            = _l('edit', _l('licence_lowercase'));


        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }


        //$data['licence_members']  = $this->licences_model->get_licence_members($id);
        //$data['licence_items']    = $this->licences_model->get_licence_item($id);


        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['offices']           = get_available_office();
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;
        $this->load->view('admin/licences/licence_update', $data);
    }

    public function get_number_settings($id){
        $this->db->select('prefix');
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'licences')->row();

    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];
        if (has_permission('licences', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'licences', [
                'prefix' => $this->input->post('prefix'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('licence'));
            }
        }

        echo json_encode($response);
        die;
    }

    public function validate_licence_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('proposed_date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix() . 'licences', [
            'YEAR(proposed_date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }


    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_licence($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'licence', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_licence($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'licence');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function mark_action_status($status, $id)
    {
        if (!has_permission('licences', '', 'edit')) {
            access_denied('licences');
        }
        $success = $this->licences_model->mark_action_status($status, $id);
        if ($success) {
            set_alert('success', _l('licence_status_changed_success'));
        } else {
            set_alert('danger', _l('licence_status_changed_fail'));
        }
        if ($this->set_licence_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect($_SERVER['HTTP_REFERER']);
//            redirect(admin_url('licences/release/' . $id));
        }
    }


    public function set_licence_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if ($this->session->has_userdata('licence_pipeline')
                && $this->session->userdata('licence_pipeline') == 'true') {
            $this->session->set_flashdata('licenceid', $id);

            return true;
        }

        return false;
    }

    public function copy($id)
    {
        if (!has_permission('licences', '', 'create')) {
            access_denied('licences');
        }
        if (!$id) {
            die('No licence found');
        }
        $new_id = $this->licences_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('licence_copied_successfully'));
            if ($this->set_licence_pipeline_autoload($new_id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('licences/propose/' . $new_id));
            }
        }
        set_alert('danger', _l('licence_copied_fail'));
        if ($this->set_licence_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('licences/release/' . $id));
        }
    }

    /* Delete licence */
    public function delete($id)
    {
        if (!has_permission('licences', '', 'delete')) {
            access_denied('licences');
        }
        if (!$id) {
            redirect(admin_url('licences'));
        }
        $success = $this->licences_model->delete($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_licence_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('licence')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('licence_lowercase')));
        }
        redirect(admin_url('licences'));
    }

    /* Used in kanban when dragging and mark as */
    public function update_licence_status()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {

            $this->licences_model->update_licence_status($this->input->post());
        }
    }


    public function small_table($licence_id='')
    {

        $this->app->get_table_data(module_views_path('licences', 'admin/tables/small_table'));
    }

    public function table_proposed($licence_id='')
    {

        $this->app->get_table_data(module_views_path('licences', 'admin/tables/table_proposed'));
    }

    public function table_related($licence_id='')
    {

        $this->app->get_table_data(module_views_path('licences', 'admin/tables/table_related'));
    }




    public function table_processed($licence_id='')
    {

        $this->app->get_table_data(module_views_path('licences', 'admin/tables/table_processed'));
    }

    public function table_released($licence_id='')
    {

        $this->app->get_table_data(module_views_path('licences', 'admin/tables/table_released'));
    }


    public function add_proposed_item()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $x = $this->input->post();
            log_activity(json_encode($x));
            $this->licences_model->licence_add_proposed_item($this->input->post());
        }
    }


    public function remove_proposed_item()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->licences_model->licence_remove_proposed_item($this->input->post());
        }
    }

    public function add_released_item()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->licences_model->licence_add_released_item($this->input->post());
        }
    }

    public function remove_released_item()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->licences_model->licence_remove_released_item($this->input->post());
        }
    }

    public function clear_signature($id)
    {
        if (has_permission('licences', '', 'delete')) {
            $this->licences_model->clear_signature($id);
        }

        redirect(admin_url('licences/release/' . $id));
    }

    public function table_suket_proposed($licence_id='')
    {
        $this->app->get_table_data(module_views_path('licences', 'admin/tables/table_suket_proposed'));
    }

    /* Add new licence or update existing */
    public function licence_proposed($id, $task_id)
    {

        $licence = $this->licences_model->get($id);
        $task = $this->tasks_model->get($task_id);

        if (!$licence || !user_can_view_licence($id)) {
            blank_page(_l('licence_not_found'));
        }
        $licence->task_id       = $task_id;
        $licence->task       = $task;
        //$licence->documentations = $this->licences_model->get_licence_documentation($id,$task_id);

        $data['licence'] = $licence;
        $data['edit']     = false;
        $title            = _l('preview_licence');


        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;

        $licence->proposed_date       = _d($licence->proposed_date);

        if ($licence->task !== null) {
            $licence->project_data = $licence->task->project_data;
        }

        //$data = licence_mail_preview_data($template_name, $licence->clientid);

        $data['activity']          = $this->licences_model->get_licence_activity($id);
        $data['task']              = $task;

        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'licence']);

        $allow_editable = get_option('allow_edit_suket_on_draft_status');
        $data['editable_class']          = 'not_editable';
        $data['editableText_class']          = 'noteditableText';

        if($licence->status == 2 || $allow_editable){
            $data['editable_class']          = 'editable';
            $data['editableText_class']          = 'editableText';
        }
        
        $tags = get_tags_in($licence->task_id, 'task');

        $equipment_type = ucfirst(strtolower(str_replace(' ', '_', $tags[0])));
        $licence->equipment_type = $equipment_type;
        $inspection_id = $this->licences_model->get_inspection_id($id, $task_id);
        $licence->inspection_id = $inspection_id;
        $inspections_model = 'Inspections_model';
        $model_path = FCPATH . 'modules/'. INSPECTIONS_MODULE_NAME .'/models/' . $inspections_model .'.php';

        include_once($model_path);
        $this->load->model($inspections_model);

        $equipment_model = $equipment_type .'_model';
        $model_path = FCPATH . 'modules/'. INSPECTIONS_MODULE_NAME .'/models/' . $equipment_model .'.php';

        if (!file_exists($model_path)) {
            set_alert('danger', _l('file_not_found ;', $equipment_model));
            log_activity('File '. $equipment_model . ' not_found');
            redirect(admin_url('licences/propose/'.$id));
        }

        include_once($model_path);
        $this->load->model($equipment_model);
        $equipment = get_available_tags($licence->task_id);
        //$equipment = get_available_tags($licence->task_id, '');

        $licence->equipment = $equipment;

        $licence->categories = get_option('tag_id_'.$equipment['0']['tag_id']);

        $data['licence']          = $licence;
        $data['equipment']          = reset($equipment);
        $licence->licence_items = $this->licences_model->get_licence_items($licence->id, $licence->task_id);
        $data['licence_item'] = $licence->licence_items[0];
        //$licence->categories = get_option('tag_id_'.$equipment['tag_id']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }
        $this->session->set_userdata('licence_id', $id);
        $this->session->set_userdata('project_id', $licence->project_id);

        /*
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/small_table'));
        }
        */

        $this->load->view('admin/licences/licence_suket_proposed_preview', $data);
    }

    public function update_licence_item(){
        if ($this->input->post() && $this->input->is_ajax_request()) {

            $task_id = $this->input->post('task_id');
            $licence_id = $this->input->post('licence_id');
            $field = $this->input->post('field');

            //log_activity(json_encode($this->input->post()));

            $this->licences_model->update_licence_item_data($this->input->post(), $licence_id, $task_id);
        }
    }

    /* Add new licence or update existing */
    public function suket_to_doc($id, $task_id)
    {
        $licence = $this->licences_model->get($id);

        if (!$licence || !user_can_view_licence($id)) {
            blank_page(_l('licence_not_found'));
        }

        $inspection_id = $this->licences_model->get_inspection_id($id, $task_id);
        $licence->inspection_id = $inspection_id;
        $inspections_model = 'Inspections_model';
        $model_path = FCPATH . 'modules/'. INSPECTIONS_MODULE_NAME .'/models/' . $inspections_model .'.php';

        include_once($model_path);
        $this->load->model($inspections_model);
        $_inspection = $this->{$inspections_model}->get($inspection_id);
        $inspection = (object)$_inspection[0];

        $tags = get_tags_in($task_id,'task');
        //$data['jenis_pesawat'] = $tags[0];

        $equipment_type = ucfirst(strtolower(str_replace(' ', '_', $tags[0])));
        
        $tag_id = $this->licences_model->get_available_tags($task_id);
        $licence->categories = get_option('tag_id_'.$tag_id['0']['tag_id']);
                
        $licence->item_number = format_licence_item_number($id, $licence->categories, $task_id);
        $licence_items = $this->licences_model->get_licence_items($licence->id, $task_id);
        $licence->licence_items = $licence_items[0];
        $equipment_model = $equipment_type .'_model';
        $model_path = FCPATH . 'modules/'. INSPECTIONS_MODULE_NAME .'/models/' . $equipment_model .'.php';
        

        if (!file_exists($model_path)) {
            set_alert('danger', _l('file_not_found ;', $equipment_model));
            log_activity('File '. $equipment_model . ' not_found');
            redirect(admin_url('licences/release/'.$id));
        }

        include_once($model_path);
        $this->load->model($equipment_model);

        $_equipment = $this->{$equipment_model}->get('', ['rel_id' => $inspection_id->id, 'task_id' =>$task_id]);
        $equipment = (object)$_equipment[0];
        $inspection->equipment = $equipment;
        $inspection->client = $licence->client;
        
        $licence->inspection = (object)$inspection;
        //$licence->equipment = $equipment;
        $tag_id = get_available_tags($task_id);

        $inspection->categories = get_option('tag_id_'.$tag_id['0']['tag_id']);
        
        $data = inspection_data($inspection, $task_id);

        $_data = licence_data($licence, $task_id);
        
        foreach ($_data as $key => $value) {
            $data[$key] = $value;
        }
        /*
        echo '<pre>';
        var_dump($equipment->jenis_pesawat);
        echo '<br />=============<br />';
        var_dump($data);
        echo '</pre>';
        die();
        */
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $file = strtolower($equipment->jenis_pesawat).'.docx';
        $dir = strtolower($data['upt']);
        $dir = str_replace(' ', '_', $dir);
        
        $template = FCPATH .'modules/'. LICENCES_MODULE_NAME . '/assets/resources/'.$dir.'/suket_'. $file;
        
        if (!file_exists($template)) {
            set_alert('danger', _l('file_not_found ;', $file));
            log_activity('File '. $file . ' not_found');
            redirect(admin_url('licences/release_item/'.$id.'/'. $task_id));
        }
        $templateProcessor = $phpWord->loadTemplate($template);
        
        $templateProcessor->setValues($data);

        //$templateProcessor->setImageValue('CompanyLogo', 'path/to/company/logo.png');
        $temp_filename = strtoupper($equipment->jenis_pesawat) .'-'. $inspection->formatted_number . '.docx';
        $templateProcessor->saveAs($temp_filename);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$temp_filename);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($temp_filename));
        flush();
        readfile($temp_filename);
        unlink($temp_filename);
        exit; 

    }
}
