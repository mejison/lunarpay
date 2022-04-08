<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    private $session_id = null;

    public function __construct() {

        parent::__construct();

        $this->load->model('api_session_model');
        $this->load->library('widget_api_202107');

        $action = $this->router->method;

        /* ------- NO ACCESS_TOKEN METHODS ------- */
        $free = ['generate_security_code', 'login', 'register', 'account_exists', 'refresh_token'];
        /* ------- ---------------- ------ */

        if (!in_array($action, $free)) {
            $result = $this->widget_api_202107->validaAccessToken();
            if ($result['status'] === false) {
                output_json_api(['errors' => $result['code'], 'details' => $result], 1, $result['http_code']);
                exit;
            }
            $this->session_id = $result['current_access_token'];
        }
    }

    public function account_exists() {
        $input_json = @file_get_contents('php://input');
        $input      = json_decode($input_json);

        try {

            $identity  = $input->username;
            $church_id = $input->org_id;

            /*if (isset($input->suborg_id) || $input->suborg_id) {
                throw new Exception('Processing with sub organizations is not ready');
            }*/

            $this->load->model('organization_model');

            $orgnx = $this->organization_model->get($church_id, 'church_name');

            if (!$orgnx) {
                throw new Exception('Invalid Company');
            }

            $this->load->model('donor_model');
            $this->donor_model->valAsArray        = true; //get validation errors as array, not a string
            $this->load->model('organization_model');
            $this->organization_model->valAsArray = true; //get validation errors as array, not a string

            $orgnx = $this->organization_model->get($church_id, 'church_name, client_id');

            if (!$orgnx) {
                throw new Exception('Invalid Company');
            }

            $customerAcc = $this->donor_model->getLoginData($identity, $church_id);

            output_json_api(['status' => $customerAcc ? true : false], 0, REST_Controller_Codes::HTTP_OK);

            return;
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }

    public function generate_security_code() {
        $input_json = @file_get_contents('php://input');
        $input      = json_decode($input_json);

        try {

            $identity  = $input->username;
            $church_id = $input->org_id;

            /*if (isset($input->suborg_id) || $input->suborg_id) {
                throw new Exception('Processing with sub organizations is not ready');
            }*/

            $this->load->model('organization_model');
            $this->organization_model->valAsArray = true; //get validation errors as array, not a string

            $orgnx = $this->organization_model->get($church_id, 'church_name');

            if (!$orgnx) {
                throw new Exception('Invalid Company');
            }
            
            $subject   = $orgnx->church_name . ' - Security Code';
            $from_name = $orgnx->church_name;

            $this->load->helper('verification_code');

            $result = sendVerificationCode($identity, $subject, $from_name);

            output_json_api($result, $result['status'] ? 0 : 1, REST_Controller_Codes::HTTP_OK);

            return;
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }

    public function register() {
        $input_json = @file_get_contents('php://input');
        $input      = json_decode($input_json);

        try {

            $customerData['email']           = $identity                        = $input->username;
            $customerData['organization_id'] = $church_id                       = $input->org_id;
            $customerData['first_name']      = $input->name; //if first name has two words, the saving processs will save first and last name on database, model does that split, cool.
            $security_code                   = $input->security_code;

            /*if (isset($input->suborg_id) || $input->suborg_id) {
                throw new Exception('Processing with sub organizations not ready');
            }*/
            $customerData['suborganization_id'] = $campus_id                          = null;

            $this->load->model('donor_model');
            $this->donor_model->valAsArray        = true; //get validation errors as array, not a string
            $this->load->model('organization_model');
            $this->organization_model->valAsArray = true; //get validation errors as array, not a string

            $orgnx = $this->organization_model->get($church_id, 'church_name, client_id');

            if (!$orgnx) {
                throw new Exception('Invalid Company');
            }

            $customerAcc = $this->donor_model->getLoginData($identity, $church_id);

            if (!$customerAcc) { //create account                
                $response = $this->donor_model->save($customerData, $orgnx->client_id);

                if (!$response['status']) {
                    output_json_api($response, 1, REST_Controller_Codes::HTTP_OK);
                    return;
                }
                $customerAcc = $this->donor_model->getLoginData($identity, $church_id);
            } else {
                output_json_api(['status' => false, 'message' => 'Account already exists'], 1, REST_Controller_Codes::HTTP_OK);
                return;
            }

            $this->load->model('code_security_model');
            $code_security = $this->code_security_model->get($identity, $security_code);

            $access_token['token']  = null;
            $refresh_token['token'] = null;

            $login_success = false;
            if ($code_security) {
                $login_success = true;
                $this->code_security_model->reset($identity);

                $access_token  = $this->widget_api_202107->resetAccessToken('on_login', $church_id, $campus_id, $customerAcc->id);
                $refresh_token = $this->widget_api_202107->resetRefreshToken('on_login', $church_id, $campus_id, $customerAcc->id);

                $this->session_id = $access_token['token'];
                $this->api_session_model->setValue($this->session_id,'user_id',$customerAcc->id);
                $this->api_session_model->setValue($this->session_id,'identity',$identity);
            }

            output_json_api([
                'status'                 => $login_success,
                'message'                => $login_success ? langx('Account Created, Access granted') : langx('Security code provided does not match'),
                WIDGET_AUTH_OBJ_VAR_NAME => [
                    WIDGET_AUTH_ACCESS_TOKEN_VAR_NAME  => $access_token['token'],
                    WIDGET_AUTH_REFRESH_TOKEN_VAR_NAME => $refresh_token['token']
                ]], $login_success ? 0 : 1, REST_Controller_Codes::HTTP_OK);
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }
    
    public function login() {
        $input_json = @file_get_contents('php://input');
        $input      = json_decode($input_json);

        try {

            $customerData['email']           = $identity                        = $input->username;
            $customerData['organization_id'] = $church_id                       = $input->org_id;            
            $security_code                   = $input->security_code;

            /*if (isset($input->suborg_id) || $input->suborg_id) {
                throw new Exception('Processing with sub organizations not ready');
            }*/
            $customerData['suborganization_id'] = $campus_id                          = null;

            $this->load->model('donor_model');
            $this->donor_model->valAsArray        = true; //get validation errors as array, not a string
            $this->load->model('organization_model');
            $this->organization_model->valAsArray = true; //get validation errors as array, not a string

            $orgnx = $this->organization_model->get($church_id, 'church_name, client_id');

            if (!$orgnx) {
                throw new Exception('Invalid Company');
            }

            $customerAcc = $this->donor_model->getLoginData($identity, $church_id);

            if (!$customerAcc) { //create account                
                output_json_api(['status' => false, 'message' => 'Account provided not found, go ahead and create one'], 1, REST_Controller_Codes::HTTP_OK);
                return;
            } 

            $this->load->model('code_security_model');
            $code_security = $this->code_security_model->get($identity, $security_code);

            $access_token['token']  = null;
            $refresh_token['token'] = null;

            $login_success = false;
            if ($code_security) {
                $login_success = true;
                $this->code_security_model->reset($identity);

                $access_token  = $this->widget_api_202107->resetAccessToken('on_login', $church_id, $campus_id, $customerAcc->id);
                $refresh_token = $this->widget_api_202107->resetRefreshToken('on_login', $church_id, $campus_id, $customerAcc->id);

                $this->session_id = $access_token['token'];
                $this->api_session_model->setValue($this->session_id,'user_id',$customerAcc->id);
                $this->api_session_model->setValue($this->session_id,'identity',$identity);
            }

            output_json_api([
                'status'                 => $login_success,
                'message'                => $login_success ? langx('Access granted') : langx('Security code provided does not match'),
                WIDGET_AUTH_OBJ_VAR_NAME => [
                    WIDGET_AUTH_ACCESS_TOKEN_VAR_NAME  => $access_token['token'],
                    WIDGET_AUTH_REFRESH_TOKEN_VAR_NAME => $refresh_token['token']
                ]], $login_success ? 0 : 1, REST_Controller_Codes::HTTP_OK);
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }

    public function sign_out()
    {
        try {
        $accessToken = $this->widget_api_202107->getAccessToken($this->session_id);

        $this->widget_api_202107->deleteAccessToken($this->session_id);
        $this->widget_api_202107->deleteRefreshTokenByUserId($accessToken->user_id);

        output_json_api(['status' => true , 'message' => 'You have successfully logged out!'], 0, REST_Controller_Codes::HTTP_OK);
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }
    
    public function refresh_token() {

        $headers = $this->input->request_headers();

        if (!isset($headers['Authorization']) || strpos($headers['Authorization'], 'Bearer ') !== 0) {
            $result = ['status' => false, 'code' => 'bad_request', 'http_code' => REST_Controller_Codes::HTTP_BAD_REQUEST];
            output_json_api($result, 1, $result['http_code']);            
            return;
        }

        $auth  = explode(' ', $headers['Authorization']);
        $refreshToken = $auth[1];

        $aResp = $this->widget_api_202107->resetAccessToken('on_refresh', false, false, false, $refreshToken);

        if (!$aResp['status']) {
            output_json_api($aResp, 1, $aResp['http_code']);
            return;
        }

        $rResp = $this->widget_api_202107->resetRefreshToken('on_refresh', false, false, false, $refreshToken);

        
        if (!$rResp['status']) {
            output_json_api($aResp, 1, $aResp['http_code']);
            return;
        }

        output_json_api([
            'status'                  => true,
            'message'                 => 'tokens refreshed!',
            WIDGET_AUTH_OBJ_VAR_NAME => [WIDGET_AUTH_ACCESS_TOKEN_VAR_NAME => $aResp['token'], WIDGET_AUTH_REFRESH_TOKEN_VAR_NAME => $rResp['token']],
            'http_code'               => REST_Controller_Codes::HTTP_OK
        ], 0, REST_Controller_Codes::HTTP_OK);

        return;
    }

    public function is_logged(){
        $input_json = @file_get_contents('php://input');
        $input      = json_decode($input_json);

        try {
            $user_id    = $this->api_session_model->getValue($this->session_id,'user_id');
            $church_id  = $input->org_id;
            $campus_id  = null; // suborg_id is not implemented yet

            $data = null;
            if($user_id){
                $this->load->model('donor_model');
                $user = $this->donor_model->is_logged($user_id,$church_id,$campus_id);

                $data = [
                    'email' => $user->email,
                    'name'  => $user->first_name . ($user->last_name ? ' ' .$user->last_name : '')
                ];
            }
            output_json_api(['status' => $user_id ? true : false , 'data' => $data], 0, REST_Controller_Codes::HTTP_OK);
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }

    public function get_user()
    {
        $input_json = @file_get_contents('php://input');
        $input      = json_decode($input_json);
        try {

            $church_id  = $input->org_id;
            $campus_id  = null; // suborg_id is not implemented yet
            $user_id = $this->api_session_model->getValue($this->session_id,'user_id');

            $this->load->model('donor_model');
            $user = $this->donor_model->getBySessionId($user_id,$church_id,$campus_id);
            if($user) {
                output_json_api(['status' => true, 'user' => $user], 0, REST_Controller_Codes::HTTP_OK);
            }
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }
}
