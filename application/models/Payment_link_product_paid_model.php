<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_link_product_paid_model extends CI_Model {

    private $table     = 'payment_link_products_paid';
    public $valAsArray = false;

    public function __construct() {
        parent::__construct();
    }

    /*
     * @holds 
      transaction_id
      payment_link_id
      product_id
      product_name
      product_price
      qty_req
     */

    public function save($data = []) {
        $this->db->insert($this->table, $data);
    }

    public function getListByTrxnId($trxn_id, $select = null) {
        if ($select) {
            $this->db->select($select);
        } else {
            $this->db->select('plpp.*, CONCAT_WS("", "' . BASE_URL_FILES . 'files/get/digital_content/", p.file_hash) as digital_content_url, p.file_hash as digital_content');
        }

        $data = $this->db->join('products as p', 'p.id = plpp.product_id', 'inner')
                        ->where('plpp.transaction_id', $trxn_id)->get($this->table . ' plpp')->result();
        
        $bigTotal = 0;
        foreach ($data as &$row) {            
            $row->_sub_total = round($row->product_price * $row->qty_req, 2);
            $bigTotal += $row->_sub_total;            
        }

        return ['data' => $data ? $data : [], '_big_total' => $bigTotal];
    }

}
