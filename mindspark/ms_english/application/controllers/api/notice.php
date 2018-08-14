<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class Notice extends REST_Controller {

protected $methods = array(
        'notice_board_post' => array('level' => 10, 'limit' => CONTROLLER_CALL_LIMIT)
       
    );
    function __construct() {
        parent::__construct();
       
       if(!check_access_token()) {
            $array = $this->common_model->set_response_array(0,'auth001',"Unauthorized Access");
            $this->response($array, 401);
        }
        $this->load->model('notice_model');
        $this->load->model('user_model');
    }
    function notice_board_post()
    {
        $user_id = get_userID();
        $information_required = array('school_code','child_class','child_section');
        $data_user = $this->user_model->get_ms_user_details($user_id,$information_required);
        extract($data_user);
        $notice_board = $this->notice_model->notice_board_message_count($user_id,$school_code,$child_class,$child_section,0);
        $array = $this->common_model->set_response_array($notice_board['eiSuccess'],$notice_board['eiCode'],$notice_board['eiMsg'],$notice_board['data']);
        $this->response($notice_board, 200);
               
    }
}