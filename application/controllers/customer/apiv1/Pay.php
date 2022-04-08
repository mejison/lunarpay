<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pay extends CI_Controller {

    private $session_id = null;

    private $mapPayOpts = ['credit_card' => 'CC', 'bank_account' => 'BANK', 'eth' => 'ETH'];

    public function __construct() {

        parent::__construct();

        $this->load->model('api_session_model');
        $this->load->library('widget_api_202107');

        $action = $this->router->method;

        /* ------- NO ACCESS_TOKEN METHODS ------- */
        $free = ['invoice', 'payment_link']; //method some times needs token validation
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
    
   /* public function update_csrf()
    {
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        echo json_encode($data);
    }maybe we will need this later*/

    //post
    public function invoice($hash = 0) {

        try {
            $input_json = @file_get_contents('php://input');
            $input      = json_decode($input_json);
            
            $this->load->model('invoice_model');
            $this->invoice_model->valAsArray = true;

            $invoice            = $this->invoice_model->getByHash($hash);
            
            if (empty($invoice)) {
                output_json_api(['errors' => [langx('Invoice not found')]], 1, REST_Controller_Codes::HTTP_OK);
                return;
            }
            
            if ($invoice->status == Invoice_model::INVOICE_PAID_STATUS) {
                output_json_api(['errors' => [langx('Invoice already paid')]], 1, REST_Controller_Codes::HTTP_OK);
                return;
            }
            
            $mappedPaymentMethod = isset($this->mapPayOpts[$input->payment_method]) ? $this->mapPayOpts[$input->payment_method] : null;
            $payment_options = json_decode($invoice->payment_options);                        
            if(!in_array($mappedPaymentMethod, $payment_options)) {
                output_json_api(['errors' => [langx('Invalid request, payment method unavailable')]], 1, REST_Controller_Codes::HTTP_OK);
                return;
            }
            
            $donorId            = $invoice->donor_id;
            $request            = new stdClass();
            $request->screen    = 'public-invoice';
            $request->church_id = $invoice->church_id;
            $request->campus_id = $invoice->campus_id;
            $request->amount    = $invoice->total_amount + $invoice->fee;
            $request->invoice   = $invoice;
            
            $input->data_payment->is_cover_fee = $invoice->cover_fee ? 'yes' : 'no';

            $church_id = $invoice->church_id;
            $campus_id = $invoice->campus_id;

            $packResult = $this->buildPaymentPackage($input, $request, $church_id, $campus_id, $donorId);
           
            if (isset($packResult['error']) && $packResult['error']) {
                output_json_api(['errors' => [$packResult['message']]], 1, $packResult['http_code']);
                return;
            }

            require_once 'application/controllers/extensions/Payments.php';
            $pResult = Payments::process($packResult['request'], $packResult['payment'], $donorId);


            $error = 0;
            if($pResult['status'] === false) {
                $error = 1;
                $pResult['errors'] = [$pResult['message']];
                unset($pResult['message']);
            } else {
                $invoiceUpdated     = $this->invoice_model->getByHash($hash); 
                $pResult['invoice'] = $invoiceUpdated; //return an updated invoice
            }
            output_json_api($pResult, $error, REST_Controller_Codes::HTTP_OK); 
        } catch (Exception $ex) {

            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }
    
    public function payment_link($hash = 0) {

        try {
            $input_json = @file_get_contents('php://input');
            $input      = json_decode($input_json);
            
            $this->load->model('payment_link_model');
            $this->payment_link_model->valAsArray = true;
            
            $paymentLink = $this->payment_link_model->getByHash($hash);
            
            if (empty($paymentLink)) {
                output_json_api(['errors' => [langx('Link not found')]], 1, REST_Controller_Codes::HTTP_OK);
                return;
            }

            if (!filter_var($input->payment_method, FILTER_VALIDATE_INT)) { //when a payment is done with a source (cc, bank, crypto) input->payment_method comes with a string
                $mappedPaymentMethod = isset($this->mapPayOpts[$input->payment_method]) ? $this->mapPayOpts[$input->payment_method] : null;
                $payment_options = json_decode($paymentLink->payment_methods); //verifyx payment_methods <= rename to => payment_options
                if(!in_array($mappedPaymentMethod, $payment_options)) {
                    output_json_api(['errors' => [langx('Invalid request, payment method unavailable')]], 1, REST_Controller_Codes::HTTP_OK);
                    return;
                }
            } else { //when a payment is done using a wallet $input->payment_method comes with an integer
                $result = $this->widget_api_202107->validaAccessToken();
                $this->session_id = $result['current_access_token'];
                $input->username = $this->api_session_model->getValue($this->session_id,'identity');
            }

            if(!filter_var($input->username,FILTER_VALIDATE_EMAIL)){
                output_json_api(['errors' => [langx('Invalid request, email is required')]], 1, REST_Controller_Codes::HTTP_OK);
                return;
            }
            
            $this->load->model('donor_model');
            $this->donor_model->valAsArray = true; //get validation errors as array, not a string            
            $customerAcc                   = $this->donor_model->getLoginData($input->username, $paymentLink->church_id);

            $isAnonymous = false;
            if (!$customerAcc) {                
                $isAnonymous = true;
            }

            $donorId              = $customerAcc ? $customerAcc->id : null;
            $request              = new stdClass();
            $request->screen      = 'public-link';
            $request->church_id   = $paymentLink->church_id;
            $request->campus_id   = $paymentLink->campus_id;
            $request->paymentLink = $paymentLink;
            
            $reqProducts = $input->products;
                      
            $this->load->helper('payment_links');            
            PL_checkProductsIntegrity($paymentLink, $reqProducts); //check the customer send safe product data
            
            //just calculate the total amount using the quantities provided by the customer and fusion products data comming from the request with the data in the database
            $productsWithRequest = PL_recalcProductsWithRequest($reqProducts); 
            
            $request->amount = $productsWithRequest['totalAmount'];
            
            $request->paymentLink->_products = $productsWithRequest['_products'];

            $input->data_payment->country = $request->paymentLink->organization->region;
            
            $packResult = $this->buildPaymentPackage($input, $request, $paymentLink->church_id, $paymentLink->campus_id, $donorId);

            if (isset($packResult['error']) && $packResult['error']) {
                output_json_api(['errors' => [$packResult['message']]], 1, $packResult['http_code']);
                return;
            }

            if($isAnonymous){
                $donorId = $input->username;
            } else {
                $packResult['payment']->save_source = 'Y';
            }
           
            require_once 'application/controllers/extensions/Payments.php';
            $pResult = Payments::process($packResult['request'], $packResult['payment'], $donorId, $isAnonymous);

            $error = 0;
            if($pResult['status'] === false) {
                $error = 1;
                $pResult['errors'] = [$pResult['message']];
                unset($pResult['message']);
            } else {
                $paymentLinkUpdated     = $this->payment_link_model->getByHash($hash, $pResult['trxn_id']);
                $pResult['payment_link'] = $paymentLinkUpdated; //return an updated paymentLink
            }
            output_json_api($pResult, $error, REST_Controller_Codes::HTTP_OK);
            
        } catch (Exception $ex) {
            output_json_api(['errors' => [$ex->getMessage()]], 1, REST_Controller_Codes::HTTP_BAD_REQUEST);
        }
    }

    private function buildPaymentPackage($input, $request, $church_id, $campus_id, $donorId) {

        $data_payment = (array) $input->data_payment;

        //though lunarPay does not use funds, the architecture and current payment process requires at least one, we load the org's default one
        $this->load->model('fund_model');
        $campus_id = $campus_id ? $campus_id : null;
        $mainfund           = $this->fund_model->getFirstOrgFund($church_id, $campus_id);
        $request->fund_data = [['fund_id' => $mainfund->id, 'fund_amount' => $request->amount]];

        //setting payment method it can be card, bank | or a wallet
        if (array_key_exists($input->payment_method, $this->mapPayOpts)) { 
            //that's okay continue ...             
            $request->payment_method = $input->payment_method;
        } elseif (filter_var($input->payment_method, FILTER_VALIDATE_INT) !== true) {
            $wallet_id               = $input->payment_method;
            $request->payment_method = 'wallet';
        } else {
            return ['error' => 1, 'message' => 'bad request', 'http_code' => REST_Controller_Codes::HTTP_BAD_REQUEST];
        }

        // -----------

        $bank_type = null;

        if (isset($data_payment['bank_type']) && $data_payment['bank_type']) {
            $bank_type = $data_payment['bank_type'];
        } elseif (isset($wallet_id)) {
            $this->load->model('sources_model');
            $src       = $this->sources_model->getOne($donorId, $wallet_id, ['id', 'bank_type'], true);
            $bank_type = $src->bank_type;
        }

        $payment            = new stdClass();
        $payment->bank_type = $bank_type;

        $save_source = null;
        if ($bank_type == 'sepa') {
            $save_source = 'Y'; // if sepa used as payment method, source saving is mandatory | bacs is not included as it is used with a token only
        } else {
            $save_source = isset($save_source) && strtolower($save_source) == '1' ? 'Y' : 'N';
        }

        // ---------

        $request->recurring = isset($data_payment['recurring']) ? $data_payment['recurring'] : 'one_time';

        if ($request->recurring == 'one_time') {
            $request->recurring = 'one_time';
        } elseif ($request->recurring == 'weekly') {
            $request->recurring = 'week';
        } elseif ($request->recurring == 'quarterly') {
            $request->recurring = 'quarterly';
        } elseif ($request->recurring == 'monthly') {
            $request->recurring = 'month';
        } elseif ($request->recurring == 'yearly') {
            $request->recurring = 'year';
        }

        if ($request->recurring != 'one_time') {
            $request->recurring_date = $data_payment['is_recurring_today'] ? date('Y-m-d') : $data_payment['recurrent_date'];
        }

        if ($request->payment_method != 'wallet') {
            
            $payment->first_name  = ucfirst(strtolower(isset($data_payment['first_name']) ? $data_payment['first_name'] : null));
            $payment->last_name   = ucfirst(strtolower(isset($data_payment['last_name'])) ? $data_payment['last_name'] : null);
            $payment->postal_code = $data_payment['postal_code'];
            $payment->save_source = $save_source;
        }

        if ($request->payment_method == 'credit_card') {
            $payment->single_use_token = $data_payment['single_use_token'];
        } elseif ($request->payment_method == 'bank_account') {
            if ($bank_type) {
                if ($bank_type == 'ach') {
                    $payment->routing_number = $data_payment['routing_number'];
                    $payment->account_number = $data_payment['account_number'];
                    $payment->account_type   = $data_payment['account_type'];
                } elseif ($bank_type == 'eft') {
                    $payment->account_number = $data_payment['account_number'];
                    $payment->transit_number = $data_payment['transit_number'];
                    $payment->institution_id = $data_payment['institution_id'];
                } elseif ($bank_type == 'sepa') {
                    $payment->iban              = $data_payment['iban'];
                    $payment->mandate_reference = $data_payment['mandate'];
                }
            } else {
                $payment->routing_number = $data_payment['routing_number'];
                $payment->account_number = $data_payment['account_number'];
                $payment->account_type   = $data_payment['account_type'];
            }
        } elseif ($request->payment_method == 'eth') {
            //alexey put here special values coming from the front end according with what is needed, they come on data_payment
            $payment->single_use_token = 'token-abc'; //$data_payment['routing_number'];
            $payment->account_number = '123';         //$data_payment['routing_number'];    
            
        } elseif ($request->payment_method == 'wallet') {
            $payment->wallet_id = $wallet_id;
        }

        $payment->street  = isset($data_payment['street']) ? $data_payment['street'] : null;
        $payment->street2 = isset($data_payment['street2']) ? $data_payment['street2'] : null;
        $payment->city    = isset($data_payment['city']) ? $data_payment['city'] : null;
        $payment->country = isset($data_payment['country']) ? $data_payment['country'] : null;

        $payment->cover_fee = isset($data_payment['is_cover_fee']) && strtolower($data_payment['is_cover_fee']) == 'yes' ? 1 : 0;
        
        return ['request' => $request, 'payment' => $payment];
    }

}
