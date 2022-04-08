<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function inAllowRemove($countInvoices) {
    $countInvoices = (int)$countInvoices;
    if($countInvoices == 0) {
        return 1;
    }
    return 0;
}

class Product_model extends CI_Model {

    private $table = 'products';
    public $valAsArray = false; //for getting validation errors as array or a string, false = string

    private $periods = ['daily','weekly','monthly','3_months','6_months','yearly'];

    public function __construct() {
        parent::__construct();
    }

    private function beforeSave($data){
        if(isset($data['name']) && $data['name']){
            $data['name'] = ucwords(strtolower(trimLR_Duplicates($data['name'])));
        }
        return $data;
    }

    public function getDt() {
        $user_id  = $this->session->userdata('user_id');
        $orgnx_id = $this->input->post('organization_id');
        $suborgnx_id = $this->input->post('sub_organization_id');
        $this->load->library("Datatables");        
        if ($orgnx_id) {
            $this->datatables->where('prod.church_id', $orgnx_id ); //chrus = organization campus= sub
        }
        
        if ($suborgnx_id) {
            $this->datatables->where('prod.campus_id', $suborgnx_id);
        } else {
            $this->datatables->where('prod.campus_id', null);
        }
        $this->datatables->select("prod.id, prod.reference, c.church_name, cm.name as cs_name, prod.name as prod_name, 
        CONCAT_WS(' / ', c.church_name, IF(LENGTH(cm.name),cm.name,NULL)) as organization, prod.price, 
        DATE_FORMAT(prod.created_at, '%m/%d/%Y') as created_at, count(ip.id) as count_invoices")
                ->join('church_detail c', 'c.ch_id = prod.church_id', 'INNER')
                ->join('campuses cm','cm.id = prod.campus_id','left')
                ->join('invoice_products ip','prod.id = ip.product_id','left')
                ->where('c.client_id', $user_id)
                ->where('prod.trash',0)
                ->from($this->table . ' prod')
                ->group_by('prod.id');
        $this->datatables->add_column('allowRemove', '$1', 'inAllowRemove(count_invoices)');
        $data = $this->datatables->generate();
        return $data;
    }

    public function remove($id, $user_id) {
        //it does not remove, it only hides
        $product = $this->get($id, $user_id);
        if (!$product) { //if not exist product associated to user return
            return ['status' => false, 'message' => 'Product not found'];
        }

        $this->load->model('invoice_products_model');
        $invProdCount = $this->invoice_products_model->productExistInInvoices($id);
        if($invProdCount){
            return ['status' => false, 'message' => 'Product is already associated to an invoice'];
        }

        $this->db->where('id', $id) //hide product found associated to user
        ->where('client_id', $user_id)
        ->update($this->table, ['trash' => 1,'slug' => $id.date('Ymd')]);
        return ['status' => true, 'message' => 'Product removed'];
    }

    public function get($id, $user_id = null) {
        $this->db->where('c.client_id', $user_id)
        ->where('prod.id', $id)
            ->where('prod.trash',0)
            ->from($this->table. ' prod')
        ->join('church_detail c', 'c.ch_id = prod.church_id');
        return $this->db->get()->row();
    }

    public function getByDigitalContentHash($hash) {
        $this->db->where('prod.file_hash', $hash)
            ->where('prod.trash',0)
            ->from($this->table. ' prod');
        return $this->db->get()->row();
    }

    public function save($data, $client_id = false) {        
        $data['id'] = (int)$data['id'];
        $val_messages    = [];
        if(!$data['id']) {
            if (!isset($data['organization_id']) || !$data['organization_id'])
                $val_messages [] = langx('The Company field is required');
        }

        if (!isset($data['product_name']) || !$data['product_name'])
            $val_messages [] = langx('The Name field is required');
        
        if (isset($data['recurrence']) && $data['recurrence'] === 'R')
            $val_messages [] = langx('Recurrence Under development');

        if (empty($val_messages)) {
            $client_id = $client_id ? $client_id : $this->session->userdata('user_id');

            // ---- Validating that the user sends an organization that belongs to him
            $orgnx_ids     = getOrganizationsIds($client_id);
            $orgnx_ids_arr = $orgnx_ids ? explode(',', $orgnx_ids) : [];
            if (!in_array($data['organization_id'], $orgnx_ids_arr)) {
                throw new Exception('Invalid organization');
            }

            if ($data['recurrence'] !== 'O' && $data['recurrence'] !== 'R') {
                throw new Exception('Invalid Recurrence');
            }

            if ($data['recurrence'] === 'R' && !in_array($data['billing_period'],$this->periods)) {
                throw new Exception('Invalid Billing Period');
            }

            $data['billing_period'] = $data['recurrence'] == 'O' ? null : $data['billing_period'];

            $campus_id = isset($data['suborganization_id']) ? (int)$data['suborganization_id'] : null;

            $data['digital_content'] = null;
            if(!isset($data['digital_content']) && isset($data['digital_content_changed']) && $data['digital_content_changed'] == "1"){
                $hashSize = 128;
                $bytes = openssl_random_pseudo_bytes($hashSize / 2, $cstrong);
                $fileNameHash  = bin2hex($bytes);

                $logo_category = 'digital_content';

                $config['upload_path']   = './application/uploads/'.$logo_category.'/';
                $config['allowed_types'] = 'pdf';
                $config['file_name']     = $fileNameHash;

                $this->load->library('upload', $config);

                if($this->upload->do_upload('digital_content'))
                {
                    $data['digital_content'] = $fileNameHash;
                }
                else
                {
                    throw new Exception($this->upload->display_errors());
                }
            }

            if(!$data['id']) { // Create Product
                $save_data = [
                    'church_id'       => $data['organization_id'],
                    'campus_id'       => $campus_id ? $campus_id : null,
                    'name'            => $data['product_name'],
                    'price'           => (float) $data['price'],
                    'client_id'       => $client_id,
                    'recurrence'      => $data['recurrence'],
                    'file_hash'       => $data['digital_content'],
                    'billing_period'  => $data['billing_period'],
                    'created_at'      => date('Y-m-d H:i:s')
                ];

                $save_data = $this->beforeSave($save_data);
                $this->db->insert($this->table, $save_data);
                $product_id = $this->db->insert_id();
                
                $hexa = strtoupper(dechex(date('ymdHi'))); //two digits year, month, hour & minute converted to hexa
                $reference = 'PR' . $hexa . '-00' .$product_id; 
                
                $this->db->where('id', $product_id)->update($this->table, ['reference' => $reference]);

                $name = $save_data['name'];

                return [
                    'status'  => true,
                    'message' => langx('Product Created'),
                    'data' => ['id' => $product_id, 'name' => $name . ' ($'. number_format($save_data['price'], 2, '.', ',') .') ' ,'product_name' => $name, 'price' => $save_data['price']]
                ];
            } else { //Update Donor
                $save_data = [
                    'church_id'          => $data['organization_id'],
                    'campus_id'          => $campus_id ? $campus_id : null,
                    'name'               => $data['product_name'],
                    'price'              => (float)$data['price'],
                    'recurrence'         => $data['recurrence'] ,
                    'file_hash'          => $data['digital_content'],
                    'billing_period'     => $data['billing_period'] ,
                ];

                $this->db->where('id', $data['id']);
                $save_data = $this->beforeSave($save_data);
                $this->db->update($this->table, $save_data);

                return [
                    'status'  => true,
                    'message' => langx('Product Updated')
                ];
            }
        }

        return [
            'status'  => false,
            'message' => langx('Validation error found'),
            'errors' => !$this->valAsArray ? stringifyFormatErrors($val_messages) : $val_messages
        ];
    }

    public function get_tags_list_pagination() {
        $limit  = 10; //it must coincide with the limit defined on front end
        $offset = ($this->input->post('page') ? $this->input->post('page') - 1 : 0) * $limit;

        $this->db->select("SQL_CALC_FOUND_ROWS id, name, price", false);

        $church_id = (int)$this->input->post('organization_id');
        $campus_id = (int)$this->input->post('suborganization_id');

        $this->db->where('trash',0);
        $this->db->where('church_id',$church_id);
        if($campus_id){
            $this->db->where('campus_id',$campus_id);
        } else {
            $this->db->where('campus_id is null');
        }

        if ($this->input->post('q')) {
            $this->db->group_start();
            $this->db->like("name", $this->input->post('q'));
            $this->db->group_end();
        }

        $this->db->limit($limit, $offset);

        $result = $this->db->get($this->table)->result();

        $data = [];
        foreach ($result as $row) {
            $data[] = ['id' => $row->id, 'text' => $row->name . ' ($'.number_format($row->price, 2, '.', ',').')' , 'name' => $row->name, 'price' => $row->price ];
        }

        $total_count = $this->db->query('SELECT FOUND_ROWS() cnt')->row();

        return [
            'items'       => $data,
            'total_count' => $total_count->cnt
        ];
    }
}
 


 