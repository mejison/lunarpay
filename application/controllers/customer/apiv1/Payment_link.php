<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_link extends CI_Controller {

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
            }
        }
    }

    //get payment link and associations
    public function index($hash = 0) {
        try {
            $this->load->model('payment_link_model');
            $this->payment_link_model->valAsArray = true;
            $paymentLink = $this->payment_link_model->getByHash($hash);

            //Remove Digital Contents
            foreach ($paymentLink->products as $product){
                $product->digital_content = null;
                $product->digital_content_url = null;
            }

            if(!$paymentLink) {
                output_json_api([
                    'payment_link' => $paymentLink,
                ], 0, REST_Controller_Codes::HTTP_OK);
                return;
            }

            require_once 'application/controllers/extensions/Payments.php';
            $orgnx_id = $paymentLink->church_id;
            $envObj = Payments::getEnvironment(PROVIDER_PAYMENT_PAYSAFE_SHORT, $orgnx_id);

            $env = null;
            $encodedKeys = null;
            if($envObj['envTest']) {
                $env = 'TEST';
                $encodedKeys = base64_encode(PAYSAFE_SINGLE_USE_API_KEY_USER_TEST . ':' . PAYSAFE_SINGLE_USE_API_KEY_PASS_TEST);
            } else {
                $env = 'LIVE';
                $encodedKeys = base64_encode(PAYSAFE_SINGLE_USE_API_KEY_USER_LIVE . ':' . PAYSAFE_SINGLE_USE_API_KEY_PASS_LIVE);
            }

            output_json_api([
                'payment_link'      => $paymentLink,
                'payment_processor' => [
                    'code'          => PROVIDER_PAYMENT_PAYSAFE_SHORT,
                    'env'           => $env,
                    'encoded_keys'  => $encodedKeys]
                    ], 0, REST_Controller_Codes::HTTP_OK);

        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }

}
