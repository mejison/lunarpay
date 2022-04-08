<?php

defined('BASEPATH') OR exit('No direct script access allowed');
function paStatusAsHtmlString($invoiceStatus) {
    return Payment_link_model::LINK_STATUS_STRING_HTML[$invoiceStatus];
}
function paCreateLink($hash){
    return Payment_link_model::PAYMENT_LINK_URL.$hash;
}
class Payment_link_model extends CI_Model {

    private $table = 'payment_links';
    public $valAsArray = false; //for getting validation errors as array or a string, false = string
    const HASH_SIZE = 128; 
    const PAYMENT_LINK_URL= CUSTOMER_APP_BASE_URL.'c/portal/payment_link/';
    
    const LINK_ACTIVE   = 1;
    const LINK_DEACTIVATED   =  0;
    
    const LINK_STATUS_STRING = [//For presenting to the final user*
        Payment_link_model::LINK_ACTIVE  => 'ACTIVE',
        Payment_link_model::LINK_DEACTIVATED => 'DEACTIVATED',
    ];
    
    const LINK_STATUS_STRING_HTML = [//For presenting to the final user as html badge
        Payment_link_model::LINK_ACTIVE  => '<span class="badge badge-primary " style="width: 60px">Active</span>',
        Payment_link_model::LINK_DEACTIVATED => '<span class="badge badge-secondary" style="width: 60px">Deactivated</span>',
    ];

    public function __construct() {
        parent::__construct();
    }

    private function beforeSave($data){
        return $data;
    }

