<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');
require(APPPATH . '/libraries/REST_Controller.php');

class Comment extends REST_Controller {

  protected $methods = array(
    'get_user_comments' => array('level' => 10, 'limit' => CONTROLLER_CALL_LIMIT),
  );

  function __construct() {
    parent::__construct();
    $this->load->model('comment_model');
    if(!check_access_token()) {
      $array = $this->common_model->set_response_array(0,'auth001',"Unauthorized Access");
      $this->response($array, 401);
    }
  }

  function get_comment_list_post() {
      if (checkparams($this->post(), array( 'no_of_comments'))) 
                 {
                        extract($this->post());
                        $userID = get_userID();
                        $data_comment = $this->comment_model->get_user_comments($userID,$no_of_comments);
                        $array = $this->common_model->set_response_array($data_comment['eiSuccess'],$data_comment['eiCode'],$data_comment['eiMsg'],$data_comment['data']);
                        $this->response($array, 200);
                }   
                else
                {
                     $msg = 'Please provide all correct parameters. Follow the Api Doc given';
                    $data = $this->common_model->set_response_array(0,'param001', $msg);
                    $this->response($data, 200);
                }
  }
}