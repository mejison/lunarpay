<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Paysafecron extends CI_Controller {

    function __construct() {
        parent::__construct();
        display_errors();
    }

    public function process_recurrent_transactions() {

        require_once 'application/controllers/extensions/Payments.php';

        $today = date('Y-m-d');
        //$today = '2021-04-26';

        $attemps_before_sub_suspension = 4; //-----when never has been a success payment
        $subs                          = $this->db->query(''
                        . 'SELECT * FROM epicpay_customer_subscriptions '
                        . 'WHERE TRUE '
                        . 'AND ispaysafe = 1 '
                        . 'AND status = "A" '
                        . "AND next_payment_on = '$today' "
                        . '')->result();

        $summary['count'] = count($subs);
        foreach ($subs as $sub) {

            $frequency = $sub->frequency;

            if ($frequency == 'week') {
                $new_next_payment = date('Y-m-d', strtotime("+1 week " . $today));
            } elseif ($frequency == 'month') {
                $new_next_payment = date('Y-m-d', strtotime("+1 month " . $today));
            } elseif ($frequency == 'quarterly') {
                $new_next_payment = date('Y-m-d', strtotime("+3 month " . $today));
            } elseif ($frequency == 'year') {
                $new_next_payment = date('Y-m-d', strtotime("+1 year " . $today));
            } else {
                log_message('error', '_INFO_LOG paysafecron process_recurrent_transactions frequency error: ' . $frequency);
                continue;
            }

            $save_data                    = [];
            $save_data['next_payment_on'] = $new_next_payment;

            $request = new stdClass();

            $request->church_id      = $sub->church_id;
            $request->campus_id      = $sub->campus_id;
            $request->amount         = $sub->amount;
            $request->payment_method = 'wallet';
            $request->screen         = $sub->giving_source;
            $request->recurring      = 'one_time';

            ///////// Build the fund_data setup
            $this->load->model('transaction_fund_model', 'trnx_funds');
            $trnx_funds = $this->trnx_funds->getBySubscription($sub->id);

            $fund_data = [];
            if ($sub->is_fee_covered) {
                foreach ($trnx_funds as $tfund)
                    $fund_data [] = ['fund_id' => $tfund['fund_id'], 'fund_amount' => $tfund['net']];
            } else {
                foreach ($trnx_funds as $tfund)
                    $fund_data [] = ['fund_id' => $tfund['fund_id'], 'fund_amount' => $tfund['amount']];
            }

            $request->fund_data = $fund_data;

            $request->from_subscription_id = $sub->id;

            $payment                        = new stdClass();
            $payment->cover_fee             = $sub->is_fee_covered;
            $payment->wallet_id             = $sub->customer_source_id;
            $payment->paysafe_success_trxns = $sub->paysafe_success_trxns;

            $donorId = $sub->account_donor_id;

            $this->load->model('sources_model');
            $src                = $this->sources_model->getOne($donorId, $payment->wallet_id, ['id', 'bank_type'], true);
            $payment->bank_type = $src->bank_type;

            $pResult = Payments::process($request, $payment, $donorId);

            if ($pResult['status']) {
                // -------- success payment
                $save_data['paysafe_success_trxns'] = $sub->paysafe_success_trxns ? ($sub->paysafe_success_trxns + 1) : 1;

                //***** for logs only
                $summary['subs_success'][] = ['request' => $request, 'payment' => $payment, 'donorId' => $donorId];
            } else {
                // -------- fail payment
                $save_data['paysafe_fail_trxns'] = $sub->paysafe_fail_trxns ? ($sub->paysafe_fail_trxns + 1) : 1;

                // -------- if never has been a success payment and attempts are reached suspend subscription
                if (!$sub->paysafe_success_trxns && $save_data['paysafe_fail_trxns'] == $attemps_before_sub_suspension) {
                    $save_data['status'] = 'D';
                    // ------- we can do something here, send an email to the donor or trigger a notification to system administrator
                }

                //***** for logs only
                $summary['subs_failed'][] = ['request' => $request, 'payment' => $payment, 'donorId' => $donorId, 'result' => $pResult];
            }

            $this->db->where('id', $sub->id)->update('epicpay_customer_subscriptions', $save_data);
        }

        $date = date('Y-m-d H:i:s');
        log_message('error', "_INFO_LOG paysafecron process_recurrent_transactions cron log $date : " . json_encode($summary));

        output_json(['summary' => $summary]);
    }

}
