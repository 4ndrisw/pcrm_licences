<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(LIBSPATH . 'pdf/App_pdf.php');

class Suket_pdf extends App_pdf
{
    protected $suket;

    private $suket_item_number;

    public function __construct($suket, $tag = '')
    {
        $this->load_language($suket->clientid);

        $suket                = hooks()->apply_filters('suket_html_pdf_data', $suket);
        $GLOBALS['suket_pdf'] = $suket;

        parent::__construct();

        $this->tag             = $tag;
        $this->suket     = $suket;
        $this->equipment       = $suket->equipment;
        $this->suket_item_number = format_licence_item_number($this->suket->id, $this->suket->categories, $this->suket->task_id);

        $this->SetTitle($this->suket_item_number);
    }

    public function prepare()
    {

        $this->set_view_vars([
            'status'          => $this->suket->status,
            'suket_item_number' => $this->suket_item_number,
            'suket'        => $this->suket,
            'office'        => $this->suket->office,
            'office_short_name'        => strtolower(str_replace(' ','_',$this->suket->office->short_name)),
            'equipment'        => $this->equipment,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'suket';
    }

    protected function file_path()
    {
        $filePath = 'my_suketpdf.php';
        if(isset($this->suket->categories)){
            $filePath = 'suket_'. $this->suket->categories .'_pdf.php';
        }
        $office_short_name = strtolower(str_replace(' ','_',$this->suket->office->short_name));
        $customPath = module_views_path('licences','themes/' . active_clients_theme() . '/views/sukets/' . $office_short_name .'/'. $filePath);
        $actualPath = module_views_path('licences','themes/' . active_clients_theme() . '/views/sukets/suket_item_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
