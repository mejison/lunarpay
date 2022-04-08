<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Chat_setting_model extends CI_Model {

    private $table = 'chat_settings';

    public function __construct() {
        parent::__construct();
    }

    private function checkBelongsToUser($id, $user_id) {
        return checkBelongsToUser([
            ['chat_settings.id' => $id, 'client_id', 'users.id', $user_id]
        ]);
    }

    public function getChatSetting($user_id,$church_id,$campus_id) {

        $this->db->from($this->table)
            ->where('client_id',$user_id)
            ->where('church_id',$church_id);

        if($campus_id)
            $this->db->where('campus_id',$campus_id);
        else
            $this->db->where('campus_id is null');

        return $this->db->get()->row();
    }

    public function getChatSettingByChurch($church_id,$campus_id) {

        $this->db->from($this->table)
            ->where('church_id',$church_id);

        if($campus_id)
            $this->db->where('campus_id',$campus_id);
        else
            $this->db->where('campus_id is null');

        $data = $this->db->get()->row();
        $data->entire_logo_url = BASE_URL_FILES . 'files/get/' . $data->logo;
        return $data;
    }

    public function getChatSettingList($select,$where) {
        $this->db
            ->select($select)
            ->where($where)
            ->from($this->table);
        return $this->db->get()->result_array();
    }

    public function save($data, $client_id = false) {

        $client_id = $client_id ? $client_id : $this->session->userdata('user_id');

        $suborganization_id = isset($data['campus_id']) ? (int) $data['campus_id'] : 0;

        $save_data = [];

        if(isset($data['theme_color'])){
            $save_data['theme_color'] = $data['theme_color'];
        }

        if(isset($data['button_text_color'])){
            $save_data['button_text_color'] = $data['button_text_color'];
        }

        ////////////// remove http:// or https:// from website
        if (isset($data['domain'])) {
            $disallowed = ['http://', 'https://'];
            foreach ($disallowed as $d) {
                if (strpos($data['domain'], $d) === 0) {
                    $data['domain'] = str_replace($d, '', $data['domain']);
                }
            }
            $save_data['domain'] = strtolower($data['domain']);
        }
        //////////////

        if(isset($data['suggested_amounts'])){
            $save_data['suggested_amounts'] = $data['suggested_amounts'];
        }

        if(isset($data['trigger_text'])){
            $save_data['trigger_text'] = $data['trigger_text'];
        }

        if(isset($data['debug_message'])){
            $save_data['debug_message'] = $data['debug_message'];
        }

        if(isset($data['type_widget'])){
            $save_data['type_widget'] = $data['type_widget'];
        }

        if(isset($data['widget_position'])){
            $save_data['widget_position'] = $data['widget_position'];
        }

        if(isset($data['widget_x_adjust'])){
            $save_data['widget_x_adjust'] = $data['widget_x_adjust'];
        }

        if(isset($data['widget_y_adjust'])){
            $save_data['widget_y_adjust'] = $data['widget_y_adjust'];
        }

        if(isset($data['conduit_funds'])){
            $save_data['conduit_funds'] = $data['conduit_funds'];
        }

        if(!isset($data['logo']) && isset($data['image_changed']) && $data['image_changed'] == "1"){
            $logo_category = 'branding_logo';

            $config['upload_path']   = './application/uploads/'.$logo_category.'/';
            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']      = BRAND_MAX_LOGO_SIZE;
            $config['overwrite']     = true;
            $config['file_name']     = 'u'.$client_id.'_ch'.$data['church_id'];

            if($suborganization_id)
                $config['file_name'] .= '_cm'.$suborganization_id;

            $this->load->library('upload', $config);

            if($this->upload->do_upload('logo'))
            {
                $image_data = $this->upload->data();
                $save_data['logo'] = $logo_category.'/'.$image_data['file_name'];
            }
            else
            {
                output_json([
                    'status'  => false,
                    'message' => $this->upload->display_errors()
                ]);
                return ['status'  => false,
                    'message' => $this->upload->display_errors()];
            }
        }

        if(isset($data['logo'])){
            $save_data['logo'] = $data['logo'];
        }

        $id = isset($data['id']) ? $data['id'] : null;

        if(!$id){ //create

            $orgnx_ids     = getOrganizationsIds($client_id);
            $orgnx_ids_arr = $orgnx_ids ? explode(',', $orgnx_ids) : [];
            if (!in_array($data['church_id'], $orgnx_ids_arr)) {
                throw new Exception('Invalid organization');
            }

            $save_data['client_id'] = $client_id;
            $save_data['church_id'] = $data['church_id'];

            if($suborganization_id)
                $save_data['campus_id'] = $suborganization_id;

            $this->db->insert($this->table,$save_data);
            return [
                'status'  => true,
                'message' => langx('Brand Settings Saved'),
                'data' => ['id' => $this->db->insert_id()]
            ];
        } else { // update
            $this->db->where('id',$id);
            $this->db->where('client_id',$client_id);
            $this->db->update($this->table,$save_data);
            return [
                'status'  => true,
                'message' => langx('Brand Settings Saved'),
            ];
        }
    }


    public function updateInstallStatus($id,$date,$status) {
        $this->db->where('id',$id);
        $result = $this->db->update($this->table,['install_status_date'=>$date,'install_status'=>$status]);
        return $result;
    }

    public function addAutomaticallyToConduitFund($user_id,$church_id,$campus_id,$fund_id)
    {
        $chatSetting = $this->getChatSetting($user_id, $church_id, $campus_id);

        if ($chatSetting && $chatSetting->type_widget == 'conduit') {
            $conduit_funds = json_decode($chatSetting->conduit_funds);
            $conduit_funds[] = $fund_id;
            $this->save(['id' => $chatSetting->id, 'conduit_funds' => json_encode(array_values($conduit_funds))]);
        }

        return;
    }

    public function removeAutomaticallyFromConduitFund($user_id,$church_id,$campus_id,$fund_id)
    {
        $chatSetting = $this->getChatSetting($user_id,$church_id,$campus_id);
        if($chatSetting) {
            $main_conduit_funds = json_decode($chatSetting->conduit_funds);
            //Checking fund id on conduit funds and getting key to unset it
            if($main_conduit_funds && ($key = array_search($fund_id, $main_conduit_funds)) !== false) {
                unset($main_conduit_funds[$key]);
                $this->save(['id'=>$chatSetting->id,'conduit_funds' => json_encode(array_values($main_conduit_funds))]);
            }
        }
        return;
    }
}
