<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Referals extends My_Controller {

    public $data = [];

    public function __construct() {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
   
        $this->template_data['view_index'] = $this->router->fetch_class() . '/' . $this->router->fetch_method();
        $this->load->use_theme();
    }

    public function save()
    {
        $this->load->model('setting_model');
        if($this->setting_model->getItem('SYSTEM_LETTER_ID') !== 'H'){
                show_404();
        } 
        $this->load->model('Referal_model');
        try {
            $data = $this->input->post();
            output_json($this->Referal_model->save($data));
        } catch (Exception $ex) {

            // ---- if $this->donation_model->valAsArray = true
            // ---- we need to send the $ex->getMessage() as an one element array, without the stringifyFormatErrors
            // ---- thinking in the future, we may use this if we install an API

            output_json(['status' => false, 'errors' => stringifyFormatErrors([$ex->getMessage()]), 'exception' => true]);
        }
    }


   


   

    

    

    
    
}
