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
    public function licence($id)
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

        $licence->date       = _d($licence->date);        
        
        if ($licence->project_id !== null) {
            $this->load->model('projects_model');
            $licence->project_data = $this->projects_model->get($licence->project_id);
        }

        //$data = licence_mail_preview_data($template_name, $licence->clientid);

        $data['licence_members'] = $this->licences_model->get_licence_members($id,true);

        //$data['licence_items']    = $this->licences_model->get_licence_item($id);

        $data['activity']          = $this->licences_model->get_licence_activity($id);
        $data['licence']          = $licence;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();

        $data['related_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id);
        $data['released_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id, true, true);

        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'licence']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/small_table'));
        }

        $this->load->view('admin/licences/licence_release_preview', $data);
    }


    /* Add new propose or update existing */
    public function propose($id)
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
                log_activity(json_encode($tasks_data));
                $this->licences_model->add_licence_data($id, $licence->project_id, $tasks_data);
            }

        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;

        $licence->date       = _d($licence->date);        
        
        if ($licence->project_id !== null) {
            $this->load->model('projects_model');
            $licence->project_data = $this->projects_model->get($licence->project_id);
        }

        //$data = licence_mail_preview_data($template_name, $licence->clientid);

        $data['licence_members'] = $this->licences_model->get_licence_members($id,true);

        //$data['licence_items']    = $this->licences_model->get_licence_item($id);

        $data['activity']          = $this->licences_model->get_licence_activity($id);
        $data['licence']          = $licence;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();

        $data['related_tasks'] = $this->licences_model->get_related_tasks('', $licence->project_data->id);
        $data['proposed_tasks'] = $this->licences_model->get_related_tasks($id, $licence->project_data->id, true);

        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'licence']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/small_table'));
        }

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

                $redUrl = admin_url('licences/licence/' . $id);

                if ($save_and_send_later) {
                    $this->session->set_userdata('send_later', true);
                    // die(redirect($redUrl));
                }

                redirect(
                    !$this->set_licence_pipeline_autoload($id) ? $redUrl : admin_url('licences/licence/')
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

                $redUrl = admin_url('licences/licence/' . $id);

                if ($save_and_send_later) {
                    $this->session->set_userdata('send_later', true);
                    // die(redirect($redUrl));
                }

                //redirect(
                //    !$this->set_licence_pipeline_autoload($id) ? $redUrl : admin_url('licences/licence/')
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

            $next_schedule_number = get_option('next_licence_number');
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
                redirect(admin_url('licences/licence/' . $id));
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


        $data['licence_members']  = $this->licences_model->get_licence_members($id);
        //$data['licence_items']    = $this->licences_model->get_licence_item($id);


        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;
        $this->load->view('admin/licences/licence_update', $data);
    }

    public function get_number_settings($id){
        $this->db->select('prefix');
        $this->db->where('id', $id);
        return $this->db->get(db_prefix() . 'schedules')->row();

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
        $date            = $this->input->post('date');
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
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
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
//            redirect(admin_url('licences/licence/' . $id));
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
                redirect(admin_url('licences/licence/' . $new_id));
            }
        }
        set_alert('danger', _l('licence_copied_fail'));
        if ($this->set_licence_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('licences/licence/' . $id));
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

    public function clear_signature($id)
    {
        if (has_permission('licences', '', 'delete')) {
            $this->licences_model->clear_signature($id);
        }

        redirect(admin_url('licences/licence/' . $id));
    }

}