    public function getDt() {
        $user_id  = $this->session->userdata('user_id');
        if (!$user_id) { 
            return ['status' => false, 'message' => ''];
        }
        $this->load->library("Datatables");
        $this->datatables->select("l.client_id AS client_id,count(0) AS product_total,l.id AS id,l.hash as _link_url,
        l.status, l.created_at,1 AS options,DATE_FORMAT(l.created_at, '%m/%d/%Y') as created_at_formatted")
        ->where('l.client_id', $user_id)
        ->where('l.status', 1)
        ->from('payment_links l') 
        ->join('payment_link_products pl', 'pl.payment_link_id = l.id','inner')
        ->join('products p', 'p.id = pl.product_id','left')
        ->group_by("l.id");
      
        $this->datatables->edit_column('status', '$1', 'paStatusAsHtmlString(status)');  
        $this->datatables->add_column('_link_url', '$1', 'paCreateLink(_link_url)'); 

        $data = $this->datatables->generate();

        return $data;
    }

    public function remove($id, $user_id) {
        $link = $this->get($id, $user_id);
        if (!$link) { 
            return ['status' => false, 'message' => ''];
        }
        $this->db->where('id', $id)  
        ->where('client_id', $user_id)
        ->update($this->table, ['status' => 0]);
        return ['status' => true, 'message' => 'Link removed'];
    }

    public function get($id, $user_id = null) {
        $this->db->select("l.client_id")
        ->where('l.client_id', $user_id)
        ->where('l.id', $id)
            ->where('l.status',1)
            ->from($this->table. ' l')
        ->join('church_detail c', 'c.ch_id = l.church_id');
        return $this->db->get()->row();
    }

    //This function is called for customer api
    public function getByHash($hash, $includeTrxnId = null) {
                
        $paymentLink = $this->db->select('id, church_id, campus_id, payment_methods, hash')
            ->where('hash', $hash)->where('status', 1)
            ->get($this->table.' as pl')->row();

        if ($paymentLink) {

            $this->load->model('donation_model');
            $this->load->model('payment_link_product_model');

            $products = $this->payment_link_product_model->getList($paymentLink->id);

            $paymentLink->products = $products ? $products : [];
            
            if($includeTrxnId) { //including transaction id will retrieve products_paid, thats products with qtys and prices defined at the moment of the payment
                $this->load->model('payment_link_product_paid_model');
                $productsPaid = $this->payment_link_product_paid_model->getListByTrxnId($includeTrxnId);
                $paymentLink->products_paid = $productsPaid ? $productsPaid : [];

                $payments = $this->donation_model->getById($includeTrxnId);
                $paymentLink->payments = $payments ? $payments : [];
            }
            
            // ---- get organization, suborganization, an customer data all from an invoice. All data in one package
            $this->load->model('organization_model');
            $paymentLink->organization = $this->organization_model->get($paymentLink->church_id, 
                    'ch_id, client_id, church_name as name, phone_no, website, street_address, street_address_suite, city, state, postal, paysafe_template');                    
            
            $this->load->helper('paysafe'); //check if fees_template are being used some where
            $paymentLink->organization->fees_template = getPaySafeTplParams($paymentLink->organization->paysafe_template);
            
            $this->load->model('orgnx_onboard_psf_model');
            $onboard = $this->orgnx_onboard_psf_model->getByOrg($paymentLink->organization->ch_id, $paymentLink->organization->client_id, 'region');
            
            $paymentLink->organization->region = $onboard->region;
            
            $this->load->model('suborganization_model');
            $paymentLink->suborganization = $this->suborganization_model->get($paymentLink->campus_id, false, 'name, phone as phone_no');
            
        }

        return $paymentLink;
    }

    public function getById($id, $client_id = false, $include_trxn_id = false) {

        $client_id = $client_id ? $client_id : $this->session->userdata('user_id');
        $orgnx_ids     = getOrganizationsIds($client_id);
            
        $link = $this->db->select('pl.id, pl.created_at, pl.church_id, pl.campus_id, pl.hash, pl.status, pl.payment_methods')                
                ->where('pl.id', $id)
                ->where_in('pl.church_id', explode(',', $orgnx_ids))
                ->where('pl.status', 1)
                ->get($this->table . ' pl')->row();
        
        if ($link) {
            $link->_link_url = paCreateLink($link->hash);
            $link->_status = paStatusAsHtmlString($link->status);
            $this->load->model('payment_link_product_model');
            $products = $this->payment_link_product_model->getList($link->id);
            $link->products =  $products ? $products : [];
            
            if ($include_trxn_id) {
                $this->load->model('payment_link_product_paid_model');
                $products_paid       = $this->payment_link_product_paid_model->getListByTrxnId($include_trxn_id);
                $link->products_paid = $products_paid ? $products_paid : [];
            }

            $this->load->model('organization_model');
            $link->organization = $this->organization_model->get($link->church_id, 
                    'ch_id, client_id, church_name as name, phone_no, website, street_address, street_address_suite, city, state, postal, paysafe_template');
            
            $this->load->model('suborganization_model');
            $link->suborganization = $this->suborganization_model->get($link->campus_id, false, 'name, phone as phone_no');
        }
         
        return $link;
    }
    
    public function save($data, $client_id = false) {
        $val_messages    = [];
       
        if(!isset($data['products'])){
            $val_messages [] = langx('At least one Product is required to create a payment link');
        }
        if(count($val_messages)==0){
            if (!isset($data['organization_id']) || !$data['organization_id']){
                throw new Exception('The Company field is required');
            }
            $client_id = $client_id ? $client_id : $this->session->userdata('user_id');
            $orgnx_ids     = getOrganizationsIds($client_id);
            $orgnx_ids_arr = $orgnx_ids ? explode(',', $orgnx_ids) : [];
            if (!in_array($data['organization_id'], $orgnx_ids_arr)) {
                throw new Exception('Invalid organization');
            }
            $this->db->trans_start();
                $this->db->insert($this->table, array(
                    'client_id'=>$client_id,
                    'hash'=>uniqid(),
                    'church_id'=>$data['organization_id'],
                    'status'=>1,
                    'campus_id'=>isset($data['suborganization_id']) && $data['suborganization_id'] ? $data['suborganization_id'] : null,
                    'payment_methods'=> json_encode($data['payment_options']),
                    'created_at'=>date('Y-m-d H:i:s')
                ));
                $payment_link_id = $this->db->insert_id();   
                $this->load->model('payment_link_product_model');
                foreach ($data['products'] as $value) {
                    $this->payment_link_product_model->save(array(
                        'payment_link_id'=>$payment_link_id,
                        'product_id'=>$value['product_id'],
                        'product_name'=>$value['product_name'],
                        'product_price'=>$value['product_price'],
                        'qty'=>$value['quantity'],
                        'is_editable'=> $value['editable']=='true' ? 1 : 0
                    ));
                }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction error');
            }
            return [
                'status'       => true,
                'message'      => langx('Link Created'),
            ];
        }else {
            return [
                'status'  => false,
                'message' => langx('Validation error found'),
                'errors' => !$this->valAsArray ? stringifyFormatErrors($val_messages) : $val_messages
            ];
        }
    }    
}
 


 