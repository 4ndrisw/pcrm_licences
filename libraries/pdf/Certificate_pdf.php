<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(LIBSPATH . 'pdf/App_pdf.php');

class Certificate_pdf extends App_pdf
{
    protected $certificate;

    private $certificate_item_number;

    public function __construct($certificate, $tag = '')
    {
        $this->load_language($certificate->clientid);

        $certificate                = hooks()->apply_filters('certificate_html_pdf_data', $certificate);
        $GLOBALS['certificate_pdf'] = $certificate;

        parent::__construct();

        $this->tag             = $tag;
        $this->certificate     = $certificate;
        $this->equipment       = $certificate->equipment;
        $this->certificate_item_number = format_licence_item_number($this->certificate->id, $this->certificate->categories, $this->certificate->task_id);

        $this->SetTitle($this->certificate_item_number);
    }

    public function prepare()
    {

        $this->set_view_vars([
            'status'          => $this->certificate->status,
            'certificate_item_number' => $this->certificate_item_number,
            'certificate'        => $this->certificate,
            'equipment'        => $this->equipment,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'certificate';
    }

    protected function file_path()
    {
        $filePath = 'my_certificatepdf.php';
        $certificate_equipment_type = isset($this->certificate->equipment_type) ? strtolower($this->certificate->equipment_type) : FALSE;

        if(isset($this->certificate->categories)){
            $filePath = 'certificate_'. $this->certificate->categories .'_pdf.php';
        }
        
        if($certificate_equipment_type){
            $equipment_type = 'certificate_'. $certificate_equipment_type .'_pdf.php';
            $equipment_type_filePath = module_views_path('licences','themes/' . active_clients_theme() . '/views/certificates/' . $equipment_type);
            if (file_exists($equipment_type_filePath)) {
                $filePath = $equipment_type;
            }
        }

        $customPath = module_views_path('licences','themes/' . active_clients_theme() . '/views/certificates/' . $filePath);
        $actualPath = module_views_path('licences','themes/' . active_clients_theme() . '/views/certificates/certificate_equipment_type_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
