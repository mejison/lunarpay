<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | Display Debug backtrace
  |--------------------------------------------------------------------------
  |
  | If set to TRUE, a backtrace will be displayed along with php errors. If
  | error_reporting is disabled, the backtrace will not display, regardless
  | of this setting
  |
 */
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
  |--------------------------------------------------------------------------
  | Exit Status Codes
  |--------------------------------------------------------------------------
  |
  | Used to indicate the conditions under which the script is exit()ing.
  | While there is no universal standard for error codes, there are some
  | broad conventions.  Three such conventions are mentioned below, for
  | those who wish to make use of them.  The CodeIgniter defaults were
  | chosen for the least overlap with these conventions, while still
  | leaving room for others to be defined in future versions and user
  | applications.
  |
  | The three main conventions used for determining exit status codes
  | are as follows:
  |
  |    Standard C/C++ Library (stdlibc):
  |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
  |       (This link also contains other GNU-specific conventions)
  |    BSD sysexits.h:
  |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
  |    Bash scripting:
  |       http://tldp.org/LDP/abs/html/exitcodes.html
  |
 */
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
//=================================================================================
//=================================================================================
//define('BASE_URL', 'http://localhost:3001/chatgive/html/dash/');

//Used to keep things on development environment without releasing it on live environment in a quick way
define('HIDE_FUTURE_FEATURES', FALSE);  //FALSE for developer machines or dev environments, TRUE for live environment, it will hide things set as "just-dev"

define('SYS_FOLDER', '');
define('SYS_FOLDER_NUM', 1);

define('BASE_URL', 'http://lunarpay.local/' . SYS_FOLDER);
define('BASE_URL_FILES', 'http://lunarpay.local/' . SYS_FOLDER);

//define('CUSTOMER_APP_BASE_URL', 'https://customer.lunarpay.io/' . SYS_FOLDER);
define('CUSTOMER_APP_BASE_URL', BASE_URL);

//USED FOR HANDLING MANY DOMAINS WITHIN THE SAME BASE WEB APP (DASHBOARD, CUSTOMER, ETC)
//IF IS DEVELOPER MACHINE ALL SYSTEMS WILL DEPEND ON THE LOCAL MACHINE BASE URL
//IF IS NOT DEVELOPER MACHINE ALL SYSTEMS WILL DEPEND ON THEIR DOMAIN - CHECK MY_CUSTOMER.PHP AND MY_CONTROLLER.PHP
define('IS_DEVELOPER_MACHINE', TRUE); 

//=================================================================================
define('THEME', 'thm2');
define('THEME_LAYOUT', 'themed/' . THEME . '/');
define('BASE_ASSETS', BASE_URL . 'assets/');
define('BASE_ASSETS_THEME', BASE_URL . 'assets/argon-dashboard-pro-v1.2.0/');
define('CSRF_TOKEN_NAME', 'csrf_token');

define('EPICPAY_ONBOARD_FORM_TEST', TRUE);

define('SET_SAME_SITE_NONE', FALSE);

//===============
define('ZAPIER_ENABLED', FALSE);
define('EMAILING_ENABLED', TRUE);
define('CODEIGNITER_SMTP_USER', 'lunarpaytests@gmail.com');
define('CODEIGNITER_SMTP_PASS', 'jipzjqvfanirjpge');
define('MAILGUN_DOMAIN', ''); //==========// https://app.mailgun.com/app/domains
define('MAILGUN_API_KEY', '');

//================
define('PROVIDER_MESSENGER_TEST', FALSE);
//==== TWILIO KEYS
define('TWILIO_ACCOUNT_SID_LIVE', '');
define('TWILIO_AUTH_TOKEN_LIVE', '');

define('TWILIO_ACCOUNT_SID_TEST', '');
define('TWILIO_AUTH_TOKEN_TEST', '');

define('TWILIO_ACCOUNT_SID', PROVIDER_MESSENGER_TEST ? TWILIO_ACCOUNT_SID_TEST : TWILIO_ACCOUNT_SID_LIVE);
define('TWILIO_AUTH_TOKEN', PROVIDER_MESSENGER_TEST ? TWILIO_AUTH_TOKEN_TEST : TWILIO_AUTH_TOKEN_LIVE);
//==== TWILIO KEYS ===========

define('APPCUES_ENABLED', FALSE);

//====== GOOD BARBER
define('GOODBARBER_COOKIES', 'c:\xampp\htdocs\chatgive\application\third_party\gbcookies\\');
define('GOODBARBER_RESELLER_USERNAME', '');
define('GOODBARBER_RESELLER_PASSWORD', '');
define('GOODBARBER_APP_WITH_ORGNX', FALSE); //==== Create app only if there is an orgnx verified

//====== RECAPTCHA
define('RECAPTCHA_ENABLED', TRUE);
define('RECAPTCHA_SECRET_KEY', '');
define('RECAPTCHA_PUBLIC_KEY', '');
define('RECAPTCHA_THRESHOLD', 0.6); //===== RECAPTCHA SUCCESS IF SCORE >= THRESHOLD [VALUES BETWEEN 0.1 & 1]

//====== PLANNING CENTER OAUTH
define('PLANNINGCENTER_REDIRECT_URL', BASE_URL . 'integrations/planningcenter/oauthcomplete');
define('PLANNINGCENTER_TOKEN_URL', 'https://api.planningcenteronline.com/oauth/token');
define('PLANNINGCENTER_CLIENT_ID', '');
define('PLANNINGCENTER_SECRET', '');

//===== INTERCOM
define('FORCE_HIDE_INTERCOM', TRUE);

//===== GOOGLE
define('GOOGLE_CODE_API',''); //<<<< Adolfo's Key, we need a chatgive google key (console.developers.com)

//Organization IDS that we want to use as test church, overriden LIVE to TEST environment / works for donor processes, not for onboarding
define('TEST_ORGNX_IDS', [5]);

//this is temporal while we pull the configuration from the dashboard/database
define('FORCE_MULTI_FUNDS', true);

define('ZAPIER_POLLING_KPIS_USER', '');                                    
define('ZAPIER_POLLING_KPIS_PASS', '');

require_once('constants_ext.php');


