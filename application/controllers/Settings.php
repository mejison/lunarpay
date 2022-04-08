<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends My_Controller {

    public $data = [];

    public function __construct() {
        parent::__construct();

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $this->template_data['view_index'] = $this->router->fetch_class() . '/' . $this->router->fetch_method();
        $this->load->use_theme();

        $this->load->library(['form_validation']);
    }

    //===== $opt holds the tab, currently "pcenter"
    public function integrations($tab = 'false') {

        $this->template_data['title']         = langx("integrations");

        //Getting Organizations
        $this->load->model('organization_model');
        $organizations = $this->organization_model->getList('ch_id,church_name,token');
        $this->template_data['organizations'] = $organizations;
        $this->template_data['tab'] = $tab;

        $view                                 = $this->load->view('setting/integrations', ['view_data' => $this->template_data], true);
        $this->template_data['content']       = $view;
        $this->load->view('main', $this->template_data);
    }
    
    public function team() {
        $this->template_data['title'] = langx("team");

        $this->template_data['identity_column'] = $this->config->item('identity', 'ion_auth');
        
        $view = $this->load->view('team/team', ['view_data' => $this->template_data], true);

        $this->template_data['content'] = $view;

        $this->load->view('main', $this->template_data);
    }

    public function referrals() {

        $this->load->model('setting_model');
        $this->load->model('User_model');
  
        if($this->setting_model->getItem('SYSTEM_LETTER_ID') !== 'H' || 
        empty($this->User_model->get($this->session->userdata('user_id'),'referral_code')->referral_code)){
                show_404();
        } 
        $this->load->library(['form_validation']);
        
        $this->template_data['title']         = langx("Referrals");
        $view                                 = $this->load->view('setting/referrals', ['view_data' => $this->template_data], true);
        $this->template_data['content']       = $view;
        $this->load->view('main', $this->template_data);
    }
    
    public function referals_get_dt() {
        $this->load->model('referal_model');
        output_json($this->referal_model->getDt(), true);
    }

    public function branding(){
        $this->template_data['title'] = langx("branding");

        $view = $this->load->view('setting/branding', ['view_data' => $this->template_data], true);

        $this->template_data['content'] = $view;

        $this->load->view('main', $this->template_data);
    }
    public function save_affiliate()
    {
        $this->load->model('user_model');
        try {
            $data = $this->input->post();
            $length = 20;
            $data['referral_code'] = substr(bin2hex(random_bytes(ceil($length / 2))), 0, $length);
            $this->user_model->update($data, $this->session->userdata('user_id'));
            output_json(['status' => true]);
        } catch (Exception $ex) {

            // ---- if $this->donation_model->valAsArray = true
            // ---- we need to send the $ex->getMessage() as an one element array, without the stringifyFormatErrors
            // ---- thinking in the future, we may use this if we install an API

            output_json(['status' => false, 'errors' => stringifyFormatErrors([$ex->getMessage()]), 'exception' => true]);
        }
    }

    public function save_branding()
    {
        $this->load->model('chat_setting_model');
        try {
            $data = $this->input->post();

            // ---- $this->product_model->valAsArray = true;

            $data['church_id'] = $data['organization_id'];
            $data['campus_id'] = $data['suborganization_id'];

            $result = $this->chat_setting_model->save($data);

            output_json($result);
        } catch (Exception $ex) {

            // ---- if $this->donation_model->valAsArray = true
            // ---- we need to send the $ex->getMessage() as an one element array, without the stringifyFormatErrors
            // ---- thinking in the future, we may use this if we install an API

            output_json(['status' => false, 'errors' => stringifyFormatErrors([$ex->getMessage()]), 'exception' => true]);
        }
    }

    public function get_branding($church_id,$campus_id = null)
    {
        $this->load->model('chat_setting_model');
        $result = $this->chat_setting_model->getChatSetting($this->session->userdata('user_id'),$church_id,$campus_id);
        output_json(['status' => true, 'data' => $result]);
    }

}
