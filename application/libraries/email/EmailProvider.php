<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 
 * Copyright 2020 Juan P. Gomez <pablogmzc@gmail.com>.
 * 
  /*
 * Description of newPHPClass
 *
 * @author Juan P. Gomez <pablogmzc@gmail.com>
 */

interface IEmailProvider {

    public function sendEmail($from_email, $from_name, $to, $sub, $msg);
}

class EmailProvider {

    const CODEIGNITER = PROVIDER_EMAIL_CODEIGNITER;
    const MAIL_GUN    = PROVIDER_EMAIL_MAILGUN;

    private static $email_object = null;

    static function init($provider = null) {

        $provider = $provider ? $provider : PROVIDER_EMAIL_DEFAULT;

        switch ($provider) {
            case self::CODEIGNITER:
                require_once 'application/libraries/email/CIEmail.php';
                self::$email_object = new CIEmail;
                break;
            case self::MAIL_GUN:
                require_once 'application/libraries/email/MailGunRoot.php';
                self::$email_object = new MailgunRoot;
                break;
            default:
                show_error('Bad Email Provider');
        }
    }

    static function getInstance() {
        if (self::$email_object) {
            return self::$email_object;
        }

        show_error('Provider has not been initialized');
    }

}
