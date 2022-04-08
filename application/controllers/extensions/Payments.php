<?php

require_once 'application/libraries/gateways/PaymentsProvider.php';

class Payments {

    //@userId can be the email when the request is from an anonymous user.
    public static function process($request, $payment, $userId, $isAnonymous = false) {

        $CI = & get_instance();

        $church    = $CI->db->where('ch_id', $request->church_id)->get('church_detail')->row();
        $dash_user = $CI->db->where('id', $church->client_id)->select('id, email, payment_processor')->get('users')->row();
   
        //$isAnonymous = false;

        $paymentData = [
            'amount'             => (int) ((string) ($request->amount * 100)), /* --- bcmul should used here we need to install this on servers (aws and ssdnodes) --- */
            'currency'           => 'usd',
            'method'             => $request->payment_method,
            'transaction_type'   => 'Sale',
            'client_customer_id' => $isAnonymous ? null : $userId,
        ];

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_EPICPAY);

            $PaymentInstance    = PaymentsProvider::getInstance();
            $PaymentInstance->setAgentCredentials($church->epicpay_credentials);
            $processor_template = $church->epicpay_template;

            if (empty($church) || $church->epicpay_credentials == null || $church->epicpay_verification_status != 'V') {
                return ['status' => false, 'message' => 'Payments for this church has not been setup'];
            }
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            
            if ($paymentData['method'] == 'eth') {
                PaymentsProvider::init(PROVIDER_PAYMENT_ETH);
                $PaymentInstance    = PaymentsProvider::getInstance();
                
                //verifyx eth tiers
                $processor_template = $church->paysafe_template;

                //check if this will be used verifyx
                //$paymentData['paysafe_success_trxns'] = isset($payment->paysafe_success_trxns) ? $payment->paysafe_success_trxns : null;
            } else {
                PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
                $PaymentInstance    = PaymentsProvider::getInstance();
                $PaymentInstance->setMainUserId($dash_user->id);
                $processor_template = $church->paysafe_template;

                $paymentData['bank_type'] = $payment->bank_type;

                $paymentData['paysafe_success_trxns'] = isset($payment->paysafe_success_trxns) ? $payment->paysafe_success_trxns : null;
            }
        }

        $donor_account = null;
        if($isAnonymous){
            $donor_account = new stdClass();
            $donor_account->email      = $userId;
            $donor_account->first_name = $payment->first_name;
            $donor_account->last_name  = $payment->last_name;
        } else {
            $CI->load->model('donor_model');
            $donor_account = $CI->donor_model->get(['id' => $userId], ['email', 'first_name', 'last_name']);

            if (!$donor_account || !$donor_account->email) {
                return ['status' => false, 'message' => 'Email not found'];
            }
        }


        
        $CI->load->model('transaction_fund_model', 'trnx_funds');
        $fund_data = $request->fund_data;
        
        $valTrxnOrgnx = $CI->trnx_funds->validateTransactionFundsBelongToOrgnx($fund_data, $request->church_id, $request->campus_id);
        
        if($valTrxnOrgnx['error']) {
            return ['status' => false, 'message' => $valTrxnOrgnx['message']];
        }

        $userEmail = $donor_account->email;

        if (in_array($request->church_id, TEST_ORGNX_IDS)) {
            $PaymentInstance->setTesting(true);
        }

        if ($paymentData['method'] == 'wallet') {
            $payment->first_name  = $donor_account->first_name;
            $payment->last_name   = $donor_account->last_name;
            $payment->postal_code = '-';
        }
        $customerData = [
            'church_id'        => $request->church_id,
            'account_donor_id' => !$isAnonymous ? $userId : null,
            'customer_address' => [
                'email'       => $userEmail,
                'first_name'  => $payment->first_name,
                'last_name'   => $payment->last_name,
                'postal_code' => $payment->postal_code,
            ],
            'billing_address'  => [
                'email'       => $userEmail,
                'first_name'  => $payment->first_name,
                'last_name'   => $payment->last_name,
                'postal_code' => $payment->postal_code,
            ],
        ];

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            $customerData['paysafe_billing_address'] = [
                'street'  => isset($payment->street) ? $payment->street : null,
                'street2' => isset($payment->street2) ? $payment->street2 : null,
                'city'    => isset($payment->city) ? $payment->city : null,
                'country' => isset($payment->country) ? $payment->country : null,
                'zip'     => isset($payment->postal_code) ? $payment->postal_code : null
            ];
        }

        $transactionData = [
            'customer_id'              => 0,
            'customer_source_id'       => 0,
            'church_id'                => $request->church_id,
            'campus_id'                => ( (isset($request->campus_id) && $request->campus_id) ? $request->campus_id : null ),
            'account_donor_id'         => !$isAnonymous ? $userId : null,
            'total_amount'             => $request->amount,
            'first_name'               => $donor_account->first_name,
            'last_name'                => $donor_account->last_name,
            'email'                    => $userEmail,
            'zip'                      => $payment->postal_code,
            'giving_source'            => $request->screen,
            //'giving_type'        => $request->fund_id,
            'template'                 => $processor_template,
            'is_fee_covered'           => $payment->cover_fee,
            'campaign_id'              => isset($request->campaign_id) && $request->campaign_id ? $request->campaign_id : null,
            'customer_subscription_id' => isset($request->from_subscription_id) && $request->from_subscription_id ? $request->from_subscription_id : null,
            'invoice_id'               => isset($request->invoice) && $request->invoice ? $request->invoice->id : null,
            'payment_link_id'          => isset($request->paymentLink) && $request->paymentLink ? $request->paymentLink->id : null
        ];

        if ($request->screen == "events") {

            if ($request->event->attendees) {
                $transactionData["event_data"] = json_encode($request->event);
            } else {
                if (!$request->event->attendeeOption) {
                    unset($request->event->attendeeOption);
                }
                $transactionData["event_data"] = json_encode($request->event);
            }
        }

        if (isset($request->extra_data)) {
            $transactionData['batch_id']         = $request->extra_data->batch_id;
            $transactionData['batch_method']     = $request->extra_data->batch_method;
            $transactionData['batch_committed']  = $request->extra_data->batch_committed;
            $transactionData['batch_extra_data'] = $request->extra_data->batch_extra_data;
            $transactionData['tags']             = $request->extra_data->tags;
        }

        $walletInfo = null;

        if ($paymentData['method'] == 'credit_card') {

            $transactionData['src'] = "CC";

            if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {

                $paymentData['credit_card'] = [
                    'single_use_token' => $payment->single_use_token,
                    'card_holder_name' => $payment->first_name . ' ' . $payment->last_name,
                ];
            } else {
                $exp = explode('/', $payment->card_date);

                if (strlen($exp[0]) == 1) {
                    $exp[0] = "0" . $exp[0];
                }
                if (strlen($exp[1]) == 4) {
                    $exp[1] = substr($exp[1], -2);
                }

                $paymentData['credit_card'] = [
                    'card_number'      => $payment->card_number,
                    'card_holder_name' => $payment->first_name . ' ' . $payment->last_name,
                    'exp_month'        => $exp[0],
                    'exp_year'         => $exp[1],
                    'cvv'              => $payment->card_cvv,
                ];
            }
        } else if ($paymentData['method'] == 'wallet') {
            $walletId     = $payment->wallet_id;
            $walletInfo   = $CI->db->where(['id' => $walletId, 'church_id' => $request->church_id, 'account_donor_id' => $userId])->get('epicpay_customer_sources')->row();
                        
            $paymentData['wallet'] = [
                'wallet_id'             => $walletInfo->epicpay_wallet_id,
                'postal_code'           => $walletInfo->postal_code,
                'processor_customer_id' => $walletInfo->epicpay_customer_id
            ];

            if ($walletInfo) {
                if ($walletInfo->source_type == 'card') {
                    $transactionData['src'] = "CC";
                    if (isset($request->wallet_update) && $request->wallet_update->execute == "1") {
                        $updWalletResp = $this->updateWallet($request, $userEmail, $walletInfo);
                        if ($updWalletResp["error"] === "validation_error") {
                            return ['status' => 'validation_error', 'reason' => $updWalletResp["message"]];
                        }
                    }
                } else if ($walletInfo->source_type == 'bank') {

                    $transactionData['src'] = "BNK";

                    $paymentData['sec_code'] = 'WEB';
                } else if ($walletInfo->source_type == 'ETH') {
                    $transactionData['src'] = "ETH";
                }

                $transactionData['customer_id']        = $walletInfo->customer_id;
                $transactionData['customer_source_id'] = $walletInfo->id;
            }
        } else if ($paymentData['method'] == 'bank_account') {

            $transactionData['src'] = "BNK";

            $paymentData['method']   = 'echeck';
            $paymentData['sec_code'] = 'WEB';

            if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
                $paymentData['bank_account'] = [
                    'account_type'        => $payment->account_type,
                    'routing_number'      => $payment->routing_number,
                    'account_number'      => $payment->account_number,
                    'account_holder_name' => $payment->first_name . ' ' . $payment->last_name,
                ];
            } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
                if ($paymentData['bank_type'] == 'ach') {
                    $paymentData['bank_account'] = [
                        'account_type'        => $payment->account_type,
                        'routing_number'      => $payment->routing_number,
                        'account_number'      => $payment->account_number,
                        'account_holder_name' => $payment->first_name . ' ' . $payment->last_name,
                    ];
                } elseif ($paymentData['bank_type'] == 'eft') {
                    $paymentData['bank_account'] = [
                        'account_number'      => $payment->account_number,
                        'transit_number'      => $payment->transit_number,
                        'institution_id'      => $payment->institution_id,
                        'account_holder_name' => $payment->first_name . ' ' . $payment->last_name,
                    ];
                } elseif ($paymentData['bank_type'] == 'sepa') {
                    $paymentData['bank_account'] = [
                        'iban'                => $payment->iban,
                        'mandate_reference'   => $payment->mandate_reference,
                        'account_holder_name' => $payment->first_name . ' ' . $payment->last_name,
                    ];
                }
            }
        } else if ($paymentData['method'] == 'eth') {
            $transactionData['src'] = "ETH";
            
            //alexey
            //adjust with values the ETH payment needs
            $paymentData['eth'] = [
                'single_use_token'    => $payment->single_use_token,
                'account_number'      => $payment->account_number,                
                'account_holder_name' => $payment->first_name . ' ' . $payment->last_name,
            ];
        }

        if ($paymentData['method'] != 'wallet') {

            if ($payment->save_source == 'Y' || $request->recurring != 'one_time') { //verifyx if this change affects epicpay
                $customerData['is_saved'] = 'Y';
            } else {
                $customerData['is_saved'] = 'N';
            }

            //For epicpay try to create customer and source always. For paysafe only when is_saved = Y
            if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT || $customerData['is_saved'] == 'Y') {

                $customerInfo = $PaymentInstance->createCustomer($customerData, $paymentData);

                if ($customerInfo['error'] == 1) {
                    return ['status' => false, 'message' => 'Could not create profile/source | ' . $customerInfo['message']];
                }

                if (isset($customerInfo['source'])) {
                    $transactionData['customer_id']        = $customerInfo['customer']['id'];
                    $transactionData['customer_source_id'] = $customerInfo['source']['id'];

                    $paymentData['wallet'] = [
                        'wallet_id'             => $customerInfo['source']['epicpay_id'],
                        'processor_customer_id' => $customerInfo['customer']['epicpay_id']
                    ];

                    if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
                        $paymentData['wallet']['postal_code'] = $customerInfo['source']['postal_code'];
                    }

                    $paymentData['method'] = 'wallet';
                    unset($paymentData['credit_card']);
                    unset($paymentData['bank_account']);
                }
            }
        }
        
        $trx                 = new stdClass();
        $trx->total_amount   = $request->amount;
        $trx->template       = $processor_template;
        $trx->src            = $transactionData['src'];
        $productsWithRequest = null;

        if(isset($transactionData['payment_link_id'])) {
            $productsWithRequest = $request->paymentLink->_products;
        }        
        
        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            $transactionData['fee'] = getEpicPayFee($trx);
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            
            $transactionData['fee'] = getPaySafeFee($trx);
            
            if($paymentData['method'] == 'eth') {
                //$transactionData['fee'] = getEthFee($trx);
            }
            
            $fund_data = $payment->cover_fee ?  setMultiFundDistrFeeCovered($fund_data, $transactionData) :
                    setMultiFundDistrFeeNotCovered($fund_data, $transactionData);
        }
        
        $transactionData['sub_total_amount'] = $transactionData['total_amount'] - $transactionData['fee'];
        
        if ($request->recurring == 'one_time') {
            
            $result = $PaymentInstance->createTransaction($transactionData, $customerData, $paymentData, $fund_data, $productsWithRequest,$isAnonymous);
           

            if ($result['error'] == 1) {
                return ['status' => false, 'message' => $result['message']];
            }

            $transactionData["trxId"] = $result["trxId"];
            $CI->load->helper('emails');
            
            if(isset($transactionData['invoice_id']) && $transactionData['invoice_id']) {               
                $CI->load->model('invoice_model');
               
                //verfix rebuild getbyhash and getbyid use just one method, check this for invoices too
                //we reload the invoice for getting transactions object, we need them when sending the email                
                $invoice = $CI->invoice_model->getById($transactionData['invoice_id'], $dash_user->id);
                   
                $CI->invoice_model->markInvoiceAs($transactionData['invoice_id'], Invoice_model::INVOICE_PAID_STATUS);
                
                $invoice->datePaid = date("F j, Y");
                $invoice->TransactionId = $transactionData["trxId"];
                sendInvoiceEmail($invoice,'paid');

                $invoice->user_to = $dash_user->email;
                sendPaymentNotificationToAdmin('invoice', $invoice);
            } else if(isset($transactionData['payment_link_id']) && $transactionData['payment_link_id']) {
                $CI->load->model('payment_link_model');
                
                //we reload the $paymentLink for getting transactions object and add some other data needed from request, we need them when sending the email                                
                $paymentLink = $CI->payment_link_model->getByHash($request->paymentLink->hash, $includeTrxnId = $transactionData["trxId"]);
                
                $paymentLink->_customer = $donor_account; //customer is not part of the paymentLink, we added it here for the email purposes only
                $paymentLink->_total_amount = $transactionData['total_amount'];
                $paymentLink->_date_paid = date("F j, Y");
                $paymentLink->_transaction_id = $transactionData["trxId"];
                
                sendPaymentLinkEmail($paymentLink);
                //sendPaymentNotificationToAdmin('paymentLink', $request->paymentLink);
            
            } else {  
                sendDonationEmail($transactionData, false, $fund_data);
            }
            
            return ['status' => true, 'message' => 'Payment Processed!', 'trxn_id' => $result["trxId"]];
        } else {

            if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
                $frequency = $PaymentInstance->getFrequency($request->recurring);

                $paymentData['cb_frequency'] = $request->recurring;
                $paymentData['frequency']    = $frequency['frequency'];
                $paymentData['period']       = $frequency['period'];
            } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
                $paymentData['frequency'] = $request->recurring;
            }

            $nextPaymentDate                  = date('Y-m-d', strtotime($request->recurring_date));
            $paymentData['next_payment_date'] = $nextPaymentDate;

            $result = $PaymentInstance->createSubscription($transactionData, $customerData, $paymentData, $fund_data);

            if ($result['error'] == 1) {
                return ['status' => false, 'message' => $result['message']];
            }
            return ['status' => true, 'message' => 'Payment scheduled!'];
        }
    }
    
    public static function refund($transaction_id, $user_id) {
        $result = checkBelongsToUser([
            ['epicpay_customer_transactions.id' => $transaction_id, 'church_id', 'church_detail.ch_id'],
            ['church_detail.ch_id' => '?', 'client_id', 'users.id', $user_id],
        ]);

        if ($result !== true) {
            return $result;
        }

        $CI = & get_instance();

        ////////////
        $dash_user = $CI->db->where('id', $user_id)->select('id, email, payment_processor')->get('users')->row();

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_EPICPAY);
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
        }

        $PaymentInstance = PaymentsProvider::getInstance();

        ///////////

        $trnx = $CI->db->select('church_id')->where('id', $transaction_id)->get('epicpay_customer_transactions')->row();
        if (in_array($trnx->church_id, TEST_ORGNX_IDS)) {
            $PaymentInstance->setTesting(true);
        }
        $result = $PaymentInstance->refundTransaction($transaction_id);

        if ($result['error'] == 1) {
            return ['status' => false, 'message' => $result['message']];
        }

        return ['status' => true, 'message' => 'Refund successfully processed'];
    }

    //for now we can move the transaction from success to failed not viceversa
    public static function toggle_bank_trxn_status($transaction_id, $user_id) {

        $result = checkBelongsToUser([
            ['epicpay_customer_transactions.id' => $transaction_id, 'church_id', 'church_detail.ch_id'],
            ['church_detail.ch_id' => '?', 'client_id', 'users.id', $user_id],
        ]);

        if ($result !== true) {
            return $result;
        }

        $CI = & get_instance();

        ////////////
        $dash_user = $CI->db->where('id', $user_id)->select('id, email, payment_processor')->get('users')->row();

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_EPICPAY);
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
        }

        $PaymentInstance = PaymentsProvider::getInstance();

        ///////////

        $trnx = $CI->db->select('church_id')->where('id', $transaction_id)->get('epicpay_customer_transactions')->row();
        if (in_array($trnx->church_id, TEST_ORGNX_IDS)) {
            $PaymentInstance->setTesting(true);
        }
        $result = $PaymentInstance->setAsFailed($transaction_id);

        if ($result['error'] == 1) {
            return ['status' => false, 'message' => $result['message']];
        }

        return ['status' => true, 'message' => 'Status successfully processed'];
    }

    public static function stopSubscription($subscription_id, $user_id = false, $donor_id = false) {

        $CI = & get_instance();

        if ($user_id) {
            $dash_user = $CI->db->where('id', $user_id)->select('id, email, payment_processor')->get('users')->row();
        } elseif ($donor_id) {
            $donor     = $CI->db->select('id_church')->where('id', $donor_id)->get('account_donor')->row();
            $church    = $CI->db->where('ch_id', $donor->id_church)->get('church_detail')->row();
            $dash_user = $CI->db->where('id', $church->client_id)->select('id, email, payment_processor')->get('users')->row();
        } else {
            return ['status' => false, 'message' => 'Bad request'];
        }

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_EPICPAY);
            $PaymentInstance = PaymentsProvider::getInstance();
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
            $PaymentInstance = PaymentsProvider::getInstance();
        }

        $result = $PaymentInstance->stopCustomerSubscription($subscription_id, $user_id, $donor_id);

        if ($result['error'] == 1) {
            return ['status' => false, 'message' => $result['message']];
        }

        return ['status' => true, 'message' => 'Subscription canceled'];
    }

    public static function addPaymentSource($request) {

        require_once 'application/controllers/extensions/Payments/SourceDataBuilder.php';

        $CI        = & get_instance();
        $church    = $CI->db->where('ch_id', $request->church_id)->get('church_detail')->row();
        $dash_user = $CI->db->where('id', $church->client_id)->select('id, email, payment_processor')->get('users')->row();

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_EPICPAY);

            $PaymentInstance = PaymentsProvider::getInstance();
            $PaymentInstance->setAgentCredentials($church->epicpay_credentials);

            if (empty($church) || $church->epicpay_credentials == null || $church->epicpay_verification_status != 'V') {
                return ['status' => false, 'message' => 'Payments for this church has not been setup'];
            }

            $data = \Payments\SourceDataBuilder::epicpay($request);
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
            $PaymentInstance = PaymentsProvider::getInstance();
            $data            = \Payments\SourceDataBuilder::paysafe($request);
            $PaymentInstance->setMainUserId($dash_user->id);
        }

        if (in_array($request->church_id, TEST_ORGNX_IDS))
            $PaymentInstance->setTesting(true);

        $result = $PaymentInstance->createCustomer($data['customerData'], $data['paymentData']);

        if ($result['error'] == 0) {
            return ['status' => true, 'message' => 'Payment source added'];
        } else {
            return ['status' => false, 'message' => $result['message']];
        }
    }

