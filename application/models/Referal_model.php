<?php

defined('BASEPATH') OR exit('No direct script access allowed');
function paCreateLink($ref){
    return Referal_model::CODE_LINK_URL.$ref;
}
class Referal_model extends CI_Model { //include suborgnx too

    private $table     = 'referals';
    const CODE_LINK_URL= CUSTOMER_APP_BASE_URL.'auth/register/ref=';
      //for getting validation errors as array or a string, false = string

    public function __construct() {
        parent::__construct();
    }
  
    public function getDt() {
        $user_id  = $this->session->userdata('user_id');
        if (!$user_id) { 
            return ['status' => false, 'message' => ''];
        }
        $this->load->library("Datatables");

        $this->datatables->select("usr.referral_code, r.email, r.full_name, DATE_FORMAT(r.date_sent, '%m/%d/%Y') as date_sent_format, DATE_FORMAT(r.date_register, '%m/%d/%Y') as date_register_format,r.date_sent,r.date_register")
        ->join('users usr', 'usr.id = r.parent_id','inner')
        ->where('r.parent_id', $user_id)
        ->from($this->table . ' r');
        
       // $this->datatables->add_column('_link_url', '$1', 'paCreateLink(usr.referral_code)'); 
        
        return $this->datatables->generate();
    }

    public function save($data, $user_id = false) {
        $user_id  = $this->session->userdata('user_id');
       
        if (!$user_id) { 
            return ['status' => false, 'message' => ''];
        }
        $data['user_id']=$user_id;
        $data['orgName'] = $this->session->userdata()['currnt_org']['orgName'];
        if(empty($data['email']) || empty($data['referal_message']) || empty($data['full_name'])){
            return [
                'status'  => false,
                'message' => langx('Validation error found'),
                'errors' =>  '<p>All fields are required.</p>'
            ]; 
        }
        if(!$this->getReferalByEmail($data['email'])){
            $this->db->insert($this->table, array(
                'parent_id'=>$user_id,
                'email'=>$data['email'],
                'referal_message' =>$data['referal_message'],
                'full_name'=>$data['full_name'],
                'date_sent'=>date('Y-m-d H:i:s')
            ));
        }
        $this->load->helper('emails');
        $result = shareReferalCode($data);
        return [
            'status'       => true,
            'message'      => langx('Email Sent'),
            'emailResponse' => $result
        ];
    }

    public function getReferalByEmail($email) {
        $this->db->select("id")
        ->where('email', $email)
        ->from($this->table. ' l');
        return $this->db->get()->row();
    }
    
}
