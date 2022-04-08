<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends CI_Controller {

    private $session_id         = null;
    private $is_session_enabled = false;

    public function __construct() {

        parent::__construct();

        $this->load->model('api_session_model');
        $this->load->library('widget_api_202107');

        $action = $this->router->method;

        /* ------- NO ACCESS_TOKEN METHODS ------- */
        $free = ['get_settings', 'setup', 'is_logged']; //method some times needs token validation
        /* ------- ---------------- ------ */

        //restrict endpoint when method/action is not in the free array OR
        if (!in_array($action, $free)) {

            if ($action == 'index') {
                // ========== CONTINUE - IT'S FREE =========
            } else { //restrict - validate access token, if it does not match cut the flow
                $result = $this->widget_api_202107->validaAccessToken();
                if ($result['status'] === false) {
                    output_json_custom($result);
                    die;
                }
                $this->is_session_enabled = true;
                $this->session_id         = $result['current_access_token'];
            }
        }
    }

    

    //get
    public function index($hash = 0) {
        
        try {
            ob_start();
            $this->load->model('invoice_model');
            $this->invoice_model->valAsArray = true;
            $invoice = $this->invoice_model->getByHash($hash);
            
            if(!$invoice) {
                output_json_api([
                    'invoice' => $invoice,
                        ], 0, REST_Controller_Codes::HTTP_OK);
                return;
            }

            require_once 'application/controllers/extensions/Payments.php';
            $orgnx_id = $invoice->church_id;            
            $envObj = Payments::getEnvironment(PROVIDER_PAYMENT_PAYSAFE_SHORT, $orgnx_id);

            $env = null;
            $encodedKeys = null;
            if ($envObj['envTest']) {
                $env = 'TEST';
                $encodedKeys = base64_encode(PAYSAFE_SINGLE_USE_API_KEY_USER_TEST . ':' . PAYSAFE_SINGLE_USE_API_KEY_PASS_TEST);
            } else {
                $env = 'LIVE';
                $encodedKeys = base64_encode(PAYSAFE_SINGLE_USE_API_KEY_USER_LIVE . ':' . PAYSAFE_SINGLE_USE_API_KEY_PASS_LIVE);
            }
            ob_get_clean();
            output_json_api([
                'invoice'            => $invoice,
                'payment_processor' => [
                    'code'         => PROVIDER_PAYMENT_PAYSAFE_SHORT,
                    'env'          => $env,
                    'encoded_keys' => $encodedKeys
                ]
            ], 0, REST_Controller_Codes::HTTP_OK);
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }
}
