<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!function_exists('printd')) {

    function printd($string) {
        echo '<pre>' . $string . '</pre>';
    }

}

function d($object, $die = true) {
    echo "<pre>";
    var_dump($object);
    echo "</pre>";
    if ($die) {
        die;
    }
}

function display_errors() {
    ini_set('display_errors', 1);
}

function display_errors2() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function output_json($data, $json_sent = false, $cache = false, $http_code = false) {
    $CI = & get_instance();
    if ($cache) {
        $CI->output->set_header("Pragma: no-cache");
        $CI->output->set_header("Cache-Control: no-store, no-cache");
    }

    if (defined('CSRF_TOKEN_AJAX_SENT')) {
        $data['new_token'] = [
            'name'  => CSRF_TOKEN_NAME,
            'value' => $CI->security->get_csrf_hash()
        ];
    }
    
    if($http_code) {
        http_response_code($http_code);
    }

    $CI->output->set_content_type('application/json');

    $CI->output->set_output($json_sent ? $data : json_encode($data));
}

function output_json_custom($data) {
    $http_code = $data['http_code'];

    if (defined('CSRF_TOKEN_AJAX_SENT')) {
        $CI = & get_instance();

        $data['new_token'] = [
            'name'  => CSRF_TOKEN_NAME,
            'value' => $CI->security->get_csrf_hash()
        ];
    }

    if ($http_code) {
        http_response_code($http_code);
    }

    header("Pragma: no-cache");
    header("Cache-Control: no-store, no-cache");
    header("Content-Type: application/json");

    echo json_encode($data);
}

function output_json_api($data, $error, $http_code) {
    
    http_response_code($http_code);
    
    header("Pragma: no-cache");
    header("Cache-Control: no-store, no-cache");
    header("Content-Type: application/json");

    echo json_encode(['error' => $error, 'response' => $data]);
}

