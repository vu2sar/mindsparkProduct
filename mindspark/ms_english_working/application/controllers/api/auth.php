<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class Auth extends REST_Controller {

  protected $methods = array(
        'login_post' => array('level' => 10, 'limit' => CONTROLLER_CALL_LIMIT)
    );
    function __construct() {
        parent::__construct();

        $this->load->model('auth_model');
        $this->load->model('session_model');
    }

    function login_post(){
             
             if (checkparams($this->post(), array('username', 'password','device_token','device_unique_id','device_type','lat','long','device_details'),array('logout_from_last_session'))) 
                 {
                        extract($this->post());
                        $data_login = $this->auth_model->auth_check_api($username, $password,$device_token,$device_unique_id,$device_type,0,0);
                      
                        if($data_login)
                        {
                               
                                $data = $this->common_model->set_response_array(1,'0','user logged in successfully',$data_login);
                                $is_student_subscribed = $this->user_model->is_subscribed_student($data['student_details']['user_id']);
                                if($is_student_subscribed['status'] == 1)
                                {
                                    $check_login_status = $this->auth_model->check_user_logged_in($data['student_details']['user_id']);
                                    if($check_login_status)
                                    {
                                        if(isset($logout_from_last_session) and $logout_from_last_session == 1)
                                        {
                                                $this->user_model->logout($data['student_details']['user_id']);
                                                $access_token = generate_access_token($data['student_details']['user_id']);
                                                $data['access_token'] = $access_token;
                                                $this->session_model->create_session_api($data['student_details']['user_id'],$access_token,$lat,$long,$device_token,$device_unique_id,$device_details,$device_type);
                                                $this->response($data, 200);
                                        }
                                        else
                                        {
                                            $msg = 'You have already logged in';
                                            $data = $this->common_model->set_response_array(0,'AUTH003', $msg);
                                            $this->response($data, 200);
                                        }
                                        
                                    }
                                    else
                                    {
                                        $access_token = generate_access_token($data['student_details']['user_id']);
                                        $data['access_token'] = $access_token;
                                         $this->session_model->create_session_api($userID,$access_token,$lat,$long,$device_token,$device_id,$device_details,$device_type);
                                        $this->response($data, 200);
                                    }
                                }
                                else
                                {       $msg = $is_student_subscribed['message'];
                                        $data = $this->common_model->set_response_array(0,'AUTH004', $msg);
                                        $this->response($data, 200);
                                }
                                
                        }
                        else
                        {
                            $check_username = $this->auth_model->validate_username($username);
                            if($check_username)
                            {
                                $msg = 'Username Exists in database but password is not correct';
                                $data = $this->common_model->set_response_array(0,'auth001', $msg);
                                $this->response($data, 200);
                            }
                            else
                            {
                              
                                $msg = 'Username does not exits. Please register first.';
                                $data = $this->common_model->set_response_array(0,'auth002', $msg);
                                $this->response($data, 200);

                            }
                        }

                 }      
             else
                {
                    $msg = 'Please provide all correct parameters. Follow the Api Doc given';
                    $data = $this->common_model->set_response_array(0,'param001', $msg);
                    $this->response($data, 200);
                 
                }
    }
    

}
