<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_link_product_model extends CI_Model {

    private $table     = 'payment_link_products';
    public $valAsArray = false;

    public function __construct() {
        parent::__construct();
    }

    private function beforeSave($data) {
        if (isset($data['product_name']) && $data['product_name']) {
            $data['product_name'] = ucfirst(trimLR_Duplicates($data['product_name']));
        }
        return $data;
    }
    
    //no secure needed yet, we spect link_id comes already secure, 
    //if you want to reuse this method check if security is needed, if yes, install the security lines
    
    public function get($link_id, $select = null) {
        $select ?  $this->db->select($select) : $this->db->select('plp.*, CONCAT_WS("", "' . BASE_URL_FILES . 'files/get/digital_content/", p.file_hash) as digital_content_url, p.file_hash as digital_content');
        $data = $this->db->join('products as p', 'p.id = plp.product_id', 'inner')
                ->where('plp.id', $link_id)->get($this->table . ' plp')->row();
        
        return $data;
    }

    public function getList($link_id, $select = null) {
        if ($select) {
            $this->db->select($select);
        } else {            
            $this->db->select('plp.*, CONCAT_WS("", "' . BASE_URL_FILES . 'files/get/digital_content/", p.file_hash) as digital_content_url, p.file_hash as digital_content');
        }
        
        $data = $this->db->join('products as p', 'p.id = plp.product_id', 'inner')
                ->where('plp.payment_link_id', $link_id)->get($this->table . ' plp')->result();
        
        return $data;
    }

    public function save($data = []) {
        $data = $this->beforeSave($data);
        $this->db->insert($this->table, $data);
    }

    public function editBulk($data, $client_id = false) {
        $client_id = $client_id ? $client_id : $this->session->userdata('user_id');
        foreach ($data as $value) {
            $result = $this->db->select('p.id')
                            ->join('payment_links p', 'p.id=lp.payment_link_id', 'inner')
                            ->where('lp.id', $value['payment_link_product_id'])
                            ->where('p.client_id', $client_id)
                            ->get($this->table . ' lp')->row();
            if ($result) {
                $this->db->set('qty', $value['qty']);
                $this->db->set('is_editable', $value['is_editable'] == 'true' ? 1 : 0);
                $this->db->where('lp.id', $value['payment_link_product_id']);
                $this->db->update($this->table . ' lp');
            }
        }
        return [
            'status'  => true,
            'message' => langx('Products Updated'),
        ];
    }

}
