<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Organization extends CI_Controller {

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

            if ($action == 'get_brand_settings') {
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

    public  function get_brand_settings($org_id = null,$suborg_id = null)
    {
        try {

            $this->load->model('chat_setting_model');
            $result = $this->chat_setting_model->getChatSettingByChurch($org_id,$suborg_id);

            output_json_api([
                'data'            => $result,
            ], 0, REST_Controller_Codes::HTTP_OK);
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }

} 
