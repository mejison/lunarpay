<?php 
    
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_status_from_callbacks_bkup extends CI_Migration {

    public function __construct() {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up() {

        $this->db->query('ALTER TABLE `epicpay_webhooks_backup`
	DROP COLUMN `status`;
');
        
        printd(get_class($this));
        
        //$this->db->query('');        
        //printd('<b>comment when adding data</b>');
        
    }

    public function down() {
        
    }

}