<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Encrypt extends My_Controller {

    public $data = [];

    public function __construct() {
        parent::__construct();

        $this->load->library('encryption');
        $this->encryptPhrase = $this->config->item('pty_epicpay_encrypt_phrase');

        $this->encryption->initialize([
            'cipher' => 'aes-256',
            'mode'   => 'ctr',
            'key'    => $this->encryptPhrase,
                ]
        );
    }

    public function generate() {

        $credential1 = $this->encryption->encrypt('your_user' . ':' . 'your_pass');
        echo $credential1;
        //Comment
    }

    public function decrypt() {
        $decrypt = $this->encryption->decrypt(');
        echo $decrypt;
    }

}
