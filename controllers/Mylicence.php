<?php defined('BASEPATH') or exit('No direct script access allowed');

class Mylicence extends ClientsController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('licences_model');
        $this->load->model('clients_model');
    }

    /* Get all licences in case user go on index page */
    public function list($id = '')
    {
        if (!is_client_logged_in() && !is_staff_logged_in()) {
            if (get_option('view_licence_only_logged_in') == 1) {
                redirect_after_login_to_current_url();
                redirect(site_url('authentication/login'));
            }
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/table'));
        }
        $contact_id = get_contact_user_id();
        $user_id = get_user_id_by_contact_id($contact_id);
        $client = $this->clients_model->get($user_id);
        $data['licences'] = $this->licences_model->get_client_licences($client);
        $data['licenceid']            = $id;
        $data['title']                 = _l('licences_tracking');

        $data['bodyclass'] = 'licences';
        $this->data($data);
        $this->view('themes/'. active_clients_theme() .'/views/licences/licences');
        $this->layout();
    }

    public function show($id, $hash)
    {
        check_licence_restrictions($id, $hash);
        $licence = $this->licences_model->get($id);

        if (!is_client_logged_in()) {
            load_client_language($licence->clientid);
        }

        $identity_confirmation_enabled = get_option('licence_accept_identity_confirmation');

        // Handle Licence PDF generator

        $licence_number = format_licence_number($licence->id);
        if ($this->input->post('licencepdf')) {
            try {
                $pdf = licence_pdf($licence);
            } catch (Exception $e) {
                echo $e->getMessage();
                die;
            }

            //$licence_number = format_licence_number($licence->id);
            $companyname     = get_option('company_name');
            if ($companyname != '') {
                $licence_number .= '-' . mb_strtoupper(slug_it($companyname), 'UTF-8');
            }

            $filename = hooks()->apply_filters('customers_area_download_licence_filename', mb_strtoupper(slug_it($licence_number), 'UTF-8') . '.pdf', $licence);

            $pdf->Output($filename, 'D');
            die();
        }

        $data['title'] = $licence_number;
        $this->disableNavigation();
        $this->disableSubMenu();

        $data['licence_number']              = $licence_number;
        $data['hash']                          = $hash;
        $data['can_be_accepted']               = false;
        $data['licence']                     = hooks()->apply_filters('licence_html_pdf_data', $licence);
        $data['bodyclass']                     = 'viewlicence';
        $data['client_company']                = $this->clients_model->get($licence->clientid)->company;
        $setSize = get_option('licence_qrcode_size');
        $data['identity_confirmation_enabled'] = $identity_confirmation_enabled;
        if ($identity_confirmation_enabled == '1') {
            $data['bodyclass'] .= ' identity-confirmation';
        }

        $qrcode_data  = '';
        $qrcode_data .= _l('licence_number') . ' : ' . $licence_number ."\r\n";
        $qrcode_data .= _l('licence_date') . ' : ' . $licence->date ."\r\n";
        $qrcode_data .= _l('licence_datesend') . ' : ' . $licence->datesend ."\r\n";
        $qrcode_data .= _l('licence_assigned_string') . ' : ' . get_staff_full_name($licence->assigned) ."\r\n";
        $qrcode_data .= _l('licence_url') . ' : ' . site_url('licences/show/'. $licence->id .'/'.$licence->hash) ."\r\n";

        $licence_path = get_upload_path_by_type('licences') . $licence->id . '/';
        _maybe_create_upload_path('uploads/licences');
        _maybe_create_upload_path('uploads/licences/'.$licence_path);

        $params['data'] = $qrcode_data;
        $params['writer'] = 'png';
        $params['setSize'] = isset($setSize) ? $setSize : 160;
        $params['encoding'] = 'UTF-8';
        $params['setMargin'] = 0;
        $params['setForegroundColor'] = ['r'=>0,'g'=>0,'b'=>0];
        $params['setBackgroundColor'] = ['r'=>255,'g'=>255,'b'=>255];

        $params['crateLogo'] = true;
        $params['logo'] = './uploads/company/favicon.png';
        $params['setResizeToWidth'] = 60;

        $params['crateLabel'] = false;
        $params['label'] = $licence_number;
        $params['setTextColor'] = ['r'=>255,'g'=>0,'b'=>0];
        $params['ErrorCorrectionLevel'] = 'hight';

        $params['saveToFile'] = FCPATH.'uploads/licences/'.$licence_path .'assigned-'.$licence_number.'.'.$params['writer'];

        $this->load->library('endroid_qrcode');
        $this->endroid_qrcode->generate($params);

        $this->data($data);
        $this->app_scripts->theme('sticky-js', 'assets/plugins/sticky/sticky.js');
        $this->view('themes/'. active_clients_theme() .'/views/licences/licencehtml');
        add_views_tracking('licence', $id);
        hooks()->do_action('licence_html_viewed', $id);
        no_index_customers_area();
        $this->layout();
    }


    /* Generates licence PDF and senting to email  */
    public function pdf($id)
    {
        $canView = user_can_view_licence($id);
        if (!$canView) {
            access_denied('Licences');
        } else {
            if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && $canView == false) {
                access_denied('Licences');
            }
        }
        if (!$id) {
            redirect(admin_url('licences'));
        }
        $licence        = $this->licences_model->get($id);
        $licence_number = format_licence_number($licence->id);

        $licence->assigned_path = FCPATH . get_licence_upload_path('licence').$licence->id.'/assigned-'.$licence_number.'.png';
        $licence->acceptance_path = FCPATH . get_licence_upload_path('licence').$licence->id .'/'.$licence->signature;
        $licence->client_company = $this->clients_model->get($licence->clientid)->company;
        $licence->acceptance_date_string = _dt($licence->acceptance_date);


        try {
            $pdf = licence_pdf($licence);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $fileNameHookData = hooks()->apply_filters('licence_file_name_admin_area', [
                            'file_name' => mb_strtoupper(slug_it($licence_number)) . '.pdf',
                            'licence'  => $licence,
                        ]);

        $pdf->Output($fileNameHookData['file_name'], $type);
    }


}