//------ donor_id must come safe
    public static function removePaymentSource($source_id, $donor_id) {

        $CI     = & get_instance();
        $source = $CI->db->where('id', $source_id)->where('account_donor_id', $donor_id)->get('epicpay_customer_sources')->row();

        if (!$source) {
            return ['status' => false, 'message' => 'An error ocurred, no source found'];
        }

        $church    = $CI->db->where('ch_id', $source->church_id)->get('church_detail')->row();
        $dash_user = $CI->db->where('id', $church->client_id)->select('id, email, payment_processor')->get('users')->row();

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_EPICPAY);

            $PaymentInstance = PaymentsProvider::getInstance();
            $PaymentInstance->setAgentCredentials($church->epicpay_credentials);

            if (empty($church) || $church->epicpay_credentials == null || $church->epicpay_verification_status != 'V') {
                return ['status' => false, 'message' => 'Payments for this church has not been setup'];
            }
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
            $PaymentInstance = PaymentsProvider::getInstance();
        }

        if (in_array($source->church_id, TEST_ORGNX_IDS)) {
            $PaymentInstance->setTesting(true);
        }

        $result = $PaymentInstance->deleteCustomerSource($source_id, $donor_id);

        if ($result['error'] == 0) {
            return ['status' => true, 'message' => 'Payment source removed'];
        } else {
            return ['status' => false, 'message' => $result['message']];
        }
    }

    public static function getPayouts($church_id, $user_id, $requestBody) {

        $result = checkBelongsToUser([['church_detail.ch_id' => $church_id, 'client_id', 'users.id', $user_id]]);

        if ($result !== true) {
            return $result;
        }

        PaymentsProvider::init();
        $PaymentInstance = PaymentsProvider::getInstance();

        $data = $PaymentInstance->depositsDetailReport($church_id, $requestBody);

        return $data;
    }

    private static function formatExpDate($exp_date, $dash_user) {
        $exp = explode('/', $exp_date);

        if (count($exp) !== 2) {
            return['status' => false, 'message' => 'Invalid Expiration Date'];
        }

        if (strlen($exp[0]) == 1) {
            $exp[0] = "0" . $exp[0];
        }

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            if (strlen($exp[1]) == 4) {
                $exp[1] = substr($exp[1], -2);
                if (!isset($exp[0]) || !$exp[0] || strlen($exp[0]) != 2) {
                    return['status' => false, 'message' => 'Invalid Expiration Date'];
                }
            }
        } elseif ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            if (!isset($exp[1]) || strlen($exp[1]) != 2) {
                return['status' => false, 'message' => 'Invalid Expiration Date'];
            }
        }

        return $exp;
    }

    public static function update_expiration_date($source_id, $postal_code, $exp_date, $holder_name = null, $street = null, $street2 = null, $city = null, $country = null) {
        $CI = & get_instance();
        $CI->load->model('donor_model');

        $source = $CI->db->where('id', $source_id)
                        ->where('is_active', 'Y')->where('is_saved', 'Y')
                        ->where_in('status', ['P', 'U'])
                        ->get('epicpay_customer_sources')->row();

        if (!$source) {
            return ['status' => false, 'message' => 'Source not found'];
        }

        $church    = $CI->db->where('ch_id', $source->church_id)->get('church_detail')->row();
        $dash_user = $CI->db->where('id', $church->client_id)->select('id, email, payment_processor')->get('users')->row();

        $result = PAYMENTS::formatExpDate($exp_date, $dash_user);

        if (isset($result['status']) && $result['status'] == false) {
            return $result;
        }

        $exp = $result;

        $walletdata = [];

        if ($dash_user->payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            PaymentsProvider::init();
            //if (!isset($postal_code) || !$postal_code) {
            //return ['status' => false, 'message' => 'Postal Code is required'];
            //}

            $walletdata["exp_month"] = $exp[0];
            $walletdata["exp_year"]  = $exp[1];

            $account_donor = $CI->db->where('id', $source->account_donor_id)->get('account_donor')->row();

            $names = explode(' ', $source->name_holder);
            $fname = isset($names[0]) && $names[0] ? $names[0] : null;
            $lname = isset($names[1]) && $names[1] ? $names[1] : null;

            $walletdata["account_holder_name"]            = $source->name_holder;
            $walletdata["billing_address"]["first_name"]  = $fname;
            $walletdata["billing_address"]["last_name"]   = $lname;
            $walletdata["billing_address"]["postal_code"] = $postal_code;
            $walletdata["billing_address"]["email"]       = $account_donor->email;
        } else if ($dash_user->payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);

            $walletdata["holderName"]                = $holder_name;
            $walletdata['cardExpiry']['month']       = $exp[0];
            $walletdata['cardExpiry']['year']        = $exp[1];
            $walletdata['billingAddress']["zip"]     = $postal_code;
            $walletdata['billingAddress']["street"]  = $street;
            $walletdata['billingAddress']["street2"] = $street2;
            $walletdata['billingAddress']["city"]    = $city;
            $walletdata['billingAddress']["country"] = $country;
        }

        $PaymentInstance = PaymentsProvider::getInstance();

        if (in_array($source->church_id, TEST_ORGNX_IDS)) {
            $PaymentInstance->setTesting(true);
        }

        $response = $PaymentInstance->processUpdateWallet($source, $walletdata);

        return [
            'status'  => $response['error'] ? false : true,
            'message' => $response['error'] ? $response['message'] : 'Source successfully updated'];
    }

    //church_id sent for determining which payment provider to use
    public static function getSingleUseTokenEncodedApiKey($payment_processor, $church_id) {

        if ($payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            return [
                'status' => false
            ];
        } else if ($payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
            $PaymentInstance = PaymentsProvider::getInstance();

            if (in_array($church_id, TEST_ORGNX_IDS)) {
                $PaymentInstance->setTesting(true);
            }

            return [
                'status'           => true,
                'single_use_token_api_key' => $PaymentInstance->getSingleUseTokenEncodedApiKey()
            ];
        }
    }
    
    //church_id sent for determining which payment provider to use
    public static function getEnvironment($payment_processor, $church_id) {

        if ($payment_processor === PROVIDER_PAYMENT_EPICPAY_SHORT) {
            return [
                'status' => false
            ];
        } else if ($payment_processor === PROVIDER_PAYMENT_PAYSAFE_SHORT) {
            PaymentsProvider::init(PROVIDER_PAYMENT_PAYSAFE);
            $PaymentInstance = PaymentsProvider::getInstance();

            if (in_array($church_id, TEST_ORGNX_IDS)) {
                $PaymentInstance->setTesting(true);
            }

            return [
                'status'           => true,
                'envTest' => $PaymentInstance->getTesting()
            ];
        }
    }

}
