<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('generate_access_token')) {

/**
This User Generate Access Token
**/
    function generate_access_token($userID) {

        $api_key_variable = config_item('rest_key_name');
        $key_name = 'HTTP_' . strtoupper(str_replace('-', '_', $api_key_variable));
        $key = $_SERVER[$key_name];

        $accesstoken = hash_hmac('md5', $key, $userID);
        $ci = & get_instance();
        $insertData = array(
            'userID' => $userID,
            'accesstoken' => $accesstoken,
            'dtCreated' => date('Y-m-d H:i:s')
        );
        $ci->db->insert('api_accesstoken', $insertData);
        if ($ci->db->affected_rows() > 0) {
            return $accesstoken;
        } else {
            return "";
        }
    }

    function check_access_token() {
        if (isset($_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'userID'))]) && $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'userID'))] != "" && isset($_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'accesstoken'))]) && $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'accesstoken'))] != "") {
            $ci = & get_instance();
            $q = $ci->db->get_where('api_accesstoken', array('userID' => $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'userID'))], 'accesstoken' => $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'accesstoken'))]));
            if ($q->num_rows() > 0) {
                $q = $q->row_array();
                $api_key_variable = config_item('rest_key_name');
                if (hash_hmac('md5', $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $api_key_variable))], $q['userID']) == $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'accesstoken'))]) {
                    return true;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function get_userID() {
        return $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', 'userID'))];
    }

    function delete_key() {
        $key = $_SERVER['HTTP_X_API_KEY'];
        $ci = & get_instance();
        if($ci->db->get_where('api_keys', array('key' => $key))->num_rows()>0){
            $ci->db->delete('api_keys', array('key' => $key));
        }
        
    }

}