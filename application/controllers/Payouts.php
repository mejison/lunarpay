<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payouts extends My_Controller {

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

    public function index() {
        $this->load->model('organization_model');

        $this->template_data['title'] = langx("payouts");

        if ($this->session->userdata('payment_processor_short') == PROVIDER_PAYMENT_EPICPAY_SHORT) {
            $this->template_data['organizations'] = $this->organization_model->getList(['ch_id', 'church_name'], 'ch_id ASC');
            $view                                 = $this->load->view('payout/payout', ['view_data' => $this->template_data], true);
        } else if ($this->session->userdata('payment_processor_short') == PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            
            $this->load->model('orgnx_onboard_psf_model');
            $user_id = $this->session->userdata('user_id');
            $backoffice_user = $this->orgnx_onboard_psf_model->getBackofficeUser($user_id);
            
            $this->template_data['backoffice_url'] = PAYSAFE_NETBANX_URL;
            $this->template_data['email']          = $backoffice_user && $backoffice_user->backoffice_email ? $backoffice_user->backoffice_email : null;
            $view                                  = $this->load->view('payout/payout_psf', ['view_data' => $this->template_data], true);
        }


        $this->template_data['content'] = $view;

        $this->load->view('main', $this->template_data);
    }
    
    public function get_dt() {

        require_once 'application/controllers/extensions/Payments.php';

        $church_id = intval($this->input->post("church_id"));
        $user_id   = $this->session->userdata('user_id');
        //$churchid = 344;

        $_POST['month'] = str_replace('/', '-', $_POST['month']);

        $requestBody["start_date"] = date('Y-m-01', strtotime($this->input->post("month")));
        $requestBody["end_date"]   = date('Y-m-t', strtotime($this->input->post("month")));

        $response = ['draw' => 0, "recordsTotal" => 0, 'recordsFiltered' => 0, 'data' => []];

        if ($church_id == 0) {
            echo json_encode($response);
            die;
        }

        $result = Payments::getPayouts($church_id, $user_id, $requestBody);

        $dtDraw = intval($this->input->post("draw"));

        $data = $result["result"]->data;

        $final_data_epic = [];
        if ($data) {
            foreach ($data as $i => $payout) {
                $final_data_epic[$i]["index"]        = $i;
                $final_data_epic[$i]["id"]           = $payout->account_no;
                $final_data_epic[$i]["account_no"]   = "";
                $final_data_epic[$i]["amount"]       = $payout->amount;
                $final_data_epic[$i]["currency"]     = "USD";
                $final_data_epic[$i]["status"]       = "NONE";
                $final_data_epic[$i]["created"]      = $payout->reported_date;
                $final_data_epic[$i]["arrival_date"] = $payout->reported_date;
                $final_data_epic[$i]["detail_data"]  = json_encode($payout->detail_data);
                $final_data_epic[$i]["description"]  = "EPICPAY PAYOUT";
                $final_data_epic[$i]["system"]       = "epicpay";
            }
        }


        $dtTotal = count($final_data_epic);

        $final_data = $final_data_epic;

        usort($final_data, "custom_sort_desc_created");

        $response = ['draw' => $dtDraw, "recordsTotal" => $dtTotal, 'recordsFiltered' => $dtTotal, 'data' => $final_data];

        echo json_encode($response);
    }

}