//YIQ Algorithm
function getContrastColor($hexcolor)
{
    $r = hexdec(substr($hexcolor, 1, 2));
    $g = hexdec(substr($hexcolor, 3, 2));
    $b = hexdec(substr($hexcolor, 5, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 128) ? 'black' : 'white';
}

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 *
 * @param	string	$result		The language line
 * @param	string	$for		The "for" value (id of the form element)
 * @param	array	$attributes	Any additional HTML attributes
 * @return	string
 */
if (!function_exists('langx')) {

    function langx($line, $for = '', $attributes = array()) {

        $CI     = & get_instance();
        $result = $CI->lang->line(strtolower($line));

        if (!$result) {
            $index_arr = explode('_', $line);
            $result2   = '';

            foreach ($index_arr as $value) {
                $result2 .= ($value) . ' ';
            }
            $result = trim(ucwords($result2));
        }

        if ($for !== '') {
            $result = '<label for="' . $for . '"' . _stringify_attributes($attributes) . '>' . $result . '</label>';
        }

        return $result;
    }

}

if (!function_exists('checkBelongsToSession')) {

    function checkBelongsToUser($data) {

        $CI              = & get_instance();
        $next_self_value = null;

        if (count(end($data)) !== 4) {
            show_error('The last array must have 4 elements, the last element must be the user id');
        }


        foreach ($data as $row) {

            //==== get self/current table params
            $self       = array_splice($row, 0, 1);
            $self_keys  = array_keys($self)[0];
            $self_arr   = explode('.', $self_keys);
            $self_table = $self_arr[0];
            $self_field = $self_arr[1];
            $self_value = $self[$self_keys];

            if ($next_self_value) {
                $self_value = $next_self_value;
            }

            //var_dump($self_table, $self_field, $self_value);

            $fk_field = array_splice($row, 0, 1)[0];

            //==== get parent table params
            $parent       = array_splice($row, 0, 1)[0];
            $parent_arr   = explode('.', $parent);
            $parent_table = $parent_arr[0];
            $parent_field = $parent_arr[1];

            //var_dump($parent_table, $parent_field);

            $self_row = $CI->db->select($self_field . ', ' . $fk_field)
                            ->where($self_field, $self_value)
                            ->get($self_table)->row();
            //var_dump($self_field, $fk_field,$self_value,$self_table);

            if ($self_row) {
                $parent_value = $self_row->{$fk_field};

                if ($row) {
                    $user_id = $row[0];
                    $CI->db->where('id', $user_id);
                }
                $parent_row = $CI->db->select($parent_field)->where($parent_field, $parent_value)->get($parent_table)->row();

                if ($parent_row && $self_row->{$fk_field} == $parent_row->{$parent_field}) {
                    //===== okay continue
                    $next_self_value = $parent_value;
                } else {
                    return ['error' => 1, 'status' => false, 'message' => '<p>Id mismatch</p>'];
                }
            } else {
                return ['error' => 1, 'status' => false, 'message' => '<p>Id mismatch</p>'];
            }
        }
        return true;
    }

}

function toDateTime($unix) {
    return date('Y-m-d H:i:s', $unix);
}

function getOrganizationsIds($user_id) {
    $CI = & get_instance();

    $result = $CI->db->select('ch_id')
                    ->from('church_detail')
                    ->where('client_id', $user_id)
                    ->where('trash', 0)
                    ->get()->result_array();

    if ($result) {
        $ids = implode(',', array_column($result, 'ch_id'));        
    } else {
        $ids = 0;
        
    }
    
    return $ids;
}

function get_client_ip_from_trusted_proxy() {
    return $_SERVER['REMOTE_ADDR'];

    //===== in normal conditition HTTP_X_FORWARDED_FOR is not a reliable variable for getting the client ip
    /*
      if(!empty($_SERVER[ 'HTTP_X_FORWARDED_FOR' ])) {
      $HTTP_X_FORWARDED_FOR_LIST = explode(',', $_SERVER[ 'HTTP_X_FORWARDED_FOR' ]);
      //===== HTTP_X_FORWARDED_FOR could give us several ips separated by comas, with take the last one
      $remote_addr = trim(end($HTTP_X_FORWARDED_FOR_LIST));
      } else {
      $remote_addr = $_SERVER[ 'REMOTE_ADDR' ];
      }

      return $remote_addr;
     */
}

function custom_sort_desc_created($a, $b) {
    return $a["created"] < $b["created"];
}

function is_valid_domain_name($domain_name) {
    return (preg_match("/^([a-zd](-*[a-zd])*)(.([a-zd](-*[a-zd])*))*$/i", $domain_name) //valid characters check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^.]{1,63}(.[^.]{1,63})*$/", $domain_name) ); //length of every label
}

function validateRecaptcha($token, $action) {

    if (RECAPTCHA_ENABLED == false) {
        return ['status' => true, 'result' => []];
    }

    $url           = "https://www.google.com/recaptcha/api/siteverify";
    $recaptchaData = [
        'secret'   => RECAPTCHA_SECRET_KEY,
        'response' => $token,
        'remoteip' => get_client_ip_from_trusted_proxy()
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($recaptchaData)
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    $result = json_decode($response, true);

    if ($result['success'] == true && $result['action'] == $action) {
        if ($result['score'] >= RECAPTCHA_THRESHOLD) {
            return ['status' => true, 'result' => $result];
        }
    }

    return ['status' => false, 'result' => $result];
}

function exports_data_csv($name,$data){
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=\"".$name.".csv\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $handle = fopen('php://output', 'w');

    foreach ($data as $data) {
        fputcsv($handle, $data);
    }
    fclose($handle);
    exit;
}

function permissionClassHide($ep) {

    $CI = & get_instance();

    if ($CI->session->userdata('is_child') === TRUE) { //evaluate is team member only

        $current_endpoint = $ep;
        $permissions_arr  = $CI->session->userdata('permissions');

        foreach (MODULE_TREE as $row) {
            foreach ($row['endpoints'] as $endpoint) { //===== loop through elements that need observance
                if (strtolower($endpoint) == strtolower($current_endpoint)) { //===== endpoint need to be observed
                    foreach ($permissions_arr as $permission_id) {
                        if ($permission_id == $row['id']) { //===== if member permission is found in array do not hide
                            return '';
                        }
                    }
                }
            }
        }
        return 'permission-hide';
    }
    return '';
}

function permissionClassHideGroup($eps){
    $CI = & get_instance();
    
     if ($CI->session->userdata('is_child') === TRUE) {

            $permissions_arr  = $CI->session->userdata('permissions');
            
            foreach (MODULE_TREE as $row) {
                foreach ($row['endpoints'] as $endpoint) { //===== loop through endpoints that need observance
                    if (in_array(strtolower($endpoint), $eps)) { //===== endpoint need to be observed
                        foreach ($permissions_arr as $permission_id) {
                            if ($permission_id == $row['id']) {
                                return ''; //if at least one endpoint is allowed do not hide
                            }
                        }
                    }
                }
            }
        return 'permission-hide';
    }

}

function slugify($string){
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

function isValidDate($date) {
    return (bool) strtotime($date);
}

function trimLR_Duplicates($text) { //remove left and righ blank spaces, remove duplicated blank spaces
    return preg_replace('/\s+/', ' ', trim($text));    
}

function getCardFullYear($year){
    return substr(date('Y'),0,2).$year;
}

function stringifyFormatErrors($error_arr, $delimiters = ['<p>', '</p>']) {

    $error_string = '';

    foreach ($error_arr as $error) {
        $error_string .= $delimiters[0] . $error . $delimiters[1];
    }
    
    return $error_string;
}

define('LOG_CUSTOM_INFO', 'INFO');
define('LOG_CUSTOM_DEBUG', 'DEBUG');

function log_custom($type, $message) {    
    log_message('error', $type . '-CUSTOM-LOG ' . $message);
}
