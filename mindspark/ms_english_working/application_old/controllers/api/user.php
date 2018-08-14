<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class User extends REST_Controller {

protected $methods = array(
        'update_user_profile_post' => array('level' => 10, 'limit' => CONTROLLER_CALL_LIMIT),
        'logout_post' => array('level' => 10, 'limit' => CONTROLLER_CALL_LIMIT),
        'change_password_post' => array('level' => 10, 'limit' => CONTROLLER_CALL_LIMIT)
    );
    function __construct() {
        parent::__construct();
       
       if(!check_access_token()) {
            $array = $this->common_model->set_response_array(0,'auth001',"Unauthorized Access");
            $this->response($array, 401);
        }
        $this->load->model('user_model');
        $this->load->model('session_model');
    }
    function update_user_profile_post()
    {
       
        if (checkparams($this->post(), array( 'dob','gender','email','city','father_name','father_email','father_mobile','mother_name','mother_email','mother_mobile','residence_phone'))) 
                 {
                        
                        $userID = get_userID();
                        $this->user_model->update_user_information($this->post(),$userID);
                        $data = $this->common_model->set_response_array(1,'0','user information saved successfully');
                        $this->response($data, 200);
                }   
                else
                {
                    $msg = 'Please provide all correct parameters. Follow the Api Doc given';
                    $data = $this->common_model->set_response_array(0,'param001', $msg);
                    $this->response($data, 200);
                }
    }
    function logout_post()
    {
        $userID = get_userID();
        $this->user_model->logout($userID);
        $data = $this->common_model->set_response_array(1,'0','user logged out successfully');
        $this->response($data, 200);
    }
    function get_secret_question_post()
    {
        $userID = get_userID();
        $this->user_model->logout($userID);
        $data = $this->common_model->set_response_array(1,'0','user logged out successfully');
        $this->response($data, 200);
    }
    function change_password_post()
    {
          if (checkparams($this->post(), array( 'password'))) 
                 {
                        extract($this->post());
                        $userID = get_userID();
                        $this->user_model->change_password($userID,$password);
                        $data = $this->common_model->set_response_array(1,'0','user password successfully changed');
                        $this->response($data, 200);
                }   
                else
                {
                     $msg = 'Please provide all correct parameters. Follow the Api Doc given';
                    $data = $this->common_model->set_response_array(0,'param001', $msg);
                    $this->response($data, 200);
                }
        
    }
    function get_session_list_post()
    {
        $user_id = get_userID();
        $session_content = $this->session_model->get_session_list($user_id);
        $array = $this->common_model->set_response_array($session_content['eiSuccess'],$session_content['eiCode'],$session_content['eiMsg'],$session_content['data']);
        $this->response($array, 200);
    }

}
