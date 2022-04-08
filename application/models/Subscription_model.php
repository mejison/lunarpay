<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription_model extends CI_Model {

    private $table = 'epicpay_customer_subscriptions';

    public function __construct() {
        parent::__construct();
    }

    public function getDt($user_id = null) {
        
        //Getting Organization Ids
        $user_id = $user_id ? $user_id : $this->session->userdata('user_id');
        
        $user = $this->db->select('payment_processor')->where('id', $user_id)->get('users')->row();

        $organizations_ids = getOrganizationsIds($user_id);

        $subs_data['monthly'] = $this->getSubsTotals($organizations_ids, 'month');
        $subs_data['all']     = $this->getSubsTotals($organizations_ids, 'all');
        $subs_data['count']   = $this->getSubsCount($organizations_ids);

        $this->load->library("Datatables");
        $this->datatables->select("sub.id, sub.amount, ROUND(sum(DISTINCT tf.net), 2) as given, ROUND(sum(DISTINCT tf.fee), 2) as fee, sum(DISTINCT tf.net) as trxs_net, 
                count(DISTINCT trx.id) trxs_count, group_concat(DISTINCT f.name ORDER BY tf2.id ASC SEPARATOR ', ') as fund, 
                group_concat(DISTINCT tf2.net ORDER BY tf2.id ASC SEPARATOR ', ') as tfSubNet, 
                CONCAT_WS(' ',sub.first_name,sub.last_name) as name, sub.email, DATE_FORMAT(sub.start_on, '%m/%d/%Y') as start_on,
                sub.frequency,  
                (CASE 
                    WHEN sub.src = 'CC' then 'Card' 
                    WHEN sub.src = 'BNK' then 'ACH' else '' 
                END) as method, 
                sub.src, DATE_FORMAT(sub.created_at, '%m/%d/%Y') as created_at, 
                sub.start_on substart_on, sub.frequency subfrequency,                 
                (CASE
                    WHEN sub.status = 'A' then 'Active'
                    WHEN sub.status = 'D' then 'Canceled'
                END) as status_text, sub.status
                    ")
                ->from($this->table . ' as sub')
                //->join('account_donor as ad', 'ad.id = sub.account_donor_id', 'LEFT')
                ->join('epicpay_customer_transactions trx', ''
                        . 'trx.customer_subscription_id = sub.id AND'
                        . '((trx.status = "P" AND trx.src = "CC") OR trx.status_ach in ("P") OR (trx.status = "P" AND trx.trx_type = "RE"))', 'LEFT') //rest refunds
                ->join('transactions_funds as tf', 'tf.transaction_id = trx.id', 'LEFT') //used for detailed sum
                ->join('transactions_funds as tf2', 'tf2.subscription_id = sub.id', 'LEFT') // used for reaching fund's names from subscription, if we reach directly from tf we wont get the fund_ids when the subscription does not have transactions
                ->join('funds as f', 'f.id = tf2.fund_id', 'LEFT')
                ->where_in('sub.status', ['A', 'D'])
                ->group_by('sub.id');

        if($user->payment_processor == PROVIDER_PAYMENT_EPICPAY_SHORT){
            $this->load->helper('epicpay_helper');
            $this->datatables->edit_column('frequency', '$1', 'getEpicpayFreqLabel(frequency)');
        }elseif($user->payment_processor == PROVIDER_PAYMENT_PAYSAFE_SHORT){
            $this->load->helper('paysafe_helper');            
            $this->datatables->edit_column('frequency', '$1', 'getPaysafeFreqLabel(frequency)');
        }
        
        //Organizations of User Filter
        $this->datatables->where('sub.church_id in (' . $organizations_ids . ')');

        //Organizations Filter
        $church_id = (int) $this->input->post('organization_id');
        if ($church_id)
            $this->datatables->where('sub.church_id', $church_id);

        //Sub Organizations Filter
        $campus_id = (int) $this->input->post('suborganization_id');
        if ($campus_id)
            $this->datatables->where('sub.campus_id', $campus_id);

        //Funds Filter
        $fund_id = (int) $this->input->post('fund_id');
        if ($fund_id)
            $this->datatables->where('tf2.fund_id = ' . $fund_id);            

        //Method Filter        
        if ($this->input->post('method'))
            $this->datatables->where('sub.src', $this->input->post('method'));

        //Frequency Filter
        $freq = $this->input->post('freq');
        if ($freq)
            $this->datatables->where('sub.frequency', $freq);

        //$data = $this->datatables->generate();
        $data = $this->datatables->generate([
            "subs_data" => $subs_data
        ]);

        return $data;
    }

    private function getSubsTotals($orgnx_ids, $type) {

        $this->db->select("sum(tf.net) as total, max(tr.sub_total_amount) as max_net, max(tf.net) as max_net_fund")
                ->join($this->table . ' as sub', 'sub.id = tr.customer_subscription_id', 'INNER')
                ->join('transactions_funds as tf', 'tf.transaction_id = tr.id', 'INNER')
                ->where('((tr.status = "P" AND tr.src = "CC") OR tr.status_ach in ("P") '
                        . 'OR (tr.status = "P" AND tr.trx_type = "RE"))', null, false); //rest refunds

        if ($type == 'month') {
            $this->db->where('sub.frequency', 'month');
        } elseif ($type == 'all') {
            //==== ALl
        }

        //Organizations of User Filter
        $this->db->where('tr.church_id in (' . $orgnx_ids . ')');

        //Organizations Filter
        $church_id = (int) $this->input->post('organization_id');
        if ($church_id)
            $this->db->where('tr.church_id', $church_id);

        //Sub Organizations Filter
        $campus_id = (int) $this->input->post('suborganization_id');
        if ($campus_id)
            $this->db->where('tr.campus_id', $campus_id);

        //Funds Filter
        $fund_id = (int) $this->input->post('fund_id');
        if ($fund_id)
            $this->db->where('tf.fund_id', $fund_id);

        //Method Filter
        $method = $this->input->post('method');
        if ($method)
            $this->db->where('tr.src', $method);

        //Frequency Filter
        $freq = $this->input->post('freq');
        if ($freq)
            $this->db->where('sub.frequency', $freq);

        $data = $this->db->get('epicpay_customer_transactions as tr')->row();

        $result            = [];
        $result['total']   = '0.00';
        $result['max_net'] = '0.00';
        $result['max_net_fund'] = '0.00';
        
        if (isset($data->total) && $data->total) {
            $result['total'] = number_format($data->total, 2, '.', '');
        }

        if (!$fund_id) {
            if (isset($data->max_net) && $data->max_net) {
                $result['max_net'] = number_format($data->max_net, 2, '.', '');
            } 
        } else {
            if (isset($data->max_net_fund) && $data->max_net_fund) {
                $result['max_net'] = number_format($data->max_net_fund, 2, '.', '');
            }
        }

        return $result;
    }

    private function getSubsCount($orgnx_ids) {

        $this->db->select('count(DISTINCT sub.id) as total, DATE_FORMAT(min(sub.start_on), "%m/%d/%Y") as since')
                ->join('transactions_funds tf', 'tf.subscription_id = sub.id', 'LEFT')
                ->where('sub.status', 'A');

        //Organizations of User Filter
        $this->db->where('sub.church_id in (' . $orgnx_ids . ')');

        //Organizations Filter
        $church_id = (int) $this->input->post('organization_id');
        if ($church_id)
            $this->db->where('sub.church_id', $church_id);

        //Sub Organizations Filter
        $campus_id = (int) $this->input->post('suborganization_id');
        if ($campus_id)
            $this->db->where('sub.campus_id', $campus_id);

        //Funds Filter
        $fund_id = (int) $this->input->post('fund_id');
        if ($fund_id)
            $this->db->where('tf.fund_id', $fund_id);

        //Method Filter
        
        if ($this->input->post('method'))
            $this->db->where('sub.src', $this->input->post('method'));

        //Frequency Filter
        $freq = $this->input->post('freq');
        if ($freq)
            $this->db->where('sub.frequency', $freq);

        $data = $this->db->get($this->table . ' as sub')->row();

        $result          = [];
        $result['count'] = 0;
        $result['since'] = '-';

        if (isset($data->total) && $data->total) {
            $result['count'] = $data->total;
        }

        if (isset($data->since) && $data->since) {
            $result['since'] = $data->since;
        }

        return $result;
    }

    public function getList($donor_id) {
        $this->db->select('s.id, s.account_donor_id, s.church_id, s.campus_id, s.amount, s.frequency, s.is_fee_covered, '
                        . 'DATE_FORMAT(s.start_on, "%m/%d/%Y") as start_on, DATE_FORMAT(s.created_at, "%m/%d/%Y") as created_at, '
                        . 'DATE_FORMAT(s.updated_at, "%m/%d/%Y") as updated_at, DATE_FORMAT(s.cancelled_at, "%m/%d/%Y") as cancelled_at,'
                        . 'GROUP_CONCAT(f.name SEPARATOR ", ") funds_name, '
                        . 'scs.last_digits, if(scs.source_type = "bank", "Bank",if(scs.source_type = "card", "Card","")) payment_method '
                        . '')
                ->join('epicpay_customer_sources scs', 'scs.id = s.customer_source_id', 'LEFT')
                ->join('transactions_funds tf', 'tf.subscription_id = s.id', 'INNER')
                ->join('funds f', 'f.id = tf.fund_id', 'INNER')
                ->where('s.status', 'A')->where('s.account_donor_id', $donor_id)
                ->group_by('s.id')
                ->order_by('s.id', 'DESC');

        $row = $this->db->get($this->table . ' s')->result_array();

        return $row;
    }

    //===== get last subscriptions
    public function getNewSubscriptionsZapierPoll($user_id) {

        $orgnx_ids = getOrganizationsIds($user_id);

        $from = '2020-09-21';
        $data = $this->db->select(''
                                . 'sub.id, sub.email, sub.first_name, sub.last_name, dnr.phone, '
                                . 'sum(tf.amount) as amount, sum(tf.fee) as fee, sum(tf.net) as net, '
                                . 'sub.frequency, DATE_FORMAT(sub.start_on, "%Y-%m-%d") as starts_on, '
                                . 'if(sub.is_fee_covered = 1, "Yes", "No") as is_fee_covered, '
                                . 'GROUP_CONCAT(f.name SEPARATOR ", ") funds, sub.src, '
                                . 'if(sub.src = "BNK", "ACH", if(sub.src = "CC", "Card","")) payment_method, '
                                //. 'trx.church_id, trx.campus_id'
                                . 'DATE(sub.created_at) as created_at')
                        ->join('transactions_funds tf', 'tf.subscription_id = sub.id', 'inner')
                        ->join('funds f', 'f.id = tf.fund_id', 'inner')
                        ->join('account_donor dnr', 'dnr.id = sub.account_donor_id', 'left') //We could put a inner join, all transactions must have a donor even if is anonymous
                        ->where('sub.status', 'A')
                        ->where('sub.church_id in (' . $orgnx_ids . ')')
                        ->where("sub.created_at >= '$from'", null, false)
                        ->group_by('sub.id')
                        ->limit(25, 0)
                        ->order_by('sub.id', 'desc')
                        ->get($this->table . ' sub')->result();

        return $data;
    }

}
