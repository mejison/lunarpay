<?php

defined('BASEPATH') OR exit('No direct script access allowed');

function users_getTeamMemberPermissionsRate($permissions_str) {

    $total_eps = 0;
    $total_per = 0;
    if (strlen($permissions_str)) {
        $total_per = count(explode(',', $permissions_str));
        $total_eps = count(MODULE_TREE);
    }
    return "$total_per / $total_eps";
}

class User_model extends CI_Model {

    private $table = 'users';
    
    CONST STARTER_STEP_BANK_CONFIRMATION = 6;

    public function __construct() {
        parent::__construct();
    }

    public function getDt() {
        $this->load->library("Datatables");
        $this->datatables->select("id, username, email, first_name, last_name, FROM_UNIXTIME(created_on) as created_on")
                ->from($this->table);
        $data = $this->datatables->generate();
        return $data;
    }

    public function getTeamDt() {

        $this->load->library("Datatables");
        $this->datatables->select("id, username, email, CONCAT_WS(' ', first_name, last_name) as name, date(FROM_UNIXTIME(created_on)) as created_on, permissions")
                ->where('parent_id', $this->session->userdata('user_id'))
                ->from($this->table);

        $this->datatables->add_column('permissions_rate', '$1', 'users_getTeamMemberPermissionsRate(permissions)');
        $data = $this->datatables->generate();
        return $data;
    }

    public function get($id, $select = false) {
        if ($select) {
            $this->db->select($select);
        }
        $row = $this->db->where('id', $id)->from($this->table)->get()->row();
        return $row;
    }

    public function getByEmail($email, $select = false) {
        if ($select) {
            $this->db->select($select);
        }
        $row = $this->db->where('email', $email)->from($this->table)->get()->row();
        return $row;
    }

    public function update($data, $user_id) {

        $this->db->where('id', $user_id);
        $this->db->update($this->table, $data);
        return true;
    }

    public function getTeamMember($id, $main_user_id) {
        $data = $this->db->select('id, first_name, last_name, email, phone, parent_id, permissions, date(FROM_UNIXTIME(created_on)) as created_on')
                        ->where('parent_id', $main_user_id) //==== security field
                        ->where('id', $id)
                        ->get($this->table)->row();

        $data->permissions = strlen($data->permissions) ? explode(',', $data->permissions) : [];

        return $data;
    }

    CONST MAX_STARTER_STEP = 8;
    public function setStarterStep($user_id, $step) {
        if($step > 0 && $step <= self::MAX_STARTER_STEP) {
            return $this->db->where('id', $user_id)->update($this->table, ['starter_step' => $step]);
        }
        throw new Exception('Step not available');
    }

    public function getStarterStep($user_id) {
        return $this->db->from($this->table)->where('id', $user_id)->select('starter_step')->get()->row();
    }

    public function getForceLogout($user_id) {
        $user = $this->db->select('force_logout')->where('id', $user_id)->get('users')->row();
        return $user ? $user->force_logout : null;
    }

    public function setForceLogout($user_id, $value_1_OR_null) {
        $this->db->where('id', $user_id)->update('users', ['force_logout' => $value_1_OR_null]);
    }
    
    public function getAllTeamMembersIds($parent_id) {
        $data = $this->db->from($this->table)->where('parent_id', $parent_id)->select('id')->get()->result();
        
        $result = [];
        foreach ($data as $row) {
            $result [] =  $row->id;
        }
        
        return $result;
    }

}
