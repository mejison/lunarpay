<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sources_model extends CI_Model {

    private $table = 'epicpay_customer_sources';

    public function __construct() {
        parent::__construct();
        //verifyx_paysafe load sources depending on the payment processor
    }

    public function getList($donor_id, $order_by = false, $result_object = false, $select = false) {

        if (!$select) {
            $select = 'id, account_donor_id, church_id, customer_id, postal_code, source_type, '
                    . 'exp_month, exp_year, last_digits, name_holder, created_at, updated_at, epicpay_wallet_id';
        }

        $this->db
                ->from($this->table)
                ->select($select)
                ->where('status', 'P')
                ->where('is_active', 'Y')
                ->where('is_saved', 'Y')
                ->where('account_donor_id', $donor_id)
                ->group_by('epicpay_wallet_id');
        if (!$order_by) {
            $this->db->order_by('id', 'DESC');
        } else {
            $this->db->order_by($order_by);
        }

        if ($result_object) {
            $data = $this->db->get()->result_object();
        } else {
            $data = $this->db->get()->result_array();
        }


        return $data;
    }

    public function getOne($donor_id, $source_id, $select = false, $return_object = false) {

        if ($select) {
            $this->db->select($select);
        } else {
            $this->db->select('id, account_donor_id, church_id, customer_id, postal_code, source_type, '
                    . 'exp_month, exp_year, last_digits, name_holder, created_at, updated_at');
        }

        $this->db
                ->from($this->table)
                ->where('status', 'P')
                ->where('is_active', 'Y')
                ->where('is_saved', 'Y')
                ->where('account_donor_id', $donor_id) //secure
                ->where('id', $source_id)
                ->group_by('epicpay_wallet_id')
                ->order_by('id', 'DESC');

        if ($return_object) {
            $row = $this->db->get()
                    ->row();
        } else {
            $row = $this->db->get()
                    ->row_array();
        }


        return $row;
    }

    //===== get last donors
    public function getExpiredSourcesZapierPoll($user_id) {

        $now_yr  = date('Y');
        $now_mth = date('m');

        $orgnx_ids = getOrganizationsIds($user_id);
        $data      = $this->db->select(''
                                . 'src.id, dnr.email, dnr.first_name, dnr.last_name, dnr.phone, '
                                . 'src.name_holder as holder_name, src.last_digits, src.postal_code, src.exp_year, src.exp_month, '
                                //. 'CONCAT(src.exp_year, "-", src.exp_month) as expires, '
                                . 'DATE(src.created_at) as created_at')
                        ->join('account_donor dnr', 'dnr.id = src.account_donor_id', 'left')
                        ->where('src.source_type', 'card')
                        ->where('src.status', 'P')->where('src.is_active', 'Y')->where('src.is_saved', 'Y')
                        ->where('src.church_id in (' . $orgnx_ids . ')')
                        ->where('DATE(CONCAT(src.exp_year, "-" , src.exp_month, "-", "01")) < DATE(CONCAT("' . $now_yr . '", "-" , "' . $now_mth . '", "-", "01"))', null, false)
                        ->order_by('src.id', 'desc')
                        ->limit(25, 0)
                        ->get($this->table . ' src')->result();

        //===== remove this code after saving the year expiration source with 4 numbers as 2021
        //===== //verifyx_paysafe too
        foreach ($data as &$row) {
            $row->expires = "20$row->exp_year-$row->exp_month";
            unset($row->exp_year);
            unset($row->exp_month);
        }

        return $data;
    }

}
